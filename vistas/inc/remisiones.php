<script>
    function buscar_factura() {
        let input_factura = document.querySelector('#input_factura').value;
        input_factura = input_factura.trim();
        if (input_factura != "") {
            let datos = new FormData();
            datos.append("buscar_factura", input_factura);

            fetch("<?php echo SERVERURL ?>ajax/remisionAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_factura = document.querySelector('#tabla_factura');
                    tabla_factura.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el Número de Factura',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function agregar_factura(idfacturaseleccionado) {
        if (!idfacturaseleccionado) return;

        // Crear form dinámico para POST
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/remisionAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'idfacturaseleccionado';
        input.value = idfacturaseleccionado;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit(); // envía y recarga la página
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const btnCancelar = document.getElementById('btnCancelarRemision');
        if (!btnCancelar) return;

        btnCancelar.addEventListener('click', function() {
            Swal.fire({
                title: 'Cancelar remision',
                text: 'Se cancelara la factura y el detalle cargados en esta remision.',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, cancelar',
                cancelButtonText: 'Volver'
            }).then(function(result) {
                const confirmado = result.isConfirmed !== undefined ? result.isConfirmed : result.value;
                if (!confirmado) return;

                fetch("<?php echo SERVERURL ?>ajax/remisionAjax.php", {
                        method: 'POST',
                        body: new URLSearchParams({
                            limpiar_remision: true
                        })
                    })
                    .then(respuesta => respuesta.json())
                    .then(respuesta => {
                        Swal.fire({
                            title: respuesta.Titulo,
                            text: respuesta.Texto,
                            type: respuesta.Tipo,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            if (respuesta.Alerta === 'recargar') {
                                location.reload();
                            }
                        });
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo cancelar la remision', 'error');
                    });
            });
        });
    });
</script>
