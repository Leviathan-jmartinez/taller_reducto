<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let detalleServicios = [];
    let timeoutGuardar = null;
    let descuentosAplicados = [];


    function guardarEstadoConDelay() {

        if (timeoutGuardar) {
            clearTimeout(timeoutGuardar);
        }

        timeoutGuardar = setTimeout(() => {
            guardarEstadoPresupuesto();
        }, 2000); // 300–500 ms es ideal
    }

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

        document.getElementById('cliente').value = datos.cliente;
        document.getElementById('vehiculo').value = datos.vehiculo;
        document.getElementById('kilometraje').value = datos.kilometraje;
        document.getElementById('observacion').value = datos.observacion;

        $('#modalRecepcion').modal('hide');
        cargarDescuentosCliente(datos.id_cliente);
        guardarEstadoPresupuesto();
    }


    function guardarEstadoPresupuesto() {

        let estado = {
            recepcion: {
                idrecepcion: document.getElementById('idrecepcion')?.value || '',
                id_cliente: document.getElementById('id_cliente')?.value || '',
                id_vehiculo: document.getElementById('id_vehiculo')?.value || '',
                cliente: document.getElementById('cliente')?.value || '',
                vehiculo: document.getElementById('vehiculo')?.value || '',
                kilometraje: document.getElementById('kilometraje')?.value || '',
                observacion: document.getElementById('observacion')?.value || ''
            },
            detalle: detalleServicios,
            descuentos: descuentosAplicados
        };

        localStorage.setItem(
            'presupuesto_servicio_tmp',
            JSON.stringify(estado)
        );

    }

    document.addEventListener('DOMContentLoaded', function() {

        let data = localStorage.getItem('presupuesto_servicio_tmp');

        if (!data) return;

        let estado = JSON.parse(data);

        // 🔹 Recepción
        if (estado.recepcion) {
            document.getElementById('idrecepcion').value = estado.recepcion.idrecepcion || '';
            document.getElementById('id_cliente').value = estado.recepcion.id_cliente || '';
            document.getElementById('id_vehiculo').value = estado.recepcion.id_vehiculo || '';

            document.getElementById('cliente').value = estado.recepcion.cliente || '';
            document.getElementById('vehiculo').value = estado.recepcion.vehiculo || '';
            document.getElementById('kilometraje').value = estado.recepcion.kilometraje || '';
            document.getElementById('observacion').value = estado.recepcion.observacion || '';
        }

        // 🔹 Detalle
        if (Array.isArray(estado.detalle) && estado.detalle.length > 0) {
            detalleServicios = estado.detalle;

            // ⚠️ esperar al DOM
            setTimeout(() => {
                renderDetalle();
            }, 100);
        }
        if (Array.isArray(estado.descuentos)) {
            descuentosAplicados = estado.descuentos;
        }
        if (estado.recepcion && estado.recepcion.id_cliente) {
            cargarDescuentosCliente(estado.recepcion.id_cliente);
        }

    });

    function limpiarFormularioPresupuesto() {

        // limpiar arrays
        detalleServicios = [];
        descuentosAplicados = [];

        // limpiar inputs hidden
        document.getElementById('detalle_json').value = '';
        document.getElementById('descuentos_json').value = '';
        document.getElementById('inp_subtotal_servicios').value = 0;
        document.getElementById('inp_total_descuento').value = 0;
        document.getElementById('inp_total_final').value = 0;

        // limpiar recepción
        document.getElementById('idrecepcion').value = '';
        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        document.getElementById('kilometraje').value = '';
        document.getElementById('observacion').value = '';

        // limpiar tablas y vistas
        renderDetalle();
        document.getElementById('descuentos_cliente').innerHTML =
            '<span class="text-muted">Seleccione una recepción para ver descuentos</span>';

        document.getElementById('txt_subtotal_servicios').innerText = 'Gs. 0';
        document.getElementById('total_subtotal').innerText = 'Gs. 0';
        document.getElementById('txt_total_descuento').innerText = 'Gs. 0';
        document.getElementById('txt_total_final').innerText = 'Gs. 0';

        // 🔥 limpiar localStorage
        localStorage.removeItem('presupuesto_servicio_tmp');
    }



    function buscarServicio() {
        let txt = document.getElementById('buscar_servicio').value.trim();

        if (txt.length < 2) {
            document.getElementById('resultado_servicios').innerHTML = '';
            return;
        }

        let datos = new FormData();
        datos.append('buscar_servicio', txt);

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('resultado_servicios').innerHTML = html;
            })
            .catch(err => console.error(err));
    }


    function agregarServicio(id, descripcion, precio) {

        let existe = detalleServicios.find(i => i.id_articulo == id);
        if (existe) {
            alert('El servicio ya fue agregado');
            return;
        }

        let item = {
            id_articulo: id,
            descripcion: descripcion,
            cantidad: 1,
            precio_base: precio,
            precio_final: precio,
            subtotal: precio,
            promocion: null
        };

        // 🔥 consultar promo
        let datos = new FormData();
        datos.append('promo_articulo', id);

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.json())
            .then(promo => {

                if (promo.id_promocion) {

                    if (promo.tipo === 'PORCENTAJE') {
                        item.precio_final =
                            precio - (precio * promo.valor / 100);
                    }

                    if (promo.tipo === 'MONTO_FIJO') {
                        item.precio_final =
                            Math.max(0, precio - promo.valor);
                    }

                    if (promo.tipo === 'PRECIO_FIJO') {
                        item.precio_final = promo.valor;
                    }

                    item.subtotal = item.precio_final * item.cantidad;
                    item.promocion = promo;
                }

                detalleServicios.push(item);
                renderDetalle();
                guardarEstadoPresupuesto();
            });
    }

    function recalcularTotales() {

        let subtotalServicios = 0;
        detalleServicios.forEach(i => subtotalServicios += i.subtotal);

        let totalDescuentos = 0;

        descuentosAplicados.forEach(d => {
            if (d.tipo === 'PORCENTAJE') {
                d.monto = subtotalServicios * d.valor / 100;
            }
            if (d.tipo === 'MONTO_FIJO') {
                d.monto = d.valor;
            }
            totalDescuentos += d.monto;
        });

        let totalFinal = Math.max(0, subtotalServicios - totalDescuentos);

        // ===== SUBTOTALES =====
        document.getElementById('txt_subtotal_servicios').innerText =
            'Gs. ' + subtotalServicios.toLocaleString();

        document.getElementById('total_subtotal').innerText =
            'Gs. ' + subtotalServicios.toLocaleString();

        // ===== DESCUENTOS =====
        document.getElementById('txt_total_descuento').innerText =
            'Gs. ' + totalDescuentos.toLocaleString();

        // ===== TOTAL FINAL =====
        document.getElementById('txt_total_final').innerText =
            'Gs. ' + totalFinal.toLocaleString();

        // ===== HIDDEN PARA SUBMIT =====
        const inpSub = document.getElementById('inp_subtotal_servicios');
        if (inpSub) inpSub.value = subtotalServicios;

        const inpDesc = document.getElementById('inp_total_descuento');
        if (inpDesc) inpDesc.value = totalDescuentos;

        const inpFinal = document.getElementById('inp_total_final');
        if (inpFinal) inpFinal.value = totalFinal;
    }





    function renderDetalle() {

        let tbody = document.querySelector('#tabla_detalle tbody');
        tbody.innerHTML = '';

        detalleServicios.forEach((item, index) => {

            let tr = document.createElement('tr');

            tr.innerHTML = `
            <td>${item.descripcion}</td>

            <td>
                <input type="number" min="1"
                       class="form-control form-control-sm"
                       value="${item.cantidad}"
                       oninput="cambiarCantidad(this, ${index})">
            </td>

            <td class="text-center">
                Gs. ${item.precio_base.toLocaleString()}
            </td>

            <td class="text-center" id="subtotal_${index}">
                ${item.subtotal.toLocaleString()}
            </td>          
            <td class="text-center">
            ${item.promocion? `<small class="text-success">Promo: ${item.promocion.nombre}</small>`: ''}
            </t d>
            <td class="text-center">
                <button class="btn btn-danger btn-sm"
                        onclick="quitarServicio(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

            tbody.appendChild(tr);
        });

        recalcularTotales();
    }


    function cambiarCantidad(input, index) {

        let cantidad = parseInt(input.value);
        if (isNaN(cantidad) || cantidad <= 0) cantidad = 1;

        let item = detalleServicios[index];
        item.cantidad = cantidad;

        recalcularPromocion(index);
        recalcularLinea(index);

        document.getElementById('subtotal_' + index).innerText =
            item.subtotal.toLocaleString();

        recalcularTotales();
        guardarEstadoConDelay();
    }



    function quitarServicio(index) {
        detalleServicios.splice(index, 1);
        renderDetalle();
        guardarEstadoConDelay();
    }

    function recalcularPromocion(index) {

        let item = detalleServicios[index];

        item.precio_final = item.precio_base;
        item.monto_promocion = 0;

        if (!item.promocion) return;

        let p = item.promocion;

        if (p.tipo === 'PORCENTAJE') {
            item.monto_promocion =
                item.precio_base * p.valor / 100;
        }

        if (p.tipo === 'MONTO_FIJO') {
            item.monto_promocion =
                Math.min(item.precio_base, p.valor);
        }

        if (p.tipo === 'PRECIO_FIJO') {
            item.monto_promocion =
                item.precio_base - p.valor;
        }

        item.precio_final =
            item.precio_base - item.monto_promocion;
    }

    function recalcularLinea(index) {
        let item = detalleServicios[index];
        item.subtotal = item.cantidad * item.precio_final;
    }


    function cargarDescuentosCliente(idCliente) {

        let datos = new FormData();
        datos.append('descuentos_cliente', idCliente);

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.json())
            .then(descuentos => {

                let html = '';

                if (!Array.isArray(descuentos) || descuentos.length === 0) {

                    html = '<span class="text-muted">Sin descuentos disponibles</span>';

                } else {

                    descuentos.forEach(d => {

                        // 🔹 verificar si ya estaba aplicado (por localStorage)
                        let checked = descuentosAplicados.some(
                            x => x.id_descuento == d.id_descuento
                        );

                        html += `
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               ${checked ? 'checked' : ''}
                               onchange="toggleDescuento(
                                   this,
                                   ${d.id_descuento},
                                   '${d.nombre}',
                                   '${d.tipo}',
                                   ${d.valor}
                               )">

                        <label class="form-check-label">
                            ${d.nombre}
                            (${d.tipo === 'PORCENTAJE'
                                ? d.valor + '%'
                                : 'Gs. ' + d.valor.toLocaleString()})
                        </label>
                    </div>`;
                    });
                }

                document.getElementById('descuentos_cliente').innerHTML = html;

                // 🔁 recalcular totales por si venían seleccionados
                recalcularTotales();
            })
            .catch(err => console.error('Error cargando descuentos:', err));
    }



    function toggleDescuento(checkbox, id, nombre, tipo, valor) {

        if (checkbox.checked) {

            // ⛔ evitar duplicados
            let existe = descuentosAplicados.some(
                d => d.id_descuento == id
            );

            if (!existe) {
                descuentosAplicados.push({
                    id_descuento: id,
                    nombre: nombre,
                    tipo: tipo,
                    valor: valor,
                    monto: 0
                });
            }

        } else {

            descuentosAplicados =
                descuentosAplicados.filter(d => d.id_descuento != id);
        }

        recalcularTotales();
        guardarEstadoPresupuesto();
    }


    document.querySelector('.FormularioAjax')
        .addEventListener('submit', prepararEnvioPresupuesto);


    function prepararEnvioPresupuesto() {

        document.getElementById('detalle_json').value =
            JSON.stringify(detalleServicios);

        document.getElementById('descuentos_json').value =
            JSON.stringify(descuentosAplicados);

        // los valores ya vienen calculados en los inputs hidden

    }

    document.addEventListener('ajax:limpiar', function(e) {
        if (!e.detail || e.detail.modulo !== 'presupuesto') return;

        limpiarFormularioPresupuesto();
    });
</script>