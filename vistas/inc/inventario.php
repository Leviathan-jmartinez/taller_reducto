<script>
    document.addEventListener("DOMContentLoaded", function() {

        if (typeof window.jQuery === "undefined") {
            console.error("jQuery no esta disponible para inventario.");
            return;
        }

        $('#formInventario').on('submit', function(e) {
            e.preventDefault();

            // Deshabilitar botón y cambiar texto
            let $btn = $('#formInventario button[type="submit"]');
            $btn.prop('disabled', true).text('Guardando...');

            $.ajax({
                url: '<?= SERVERURL ?>ajax/inventarioAjax.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status && resp.status !== 'ok') {
                        Swal.fire({
                            title: "Error",
                            text: resp.msg || "No se pudo procesar el inventario",
                            type: "error",
                            confirmButtonText: "Aceptar"
                        });
                        return;
                    }

                    // Usar alertas.js
                    alertasAjax(resp);
                    // Si el tipo de alerta no recarga, igual limpiar formulario
                    if (resp.Alerta !== 'recargar') {
                        $('#modalInventario').modal('hide');
                        $('#formInventario')[0].reset();
                        $('#subtipo_categoria, #subtipo_proveedor, #subtipo_producto').val(null).trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error, xhr.responseText);
                    Swal.fire({
                        title: "Error",
                        text: "Ocurrió un error en la petición AJAX",
                        icon: "error",
                        confirmButtonText: "Aceptar"
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Guardar');
                }
            });
        });

        // Cambio de tipo de inventario para mostrar los subtipo correspondientes
        $('#tipo_inventario').on('change', function() {
            const tipo = $(this).val();

            // Ocultar todos los grupos
            $('#grupo_categoria, #grupo_proveedor, #grupo_producto').hide();
            $('#subtipo_categoria, #subtipo_proveedor, #subtipo_producto').empty();

            if (tipo === 'Categoria') {
                $('#grupo_categoria').show();
                $.post('<?= SERVERURL ?>ajax/inventarioAjax.php', {
                    cargar_categorias: true
                }, function(resp) {
                    let items = JSON.parse(resp);
                    items.forEach(item => {
                        $('#subtipo_categoria').append(`<option value="${item.id}">${item.nombre}</option>`);
                    });
                });
            }

            if (tipo === 'Proveedor') {
                $('#grupo_proveedor').show();
                $.post('<?= SERVERURL ?>ajax/inventarioAjax.php', {
                    cargar_proveedores: true
                }, function(resp) {
                    let items = JSON.parse(resp);
                    items.forEach(item => {
                        $('#subtipo_proveedor').append(`<option value="${item.id}">${item.nombre}</option>`);
                    });
                });
            }

            if (tipo === 'Producto') {
                $('#grupo_producto').show();
                $('#subtipo_producto').select2({
                    placeholder: 'Buscar artículos...',
                    ajax: {
                        url: '<?= SERVERURL ?>ajax/inventarioAjax.php',
                        type: 'POST',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                buscar_producto: params.term,
                                tipo_articulo_buscar: $('#tipo_articulo').val() || 'producto'
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }
        });

        $('#tipo_articulo').on('change', function() {
            $('#subtipo_producto').val(null).trigger('change');
        });

    });

    function buscar_INV() {
        let input_inv = document.querySelector('#input_inv').value.trim();
        let tabla_INV = document.querySelector('#tabla_INV');

        if (input_inv !== "") {

            let datos = new FormData();
            datos.append("buscar_inv", input_inv);

            fetch("<?php echo SERVERURL ?>ajax/inventarioAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    tabla_INV.innerHTML = respuesta;
                });

        } else {

            tabla_INV.innerHTML = `
            <div class="alert alert-warning" role="alert">
                <p class="text-center mb-0">
                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                    Debes introducir el Número de Inventario o la Descripción.
                </p>
            </div>
        `;
        }
    }


    function agregar_inv(idinvseleccionado) {
        if (!idinvseleccionado) return;

        // Crear form dinámico para POST
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/inventarioAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_inv_seleccionado';
        input.value = idinvseleccionado;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit(); // envía y recarga la página
    }


    document.addEventListener("DOMContentLoaded", function() {

        const timers = {};

        function recalcularYActualizar(fila) {

            let index = fila.dataset.index;
            let inputCantidad = fila.querySelector(".cantidad");

            let teorica = parseFloat(fila.querySelector(".teorica").value) || 0;
            let fisica = parseFloat(inputCantidad.value) || 0;

            if (fisica < 0) {
                fisica = 0;
                inputCantidad.value = 0;
            }

            let diferencia = fisica - teorica;

            // Mostrar diferencia en pantalla
            fila.querySelector(".diferencia").innerText = diferencia;

            // Debounce → guardar en sesión
            clearTimeout(timers[index]);
            timers[index] = setTimeout(() => {

                fetch("<?php echo SERVERURL; ?>ajax/inventarioAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            index: index,
                            cantidad_fisica: fisica,
                            diferencia: diferencia
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        console.log("SESSION ACTUALIZADA:", data);
                    });

            }, 800);
        }

        // Detectar cambios SOLO en cantidad física
        document.addEventListener("input", function(e) {
            if (e.target.classList.contains("cantidad")) {
                if (parseFloat(e.target.value) < 0) {
                    e.target.value = 0;
                }
                let fila = e.target.closest("tr");
                recalcularYActualizar(fila);
            }
        });

        document.addEventListener("paste", function(e) {
            if (!e.target.classList.contains("cantidad")) return;

            const texto = (e.clipboardData || window.clipboardData).getData("text");
            if (parseFloat(texto) < 0 || texto.includes("-")) {
                e.preventDefault();
                e.target.value = 0;
                recalcularYActualizar(e.target.closest("tr"));
            }
        });

    });


    document.addEventListener("DOMContentLoaded", function() {

        const filtro = document.getElementById("filtro-productos");
        const filas = document.querySelectorAll("#tabla-detalle tbody tr");

        if (!filtro) return;

        filtro.addEventListener("keyup", function() {

            const texto = this.value.toLowerCase().trim();

            filas.forEach(fila => {

                const codigo = fila.children[0].innerText.toLowerCase();
                const descripcion = fila.children[1].innerText.toLowerCase();

                const coincide =
                    codigo.includes(texto) ||
                    descripcion.includes(texto);

                fila.style.display = coincide ? "" : "none";
            });
        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const botonGuardar = document.getElementById("guardar-ajuste");
        if (!botonGuardar) return;

        botonGuardar.addEventListener("click", function(e) {
            e.preventDefault();

            const cantidades = document.querySelectorAll("#tabla-detalle .cantidad");
            for (const input of cantidades) {
                const valor = parseFloat(input.value) || 0;
                if (valor < 0) {
                    input.value = 0;
                    Swal.fire({
                        type: 'error',
                        title: 'Cantidad inválida',
                        text: 'No se puede guardar el ajuste con cantidades negativas.'
                    });
                    return;
                }
            }

            Swal.fire({
                title: '¿Desea guardar este ajuste?',
                text: 'Se guardarán las cantidades inventariadas y el ajuste quedará listo para aplicar al stock.',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'No, cancelar'
            }).then(function(result) {
                const confirmado = result.isConfirmed !== undefined ? result.isConfirmed : result.value;
                if (!confirmado) return;

                fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            guardar_ajuste: true
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "ok") {
                            Swal.fire({
                                type: 'success',
                                title: 'Ajuste guardado correctamente!',
                                text: data.msg
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Error',
                                text: data.msg
                            });
                        }
                    })
                    .catch(err => console.error("Error AJAX:", err));
            });
        });

    });


    document.addEventListener("DOMContentLoaded", function() {

        const btnAjustar = document.getElementById("btn-ajustar-stock");
        if (btnAjustar) {
            btnAjustar.addEventListener("click", function() {
                Swal.fire({
                    title: '¿Desea aplicar este ajuste al stock?',
                    text: 'Esta acción actualizará el stock según las diferencias del inventario.',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aplicar',
                    cancelButtonText: 'No, cancelar'
                }).then(function(result) {

                    const confirmado = result.isConfirmed !== undefined ? result.isConfirmed : result.value;

                    if (confirmado) {
                        fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: new URLSearchParams({
                                    aplicar_stock: true
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === "ok") {
                                    Swal.fire({
                                        type: 'success',
                                        title: 'Stock actualizado',
                                        text: data.msg
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire({
                                        type: 'error',
                                        title: 'Error',
                                        text: data.msg
                                    });
                                }
                            });
                    }
                });
            });
        }

        const btnLimpiar = document.getElementById("btn-limpiar-todo");
        if (btnLimpiar) {
            btnLimpiar.addEventListener("click", function() {
                if (!confirm("¿Desea cancelar el ajuste y limpiar la tabla?")) return;

                fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            limpiar_ajuste: true
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === "ok") {
                            Swal.fire({
                                type: 'success',
                                title: 'Ajuste cancelado',
                                text: data.msg
                            }).then(() => location.reload());
                        }
                    });
            });
        }

    });



    function anularInventario(id) {
        Swal.fire({
            title: "Anular ajuste de inventario",
            text: "Ingrese el motivo de anulacion",
            input: "textarea",
            inputPlaceholder: "Motivo de anulacion",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Si, anular",
            cancelButtonText: "Cancelar",
            inputValidator: (value) => {
                if (!value || value.trim().length < 5) {
                    return "Debe ingresar un motivo de al menos 5 caracteres";
                }
            }
        }).then((result) => {
            if (!result.value) return;

            fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    inv_id_del: id,
                    motivo_anulacion: result.value
                })
            })
            .then(res => res.json())
            .then(resp => {

                Swal.fire({
                    title: resp.Titulo,
                    text: resp.Texto,
                    icon: resp.Tipo
                }).then(() => {
                    if (resp.Alerta === "recargar") {
                        location.reload();
                    }
                });

            });
        });
    }

    let inventarioDetalleActual = null;
    let inventarioDetalleTimer = null;

    function verDetalleInventario(id) {
        inventarioDetalleActual = id;
        const buscar = document.getElementById("detalleInventarioBuscar");
        const filtro = document.getElementById("detalleInventarioFiltro");
        const registros = document.getElementById("detalleInventarioRegistros");

        if (buscar) buscar.value = "";
        if (filtro) filtro.value = "todos";
        if (registros) registros.value = "100";

        $("#modalDetalleInventario").modal("show");
        cargarDetalleInventario(1);
    }

    function cargarDetalleInventario(pagina = 1) {
        const contenedor = document.getElementById("detalleInventarioBody");
        const cabecera = document.getElementById("detalleInventarioCabecera");
        const paginacion = document.getElementById("detalleInventarioPaginacion");

        if (!contenedor || !inventarioDetalleActual) return;

        contenedor.innerHTML = `
            <div class="text-center p-4">
                <div class="spinner-border text-info" role="status"></div>
                <p class="mt-2 mb-0">Cargando detalle...</p>
            </div>
        `;

        if (paginacion) paginacion.innerHTML = "";

        fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    detalle_inventario: inventarioDetalleActual,
                    detalle_pagina: pagina,
                    detalle_buscar: document.getElementById("detalleInventarioBuscar")?.value || "",
                    detalle_filtro: document.getElementById("detalleInventarioFiltro")?.value || "todos",
                    detalle_registros: document.getElementById("detalleInventarioRegistros")?.value || "100"
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status !== "ok") {
                    contenedor.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            ${data.msg || "No se pudo cargar el detalle del inventario."}
                        </div>
                    `;
                    return;
                }

                if (cabecera) cabecera.innerHTML = data.cabecera;
                contenedor.innerHTML = data.tabla;
                if (paginacion) paginacion.innerHTML = data.paginacion;
            })
            .catch(() => {
                contenedor.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        No se pudo cargar el detalle del inventario.
                    </div>
                `;
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const buscar = document.getElementById("detalleInventarioBuscar");
        const filtro = document.getElementById("detalleInventarioFiltro");
        const registros = document.getElementById("detalleInventarioRegistros");

        if (buscar) {
            buscar.addEventListener("input", function() {
                clearTimeout(inventarioDetalleTimer);
                inventarioDetalleTimer = setTimeout(() => cargarDetalleInventario(1), 350);
            });
        }

        if (filtro) {
            filtro.addEventListener("change", function() {
                cargarDetalleInventario(1);
            });
        }

        if (registros) {
            registros.addEventListener("change", function() {
                cargarDetalleInventario(1);
            });
        }
    });
</script>
