<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let timerBuscarRegistro = null;
    let detallesReclamo = [];

    function setReclamoHabilitado(habilitado) {
        const boton = document.getElementById('btnRegistrarReclamo');
        if (boton) boton.disabled = !habilitado;
    }

    function buscarRegistro(texto) {
        const resultado = document.getElementById('resultado_registro');
        if (!resultado) return;

        texto = (texto || '').trim();
        clearTimeout(timerBuscarRegistro);

        if (texto.length < 2) {
            resultado.innerHTML = '';
            return;
        }

        timerBuscarRegistro = setTimeout(() => {
            let data = new FormData();
            data.append('accion', 'buscar_registro');
            data.append('buscar', texto);

            fetch(SERVERURL + 'ajax/reclamoServicioAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(r => {
                    resultado.innerHTML = r;
                });
        }, 300);
    }

    function seleccionarRegistro(idEnc) {
        let data = new FormData();
        data.append('accion', 'cargar_registro_reclamo');
        data.append('idregistro_servicio', idEnc);

        fetch(SERVERURL + 'ajax/reclamoServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(data => {
                if (!data || data.error) {
                    alert('No se pudo cargar el registro');
                    return;
                }

                const r = data.registro;

                document.getElementById('idregistro_servicio').value = idEnc;
                document.getElementById('registro_numero').value = r.idregistro_servicio;
                document.getElementById('cliente').value = `${r.nombre_cliente} ${r.apellido_cliente}`;
                document.getElementById('vehiculo').value = `${r.mod_descri} ${r.placa}`;

                detallesReclamo = data.detalle || [];
                renderDetallesReclamo();
                actualizarTipoReclamo();

                const garantiaEstado = document.getElementById('garantia_estado');
                const garantiaSelect = document.getElementById('requiere_garantia');
                const hoy = new Date().toISOString().slice(0, 10);
                const dentroFecha = r.garantia_fecha_vencimiento && hoy <= r.garantia_fecha_vencimiento;
                const garantiaTexto = dentroFecha ?
                    `Garantia por fecha disponible hasta ${r.garantia_fecha_vencimiento}.` :
                    `Garantia no disponible por fecha${r.garantia_fecha_vencimiento ? ', vencida el ' + r.garantia_fecha_vencimiento : ''}.`;
                const kmTexto = r.garantia_km_limite ? ` Limite: ${r.garantia_km_limite} km.` : ' Limite de kilometraje pendiente.';

                if (garantiaEstado) {
                    garantiaEstado.className = dentroFecha ? 'alert alert-success mb-0' : 'alert alert-warning mb-0';
                    garantiaEstado.innerHTML = dentroFecha ?
                        `${garantiaTexto}${kmTexto} Se volvera a validar por kilometraje en la recepcion.` :
                        garantiaTexto;
                }

                if (garantiaSelect) {
                    garantiaSelect.value = dentroFecha ? '1' : '0';
                    garantiaSelect.disabled = !dentroFecha;
                }

                setReclamoHabilitado(true);
            });
    }

    function tipoReclamoActual() {
        return document.getElementById('tipo_reclamo')?.value || 'SERVICIO';
    }

    function detalleCompatibleConTipo(item) {
        const tipo = tipoReclamoActual();
        if (tipo === 'SERVICIO') return item.tipo === 'servicio';
        if (tipo === 'REPUESTO') return item.tipo === 'producto';
        return true;
    }

    function actualizarTipoReclamo() {
        document.querySelectorAll('.chk-detalle-reclamo').forEach(chk => {
            const item = detallesReclamo[chk.dataset.index];
            const compatible = item && detalleCompatibleConTipo(item);
            chk.disabled = !compatible || tipoReclamoActual() === 'ATENCION';
            if (chk.disabled) chk.checked = false;
        });

        document.querySelectorAll('.motivo-detalle').forEach(input => {
            const item = detallesReclamo[input.dataset.index];
            const compatible = item && detalleCompatibleConTipo(item);
            input.disabled = !compatible || tipoReclamoActual() === 'ATENCION';
            if (input.disabled) input.value = '';
        });
    }

    function renderDetallesReclamo() {
        const tbody = document.getElementById('detalle_reclamo_items');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (detallesReclamo.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No hay detalles para reclamar
                    </td>
                </tr>`;
            return;
        }

        detallesReclamo.forEach((item, index) => {
            const tipoTexto = item.tipo === 'servicio' ? 'Servicio' : 'Repuesto';
            tbody.innerHTML += `
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                            class="chk-detalle-reclamo"
                            data-index="${index}">
                    </td>
                    <td>${item.desc_articulo}</td>
                    <td class="text-center">${tipoTexto}</td>
                    <td class="text-center">${item.cantidad}</td>
                    <td class="text-center">${item.origen}</td>
                    <td>
                        <input type="text"
                            class="form-control form-control-sm motivo-detalle"
                            data-index="${index}"
                            placeholder="Motivo especifico">
                    </td>
                </tr>`;
        });
    }

    function limpiarReclamoServicio() {
        const form = document.querySelector('.FormularioAjax');
        if (form) form.reset();

        document.getElementById('idregistro_servicio').value = '';
        document.getElementById('registro_numero').value = '';
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        detallesReclamo = [];
        document.getElementById('detalles_reclamo_json').value = '';
        document.getElementById('detalle_reclamo_items').innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Seleccione un servicio realizado
                </td>
            </tr>`;
        document.getElementById('resultado_registro').innerHTML = '';

        const garantiaEstado = document.getElementById('garantia_estado');
        if (garantiaEstado) {
            garantiaEstado.className = 'alert alert-secondary mb-0';
            garantiaEstado.innerHTML = 'Seleccione un servicio para validar la garantia.';
        }

        const garantiaSelect = document.getElementById('requiere_garantia');
        if (garantiaSelect) {
            garantiaSelect.value = '0';
            garantiaSelect.disabled = true;
        }

        const buscarRegistroInput = document.getElementById('buscar_registro');
        if (buscarRegistroInput) buscarRegistroInput.value = '';

        setReclamoHabilitado(false);
    }

    const formReclamoServicio = document.getElementById('idregistro_servicio')?.closest('.FormularioAjax');
    if (formReclamoServicio) {
        formReclamoServicio.addEventListener('submit', function(e) {
            const idRegistro = document.getElementById('idregistro_servicio');
            if (!idRegistro || !idRegistro.value) {
                e.preventDefault();
                alert('Seleccione un servicio realizado');
                return;
            }

            const seleccionados = [];
            document.querySelectorAll('.chk-detalle-reclamo:checked').forEach(chk => {
                const index = chk.dataset.index;
                const item = detallesReclamo[index];
                if (!item) return;

                const motivo = document.querySelector(`.motivo-detalle[data-index="${index}"]`)?.value || '';
                seleccionados.push({
                    id_registro_servicio_detalle: item.id_registro_servicio_detalle,
                    motivo: motivo
                });
            });

            const tipo = tipoReclamoActual();
            if ((tipo === 'SERVICIO' || tipo === 'REPUESTO') && seleccionados.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un detalle reclamado');
                return;
            }

            document.getElementById('detalles_reclamo_json').value = JSON.stringify(seleccionados);
        }, true);
    }
</script>
