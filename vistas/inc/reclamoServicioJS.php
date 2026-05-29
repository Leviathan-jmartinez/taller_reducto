<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let timerBuscarRegistro = null;

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

    function seleccionarRegistro(idEnc, numero, cliente, vehiculo, trabajos, dentroGarantiaFecha, garantiaTexto, kmLimite) {

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

        const garantiaEstado = document.getElementById('garantia_estado');
        const garantiaSelect = document.getElementById('requiere_garantia');
        const dentroFecha = Number(dentroGarantiaFecha) === 1;
        const kmTexto = kmLimite ? ` Limite: ${kmLimite} km.` : ' Limite de kilometraje pendiente.';

        if (garantiaEstado) {
            garantiaEstado.className = dentroFecha ? 'alert alert-success mb-0' : 'alert alert-warning mb-0';
            garantiaEstado.innerHTML = dentroFecha ?
                `Garantia por fecha disponible. ${garantiaTexto}.${kmTexto} Se volvera a validar por kilometraje en la recepcion.` :
                `Garantia no disponible por fecha. ${garantiaTexto}.`;
        }

        if (garantiaSelect) {
            garantiaSelect.value = dentroFecha ? '1' : '0';
            garantiaSelect.disabled = !dentroFecha;
        }

        setReclamoHabilitado(true);

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
            }
        }, true);
    }
</script>
