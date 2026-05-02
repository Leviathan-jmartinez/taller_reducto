<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let insumos = [];
    let detalleInsumos = [];

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

        let texto = document.getElementById('buscar_insumo').value;

        fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'buscar_insumo',
                    texto: texto
                })
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('resultado_insumos').innerHTML = html;
            });
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

        document.getElementById('idorden_trabajo').value = '';
        document.getElementById('insumos_json').value = '';
        document.getElementById('ot_numero').value = '';
        document.getElementById('ot_cliente').value = '';
        document.getElementById('ot_vehiculo').value = '';
        document.getElementById('resultado_ot').innerHTML = '';
        document.getElementById('resultado_insumos').innerHTML = '';
        document.getElementById('detalle_insumos').innerHTML = '';
        document.getElementById('detalle_ot').innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    Seleccione una orden de trabajo
                </td>
            </tr>`;

        const buscarInsumoInput = document.getElementById('buscar_insumo');
        if (buscarInsumoInput) buscarInsumoInput.value = '';

        document.querySelector('[name="fecha_ejecucion"]').disabled = true;
        document.querySelector('[name="observacion"]').value = '';
        document.querySelector('[name="observacion"]').disabled = true;
    }

    document.querySelector('.FormularioAjax')
        .addEventListener('submit', function() {

            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'insumos_json';
            input.value = JSON.stringify(detalleInsumos);

            this.appendChild(input);
        });

    document.querySelector('.FormularioAjax')
        .addEventListener('submit', function() {

            document.getElementById('insumos_json').value =
                JSON.stringify(detalleInsumos);

        });
</script>
