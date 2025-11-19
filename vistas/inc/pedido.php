<script>
    /**funcion buscar proveedor */
    function buscar_proveedor() {
        let input_proveedor = document.querySelector('#input_proveedor').value;
        input_proveedor = input_proveedor.trim();
        if (input_proveedor != "") {
            let datos = new FormData();
            datos.append("buscar_proveedor", input_proveedor);

            fetch("<?php echo SERVERURL ?>ajax/pedidoAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_proveedor = document.querySelector('#tabla_proveedor');
                    tabla_proveedor.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el RUC o RAZON SOCIAL',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }
    /**agregar proveedor */
    function agregar_proveedor(id) {
        $('#Modalproveedor').modal('hide');

        Swal.fire({
            title: '¿Quieres agregar este proveedor?',
            text: 'Se va agregar este proveedor al pedido',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#008000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, agregar',
            cancelButtonText: 'No, Cancelar'
        }).then((result) => {
            if (result.value) {
                let datos = new FormData();
                datos.append("id_agregar_proveedor", id);

                fetch("<?php echo SERVERURL ?>ajax/pedidoAjax.php", {
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertasAjax(respuesta);
                    });
            } else {
                $('#Modalproveedor').modal('show');
            }
        });
    }

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
                    tabla_articulos.innerHTML = respuesta;
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
        $('#ModalAgregarArticulo').modal('show');
        document.querySelector('#id_agregar_articulo').setAttribute("value", id);
    }

    function modal_buscar_articulo() {
        $('#ModalAgregarArticulo').modal('hide');
        $('#ModalArticulo').modal('show');
    }


    /** AGREGAR ARTÍCULO
    function agregar_articulo(id) {
        $('#ModalArticulo').modal('hide');
        let cantidad = document.querySelector('#cantidad_' + id).value.trim();
        let datos = new FormData();
        datos.append('id_agregar_articulo', id);
        datos.append('detalle_cantidad', cantidad);

        fetch("<?php echo SERVERURL ?>ajax/pedidoAjax.php", {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.json()) // ya es JSON válido
            .then(datosJSON => {
                Swal.fire({
                    title: 'Articulo agregado!',
                    text: 'El articulo ha sido agregado',
                    type: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Si la alerta indica recargar, actualizamos la página
                    if (datosJSON.Alerta === "recargar") {
                        location.reload(); // recarga toda la página
                        // Si solo quieres recargar la tabla, puedes llamar a tu función buscar_articulo()
                        // buscar_articulo();
                    }
                });
            })
            .catch(error => {
                console.error("Error en fetch o parseo JSON:", error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo procesar la petición',
                    type: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
    }**/
    function agregar_articulo(id) {
        $('#ModalArticulo').modal('hide');
        let cantidad = document.querySelector('#cantidad_' + id).value.trim();

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
                    type: resp.Tipo, // <<-- aquí sí usamos el tipo real
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