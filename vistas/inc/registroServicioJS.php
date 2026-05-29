<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let detalleInsumos = [];
    let timerBuscarOT = null;
    let timerBuscarInsumo = null;

    function setRegistroServicioHabilitado(habilitado) {
        const fecha = document.querySelector('[name="fecha_servicio"]');
        const kilometraje = document.querySelector('[name="kilometraje_salida"]');
        const observacion = document.querySelector('[name="observacion"]');
        const boton = document.getElementById('btnRegistrar');

        if (kilometraje) kilometraje.value = '';
        if (fecha) fecha.disabled = !habilitado;
        if (observacion) observacion.disabled = !habilitado;
        if (boton) boton.disabled = !habilitado;
    }

    function buscarOT(texto) {
        const resultado = document.getElementById('resultado_ot');
        if (!resultado) return;

        texto = (texto || '').trim();
        clearTimeout(timerBuscarOT);

        if (texto.length < 2) {
            resultado.innerHTML = '';
            return;
        }

        timerBuscarOT = setTimeout(() => {
            let data = new FormData();
            data.append('accion', 'buscar_ot');
            data.append('buscar_ot', texto);

            fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(r => resultado.innerHTML = r);
        }, 300);
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
                if (data.error) {
                    alert('La OT no esta activa para registro');
                    limpiarRegistroServicio();
                    return;
                }

                document.getElementById('idorden_trabajo').value = idOT;
                document.getElementById('ot_numero').value = data.ot.idorden_trabajo;
                document.getElementById('ot_cliente').value =
                    data.ot.nombre_cliente + ' ' + data.ot.apellido_cliente;
                document.getElementById('ot_vehiculo').value =
                    data.ot.mod_descri + ' ' + data.ot.placa;

                detalleInsumos = [];
                renderInsumos();
                const resultadoInsumos = document.getElementById('resultado_insumos');
                const buscarInsumoInput = document.getElementById('buscar_insumo');
                if (resultadoInsumos) resultadoInsumos.innerHTML = '';
                if (buscarInsumoInput) buscarInsumoInput.value = '';

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

                setRegistroServicioHabilitado(true);
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

    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.btn-limpiar-busqueda');
        if (!btn) return;

        fetch("<?php echo SERVERURL; ?>ajax/buscadorAjax.php", {
                method: "POST",
                body: new URLSearchParams({
                    modulo: "registro_servicio",
                    eliminar_busqueda: 1
                })
            })
            .then(r => r.json())
            .then(res => {
                if (res.Alerta === "redireccionar") {
                    window.location.href = res.URL;
                } else {
                    alert(res.Texto);
                }
            });

    });

    function buscarInsumo() {
        const input = document.getElementById('buscar_insumo');
        const resultado = document.getElementById('resultado_insumos');
        if (!input || !resultado) return;

        let texto = input.value.trim();
        clearTimeout(timerBuscarInsumo);

        if (texto.length < 2) {
            resultado.innerHTML = '';
            return;
        }

        timerBuscarInsumo = setTimeout(() => {
            fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        accion: 'buscar_insumo',
                        texto: texto
                    })
                })
                .then(r => r.text())
                .then(html => {
                    resultado.innerHTML = html;
                });
        }, 300);
    }

    function agregarInsumo(id, descripcion, stock) {

        let existe = detalleInsumos.find(i => i.id_articulo == id);

        if (existe) {
            alert('El insumo ya fue agregado');
            return;
        }

        if (stock <= 0) {
            alert('Sin stock disponible');
            return;
        }

        let item = {
            id_articulo: id,
            descripcion: descripcion,
            cantidad: 1,
            stock: stock
        };

        detalleInsumos.push(item);

        renderInsumos();
    }

    function renderInsumos() {

        let tbody = document.getElementById('detalle_insumos');
        tbody.innerHTML = '';

        detalleInsumos.forEach((item, index) => {

            let tr = document.createElement('tr');

            tr.innerHTML = `
            <td>${item.descripcion}</td>

            <td>
                <input type="number" min="1"
                    class="form-control form-control-sm"
                    value="${item.cantidad}"
                    oninput="cambiarCantidadInsumo(this, ${index})">
            </td>

            <td class="text-center">
                <button class="btn btn-danger btn-sm"
                    onclick="quitarInsumo(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(tr);
        });
    }

    function cambiarCantidadInsumo(input, index) {

        let item = detalleInsumos[index];

        let cantidad = parseInt(input.value);

        if (isNaN(cantidad) || cantidad <= 0) cantidad = 1;

        if (cantidad > item.stock) {
            alert('Stock insuficiente');
            cantidad = item.stock;
            input.value = cantidad;
        }

        item.cantidad = cantidad;
    }

    function quitarInsumo(index) {
        detalleInsumos.splice(index, 1);
        renderInsumos();
    }

    function limpiarRegistroServicio() {
        detalleInsumos = [];

        const idOT = document.getElementById('idorden_trabajo');
        const insumosJson = document.getElementById('insumos_json');
        const otNumero = document.getElementById('ot_numero');
        const otCliente = document.getElementById('ot_cliente');
        const otVehiculo = document.getElementById('ot_vehiculo');
        const resultadoOT = document.getElementById('resultado_ot');
        const resultadoInsumos = document.getElementById('resultado_insumos');
        const detalleInsumosBody = document.getElementById('detalle_insumos');
        const detalleOT = document.getElementById('detalle_ot');

        if (idOT) idOT.value = '';
        if (insumosJson) insumosJson.value = '';
        if (otNumero) otNumero.value = '';
        if (otCliente) otCliente.value = '';
        if (otVehiculo) otVehiculo.value = '';
        if (resultadoOT) resultadoOT.innerHTML = '';
        if (resultadoInsumos) resultadoInsumos.innerHTML = '';
        if (detalleInsumosBody) detalleInsumosBody.innerHTML = '';
        if (detalleOT) {
            detalleOT.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    Seleccione una orden de trabajo
                </td>
            </tr>`;
        }

        const buscarInsumoInput = document.getElementById('buscar_insumo');
        if (buscarInsumoInput) buscarInsumoInput.value = '';

        const observacion = document.querySelector('[name="observacion"]');
        if (observacion) observacion.value = '';

        setRegistroServicioHabilitado(false);
    }

    const formRegistroServicio = document.getElementById('idorden_trabajo')?.closest('.FormularioAjax');
    if (formRegistroServicio) {
        formRegistroServicio.addEventListener('submit', function(e) {
            const idOT = document.getElementById('idorden_trabajo');
            const insumosJson = document.getElementById('insumos_json');

            if (!idOT || !idOT.value) {
                e.preventDefault();
                alert('Seleccione una orden de trabajo');
                return;
            }

            if (insumosJson) {
                insumosJson.value = JSON.stringify(detalleInsumos);
            }
        }, true);
    }
</script>