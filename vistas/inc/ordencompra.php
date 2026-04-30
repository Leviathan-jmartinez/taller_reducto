<script>
    /**funcion buscar proveedor */
    function buscar_proveedorOC() {
        let input_proveedor = document.querySelector('#input_proveedor').value;
        input_proveedor = input_proveedor.trim();
        if (input_proveedor != "") {
            let datos = new FormData();
            datos.append("buscar_proveedorOC", input_proveedor);

            fetch("<?php echo SERVERURL ?>ajax/ordencompraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_proveedor = document.querySelector('#tabla_proveedorOC');
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
    function agregar_proveedorOC(id) {
        $('#ModalproveedorOC').modal('hide');

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
                datos.append("id_agregar_proveedorOC", id);

                fetch("<?php echo SERVERURL ?>ajax/ordencompraAjax.php", {
                        method: 'POST',
                        body: datos
                    })
                    .then(res => res.text())
                    .then(text => {
                        try {
                            let respuesta = JSON.parse(text);
                            return alertasAjax(respuesta);
                        } catch (e) {
                            console.error("Respuesta inválida:", text);
                            Swal.fire("Error", "El servidor devolvió algo inesperado", "error");
                        }
                    });
            } else {
                $('#ModalproveedorOC').modal('show');
            }
        });
    }

    function buscar_articuloOC() {
        let input_articulo = document.querySelector('#input_articulo').value;
        input_articulo = input_articulo.trim();
        if (input_articulo != "") {
            let datos = new FormData();
            datos.append("buscar_articuloOC", input_articulo);

            fetch("<?php echo SERVERURL ?>ajax/ordencompraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_articulosOC');
                    tabla_articulosOC.innerHTML = respuesta;
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
        $('#ModalArticuloOC').modal('hide');
        document.querySelector('#id_agregar_articulo').setAttribute("value", id);
    }

    function modal_buscar_articulo() {
        $('#ModalArticuloOC').modal('show');
    }

    function agregar_articuloOC(id) {
        $('#ModalArticuloOC').modal('hide');

        let cantidad = document.querySelector('#cantidad_' + id).value.trim();
        let precio = document.querySelector('#precio_' + id).value.trim();

        let datos = new FormData();
        datos.append('id_agregar_articuloOC', id);
        datos.append('detalle_cantidad', cantidad);
        datos.append('detalle_precio', precio);

        fetch("<?php echo SERVERURL ?>ajax/ordencompraAjax.php", {
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

    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("generar-oc-btn")) {
            let id = e.target.getAttribute("data-id");

            // Guardar el ID global para usarlo en btnGuardarOC
            window.idPresupuestoActual = id;

            fetch("<?php echo SERVERURL; ?>ajax/presupuestoDetalleAjax.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        idpresupuesto: id
                    })
                })
                .then(res => res.text())
                .then(html => {
                    document.querySelector("#tbodyDetallePresupuesto").innerHTML = html;
                    $('#modalDetallePresupuesto').modal('show');
                });
        }
    });

    document.getElementById("filtroProductos").addEventListener("keyup", function() {
        let filtro = this.value.toLowerCase();
        document.querySelectorAll("#tbodyDetallePresupuesto tr").forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filtro) ? "" : "none";
        });
    });

    document.getElementById("btnGuardarOC").addEventListener("click", function() {

        let form = document.getElementById("formOcProductos");
        let datos = new FormData(form);

        // Aquí le pasamos el ID del presupuesto seleccionado
        datos.append("idpresupuesto", window.idPresupuestoActual);

        // Si quieres, también puedes pasar módulo u otros datos
        datos.append("modulo", "ordencompra");

        fetch("<?php echo SERVERURL; ?>ajax/ordencompraAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {

                if (r.includes("ok:")) {
                    let idOC = r.replace("ok:", "");
                    Swal.fire("OC generada", "N° " + idOC, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", r, "error");
                }
            });
    });
</script>
