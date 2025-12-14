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

            if (tipo === 'categoria') {
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

            if (tipo === 'proveedor') {
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

            if (tipo === 'producto') {
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
</script>