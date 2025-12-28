<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function buscarOT(texto) {

        let data = new FormData();
        data.append('accion', 'buscar_ot');
        data.append('buscar_ot', texto);

        fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.text())
            .then(r => document.getElementById('resultado_ot').innerHTML = r);
    }

    function seleccionarOT(idOT) {

        let data = new FormData();
        data.append('accion', 'cargar_ot');
        data.append('id_ot', idOT);

        fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(data => {

                document.getElementById('idorden_trabajo').value = idOT;
                document.getElementById('ot_numero').value = data.ot.idorden_trabajo;
                document.getElementById('ot_cliente').value =
                    data.ot.nombre_cliente + ' ' + data.ot.apellido_cliente;
                document.getElementById('ot_vehiculo').value =
                    data.ot.mod_descri + ' ' + data.ot.placa;

                let html = '';
                let total = 0;

                data.detalle.forEach(d => {
                    total += parseFloat(d.subtotal);
                    html += `
                <tr>
                    <td>${d.desc_articulo}</td>
                    <td class="text-center">${d.cantidad}</td>
                    <td class="text-right">${d.precio_unitario}</td>
                    <td class="text-right">${d.subtotal}</td>
                </tr>`;
                });

                document.getElementById('detalle_ot').innerHTML = html;

                document.querySelector('[name="fecha_ejecucion"]').disabled = false;
                document.querySelector('[name="observacion"]').disabled = false;
                document.getElementById('btnRegistrar').disabled = false;
            });
    }

    function anularRegistroServicio(idRegistro) {

        if (!confirm('¿Desea anular este registro de servicio?')) return;

        let data = new FormData();
        data.append('accion', 'anular_registro');
        data.append('id_registro', idRegistro);

        fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(r => {
                if (r.Alerta === 'recargar') {
                    location.reload();
                }
            });
    }
</script>