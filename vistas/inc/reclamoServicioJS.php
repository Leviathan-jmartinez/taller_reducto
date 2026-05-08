<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function buscarRegistro(texto) {

        let data = new FormData();
        data.append('accion', 'buscar_registro');
        data.append('buscar', texto);

        fetch(SERVERURL + 'ajax/reclamoServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('resultado_registro').innerHTML = r;
            });
    }

    function seleccionarRegistro(idEnc, numero, cliente, vehiculo, trabajos) {

        document.getElementById('idregistro_servicio').value = idEnc;
        document.getElementById('registro_numero').value = numero;
        document.getElementById('cliente').value = cliente;
        document.getElementById('vehiculo').value = vehiculo;

        let html = '';

        if (trabajos) {

            let items = trabajos.split('|');

            html += '<ul style="margin:0;padding-left:15px;">';

            items.forEach(t => {
                html += '<li>' + t + '</li>';
            });

            html += '</ul>';
        }

        document.getElementById('trabajos_realizados').innerHTML = html;

    }

    function limpiarReclamoServicio() {
        const form = document.querySelector('.FormularioAjax');
        if (form) form.reset();

        document.getElementById('idregistro_servicio').value = '';
        document.getElementById('registro_numero').value = '';
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        document.getElementById('trabajos_realizados').innerHTML = '';
        document.getElementById('resultado_registro').innerHTML = '';

        const buscarRegistroInput = document.getElementById('buscar_registro');
        if (buscarRegistroInput) buscarRegistroInput.value = '';
    }
</script>
