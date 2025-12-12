<script src="<?php echo SERVERURL; ?>vistas/js/jquery-3.6.0.min.js"></script>

<script>
    function buscar_OC() {
        let input_oc = document.querySelector('#input_oc').value;
        input_oc = input_oc.trim();
        if (input_oc != "") {
            let datos = new FormData();
            datos.append("buscar_oc", input_oc);

            fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_OC');
                    tabla_OC.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el Número de OC o el Nombre del Proveedor',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function agregar_OC(idcoseleccionado) {
        if (!idcoseleccionado) return;

        // Crear form dinámico para POST
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/compraAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_oc_seleccionado';
        input.value = idcoseleccionado;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit(); // envía y recarga la página
    }

    document.addEventListener("DOMContentLoaded", function() {

        // Temporizadores por fila para debounce
        const timers = {};

        function recalcularYActualizar(fila) {
            let index = fila.dataset.index;

            // Leer valores
            let cantidad = parseFloat(fila.querySelector(".cantidad").value) || 0;
            let precioTexto = fila.querySelector(".precio").value;
            let precio = parseFloat(precioTexto.replace(/\./g, '').replace(',', '.')) || 0;

            // Subtotal e IVA
            let subtotal = cantidad * precio;
            fila.querySelector(".subtotal").innerText = subtotal.toLocaleString('es-ES');
            let divisor = parseFloat(fila.dataset.divisor);
            let iva = divisor > 0 ? subtotal / divisor : 0;
            fila.querySelector(".iva-monto").innerText = iva.toLocaleString('es-ES');

            // Totales generales en pantalla
            recalcularTotalesGenerales();

            // Debounce: actualizar sesión después de 500ms de inactividad
            clearTimeout(timers[index]);
            timers[index] = setTimeout(() => {
                fetch("<?php echo SERVERURL; ?>ajax/compraAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            index: index,
                            cantidad: cantidad,
                            precio: precio,
                            subtotal: subtotal,
                            iva: iva
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        console.log("SESSION ACTUALIZADA:", data);
                    });
            }, 1000); // 0.5s
        }

        function recalcularTotalesGenerales() {
            let iva5 = 0,
                iva10 = 0,
                subtotalGeneral = 0;
            document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
                let sub = parseFloat(fila.querySelector(".subtotal").innerText.replace(/\./g, '').replace(',', '.')) || 0;
                let iva = parseFloat(fila.querySelector(".iva-monto").innerText.replace(/\./g, '').replace(',', '.')) || 0;
                let rate = parseFloat(fila.dataset.rate);
                if (rate === 0.05) iva5 += iva;
                if (rate === 0.10) iva10 += iva;
                subtotalGeneral += sub;
            });

            let totalIVA = iva5 + iva10;
            let totalFactura = subtotalGeneral;

            document.getElementById("iva5").innerText = iva5.toLocaleString('es-ES');
            document.getElementById("iva10").innerText = iva10.toLocaleString('es-ES');
            document.getElementById("total-iva").innerText = totalIVA.toLocaleString('es-ES');
            document.getElementById("subtotal-general").innerText = subtotalGeneral.toLocaleString('es-ES');
            document.getElementById("total-factura").innerText = totalFactura.toLocaleString('es-ES');

            document.getElementById("input-subtotal-general").value = subtotalGeneral.toFixed(0);
            document.getElementById("input-iva-total").value = totalIVA.toFixed(0);
            document.getElementById("input-total-factura").value = totalFactura.toFixed(0);
            document.getElementById("input-iva5").value = iva5.toFixed(0);
            document.getElementById("input-iva10").value = iva10.toFixed(0);
        }

        // Inicializar totales al cargar
        document.querySelectorAll("#tabla-detalle tbody tr").forEach(fila => {
            recalcularTotalesGenerales();
        });

        // Detectar cambios en inputs con debounce
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("cantidad") || e.target.classList.contains("precio")) {
                let fila = e.target.closest("tr");
                recalcularYActualizar(fila);
            }
        });

    });

    $(document).on("click", "#btnSinOC", function(e) {
        e.preventDefault(); // evita que el formulario capture el click
        e.stopPropagation(); // evita que FormularioAjax lo capture

        $.post("", {
            factura_tipo: "sin_oc"
        }, function(resp) {
            window.location.href = "<?php echo SERVERURL; ?>factura-nuevo";
            // aquí SERVERURL apunta a index.php?vistas=factura-nuevo
        });
    });

    function buscar_proveedorCO() {
        let input_proveedor = document.querySelector('#input_proveedor').value;
        input_proveedor = input_proveedor.trim();
        if (input_proveedor != "") {
            let datos = new FormData();
            datos.append("buscar_proveedorCO", input_proveedor);

            fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_proveedorCO = document.querySelector('#tabla_proveedorCO');
                    tabla_proveedorCO.innerHTML = respuesta;
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
    function agregar_proveedorCO(id) {
        $('#ModalproveedorCO').modal('hide');

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
                datos.append("id_agregar_proveedorCO", id);

                fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        return alertasAjax(respuesta);
                    });
            } else {
                $('#ModalproveedorCO').modal('show');
            }
        });
    }

    function buscar_articuloCO() {
        let input_articulo = document.querySelector('#input_articulo').value;
        input_articulo = input_articulo.trim();
        if (input_articulo != "") {
            let datos = new FormData();
            datos.append("buscar_articuloCO", input_articulo);

            fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articuloCO = document.querySelector('#tabla_articuloCO');
                    tabla_articuloCO.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el CÓDIGO o NOMBRE del artículo',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function agregar_articuloCO(id) {
        $('#ModalArticuloCO').modal('hide');

        let cantidad = document.querySelector('#cantidad_' + id).value.trim();
        let precio = document.querySelector('#precio_' + id).value.trim();

        let datos = new FormData();
        datos.append('id_agregar_articuloCO', id);
        datos.append('detalle_cantidad', cantidad);
        datos.append('detalle_precio', precio);

        fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
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