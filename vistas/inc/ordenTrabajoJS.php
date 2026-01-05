<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function abrirModalEquipo(idOT) {

        document.getElementById('modal_id_ot').value = idOT;

        const select = document.getElementById('modal_idtrabajos');
        select.innerHTML = '<option value="">Cargando...</option>';

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: "POST",
                body: new URLSearchParams({
                    accion: "listar_equipos"
                })
            })
            .then(r => r.json())
            .then(data => {

                let html = '<option value="">Seleccione equipo</option>';

                data.forEach(e => {
                    html += `<option value="${e.idtrabajos}">
                        ${e.nombre}
                     </option>`;
                });

                select.innerHTML = html;

                $('#modalAsignarTecnico').modal('show');
            });
    }

    function cargarEquiposNuevo() {

        const select = document.getElementById('idtrabajos');
        if (!select) return; // seguridad

        select.innerHTML = '<option value="">Cargando...</option>';

        fetch(SERVERURL + 'ajax/ordenTrabajoAjax.php', {
                method: "POST",
                body: new URLSearchParams({
                    accion: "listar_equipos"
                })
            })
            .then(r => r.json())
            .then(data => {

                let html = '<option value="">Seleccione equipo de trabajo</option>';

                data.forEach(e => {
                    html += `<option value="${e.idtrabajos}">
                        ${e.nombre}
                     </option>`;
                });

                select.innerHTML = html;
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

    document.addEventListener('DOMContentLoaded', () => {
        cargarEquiposNuevo();
    });
</script>