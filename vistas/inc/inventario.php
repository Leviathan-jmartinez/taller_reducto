<!-- jQuery -->
<script src="<?= SERVERURL ?>vistas/js/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="<?= SERVERURL ?>vistas/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

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
                    placeholder: 'Buscar productos...',
                    ajax: {
                        url: '<?= SERVERURL ?>ajax/inventarioAjax.php',
                        type: 'POST',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                buscar_producto: params.term
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

            let teorica = parseFloat(fila.querySelector(".teorica").value) || 0;
            let fisica = parseFloat(fila.querySelector(".cantidad").value) || 0;

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
                let fila = e.target.closest("tr");
                recalcularYActualizar(fila);
            }
        });

    });


    document.addEventListener("DOMContentLoaded", function() {

        const filtro = document.getElementById("filtro-productos");
        const filas = document.querySelectorAll("#tabla-detalle tbody tr");

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

        botonGuardar.addEventListener("click", function(e) {
            e.preventDefault();

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


    document.getElementById("btn-ajustar-stock").addEventListener("click", function() {
        Swal.fire({
            title: '¿Desea aplicar este ajuste al stock?',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, aplicar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            // Compatibilidad con diferentes versiones de SweetAlert2
            const confirmado = result.isConfirmed !== undefined ? result.isConfirmed : result.value;

            if (confirmado) {
                // Llamada AJAX
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
                    })
                    .catch(err => {
                        Swal.fire({
                            type: 'error',
                            title: 'Error AJAX',
                            text: err
                        });
                    });
            } else {
                Swal.fire({
                    type: 'info',
                    title: 'Cancelado',
                    text: 'No se aplicó ningún ajuste al stock'
                });
            }
        });
    });




    document.getElementById("btn-limpiar-todo").addEventListener("click", function() {
        if (!confirm("¿Desea limpiar todo el ajuste y la tabla?")) return;

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
                        title: 'Todo limpiado',
                        text: data.msg
                    }).then(() => {
                        // Recargar página o vaciar tabla en front
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
            .catch(err => {
                Swal.fire({
                    type: 'error',
                    title: 'Error AJAX',
                    text: err
                });
            });
    });


    function anularInventario(id) {

        fetch("<?= SERVERURL ?>ajax/inventarioAjax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    inv_id_del: id
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
    }
</script>