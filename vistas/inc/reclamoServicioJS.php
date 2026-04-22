<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function abrirModalRegistro() {
        $('#modalRegistro').modal('show');
    }

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

        $('#modalRegistro').modal('hide');
    }
</script>