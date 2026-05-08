<script>
    window.diagnosticoServerUrl = "<?php echo SERVERURL; ?>";
    let indexDetalle = 0;
    let recepcionTimer = null;

    function abrirModalRecepcion() {
        const input = document.getElementById('buscar_recepcion');
        if (input) input.focus();
    }

    function textoSeguro(valor) {
        return valor === null || valor === undefined || valor === '' ? '-' : valor;
    }

    function escaparHtml(valor) {
        const div = document.createElement('div');
        div.textContent = textoSeguro(valor);
        return div.innerHTML;
    }

    function buscarRecepcionAjax() {
        clearTimeout(recepcionTimer);

        recepcionTimer = setTimeout(() => {
            const input = document.getElementById('buscar_recepcion');
            const contenedor = document.getElementById('resultado_recepciones');
            if (!input || !contenedor) return;

            const valor = input.value.trim();

            if (valor.length < 3) {
                contenedor.innerHTML = '';
                contenedor.style.display = 'none';
                if (!document.getElementById('idrecepcion').value) {
                    ocultarDetalleRecepcion();
                }
                return;
            }

            if (document.getElementById('idrecepcion').value && valor !== document.getElementById('recepcion_info').value) {
                limpiarRecepcionDiagnostico(false);
            }

            $.ajax({
                url: window.diagnosticoServerUrl + "ajax/diagnosticoAjax.php",
                method: "POST",
                data: {
                    buscar_recepcion: valor
                },
                success: function(respuesta) {
                    contenedor.innerHTML = respuesta;
                    contenedor.style.display = 'block';
                }
            });
        }, 220);
    }

    function seleccionarRecepcion(id, desc, sucursal, origen = 'NORMAL', idreclamo = null) {
        $("#idrecepcion").val(id);
        $("#recepcion_info").val(desc);
        $("#buscar_recepcion").val(desc);
        $("#id_sucursal").val(sucursal);
        $("#resultado_recepciones").hide().html('');

        cargarDetalleRecepcion(id);

        const esReclamo = (origen || '').trim().toUpperCase() === 'RECLAMO';

        if (esReclamo) {
            $("#alerta_reclamo").show();
            $("#card_reclamo").show();
            $("#bloque_reclamo_resultado").show();

            if (idreclamo && idreclamo !== "null" && idreclamo !== "undefined") {
                fetch(window.diagnosticoServerUrl + "ajax/diagnosticoAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            accion: "obtener_reclamo_detalle",
                            idreclamo: idreclamo
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById("rec_desc").innerText = textoSeguro(data.descripcion);
                        document.getElementById("rec_fecha").innerText = textoSeguro(data.fecha_reclamo);
                        document.getElementById("rec_tipo").innerText = textoSeguro(data.tipo_reclamo);
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

    function cargarDetalleRecepcion(id) {
        fetch(window.diagnosticoServerUrl + "ajax/diagnosticoAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    accion: "obtener_recepcion_detalle",
                    idrecepcion: id
                })
            })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.idrecepcion) {
                    ocultarDetalleRecepcion();
                    return;
                }

                document.getElementById('det_recepcion_cliente').innerText = textoSeguro(data.cliente);
                document.getElementById('det_recepcion_vehiculo').innerText = textoSeguro(data.vehiculo);
                document.getElementById('det_recepcion_fecha').innerText = textoSeguro(data.fecha_ingreso);
                document.getElementById('det_recepcion_km').innerText = textoSeguro(data.kilometraje);
                document.getElementById('det_recepcion_combustible').innerText = textoSeguro(data.nivel_combustible);
                document.getElementById('det_recepcion_servicio').innerText = textoSeguro(data.tipo_servicio);
                document.getElementById('det_recepcion_prioridad').innerText = textoSeguro(data.prioridad);
                document.getElementById('det_recepcion_area').innerText = textoSeguro(data.area_problema);
                document.getElementById('det_recepcion_exterior').innerText = textoSeguro(data.estado_exterior);
                document.getElementById('det_recepcion_accesorios').innerText = textoSeguro(data.accesorios);
                document.getElementById('det_recepcion_observacion').innerText = textoSeguro(data.observacion);

                const origen = document.getElementById('det_recepcion_origen');
                origen.innerText = textoSeguro(data.origen);
                origen.className = (data.origen || '').toUpperCase() === 'RECLAMO' ? 'badge badge-warning' : 'badge badge-secondary';

                document.getElementById('detalle_recepcion_seleccionada').style.display = 'block';
            })
            .catch(err => {
                console.error("ERROR detalle recepcion:", err);
                ocultarDetalleRecepcion();
            });
    }

    function ocultarDetalleRecepcion() {
        const panel = document.getElementById('detalle_recepcion_seleccionada');
        if (panel) panel.style.display = 'none';
    }

    function limpiarRecepcionDiagnostico(limpiarInput = true) {
        $("#idrecepcion").val('');
        $("#recepcion_info").val('');
        $("#id_sucursal").val('');
        $("#resultado_recepciones").hide().html('');

        if (limpiarInput) {
            $("#buscar_recepcion").val('');
        }

        ocultarDetalleRecepcion();
        $("#alerta_reclamo").hide();
        $("#card_reclamo").hide();
        $("#bloque_reclamo_resultado").hide();

        document.getElementById("rec_desc").innerText = '';
        document.getElementById("rec_tipo").innerText = '';
        document.getElementById("rec_prioridad").innerHTML = '';
        document.getElementById("rec_fecha").innerText = '';
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

    function eliminarDetalle(index) {
        const fila = document.getElementById('fila_' + index);
        if (fila) fila.remove();
    }

    function limpiarDiagnostico() {
        const form = document.querySelector('.FormularioAjax');
        if (form) form.reset();

        limpiarRecepcionDiagnostico();
        document.getElementById("detalleDiagnostico").innerHTML = '';
        indexDetalle = 0;

        const reclamoValido = document.querySelector('[name="es_reclamo_valido"]');
        const garantia = document.querySelector('[name="es_garantia"]');
        const cobro = document.querySelector('[name="requiere_cobro"]');

        if (reclamoValido) reclamoValido.value = "1";
        if (garantia) garantia.value = "1";
        if (cobro) cobro.value = "0";
    }

    function cargarEquiposDiagnostico() {
        const select = document.getElementById('id_equipo');
        if (!select) return;

        select.innerHTML = '<option value="">Cargando...</option>';

        fetch(window.diagnosticoServerUrl + 'ajax/diagnosticoAjax.php', {
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

    document.addEventListener('DOMContentLoaded', () => {
        cargarEquiposDiagnostico();
    });

    document.addEventListener('ajax:limpiar', function(e) {
        const modulo = e.detail ? e.detail.modulo : null;

        if (modulo && modulo !== 'diagnostico') {
            return;
        }

        limpiarDiagnostico();
        cargarEquiposDiagnostico();
    });

    document.addEventListener('click', function(e) {
        const itemRecepcion = e.target.closest('.diagnostico-autocomplete-item');
        if (itemRecepcion) {
            e.preventDefault();
            e.stopPropagation();

            seleccionarRecepcion(
                itemRecepcion.dataset.id,
                itemRecepcion.dataset.desc,
                itemRecepcion.dataset.sucursal,
                itemRecepcion.dataset.origen || 'NORMAL',
                itemRecepcion.dataset.reclamo || null
            );

            return;
        }

        const btnDetalleDiagnostico = e.target.closest('.btn-ver-diagnostico');
        if (btnDetalleDiagnostico) {
            e.preventDefault();
            e.stopPropagation();
            verDetalleDiagnostico(btnDetalleDiagnostico.dataset.id);
            return;
        }

        const resultados = document.getElementById('resultado_recepciones');
        const wrap = e.target.closest('.diagnostico-recepcion-wrap');

        if (resultados && !wrap) {
            resultados.style.display = 'none';
        }

        const btn = e.target.closest('.btn-anular');
        if (!btn) return;

        const id = btn.dataset.id;

        Swal.fire({
            title: 'Anular diagnostico?',
            text: 'El diagnostico sera anulado y no podra utilizarse en el flujo',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Si, anular',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.value) return;

            fetch(window.diagnosticoServerUrl + "ajax/diagnosticoAjax.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        accion: "anular_diagnostico",
                        id_diagnostico: id
                    })
                })
                .then(r => r.json())
                .then(res => {
                    alertasAjax(res);
                })
                .catch(err => console.error("ERROR:", err));
        });
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch(window.diagnosticoServerUrl + "ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "diagnostico",
                    eliminar_busqueda: 1
                })
            })
            .then(r => r.json())
            .then(res => {
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

    function evaluarDiagnostico(id, esReclamo, esGarantia, requiereCobro, idReclamo, esReclamoValido = 1) {
        esReclamo = parseInt(esReclamo) || 0;
        esGarantia = parseInt(esGarantia) || 0;
        requiereCobro = parseInt(requiereCobro) || 0;
        esReclamoValido = parseInt(esReclamoValido) || 0;

        if (esReclamo === 1) {
            if (esReclamoValido !== 1) {
                Swal.fire({
                    title: "Reclamo no valido",
                    text: "El diagnostico no habilita presupuesto ni OT por reclamo",
                    type: "warning"
                });
                return;
            }

            if (esGarantia === 1 && requiereCobro === 0) {
                Swal.fire({
                    title: "Reclamo en garantia",
                    text: "Desea generar la OT directamente?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, generar OT"
                }).then((result) => {
                    if (result.value) {
                        crearOTReclamo(id, idReclamo);
                    }
                });
            } else {
                Swal.fire({
                    title: "Reclamo con costo",
                    text: "Se debe generar presupuesto",
                    icon: "info"
                }).then(() => {
                    window.location.href = window.diagnosticoServerUrl + "presupuesto-servicio-nuevo/" + id;
                });
            }
        } else {
            window.location.href = window.diagnosticoServerUrl + "presupuesto-servicio-nuevo/" + id;
        }
    }

    function crearOTReclamo(idDiagnostico, idReclamo) {
        let datos = new URLSearchParams();
        datos.append("accion", "crear_ot_reclamo");
        datos.append("idreclamo_servicio", idReclamo);

        fetch(window.diagnosticoServerUrl + "ajax/ordenTrabajoAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: datos
            })
            .then(async r => {
                let txt = await r.text();
                if (!txt) throw "VACIO";

                return JSON.parse(txt);
            })
            .then(data => {
                if (data.Alerta === "recargar") {
                    location.reload();
                } else {
                    Swal.fire(data.Titulo, data.Texto, data.Tipo);
                }
            })
            .catch(err => {
                console.error("ERROR:", err);
                alert("Error en respuesta");
            });
    }

    function estadoDiagnosticoTexto(estado) {
        const estados = {
            0: 'Anulado',
            1: 'En proceso',
            2: 'Presupuestado',
            3: 'Finalizado'
        };

        return estados[parseInt(estado)] || 'Pendiente';
    }

    function renderBoolTexto(valor) {
        return parseInt(valor) === 1 ? 'Si' : 'No';
    }

    function verDetalleDiagnostico(id) {
        const contenedor = document.getElementById('contenidoDetalleDiagnostico');
        if (!contenedor) return;

        contenedor.innerHTML = '<div class="text-center text-muted py-4">Cargando...</div>';
        $('#modalDetalleDiagnostico').modal('show');

        fetch(window.diagnosticoServerUrl + "ajax/diagnosticoAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    accion: "obtener_diagnostico_detalle",
                    id_diagnostico: id
                })
            })
            .then(r => r.json())
            .then(data => {
                if (!data || !data.cabecera) {
                    contenedor.innerHTML = '<div class="alert alert-warning mb-0">No se encontro el diagnostico.</div>';
                    return;
                }

                const c = data.cabecera;
                const detalles = Array.isArray(data.detalles) ? data.detalles : [];
                const filas = detalles.length ? detalles.map((d) => `
                    <tr>
                        <td>${escaparHtml(d.sistema)}</td>
                        <td>${escaparHtml(d.problema)}</td>
                        <td>${escaparHtml(d.gravedad)}</td>
                        <td>${escaparHtml(d.solucion_propuesta)}</td>
                        <td class="text-center">${renderBoolTexto(d.requiere_repuesto)}</td>
                        <td class="text-center">${renderBoolTexto(d.requiere_mano_obra)}</td>
                    </tr>
                `).join('') : '<tr><td colspan="6" class="text-center text-muted">Sin detalles cargados.</td></tr>';

                contenedor.innerHTML = `
                    <div class="row">
                        <div class="col-md-3"><strong>Diagnostico:</strong><br>${escaparHtml(c.id_diagnostico)}</div>
                        <div class="col-md-3"><strong>Recepcion:</strong><br>${escaparHtml(c.idrecepcion)}</div>
                        <div class="col-md-3"><strong>Fecha:</strong><br>${escaparHtml(c.fecha_diagnostico)}</div>
                        <div class="col-md-3"><strong>Estado:</strong><br>${estadoDiagnosticoTexto(c.estado)}</div>
                        <div class="col-md-4 mt-3"><strong>Cliente:</strong><br>${escaparHtml(c.cliente)} (${escaparHtml(c.doc_number)})</div>
                        <div class="col-md-4 mt-3"><strong>Vehiculo:</strong><br>${escaparHtml(c.vehiculo)}</div>
                        <div class="col-md-4 mt-3"><strong>Equipo:</strong><br>${escaparHtml(c.equipo)}</div>
                        <div class="col-md-3 mt-3"><strong>Origen:</strong><br>${escaparHtml(c.origen)}</div>
                        <div class="col-md-3 mt-3"><strong>Servicio:</strong><br>${escaparHtml(c.tipo_servicio)}</div>
                        <div class="col-md-3 mt-3"><strong>Garantia:</strong><br>${renderBoolTexto(c.es_garantia)}</div>
                        <div class="col-md-3 mt-3"><strong>Requiere cobro:</strong><br>${renderBoolTexto(c.requiere_cobro)}</div>
                        <div class="col-md-12 mt-3"><strong>Observacion recepcion:</strong><br>${escaparHtml(c.recepcion_observacion)}</div>
                        <div class="col-md-12 mt-3"><strong>Observacion diagnostico:</strong><br>${escaparHtml(c.observaciones)}</div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sistema</th>
                                    <th>Problema</th>
                                    <th>Gravedad</th>
                                    <th>Solucion</th>
                                    <th>Repuesto</th>
                                    <th>Mano de obra</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                `;
            })
            .catch(err => {
                console.error("ERROR detalle diagnostico:", err);
                contenedor.innerHTML = '<div class="alert alert-danger mb-0">Error al cargar el detalle.</div>';
            });
    }
</script>
