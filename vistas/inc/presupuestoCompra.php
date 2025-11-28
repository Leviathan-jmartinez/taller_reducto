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

    function buscar_pedidoPre() {
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

    function agregar_pedidoPre(idPedido) {
        if (!idPedido) return;

        // Crear form dinámico para POST
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/presupuestoAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_pedido_seleccionado';
        input.value = idPedido;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit(); // envía y recarga la página
    }

    let timer;

    // Función para formatear número a estilo "1.234"
    function formatearNumero(num) {
        return num.toLocaleString('de-DE');
    }

    // Función para quitar puntos y devolver número real
    function limpiarNumero(str) {
        return parseFloat(str.replace(/\./g, '')) || 0;
    }

    function actualizarTotales(enviar = false) {
        let totalUnidades = 0;
        let totalGeneral = 0;

        document.querySelectorAll(".precio-articulo").forEach(input => {
            const tr = input.closest("tr");
            const cantidad = parseFloat(tr.querySelector("td:nth-child(4)").innerText) || 0;
            const precio = limpiarNumero(input.value);

            const subtotal = cantidad * precio;

            // Actualizar subtotal en la tabla
            tr.querySelector(".subtotal-articulo").innerText = formatearNumero(subtotal);

            totalUnidades += cantidad;
            totalGeneral += subtotal;

            // Guardar en sesión vía AJAX solo si enviar=true
            if (enviar) {
                const idArticulo = input.dataset.id;
                const formData = new FormData();
                formData.append("id_actualizar_precio", idArticulo);
                formData.append("precio", precio);

                fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                    method: "POST",
                    body: formData
                });
            }
        });

        // Actualizar fila TOTAL
        document.getElementById("total-unidades").innerText = totalUnidades + " unidades";
        document.getElementById("total-general").innerText = formatearNumero(totalGeneral);
    }

    // Escuchar cambios en los inputs de precio con debounce y formateo
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("precio-articulo")) {
            clearTimeout(timer);

            // Guardamos la posición del cursor
            let start = e.target.selectionStart;

            // Limpiamos todo menos números y formateamos
            let valor = e.target.value.replace(/[^\d]/g, '');
            e.target.value = formatearNumero(valor);

            // Restauramos posición del cursor
            e.target.setSelectionRange(start, start);

            timer = setTimeout(() => {
                actualizarTotales(true);
            }, 400);
        }
    });

    // Formatear al cargar la página los precios existentes
    window.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".precio-articulo").forEach(input => {
            input.value = formatearNumero(limpiarNumero(input.value));
        });
    });
</script>