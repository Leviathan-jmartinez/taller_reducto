<script>
    /**funcion buscar proveedor */
    function buscar_proveedorPre() {
        let input_proveedor = document.querySelector('#input_proveedor').value;
        input_proveedor = input_proveedor.trim();
        if (input_proveedor != "") {
            let datos = new FormData();
            datos.append("buscar_proveedorPre", input_proveedor);

            fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_proveedor = document.querySelector('#tabla_proveedorPre');
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
    function agregar_proveedorPre(id) {
        $('#ModalproveedorPre').modal('hide');

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
                datos.append("id_agregar_proveedorPre", id);

                fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertasAjax(respuesta);
                    });
            } else {
                $('#ModalproveedorPre').modal('show');
            }
        });
    }

    function buscar_articuloPre() {
        let input_articulo = document.querySelector('#input_articulo').value;
        input_articulo = input_articulo.trim();
        if (input_articulo != "") {
            let datos = new FormData();
            datos.append("buscar_articuloPre", input_articulo);

            fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_articulosPre');
                    tabla_articulosPre.innerHTML = respuesta;
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
        $('#ModalArticuloPre').modal('hide');
        document.querySelector('#id_agregar_articulo').setAttribute("value", id);
    }

    function modal_buscar_articulo() {
        $('#ModalArticuloPre').modal('show');
    }

    function agregar_articuloPre(id) {
        $('#ModalArticuloPre').modal('hide');

        let cantidad = document.querySelector('#cantidad_' + id).value.trim();
        let precio = document.querySelector('#precio_' + id).value.trim(); 

        let datos = new FormData();
        datos.append('id_agregar_articuloPre', id);
        datos.append('detalle_cantidad', cantidad);
        datos.append('detalle_precio', precio); 

        fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
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

    function buscar_pedidoPre(){
        let input_pedido = document.querySelector('#input_pedido').value;
        input_pedido = input_pedido.trim();
        if (input_pedido != "") {
            let datos = new FormData();
            datos.append("buscar_pedidoPre", input_pedido);

            fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_pedidosPre');
                    tabla_pedidosPre.innerHTML = respuesta;
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
</script>