<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let indexDetalle = 0;


    function abrirModalRecepcion() {
        $("#modalRecepcion").modal("show");
    }

    function buscarRecepcionAjax() {

        let valor = $("#buscar_recepcion").val();

        $.ajax({
            url: SERVERURL + "ajax/diagnosticoAjax.php",
            method: "POST",
            data: {
                buscar_recepcion: valor
            },
            success: function(respuesta) {
                $("#tabla_recepciones").html(respuesta);
            }
        });
    }

    function seleccionarRecepcion(id, desc, sucursal) {

        $("#idrecepcion").val(id);
        $("#recepcion_info").val(desc);
        $("#id_sucursal").val(sucursal);
        $("#modalRecepcion").modal("hide");
    }

    function agregarDetalleDiagnostico() {

        const tbody = document.getElementById('detalleDiagnostico');

        let fila = `
        <tr id="fila_${indexDetalle}">

            <td>
                <input type="text" name="detalles[${indexDetalle}][sistema]" 
                    class="form-control" required>
            </td>

            <td>
                <input type="text" name="detalles[${indexDetalle}][problema]" 
                    class="form-control" required>
            </td>

            <td>
                <select name="detalles[${indexDetalle}][gravedad]" 
                    class="form-control">
                    <option value="leve">Leve</option>
                    <option value="media">Media</option>
                    <option value="grave">Grave</option>
                </select>
            </td>

            <td>
                <input type="text" name="detalles[${indexDetalle}][solucion_propuesta]" 
                    class="form-control">
            </td>

            <td class="text-center">
                <input type="checkbox" 
                    name="detalles[${indexDetalle}][requiere_repuesto]" 
                    value="1">
            </td>

            <td class="text-center">
                <input type="checkbox" 
                    name="detalles[${indexDetalle}][requiere_mano_obra]" 
                    value="1" checked>
            </td>

            <td class="text-center">
                <button type="button" 
                    class="btn btn-danger btn-sm"
                    onclick="eliminarDetalle(${indexDetalle})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>

        </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', fila);

        indexDetalle++;
    }

    /* ================= ELIMINAR DETALLE ================= */
    function eliminarDetalle(index) {
        const fila = document.getElementById('fila_' + index);
        if (fila) fila.remove();
    }
    /* ================= LIMPIAR FORM ================= */
    function limpiarDiagnostico() {

        // reset form
        const form = document.querySelector('.FormularioAjax');
        if (form) form.reset();

        // limpiar recepción
        document.getElementById('idrecepcion').value = '';
        document.getElementById('recepcion_info').value = '';

        // 🔥 OCULTAR RECLAMO
        document.getElementById("alerta_reclamo").style.display = "none";
        document.getElementById("card_reclamo").style.display = "none";

        // 🔥 LIMPIAR DATOS
        document.getElementById("rec_desc").innerText = '';
        document.getElementById("rec_tipo").innerText = '';
        document.getElementById("rec_prioridad").innerHTML = '';
        document.getElementById("rec_fecha").innerText = '';

        // limpiar tabla detalle
        document.getElementById("detalleDiagnostico").innerHTML = '';

        // reset selects de reclamo
        document.querySelector('[name="es_reclamo_valido"]').value = "1";
        document.querySelector('[name="es_garantia"]').value = "1";
        document.querySelector('[name="requiere_cobro"]').value = "0";
        document.getElementById("bloque_reclamo_resultado").style.display = "none";
    }

    /* ================= CARGAR EQUIPOS ================= */
    function cargarEquiposDiagnostico() {

        const select = document.getElementById('id_equipo');
        if (!select) return;

        select.innerHTML = '<option value="">Cargando...</option>';

        fetch(SERVERURL + 'ajax/diagnosticoAjax.php', {
                method: "POST",
                body: new URLSearchParams({
                    accion: "listar_equipos"
                })
            })
            .then(r => r.json())
            .then(data => {

                let html = '<option value="">Seleccione equipo</option>';

                data.forEach(e => {
                    html += `<option value="${e.id_equipo}">
                        ${e.nombre}
                     </option>`;
                });

                select.innerHTML = html;
            });
    }

    /* ================= INIT ================= */
    document.addEventListener('DOMContentLoaded', () => {
        cargarEquiposDiagnostico();
    });

    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-anular');
        if (!btn) return;

        const id = btn.dataset.id;

        Swal.fire({
            title: '¿Anular diagnóstico?',
            text: 'El diagnóstico será anulado y no podrá utilizarse en el flujo',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (!result.value) return;

            fetch(SERVERURL + "ajax/diagnosticoAjax.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        accion: "anular_diagnostico",
                        id_diagnostico: id
                    })
                })
                .then(r => r.json())
                .then(res => {

                    // 🔥 usar tu sistema central de alertas
                    alertasAjax(res);

                })
                .catch(err => console.error("ERROR:", err));
        });
    });


    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch(SERVERURL + "ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "diagnostico",
                    eliminar_busqueda: 1
                })
            })
            .then(r => r.json())
            .then(res => {

                console.log("RESPUESTA LIMPIAR:", res);

                if (res.Alerta === "redireccionar") {
                    window.location.href = res.URL;
                } else {
                    alert(res.Texto);
                }
            });
    });


    function getPrioridadTexto(p) {
        switch (parseInt(p)) {
            case 1:
                return '<span class="badge badge-secondary">Baja</span>';
            case 2:
                return '<span class="badge badge-warning">Media</span>';
            case 3:
                return '<span class="badge badge-danger">Alta</span>';
            default:
                return '<span class="badge badge-dark">-</span>';
        }
    }

    function seleccionarRecepcion(id, desc, sucursal, origen = 'NORMAL', idreclamo = null) {

        $("#idrecepcion").val(id);
        $("#recepcion_info").val(desc);
        $("#id_sucursal").val(sucursal);

        $("#modalRecepcion").modal("hide");

        const esReclamo = (origen || '').trim().toUpperCase() === 'RECLAMO';

        if (esReclamo) {

            $("#alerta_reclamo").show();
            $("#card_reclamo").show();
            $("#bloque_reclamo_resultado").show(); // 🔥 ESTE ES EL QUE TE FALLA

            if (idreclamo && idreclamo !== "null" && idreclamo !== "undefined") {

                fetch(SERVERURL + "ajax/diagnosticoAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `accion=obtener_reclamo_detalle&idreclamo=${idreclamo}`
                    })
                    .then(r => r.json())
                    .then(data => {

                        document.getElementById("rec_desc").innerText = data.descripcion || '-';
                        document.getElementById("rec_fecha").innerText = data.fecha_reclamo || '-';
                        document.getElementById("rec_tipo").innerText = data.tipo_reclamo || '-';
                        document.getElementById("rec_prioridad").innerHTML = getPrioridadTexto(data.prioridad);

                    })
                    .catch(err => console.error("ERROR JS:", err));
            }

        } else {

            $("#alerta_reclamo").hide();
            $("#card_reclamo").hide();
            $("#bloque_reclamo_resultado").hide();
        }
    }

    function evaluarDiagnostico(id, esReclamo, esGarantia, requiereCobro, idReclamo) {

        esReclamo = parseInt(esReclamo) || 0;
        esGarantia = parseInt(esGarantia) || 0;
        requiereCobro = parseInt(requiereCobro) || 0;

        if (esReclamo === 1) {

            if (esGarantia === 1 && requiereCobro === 0) {

                Swal.fire({
                    title: "Reclamo en garantía",
                    text: "¿Desea generar la OT directamente?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, generar OT"
                }).then((result) => {
                    if (result.isConfirmed) {
                        crearOTReclamo(id, idReclamo);
                    }
                });

            } else {

                Swal.fire({
                    title: "Reclamo con costo",
                    text: "Se debe generar presupuesto",
                    icon: "info"
                }).then(() => {
                    window.location.href = SERVERURL + "presupuesto-servicio-nuevo/" + id;
                });

            }

        } else {

            // NORMAL
            window.location.href = SERVERURL + "presupuesto-servicio-nuevo/" + id;
        }
    }

    function crearOTReclamo(idDiagnostico, idReclamo) {

        let datos = new URLSearchParams();
        datos.append("accion", "crear_ot_reclamo");
        datos.append("id_diagnostico", idDiagnostico);
        datos.append("idreclamo_servicio", idReclamo);

        fetch(SERVERURL + "ajax/ordenTrabajoAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: datos
            })
            .then(r => r.json())
            .then(r => {
                if (r.Alerta === "recargar") {
                    location.reload();
                } else {
                    Swal.fire(r.Titulo, r.Texto, r.Tipo);
                }
            });
    }
</script>