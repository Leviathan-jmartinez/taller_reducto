<script>
    const SERVERURL = "<?= SERVERURL ?>";

    document.addEventListener('DOMContentLoaded', () => {

        const form = document.querySelector('.FormularioAjax[data-modulo="recibir"]');
        if (!form) return;

        // ===== diferencias en vivo =====
        document.querySelectorAll(".recibido").forEach(input => {
            input.addEventListener("input", function() {
                const row = this.closest("tr");
                const enviado = parseFloat(row.querySelector(".enviado").innerText.replace(',', '.'));
                const recibido = parseFloat(this.value || 0);
                const diff = recibido - enviado;

                row.querySelector(".diferencia").innerText = diff.toFixed(2);
                row.querySelector(".diferencia").classList.toggle("text-danger", diff < 0);
            });
        });

        // ===== submit único =====
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            let hayDiferencias = false;
            let resumen = [];

            document.querySelectorAll("tbody tr").forEach(row => {
                const producto = row.children[0].innerText;
                const diff = parseFloat(row.querySelector(".diferencia").innerText);

                if (diff !== 0) {
                    hayDiferencias = true;
                    resumen.push(producto);
                }
            });

            const confirmarEnvio = () => {
                fetch(form.action, {
                        method: form.method,
                        body: new FormData(form)
                    })
                    .then(r => r.json())
                    .then(resp => {
                        alertasAjax(resp, form);
                    })
                    .catch(() => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error de comunicación con el servidor',
                            type: 'error'
                        });
                    });
            };

            if (hayDiferencias) {

                Swal.fire({
                    title: "Confirmar recepción",
                    html: "Se detectaron diferencias:<br><br>" + resumen.join("<br>"),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.value) { // 🔑 ESTA ES LA CLAVE
                        confirmarEnvio();
                    }
                });

                return;
            }

            Swal.fire({
                title: "Confirmar recepción",
                text: "¿Desea confirmar la recepción de la transferencia?",
                type: "question",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (result.value) { // 🔑 Y ACÁ TAMBIÉN
                    confirmarEnvio();
                }
            });

        });

    });

    const inputBuscar = document.getElementById('buscarProducto');

    if (inputBuscar) {
        inputBuscar.addEventListener('keyup', function() {
            const texto = this.value.toLowerCase();

            document.querySelectorAll('tbody tr').forEach(row => {
                const producto = row.children[0].innerText.toLowerCase();
                row.style.display = producto.includes(texto) ? '' : 'none';
            });
        });
    }

    function aplicarFiltros(e) {
        e.preventDefault();

        const estado = document.getElementById('filtroEstado').value || '-';
        const fecha = document.getElementById('filtroFecha').value || '-';
        const id = document.getElementById('filtroId').value || '-';

        const url = "<?= SERVERURL ?>transferencia-historial/filtro/" +
            estado + "/" +
            fecha + "/" +
            id + "/";

        window.location.href = url;
    }

    function limpiarFiltros() {
        window.location.href = "<?= SERVERURL ?>transferencia-historial/";
    }
</script>