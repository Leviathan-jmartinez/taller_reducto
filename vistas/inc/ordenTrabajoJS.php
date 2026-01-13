<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function abrirModalEquipo(idOT) {
        document.getElementById('modal_id_ot').value = idOT;

        const select = document.getElementById('modal_idtrabajos');
        select.innerHTML = '<option value="">Cargando...</option>';

        document.getElementById('modal_tecnico').innerHTML =
            '<option value="">Seleccione un técnico</option>';

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
                    html += `<option value="${e.idtrabajos}">${e.nombre}</option>`;
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

    function cargarTecnicosOT(idOT) {
        let datos = new FormData();
        datos.append("cargar_tecnicos_ot", idOT);

        fetch(SERVERURL + "ajax/ordenTrabajoAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('tecnico_responsable').innerHTML = html;
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

    document.addEventListener('DOMContentLoaded', () => {

        const selNuevo = document.getElementById('idtrabajos');
        if (selNuevo) {
            selNuevo.addEventListener('change', function() {
                const idEquipo = this.value;
                const sel = document.getElementById('tecnico_responsable');

                if (!sel) return;

                if (!idEquipo) {
                    sel.innerHTML = '<option value="">Seleccione un técnico</option>';
                    return;
                }

                let datos = new FormData();
                datos.append("cargar_tecnicos_equipo", idEquipo);

                fetch(SERVERURL + "ajax/ordenTrabajoAjax.php", {
                        method: "POST",
                        body: datos
                    })
                    .then(r => r.text())
                    .then(html => sel.innerHTML = html);
            });
        }

        const selModal = document.getElementById('modal_idtrabajos');
        if (selModal) {
            selModal.addEventListener('change', function() {
                const idEquipo = this.value;
                const selTec = document.getElementById('modal_tecnico');

                if (!selTec) return;

                if (!idEquipo) {
                    selTec.innerHTML = '<option value="">Seleccione un técnico</option>';
                    return;
                }

                let datos = new FormData();
                datos.append("cargar_tecnicos_equipo", idEquipo);

                fetch(SERVERURL + "ajax/ordenTrabajoAjax.php", {
                        method: "POST",
                        body: datos
                    })
                    .then(r => r.text())
                    .then(html => {
                        selTec.innerHTML = html;
                    });
            });
        }

    });

    function limpiarOrdenTrabajo() {

        const form = document.querySelector('form.FormularioAjax');

        // Resetear campos del form
        form.reset();

        // Limpiar hidden relacionados
        document.getElementById('idpresupuesto_servicio').value = '';
        document.getElementById('idrecepcion').value = '';

        // Limpiar campos readonly
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        document.getElementById('nro_presupuesto').value = '';

        // Resetear selects
        document.getElementById('idtrabajos').value = '';
        document.getElementById('tecnico_responsable').innerHTML =
            '<option value="">Seleccione un técnico</option>';

        // Limpiar detalle de servicios
        const tbody = document.getElementById('detalle_presupuesto');
        tbody.innerHTML = `
        <tr>
            <td colspan="4" class="text-center text-muted">
                Seleccione un presupuesto
            </td>
        </tr>
    `;
    }
</script>