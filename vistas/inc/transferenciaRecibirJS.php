<script>
    const SERVERURL = "<?= SERVERURL ?>";

    document.addEventListener('DOMContentLoaded', () => {

        const form = document.querySelector(
            '.FormularioAjax[data-modulo="recibir"]'
        );

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmar recepción',
                text: '¿Desea confirmar la recepción de la transferencia?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, recibir',
                cancelButtonText: 'Cancelar'
            }).then(result => {

                if (!result.isConfirmed) return;

                const datos = new FormData(form);

                fetch(form.action, {
                        method: form.method,
                        body: datos
                    })
                    .then(r => r.json())
                    .then(resp => {

                        if (resp.Tipo === 'success') {
                            Swal.fire({
                                title: resp.Titulo,
                                text: resp.Texto,
                                icon: 'success'
                            }).then(() => {
                                window.location.href =
                                    SERVERURL + 'transferencia-historial/';
                            });
                        } else {
                            Swal.fire({
                                title: resp.Titulo || 'Error',
                                text: resp.Texto || 'No se pudo completar',
                                icon: 'error'
                            });
                        }

                    })
                    .catch(() => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error de comunicación con el servidor',
                            icon: 'error'
                        });
                    });

            });
        });
    });
</script>