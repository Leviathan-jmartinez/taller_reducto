<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function agregarDetalleDiagnostico() {

        let fila = `
    <tr>
        <td>
            <input type="text" name="descripcion[]" class="form-control" required>
        </td>
        <td>
            <select name="tipo[]" class="form-control">
                <option value="1">Problema</option>
                <option value="2">Recomendación</option>
                <option value="3">Urgente</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm"
                onclick="eliminarFila(this)">
                X
            </button>
        </td>
    </tr>
    `;

        $("#detalleDiagnostico").append(fila);
    }

    function eliminarFila(btn) {
        $(btn).closest("tr").remove();
    }

    function limpiarDiagnostico() {

        // Reset del formulario completo
        document.querySelector("form").reset();

        // Limpiar recepción
        $("#idrecepcion").val("");
        $("#recepcion_info").val("");

        // Limpiar tabla de detalles
        $("#detalleDiagnostico").html("");

    }


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
</script>