<script>
    let condicionesRegla = Array.isArray(window.REGLA_CONDICIONES_INICIALES) ? window.REGLA_CONDICIONES_INICIALES : [];
    let descuentosRegla = Array.isArray(window.REGLA_DESCUENTOS_INICIALES) ? window.REGLA_DESCUENTOS_INICIALES : [];

    function agregarCondicionRegla() {
        const condicion = {
            tipo_condicion: document.getElementById('cond_tipo').value,
            operador: document.getElementById('cond_operador').value,
            valor_ref: document.getElementById('cond_valor_ref').value || null,
            valor_texto: document.getElementById('cond_valor_texto').value.trim()
        };

        if (!condicion.valor_ref && !condicion.valor_texto) {
            alert('Debe ingresar un valor para la condicion');
            return;
        }

        if (!operadorValidoParaCondicion(condicion.tipo_condicion, condicion.operador)) {
            alert('Para cliente, articulo, categoria y sucursal solo se permite = o !=');
            return;
        }

        condicionesRegla.push(condicion);
        document.getElementById('cond_valor_ref').value = '';
        document.getElementById('cond_valor_texto').value = '';
        renderCondicionesRegla();
    }

    function quitarCondicionRegla(index) {
        condicionesRegla.splice(index, 1);
        renderCondicionesRegla();
    }

    function renderCondicionesRegla() {
        const tbody = document.getElementById('tabla_condiciones');
        tbody.innerHTML = '';

        condicionesRegla.forEach((c, index) => {
            tbody.innerHTML += `
                <tr class="text-center">
                    <td>${escapeHtml(c.tipo_condicion || '')}</td>
                    <td>${escapeHtml(c.operador || '=')}</td>
                    <td>${escapeHtml(c.valor_ref || '')}</td>
                    <td>${escapeHtml(c.valor_texto || '')}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="quitarCondicionRegla(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>`;
        });

        prepararEnvioRegla();
    }

    function agregarDescuentoRegla() {
        const descuento = {
            nombre: document.getElementById('desc_nombre').value.trim(),
            tipo: document.getElementById('desc_tipo').value,
            valor: parseFloat(document.getElementById('desc_valor').value || 0),
            cantidad_requerida: parseFloat(document.getElementById('desc_cantidad_requerida').value || 0),
            cantidad_cobrada: parseFloat(document.getElementById('desc_cantidad_cobrada').value || 0),
            aplica_a: document.getElementById('desc_aplica_a').value,
            alcance_tipo: document.getElementById('desc_aplica_a').value,
            alcance_ref: document.getElementById('desc_alcance_ref').value || null
        };

        if (!descuento.nombre) {
            alert('Debe ingresar nombre del descuento');
            return;
        }

        if (descuento.tipo === 'NXM') {
            if (descuento.cantidad_requerida <= 0 || descuento.cantidad_cobrada <= 0 || descuento.cantidad_cobrada >= descuento.cantidad_requerida) {
                alert('En Lleva N paga M, la cantidad pagada debe ser menor a la cantidad llevada');
                return;
            }
            descuento.valor = 0;
        } else if (descuento.tipo === 'GRATIS') {
            descuento.valor = 0;
        } else if (descuento.valor <= 0) {
            alert('Debe ingresar nombre y valor del descuento');
            return;
        }

        if (existeDescuentoMismoAlcance(descuento)) {
            alert('Ya existe un descuento para este mismo alcance dentro de la regla');
            return;
        }

        descuentosRegla.push(descuento);
        document.getElementById('desc_nombre').value = '';
        document.getElementById('desc_valor').value = '';
        document.getElementById('desc_cantidad_requerida').value = '';
        document.getElementById('desc_cantidad_cobrada').value = '';
        document.getElementById('desc_alcance_ref').value = '';
        renderDescuentosRegla();
    }

    function quitarDescuentoRegla(index) {
        descuentosRegla.splice(index, 1);
        renderDescuentosRegla();
    }

    function renderDescuentosRegla() {
        const tbody = document.getElementById('tabla_descuentos_regla');
        tbody.innerHTML = '';

        descuentosRegla.forEach((d, index) => {
            const nxm = d.tipo === 'NXM'
                ? `${Number(d.cantidad_requerida || 0).toLocaleString()} x ${Number(d.cantidad_cobrada || 0).toLocaleString()}`
                : '-';
            const valor = (d.tipo === 'NXM' || d.tipo === 'GRATIS') ? '-' : Number(d.valor || 0).toLocaleString();

            tbody.innerHTML += `
                <tr class="text-center">
                    <td>${escapeHtml(d.nombre || '')}</td>
                    <td>${escapeHtml(d.tipo || '')}</td>
                    <td>${valor}</td>
                    <td>${nxm}</td>
                    <td>${escapeHtml(d.aplica_a || '')}</td>
                    <td>${escapeHtml(d.alcance_ref || '')}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="quitarDescuentoRegla(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>`;
        });

        prepararEnvioRegla();
    }

    function prepararEnvioRegla() {
        const condicionesInput = document.getElementById('condiciones_json');
        const descuentosInput = document.getElementById('descuentos_json');

        if (condicionesInput) {
            condicionesInput.value = JSON.stringify(condicionesRegla);
        }

        if (descuentosInput) {
            descuentosInput.value = JSON.stringify(descuentosRegla);
        }
    }

    function operadorValidoParaCondicion(tipo, operador) {
        const condicionesPorId = ['CLIENTE', 'ARTICULO', 'CATEGORIA', 'SUCURSAL'];
        return !condicionesPorId.includes(tipo) || ['=', '!='].includes(operador);
    }

    function actualizarOperadoresCondicion() {
        const tipo = document.getElementById('cond_tipo').value;
        const operador = document.getElementById('cond_operador');
        const condicionesPorId = ['CLIENTE', 'ARTICULO', 'CATEGORIA', 'SUCURSAL'];
        const soloIgualdad = condicionesPorId.includes(tipo);

        Array.from(operador.options).forEach(option => {
            option.disabled = soloIgualdad && !['=', '!='].includes(option.value);
        });

        if (soloIgualdad && !['=', '!='].includes(operador.value)) {
            operador.value = '=';
        }
    }

    function existeDescuentoMismoAlcance(descuento) {
        const alcance = claveAlcanceDescuento(descuento);

        return descuentosRegla.some(d => claveAlcanceDescuento(d) === alcance);
    }

    function claveAlcanceDescuento(descuento) {
        const aplicaA = descuento.aplica_a || '';
        const alcanceTipo = descuento.alcance_tipo || aplicaA;
        const alcanceRef = descuento.alcance_ref || '';

        return `${aplicaA}|${alcanceTipo}|${alcanceRef}`;
    }

    function alternarCamposTipoDescuento() {
        const tipo = document.getElementById('desc_tipo').value;
        const esNxm = tipo === 'NXM';
        const esGratis = tipo === 'GRATIS';

        document.getElementById('grupo_desc_valor').classList.toggle('d-none', esNxm || esGratis);
        document.getElementById('grupo_desc_requerida').classList.toggle('d-none', !esNxm);
        document.getElementById('grupo_desc_cobrada').classList.toggle('d-none', !esNxm);

        if (esNxm || esGratis) {
            document.getElementById('desc_aplica_a').value = 'ARTICULO';
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderCondicionesRegla();
        renderDescuentosRegla();

        const form = document.querySelector('.FormularioAjax[data-modulo="reglas_comerciales"]');
        if (form) {
            form.addEventListener('submit', prepararEnvioRegla, true);
        }

        const tipoDescuento = document.getElementById('desc_tipo');
        if (tipoDescuento) {
            tipoDescuento.addEventListener('change', alternarCamposTipoDescuento);
            alternarCamposTipoDescuento();
        }

        const tipoCondicion = document.getElementById('cond_tipo');
        if (tipoCondicion) {
            tipoCondicion.addEventListener('change', actualizarOperadoresCondicion);
            actualizarOperadoresCondicion();
        }
    });

    document.addEventListener('ajax:limpiar', function(e) {
        if (!e.detail || e.detail.modulo !== 'reglas_comerciales') return;
        condicionesRegla = [];
        descuentosRegla = [];
        renderCondicionesRegla();
        renderDescuentosRegla();
    });
</script>
