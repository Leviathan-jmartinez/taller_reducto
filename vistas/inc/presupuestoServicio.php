<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function abrirModalRecepcion() {
        document.getElementById('resultado_recepcion').innerHTML = '';
        $('#modalRecepcion').modal('show');
    }


    function buscarRecepcion(texto) {

        texto = texto.trim();

        if (texto.length < 2) {
            document.getElementById('resultado_recepcion').innerHTML = '';
            return;
        }

        let datos = new FormData();
        datos.append('buscar_recepcion', texto);

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.text())
            .then(html => {
                document.getElementById('resultado_recepcion').innerHTML = html;
            })
            .catch(error => {
                console.error('Error buscando recepción:', error);
            });
    }

    function seleccionarRecepcion(datos) {

        document.getElementById('idrecepcion').value = datos.idrecepcion;
        document.getElementById('id_cliente').value = datos.id_cliente;
        document.getElementById('id_vehiculo').value = datos.id_vehiculo;

        document.getElementById('cliente_txt').value = datos.cliente;
        document.getElementById('vehiculo_txt').value = datos.vehiculo;
        document.getElementById('kilometraje_txt').value = datos.kilometraje;
        document.getElementById('observacion_txt').value = datos.observacion;

        $('#modalRecepcion').modal('hide');

        /* Luego acá llamaremos:
           cargarDescuentosCliente(datos.id_cliente);
        */
    }

    function limpiarRecepcion() {

        document.getElementById('idrecepcion').value = '';
        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';

        document.getElementById('cliente_txt').value = '';
        document.getElementById('vehiculo_txt').value = '';
        document.getElementById('kilometraje_txt').value = '';
        document.getElementById('observacion_txt').value = '';

        document.getElementById('descuentos_cliente').innerHTML =
            '<span class="text-muted">Seleccione una recepción para ver descuentos</span>';
    }
</script>