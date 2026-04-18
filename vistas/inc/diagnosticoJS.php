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

    function seleccionarRecepcion(id, desc) {

        $("#idrecepcion").val(id);
        $("#recepcion_info").val(desc);

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

        const form = document.querySelector('.FormularioAjax');

        form.reset();

        // limpiar recepcion
        document.getElementById('idrecepcion').value = '';
        document.getElementById('recepcion_info').value = '';

        // limpiar tabla detalles
        document.getElementById('detalleDiagnostico').innerHTML = '';

        indexDetalle = 0;
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
</script>