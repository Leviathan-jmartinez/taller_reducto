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

    function seleccionarRegistro(id, nro, cliente, vehiculo) {

        document.getElementById('idregistro_servicio').value = id;
        document.getElementById('registro_numero').value = nro;
        document.getElementById('cliente').value = cliente;
        document.getElementById('vehiculo').value = vehiculo;

        $('#modalRegistro').modal('hide');
    }
</script>