<script>
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

    function agregar_proveedorPre(id) {
        $('#ModalproveedorPre').modal('hide');

        Swal.fire({
            title: 'Quieres agregar este proveedor?',
            text: 'Se va agregar este proveedor al presupuesto',
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
                    let tabla_pedidos = document.querySelector('#tabla_pedidosPre');
                    tabla_pedidos.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el parámetro de búsqueda',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function agregar_pedidoPre(idPedido) {
        if (!idPedido) return;

        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/presupuestoAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_pedido_seleccionado';
        input.value = idPedido;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    let timer;

    function formatearNumero(num) {
        return Number(num || 0).toLocaleString('de-DE');
    }

    function limpiarNumero(str) {
        return parseFloat(String(str).replace(/\./g, '')) || 0;
    }

    function actualizarTotales(enviar = false) {
        let totalUnidades = 0;
        let totalGeneral = 0;

        document.querySelectorAll(".precio-articulo").forEach(input => {
            const tr = input.closest("tr");
            const cantidad = parseFloat(tr.querySelector("td:nth-child(4)").innerText) || 0;
            const precio = limpiarNumero(input.value);
            const subtotal = cantidad * precio;

            tr.querySelector(".subtotal-articulo").innerText = formatearNumero(subtotal);

            totalUnidades += cantidad;
            totalGeneral += subtotal;

            if (enviar) {
                const idArticulo = input.dataset.id;
                const formData = new FormData();

                if (precio <= 0) {
                    Swal.fire({
                        title: 'Precio invalido',
                        text: 'El precio del articulo debe ser mayor a cero',
                        type: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                formData.append("id_actualizar_precio", idArticulo);
                formData.append("precio", precio);

                fetch("<?php echo SERVERURL ?>ajax/presupuestoAjax.php", {
                    method: "POST",
                    body: formData
                })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        if (respuesta.Alerta !== "simple" || respuesta.Tipo !== "success") {
                            alertasAjax(respuesta);
                        }
                    });
            }
        });

        const totalUnidadesEl = document.getElementById("total-unidades");
        const totalGeneralEl = document.getElementById("total-general");

        if (totalUnidadesEl) totalUnidadesEl.innerText = totalUnidades + " unidades";
        if (totalGeneralEl) totalGeneralEl.innerText = formatearNumero(totalGeneral);
    }

    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("precio-articulo")) {
            clearTimeout(timer);

            let start = e.target.selectionStart;
            let valor = e.target.value.replace(/[^\d]/g, '');
            e.target.value = formatearNumero(valor);
            e.target.setSelectionRange(start, start);

            timer = setTimeout(() => {
                actualizarTotales(true);
            }, 400);
        }
    });

    window.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".precio-articulo").forEach(input => {
            input.value = formatearNumero(limpiarNumero(input.value));
        });
    });
</script>
