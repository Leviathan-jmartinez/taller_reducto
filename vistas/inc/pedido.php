<script>

    function buscar_articulo() {
        let input_articulo = document.querySelector('#input_articulo').value;
        input_articulo = input_articulo.trim();
        if (input_articulo != "") {
            let datos = new FormData();
            datos.append("buscar_articulo", input_articulo);

            fetch("<?php echo SERVERURL ?>ajax/pedidoAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_articulos');
                    tabla_articulo.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el Código o el Nombre del articulo',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function modal_agregar_articulo(id) {
        $('#ModalArticulo').modal('hide');
        document.querySelector('#id_agregar_articulo').setAttribute("value", id);
    }

    function modal_buscar_articulo() {
        $('#ModalArticulo').modal('show');
    }

    function agregar_articulo(id) {
        $('#ModalArticulo').modal('hide');
        let cantidad = document.querySelector('#cantidad_' + id).value.trim();

        if (cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) {
            Swal.fire("Error", "La cantidad debe ser mayor a 0", "error");
            $('#ModalArticulo').modal('show');
            return;
        }

        let datos = new FormData();
        datos.append('id_agregar_articulo', id);
        datos.append('detalle_cantidad', cantidad);

        fetch("<?php echo SERVERURL ?>ajax/pedidoAjax.php", {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(resp => {

                Swal.fire({
                    title: resp.Titulo,
                    text: resp.Texto,
                    type: resp.Tipo, 
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    if (resp.Alerta === "recargar") {
                        location.reload();
                    }
                });
            })
            .catch(err => {
                console.error("Error:", err);
                Swal.fire("Error", "No se pudo procesar la petición", "error");
            });
    }
</script>
