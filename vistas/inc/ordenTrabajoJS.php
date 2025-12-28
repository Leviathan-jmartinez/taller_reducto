<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function abrirModalTecnico(idOT) {

        document.getElementById('modal_id_ot').value = idOT;
        document.getElementById('idtrabajos').innerHTML =
            '<option value="">Cargando...</option>';

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: "POST",
                body: new URLSearchParams({
                    accion: "listar_tecnicos"
                })
            })
            .then(r => r.json())
            .then(data => {

                let html = '<option value="">Seleccione técnico</option>';

                data.forEach(t => {
                    html += `
                <option value="${t.idtrabajos}">
                    ${t.tecnico}
                </option>`;
                });

                document.getElementById('idtrabajos').innerHTML = html;
                $('#modalAsignarTecnico').modal('show');
            });
    }


    function cargarTecnicos() {

        let data = new FormData();
        data.append('accion', 'listar_tecnicos');

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(data => {

                let select = document.getElementById('idtrabajos');
                select.innerHTML = '<option value="">Seleccione técnico</option>';

                data.forEach(t => {
                    let option = document.createElement('option');
                    option.value = t.idtrabajos;
                    option.textContent = t.tecnico;
                    select.appendChild(option);
                });
            });
    }


    function abrirModalPresupuesto() {
        $('#modalPresupuesto').modal('show');
    }

    function buscarPresupuesto(texto) {
        let data = new FormData();
        data.append('buscar_presupuesto', texto);

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.text())
            .then(r => document.getElementById('resultado_presupuesto').innerHTML = r);
    }

    function seleccionarPresupuesto(nro_presupuesto, recepcion, cliente, vehiculo) {

        document.getElementById('idpresupuesto_servicio').value = nro_presupuesto;
        document.getElementById('nro_presupuesto').value = nro_presupuesto;
        document.getElementById('idrecepcion').value = recepcion;
        document.getElementById('cliente').value = cliente;
        document.getElementById('vehiculo').value = vehiculo;

        cargarDetallePresupuesto(nro_presupuesto);

        $('#modalPresupuesto').modal('hide');
    }

    function cargarDetallePresupuesto(idpresupuesto) {

        let data = new FormData();
        data.append('accion', 'detalle_presupuesto');
        data.append('idpresupuesto_servicio', idpresupuesto);

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('detalle_presupuesto').innerHTML = r;
            });
    }

    function anularOT(idOT) {

        if (!confirm('¿Desea anular esta Orden de Trabajo?')) return;

        let data = new FormData();
        data.append('accion', 'anular_ot');
        data.append('id_ot', idOT);

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
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