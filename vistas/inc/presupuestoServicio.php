<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    let detalleServicios = [];
    let timeoutGuardar = null;
    let descuentosAplicados = [];
    let detalleDiagnosticoOriginal = [];
    let detalleDesdePreliminarId = '';
    let estadoPorModo = {
        DIAGNOSTICO: null,
        PRELIMINAR: null
    };

    function modoPresupuesto() {
        return document.getElementById('origen_presupuesto')?.value || 'DIAGNOSTICO';
    }

    function diagnosticoSeleccionado() {
        return (document.getElementById('id_diagnostico')?.value || '').trim() !== '';
    }

    function preliminarSeleccionado() {
        return (document.getElementById('id_cliente')?.value || '').trim() !== '' &&
            (document.getElementById('id_vehiculo')?.value || '').trim() !== '';
    }

    function contextoPresupuestoSeleccionado() {
        return modoPresupuesto() === 'PRELIMINAR' ? preliminarSeleccionado() : diagnosticoSeleccionado();
    }

    function actualizarBloqueoPresupuesto() {
        const habilitado = contextoPresupuestoSeleccionado();
        const buscadorServicio = document.getElementById('buscar_servicio');
        const btnGuardar = document.getElementById('btn_guardar_presupuesto_servicio');
        const mensajeContexto = modoPresupuesto() === 'PRELIMINAR' ?
            'Seleccione cliente y vehiculo antes de agregar servicios' :
            'Seleccione un diagnostico antes de agregar servicios';

        if (buscadorServicio) {
            buscadorServicio.disabled = !habilitado;
            buscadorServicio.placeholder = habilitado ?
                'Buscar servicio o artículo' :
                mensajeContexto;
        }

        if (btnGuardar) {
            btnGuardar.disabled = !habilitado;
        }
    }

    function clonarDetalle(items) {
        return JSON.parse(JSON.stringify(Array.isArray(items) ? items : []));
    }

    function mostrarAvisoDetallePreliminar() {
        const contenedor = document.getElementById('aviso_detalle_preliminar');
        if (!contenedor) return;

        if (!detalleDesdePreliminarId) {
            contenedor.style.display = 'none';
            contenedor.innerHTML = '';
            return;
        }

        const puedeRestaurar = Array.isArray(detalleDiagnosticoOriginal) && detalleDiagnosticoOriginal.length > 0;
        contenedor.style.display = 'block';
        contenedor.innerHTML = `
            <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                <span>
                    Detalle cargado desde presupuesto preliminar #${detalleDesdePreliminarId}.
                    ${puedeRestaurar ? 'Puede restaurar el detalle generado desde el diagnóstico.' : ''}
                </span>
                ${puedeRestaurar ? `
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="restaurarDetalleDiagnostico()">
                        Restaurar diagnóstico
                    </button>` : ''}
            </div>`;
    }

    function snapshotModoActual() {
        return {
            convertido_desde: document.getElementById('convertido_desde')?.value || '',
            detalle: clonarDetalle(detalleServicios),
            descuentos: clonarDetalle(descuentosAplicados),
            detalle_diagnostico_original: clonarDetalle(detalleDiagnosticoOriginal),
            detalle_desde_preliminar_id: detalleDesdePreliminarId,
            diagnostico: {
                id_diagnostico: document.getElementById('id_diagnostico')?.value || '',
                diagnostico_info: document.getElementById('diagnostico_info')?.value || '',
                cliente: document.getElementById('cliente')?.value || '',
                vehiculo: document.getElementById('vehiculo')?.value || '',
                id_cliente: modoPresupuesto() === 'DIAGNOSTICO' ? (document.getElementById('id_cliente')?.value || '') : '',
                id_vehiculo: modoPresupuesto() === 'DIAGNOSTICO' ? (document.getElementById('id_vehiculo')?.value || '') : '',
                kilometraje: document.getElementById('kilometraje')?.value || '',
                observacion: document.getElementById('observacion')?.value || '',
                lista_html: document.getElementById('lista_diagnostico')?.innerHTML || ''
            },
            preliminar: {
                id_cliente: modoPresupuesto() === 'PRELIMINAR' ? (document.getElementById('id_cliente')?.value || '') : '',
                id_vehiculo: modoPresupuesto() === 'PRELIMINAR' ? (document.getElementById('id_vehiculo')?.value || '') : '',
                cliente: document.getElementById('cliente_preliminar')?.value || '',
                vehiculo: document.getElementById('vehiculo_preliminar')?.value || ''
            }
        };
    }

    function aplicarSnapshotModo(modo, estado) {
        if (!estado) return false;

        document.getElementById('convertido_desde').value = estado.convertido_desde || '';
        detalleServicios = clonarDetalle(estado.detalle);
        descuentosAplicados = clonarDetalle(estado.descuentos);
        detalleDiagnosticoOriginal = clonarDetalle(estado.detalle_diagnostico_original);
        detalleDesdePreliminarId = estado.detalle_desde_preliminar_id || estado.convertido_desde || '';

        if (modo === 'DIAGNOSTICO') {
            const diagnostico = estado.diagnostico || {};
            limpiarDatosPreliminar();
            document.getElementById('id_diagnostico').value = diagnostico.id_diagnostico || '';
            document.getElementById('diagnostico_info').value = diagnostico.diagnostico_info || '';
            document.getElementById('id_cliente').value = diagnostico.id_cliente || '';
            document.getElementById('id_vehiculo').value = diagnostico.id_vehiculo || '';
            document.getElementById('cliente').value = diagnostico.cliente || '';
            document.getElementById('vehiculo').value = diagnostico.vehiculo || '';
            document.getElementById('kilometraje').value = diagnostico.kilometraje || '';
            document.getElementById('observacion').value = diagnostico.observacion || '';
            if (diagnostico.lista_html) {
                document.getElementById('lista_diagnostico').innerHTML = diagnostico.lista_html;
            }
        } else {
            const preliminar = estado.preliminar || {};
            limpiarDatosDiagnostico();
            document.getElementById('id_cliente').value = preliminar.id_cliente || '';
            document.getElementById('id_vehiculo').value = preliminar.id_vehiculo || '';
            document.getElementById('cliente_preliminar').value = preliminar.cliente || '';
            document.getElementById('vehiculo_preliminar').value = preliminar.vehiculo || '';
        }

        renderDetalle();
        mostrarAvisoDetallePreliminar();
        actualizarBloqueoPresupuesto();
        return true;
    }

    function cambiarModoPresupuesto(modo) {
        const modoAnterior = modoPresupuesto();
        estadoPorModo[modoAnterior] = snapshotModoActual();

        document.getElementById('origen_presupuesto').value = modo;
        document.getElementById('bloque_diagnostico').style.display = modo === 'DIAGNOSTICO' ? 'block' : 'none';
        document.getElementById('bloque_preliminar').style.display = modo === 'PRELIMINAR' ? 'block' : 'none';

        if (aplicarSnapshotModo(modo, estadoPorModo[modo])) {
            guardarEstadoPresupuesto();
            return;
        }

        detalleServicios = [];
        descuentosAplicados = [];
        detalleDiagnosticoOriginal = [];
        detalleDesdePreliminarId = '';
        document.getElementById('convertido_desde').value = '';
        document.getElementById('resultado_servicios').innerHTML = '';
        const preliminares = document.getElementById('presupuestos_preliminares');
        if (preliminares) preliminares.innerHTML = '';
        renderDetalle();
        mostrarAvisoDetallePreliminar();

        if (modo === 'DIAGNOSTICO') {
            limpiarDatosPreliminar();
        } else {
            limpiarDatosDiagnostico();
        }

        actualizarBloqueoPresupuesto();
        guardarEstadoPresupuesto();
    }

    function limpiarDatosDiagnostico() {
        document.getElementById('id_diagnostico').value = '';
        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('diagnostico_info').value = '';
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        document.getElementById('kilometraje').value = '';
        document.getElementById('observacion').value = '';
        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('cliente_preliminar').value = '';
        document.getElementById('vehiculo_preliminar').value = '';
        document.getElementById('lista_diagnostico').innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Seleccione un diagnostico para ver los trabajos...
                </td>
            </tr>`;
    }

    function limpiarDatosPreliminar() {
        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('cliente_preliminar').value = '';
        document.getElementById('vehiculo_preliminar').value = '';
    }

    function abrirModalClientePresupuesto() {
        $("#modalClientePresupuesto").modal("show");
    }

    function abrirModalVehiculoPresupuesto() {
        if (!document.getElementById('id_cliente').value) {
            alert('Debe seleccionar un cliente primero');
            return;
        }
        $("#modalVehiculoPresupuesto").modal("show");
    }

    function buscarClientePresupuesto() {
        let txt = document.getElementById('buscar_cliente').value.trim();
        let datos = new FormData();
        datos.append('buscar_cliente', txt);

        fetch(SERVERURL + 'ajax/recepcionservicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('tabla_clientes').innerHTML = html;
            });
    }

    function buscarVehiculoPresupuesto() {
        let txt = document.getElementById('buscar_vehiculo').value.trim();
        let idCliente = document.getElementById('id_cliente').value;

        if (!idCliente) {
            document.getElementById('tabla_vehiculos').innerHTML =
                '<div class="alert alert-warning text-center">Seleccione un cliente</div>';
            return;
        }

        let datos = new FormData();
        datos.append('buscar_vehiculo', txt);
        datos.append('id_cliente', idCliente);

        fetch(SERVERURL + 'ajax/recepcionservicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('tabla_vehiculos').innerHTML = html;
            });
    }

    function seleccionarCliente(id, nombre, doc) {
        document.getElementById('id_cliente').value = id;
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('cliente_preliminar').value = nombre + ' - ' + doc;
        document.getElementById('vehiculo_preliminar').value = '';
        descuentosAplicados = [];
        cargarDescuentosCliente(id);
        actualizarBloqueoPresupuesto();
        guardarEstadoPresupuesto();
        $("#modalClientePresupuesto").modal("hide");
    }

    function seleccionarVehiculo(id, desc) {
        document.getElementById('id_vehiculo').value = id;
        document.getElementById('vehiculo_preliminar').value = desc;
        actualizarBloqueoPresupuesto();
        guardarEstadoPresupuesto();
        $("#modalVehiculoPresupuesto").modal("hide");
    }

    function buscarPresupuestosPreliminares(idCliente, idVehiculo) {
        const contenedor = document.getElementById('presupuestos_preliminares');
        if (!contenedor) return;

        contenedor.innerHTML = '';

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'buscar_preliminares',
                    id_cliente: idCliente,
                    id_vehiculo: idVehiculo
                })
            })
            .then(r => r.text())
            .then(html => {
                contenedor.innerHTML = html;
            });
    }

    function usarPresupuestoPreliminar(idPresupuesto) {
        if (modoPresupuesto() === 'DIAGNOSTICO' && detalleServicios.length > 0) {
            const confirma = confirm(
                'Esto reemplazara el detalle generado desde el diagnostico por el detalle del presupuesto preliminar seleccionado. Desea continuar?'
            );

            if (!confirma) {
                return;
            }

            if (!Array.isArray(detalleDiagnosticoOriginal) || detalleDiagnosticoOriginal.length === 0) {
                detalleDiagnosticoOriginal = clonarDetalle(detalleServicios);
            }
        }

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'detalle_preliminar',
                    id_presupuesto: idPresupuesto
                })
            })
            .then(r => r.json())
            .then(data => {
                if (!data || data.error) {
                    alert(data && data.msg ? data.msg : 'No se pudo cargar el presupuesto preliminar');
                    return;
                }

                detalleServicios = (data.detalle || []).map(item => {
                    const cantidad = Number(item.cantidad || 1);
                    const precio = Number(item.preciouni || 0);
                    return {
                        id_articulo: Number(item.id_articulo),
                        descripcion: item.desc_articulo,
                        cantidad: cantidad,
                        precio_base: precio,
                        precio_final: precio,
                        subtotal: precio * cantidad,
                        tipo: item.tipo,
                        stock: Number(item.stock || 0),
                        promocion: null,
                        monto_promocion: 0
                    };
                });

                document.getElementById('convertido_desde').value = data.id_presupuesto;
                detalleDesdePreliminarId = data.id_presupuesto || idPresupuesto;
                renderDetalle();
                mostrarAvisoDetallePreliminar();
                guardarEstadoPresupuesto();
                alert('Presupuesto preliminar cargado para revision');
            });
    }

    function guardarEstadoConDelay() {

        if (timeoutGuardar) {
            clearTimeout(timeoutGuardar);
        }

        timeoutGuardar = setTimeout(() => {
            guardarEstadoPresupuesto();
        }, 2000); // 300–500 ms es ideal
    }

    function abrirModalDiagnostico() {
        $("#modalDiagnostico").modal("show");
    }

    function buscarDiagnostico() {

        let texto = document.getElementById("buscar_diagnostico").value;

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: new URLSearchParams({
                    buscar_diagnostico: texto
                })
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById('tabla_diagnostico').innerHTML = html;
            });
    }

    function seleccionarDiagnostico(id, desc) {
        console.log("Seleccionado:", id, desc);
        document.getElementById('id_diagnostico').value = id;
        document.getElementById('diagnostico_info').value = desc;
        actualizarBloqueoPresupuesto();

        $("#modalDiagnostico").modal("hide");

        cargarDatosDiagnostico(id);
    }

    function cargarDatosDiagnostico(id) {

        fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'datos_diagnostico',
                    id_diagnostico: id
                })
            })
            .then(r => r.json())
            .then(data => {

                console.log("DATA:", data);

                if (!data || !data.id_cliente) {
                    alert('El diagnostico seleccionado no esta disponible para presupuesto');
                    limpiarFormularioPresupuesto();
                    return;
                }

                document.getElementById('cliente').value = data.cliente;
                document.getElementById('vehiculo').value = data.vehiculo;
                document.getElementById('kilometraje').value = data.kilometraje;
                document.getElementById('observacion').value = data.observaciones;
                document.getElementById('id_sucursal').value = data.id_sucursal;
                document.getElementById('id_cliente').value = data.id_cliente;
                document.getElementById('id_vehiculo').value = data.id_vehiculo;

                // DETALLE DESDE DIAGNÓSTICO
                if (data.detalle) {

                    let html = '';

                    data.detalle.forEach(d => {

                        html += `
                        <tr>
                            <td>${d.servicio_desc || '-'}</td>
                            <td>${d.repuesto_origen || '-'}</td>
                            <td>${d.repuesto_desc || '-'}</td>
                            <td class="text-center">${d.repuesto_desc ? d.cantidad_repuesto : '-'}</td>
                            <td>${d.gravedad || '-'}</td>
                            <td>${d.problema || '-'}</td>
                        </tr>`;
                    });

                    document.getElementById('lista_diagnostico').innerHTML = html;
                }

                cargarDetallePresupuestoDiagnostico(data.detalle_presupuesto || []);

                cargarDescuentosCliente(data.id_cliente);
                buscarPresupuestosPreliminares(data.id_cliente, data.id_vehiculo);
            })
            .catch(err => {
                console.error("ERROR FETCH:", err);
            });
    }

    function aplicarPromoAItem(item) {
        let datos = new FormData();
        datos.append('promo_articulo', item.id_articulo);

        return fetch(SERVERURL + 'ajax/presupuestoServicioAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(r => r.json())
            .then(promo => {
                if (promo.id_promocion) {
                    item.monto_promocion = 0;
                    if (promo.tipo === 'PORCENTAJE') {
                        item.monto_promocion = item.precio_base * promo.valor / 100;
                        item.precio_final = item.precio_base - (item.precio_base * promo.valor / 100);
                    }

                    if (promo.tipo === 'MONTO_FIJO') {
                        item.monto_promocion = Math.min(item.precio_base, promo.valor);
                        item.precio_final = Math.max(0, item.precio_base - promo.valor);
                    }

                    if (promo.tipo === 'PRECIO_FIJO') {
                        item.monto_promocion = Math.max(0, item.precio_base - promo.valor);
                        item.precio_final = promo.valor;
                    }

                    item.monto_promocion = Math.min(item.precio_base, Math.max(0, item.monto_promocion));
                    item.precio_final = Math.max(0, item.precio_base - item.monto_promocion);
                    item.promocion = promo;
                }

                item.subtotal = item.precio_base * item.cantidad;
                return item;
            });
    }

    function cargarDetallePresupuestoDiagnostico(items) {
        detalleServicios = [];
        detalleDiagnosticoOriginal = [];
        detalleDesdePreliminarId = '';
        document.getElementById('convertido_desde').value = '';

        if (!Array.isArray(items) || items.length === 0) {
            renderDetalle();
            mostrarAvisoDetallePreliminar();
            guardarEstadoPresupuesto();
            return;
        }

        const base = items.map(item => {
            const cantidad = Number(item.cantidad || 1);
            const precio = Number(item.precio_venta || 0);

            return {
                id_articulo: Number(item.id_articulo),
                descripcion: item.desc_articulo,
                cantidad: cantidad > 0 ? cantidad : 1,
                precio_base: precio,
                precio_final: precio,
                subtotal: precio * (cantidad > 0 ? cantidad : 1),
                tipo: item.tipo,
                stock: Number(item.stock || 0),
                promocion: null,
                monto_promocion: 0
            };
        });

        Promise.all(base.map(aplicarPromoAItem))
            .then(detalle => {
                detalleServicios = detalle;
                detalleDiagnosticoOriginal = clonarDetalle(detalle);
                renderDetalle();
                mostrarAvisoDetallePreliminar();
                guardarEstadoPresupuesto();
            })
            .catch(err => {
                console.error('Error cargando detalle del diagnostico:', err);
                detalleServicios = base;
                detalleDiagnosticoOriginal = clonarDetalle(base);
                renderDetalle();
                mostrarAvisoDetallePreliminar();
                guardarEstadoPresupuesto();
            });
    }

    function restaurarDetalleDiagnostico() {
        if (!Array.isArray(detalleDiagnosticoOriginal) || detalleDiagnosticoOriginal.length === 0) {
            alert('No hay detalle de diagnostico para restaurar');
            return;
        }

        detalleServicios = clonarDetalle(detalleDiagnosticoOriginal);
        detalleDesdePreliminarId = '';
        document.getElementById('convertido_desde').value = '';
        renderDetalle();
        mostrarAvisoDetallePreliminar();
        guardarEstadoPresupuesto();
    }

    function guardarEstadoPresupuesto() {
        estadoPorModo[modoPresupuesto()] = snapshotModoActual();

        let estado = {
            origen: modoPresupuesto(),
            convertido_desde: document.getElementById('convertido_desde')?.value || '',
            diagnostico: {
                id_diagnostico: document.getElementById('id_diagnostico')?.value || '',
                cliente: document.getElementById('cliente')?.value || '',
                vehiculo: document.getElementById('vehiculo')?.value || '',
                id_cliente: modoPresupuesto() === 'DIAGNOSTICO' ? (document.getElementById('id_cliente')?.value || '') : '',
                id_vehiculo: modoPresupuesto() === 'DIAGNOSTICO' ? (document.getElementById('id_vehiculo')?.value || '') : '',
                kilometraje: document.getElementById('kilometraje')?.value || '',
                observacion: document.getElementById('observacion')?.value || ''
            },
            preliminar: {
                id_cliente: document.getElementById('id_cliente')?.value || '',
                id_vehiculo: document.getElementById('id_vehiculo')?.value || '',
                cliente: document.getElementById('cliente_preliminar')?.value || '',
                vehiculo: document.getElementById('vehiculo_preliminar')?.value || ''
            },
            detalle: detalleServicios,
            detalle_diagnostico_original: detalleDiagnosticoOriginal,
            detalle_desde_preliminar_id: detalleDesdePreliminarId,
            estado_por_modo: estadoPorModo,
            descuentos: descuentosAplicados
        };

        localStorage.setItem(
            'presupuesto_servicio_tmp',
            JSON.stringify(estado)
        );

    }

    document.addEventListener('DOMContentLoaded', function() {
        actualizarBloqueoPresupuesto();

        let data = localStorage.getItem('presupuesto_servicio_tmp');

        if (!data) return;

        let estado = JSON.parse(data);
        if (estado.estado_por_modo) {
            estadoPorModo = {
                DIAGNOSTICO: estado.estado_por_modo.DIAGNOSTICO || null,
                PRELIMINAR: estado.estado_por_modo.PRELIMINAR || null
            };
        }
        const origen = estado.origen || 'DIAGNOSTICO';
        const radioModo = document.getElementById(origen === 'PRELIMINAR' ? 'modo_preliminar' : 'modo_diagnostico');
        if (radioModo) radioModo.checked = true;
        document.getElementById('origen_presupuesto').value = origen;
        document.getElementById('convertido_desde').value = estado.convertido_desde || '';
        document.getElementById('bloque_diagnostico').style.display = origen === 'DIAGNOSTICO' ? 'block' : 'none';
        document.getElementById('bloque_preliminar').style.display = origen === 'PRELIMINAR' ? 'block' : 'none';

        if (estadoPorModo[origen] && aplicarSnapshotModo(origen, estadoPorModo[origen])) {
            const clienteSnapshot = origen === 'PRELIMINAR' ?
                (estadoPorModo[origen].preliminar && estadoPorModo[origen].preliminar.id_cliente) :
                (estadoPorModo[origen].diagnostico && estadoPorModo[origen].diagnostico.id_cliente);

            if (clienteSnapshot) {
                cargarDescuentosCliente(clienteSnapshot);
            }

            return;
        }

        // 🔹 
        if (estado.diagnostico) {
            document.getElementById('id_diagnostico').value = estado.diagnostico.id_diagnostico || '';
            document.getElementById('id_cliente').value = estado.diagnostico.id_cliente || document.getElementById('id_cliente').value;
            document.getElementById('id_vehiculo').value = estado.diagnostico.id_vehiculo || document.getElementById('id_vehiculo').value;
            document.getElementById('cliente').value = estado.diagnostico.cliente || '';
            document.getElementById('vehiculo').value = estado.diagnostico.vehiculo || '';
            document.getElementById('kilometraje').value = estado.diagnostico.kilometraje || '';
            actualizarBloqueoPresupuesto();
        }

        if (estado.preliminar) {
            document.getElementById('id_cliente').value = estado.preliminar.id_cliente || document.getElementById('id_cliente').value;
            document.getElementById('id_vehiculo').value = estado.preliminar.id_vehiculo || document.getElementById('id_vehiculo').value;
            document.getElementById('cliente_preliminar').value = estado.preliminar.cliente || '';
            document.getElementById('vehiculo_preliminar').value = estado.preliminar.vehiculo || '';
            actualizarBloqueoPresupuesto();
        }

        // 🔹 Detalle
        if (Array.isArray(estado.detalle) && estado.detalle.length > 0) {
            detalleServicios = estado.detalle;

            // ⚠️ esperar al DOM
            setTimeout(() => {
                renderDetalle();
            }, 100);
        }
        if (Array.isArray(estado.detalle_diagnostico_original)) {
            detalleDiagnosticoOriginal = estado.detalle_diagnostico_original;
        }
        detalleDesdePreliminarId = estado.detalle_desde_preliminar_id || estado.convertido_desde || '';
        mostrarAvisoDetallePreliminar();
        if (Array.isArray(estado.descuentos)) {
            descuentosAplicados = estado.descuentos;
        }
        const clienteDescuento = origen === 'PRELIMINAR' ?
            (estado.preliminar && estado.preliminar.id_cliente) :
            (estado.diagnostico && estado.diagnostico.id_cliente);

        if (clienteDescuento) {
            cargarDescuentosCliente(clienteDescuento);
        }

    });

    function limpiarFormularioPresupuesto() {

        detalleServicios = [];
        descuentosAplicados = [];
        detalleDiagnosticoOriginal = [];
        detalleDesdePreliminarId = '';

        document.getElementById('detalle_json').value = '';
        document.getElementById('descuentos_json').value = '';
        document.getElementById('convertido_desde').value = '';

        // DIAGNOSTICO
        document.getElementById('id_diagnostico').value = '';
        document.getElementById('diagnostico_info').value = '';
        document.getElementById('cliente').value = '';
        document.getElementById('vehiculo').value = '';
        document.getElementById('kilometraje').value = '';
        document.getElementById('observacion').value = '';

        // LIMPIAR DETALLE DIAGNOSTICO
        document.getElementById('lista_diagnostico').innerHTML = `
    <tr>
        <td colspan="6" class="text-center text-muted">
            Seleccione un diagnóstico para ver los trabajos...
        </td>
    </tr>`;

        // LIMPIAR BUSCADOR
        document.getElementById('buscar_servicio').value = '';
        document.getElementById('resultado_servicios').innerHTML = '';
        const preliminares = document.getElementById('presupuestos_preliminares');
        if (preliminares) preliminares.innerHTML = '';
        actualizarBloqueoPresupuesto();

        // TABLA SERVICIOS
        renderDetalle();
        mostrarAvisoDetallePreliminar();

        // DESCUENTOS
        document.getElementById('descuentos_cliente').innerHTML =
            '<span class="text-muted">Seleccione una recepción para ver descuentos</span>';

        // TOTALES
        document.getElementById('txt_subtotal_servicios').innerText = 'Gs. 0';
        const txtPromo = document.getElementById('txt_total_promociones');
        if (txtPromo) txtPromo.innerText = 'Gs. 0';
        const totalPromo = document.getElementById('total_promociones');
        if (totalPromo) totalPromo.innerText = 'Gs. 0';
        document.getElementById('txt_total_descuento').innerText = 'Gs. 0';
        document.getElementById('txt_total_final').innerText = 'Gs. 0';

        // LOCALSTORAGE
        localStorage.removeItem('presupuesto_servicio_tmp');
    }

    function buscarServicio() {
        if (!contextoPresupuestoSeleccionado()) {
            document.getElementById('resultado_servicios').innerHTML =
                '<div class="alert alert-warning text-center">Complete el origen del presupuesto antes de agregar servicios</div>';
            return;
        }

        let txt = document.getElementById('buscar_servicio').value.trim();

        if (txt.length < 2) {
            document.getElementById('resultado_servicios').innerHTML = '';
            return;
        }

        let datos = new FormData();
        datos.append('buscar_servicio', txt);
        datos.append('origen_presupuesto', modoPresupuesto());
        datos.append('id_diagnostico', document.getElementById('id_diagnostico').value);
        datos.append('id_cliente', document.getElementById('id_cliente').value);
        datos.append('id_vehiculo', document.getElementById('id_vehiculo').value);

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

    function agregarServicio(id, descripcion, precio, tipo, stock) {
        if (!contextoPresupuestoSeleccionado()) {
            alert('Complete el origen del presupuesto antes de agregar servicios');
            return;
        }

        let existe = detalleServicios.find(i => i.id_articulo == id);
        if (existe) {
            alert('El servicio ya fue agregado');
            return;
        }
        if (tipo === 'producto' && stock <= 0) {
            alert('Sin stock disponible');
            return;
        }

        let item = {
            id_articulo: id,
            descripcion: descripcion,
            cantidad: 1,
            precio_base: precio,
            precio_final: precio,
            subtotal: precio,
            tipo: tipo,
            stock: stock,
            promocion: null,
            monto_promocion: 0
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
                    item.monto_promocion = 0;

                    if (promo.tipo === 'PORCENTAJE') {
                        item.monto_promocion = precio * promo.valor / 100;
                        item.precio_final =
                            precio - (precio * promo.valor / 100);
                    }

                    if (promo.tipo === 'MONTO_FIJO') {
                        item.monto_promocion = Math.min(precio, promo.valor);
                        item.precio_final =
                            Math.max(0, precio - promo.valor);
                    }

                    if (promo.tipo === 'PRECIO_FIJO') {
                        item.monto_promocion = Math.max(0, precio - promo.valor);
                        item.precio_final = promo.valor;
                    }

                    item.monto_promocion = Math.min(precio, Math.max(0, item.monto_promocion));
                    item.precio_final = Math.max(0, precio - item.monto_promocion);
                    item.subtotal = item.precio_base * item.cantidad;
                    item.promocion = promo;
                }

                detalleServicios.push(item);
                renderDetalle();
                guardarEstadoPresupuesto();
            });
    }

    function recalcularTotales() {

        let subtotalServicios = 0;
        detalleServicios.forEach(i => {
            i.subtotal = Number(i.precio_base || 0) * Number(i.cantidad || 0);
            i.descuento_linea = 0;
            i.subtotal_final = Math.max(0, i.subtotal - (Number(i.monto_promocion || 0) * Number(i.cantidad || 0)));
            subtotalServicios += i.subtotal;
        });
        let totalPromociones = 0;
        detalleServicios.forEach(i => totalPromociones += (Number(i.monto_promocion || 0) * Number(i.cantidad || 0)));
        let baseDescuentos = Math.max(0, subtotalServicios - totalPromociones);

        let totalDescuentos = 0;
        let descuentosPorAlcance = {
            TOTAL: 0,
            PRODUCTO: 0,
            SERVICIO: 0
        };

        descuentosAplicados.forEach(d => {
            const alcance = normalizarAlcanceDescuento(d.aplica_a);
            let baseAlcance = 0;

            detalleServicios.forEach(item => {
                const tipoItem = String(item.tipo || '').toLowerCase();
                if (alcance === 'PRODUCTO' && tipoItem !== 'producto') return;
                if (alcance === 'SERVICIO' && tipoItem !== 'servicio') return;

                const subtotalLinea = Number(item.precio_base || 0) * Number(item.cantidad || 0);
                const promocionLinea = Number(item.monto_promocion || 0) * Number(item.cantidad || 0);
                baseAlcance += Math.max(0, subtotalLinea - promocionLinea);
            });

            const baseDisponibleAlcance = Math.max(0, baseAlcance - (descuentosPorAlcance[alcance] || 0));
            const baseDisponibleGlobal = Math.max(0, baseDescuentos - totalDescuentos);
            const baseDisponible = Math.min(baseDisponibleAlcance, baseDisponibleGlobal);

            if (d.tipo === 'PORCENTAJE') {
                d.monto = baseAlcance * Number(d.valor || 0) / 100;
            }
            if (d.tipo === 'MONTO_FIJO') {
                d.monto = Number(d.valor || 0);
            }
            d.aplica_a = alcance;
            d.base_aplicada = baseAlcance;
            d.monto = Math.min(Number(d.monto || 0), baseDisponible);

            distribuirDescuentoEnLineas(alcance, baseAlcance, d.monto);
            totalDescuentos += d.monto;
            descuentosPorAlcance[alcance] = (descuentosPorAlcance[alcance] || 0) + d.monto;
        });

        detalleServicios.forEach((item, index) => {
            const subtotalLinea = Number(item.subtotal || 0);
            const promocionLinea = Number(item.monto_promocion || 0) * Number(item.cantidad || 0);
            item.subtotal_final = Math.max(0, subtotalLinea - promocionLinea - Number(item.descuento_linea || 0));

            const celdaFinal = document.getElementById('subtotal_final_' + index);
            if (celdaFinal) {
                celdaFinal.innerText = item.subtotal_final.toLocaleString();
            }
        });

        let totalFinal = Math.max(0, subtotalServicios - totalPromociones - totalDescuentos);

        // ===== SUBTOTALES =====
        document.getElementById('txt_subtotal_servicios').innerText =
            'Gs. ' + subtotalServicios.toLocaleString();

        document.getElementById('total_subtotal').innerText =
            'Gs. ' + subtotalServicios.toLocaleString();

        const txtPromos = document.getElementById('txt_total_promociones');
        if (txtPromos) txtPromos.innerText = 'Gs. ' + totalPromociones.toLocaleString();

        const totalPromos = document.getElementById('total_promociones');
        if (totalPromos) totalPromos.innerText = 'Gs. ' + totalPromociones.toLocaleString();

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
                       class="form-control form-control-sm text-center"
                       value="${item.cantidad}"
                       oninput="cambiarCantidad(this, ${index})">
            </td>

            <td class="text-center">
                Gs. ${item.precio_base.toLocaleString()}
            </td>

            <td class="text-center" id="subtotal_${index}">
                ${item.subtotal.toLocaleString()}
            </td>          
            <td class="text-center" id="promo_${index}">
            ${item.promocion ? `<small class="text-success">${item.promocion.nombre}<br>- Gs. ${(Number(item.monto_promocion || 0) * Number(item.cantidad || 0)).toLocaleString()}</small>` : ''}
            </td>
            <td class="text-center" id="subtotal_final_${index}">
                ${Number(item.subtotal_final || item.subtotal || 0).toLocaleString()}
            </td>
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

        let item = detalleServicios[index];

        let cantidad = parseInt(input.value);
        if (isNaN(cantidad) || cantidad <= 0) {
            alert('La cantidad debe ser mayor a cero');
            cantidad = 1;
            input.value = cantidad;
        }


        if (item.tipo === 'producto' && cantidad > item.stock) {
            alert('Stock insuficiente');
            input.value = item.stock;
            cantidad = item.stock;
        }

        item.cantidad = cantidad;

        recalcularPromocion(index);
        recalcularLinea(index);

        document.getElementById('subtotal_' + index).innerText =
            item.subtotal.toLocaleString();
        const promoCelda = document.getElementById('promo_' + index);
        if (promoCelda) {
            promoCelda.innerHTML = item.promocion ?
                `<small class="text-success">${item.promocion.nombre}<br>- Gs. ${(Number(item.monto_promocion || 0) * Number(item.cantidad || 0)).toLocaleString()}</small>` :
                '';
        }

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
                Math.max(0, item.precio_base - p.valor);
        }

        item.monto_promocion =
            Math.min(item.precio_base, Math.max(0, item.monto_promocion));

        item.precio_final =
            item.precio_base - item.monto_promocion;
    }

    function recalcularLinea(index) {
        let item = detalleServicios[index];
        item.subtotal = item.cantidad * item.precio_base;
    }

    function distribuirDescuentoEnLineas(alcance, baseAlcance, montoDescuento) {
        if (baseAlcance <= 0 || montoDescuento <= 0) return;

        let indicesAlcanzados = [];
        detalleServicios.forEach((item, index) => {
            const tipoItem = String(item.tipo || '').toLowerCase();
            if (alcance === 'PRODUCTO' && tipoItem !== 'producto') return;
            if (alcance === 'SERVICIO' && tipoItem !== 'servicio') return;

            const subtotalLinea = Number(item.precio_base || 0) * Number(item.cantidad || 0);
            const promocionLinea = Number(item.monto_promocion || 0) * Number(item.cantidad || 0);
            const baseLinea = Math.max(0, subtotalLinea - promocionLinea);
            if (baseLinea > 0) {
                indicesAlcanzados.push({
                    index,
                    baseLinea
                });
            }
        });

        let acumulado = 0;
        indicesAlcanzados.forEach((linea, pos) => {
            let montoLinea = montoDescuento * (linea.baseLinea / baseAlcance);
            if (pos === indicesAlcanzados.length - 1) {
                montoLinea = Math.max(0, montoDescuento - acumulado);
            } else {
                montoLinea = Math.round(montoLinea);
                acumulado += montoLinea;
            }

            detalleServicios[linea.index].descuento_linea =
                Number(detalleServicios[linea.index].descuento_linea || 0) + montoLinea;
        });
    }

    function normalizarAlcanceDescuento(aplicaA) {
        const alcance = String(aplicaA || 'TOTAL').toUpperCase();
        return ['TOTAL', 'PRODUCTO', 'SERVICIO'].includes(alcance) ? alcance : 'TOTAL';
    }

    function etiquetaAlcanceDescuento(aplicaA) {
        const alcance = normalizarAlcanceDescuento(aplicaA);
        if (alcance === 'PRODUCTO') return 'Producto';
        if (alcance === 'SERVICIO') return 'Servicio';
        return 'Total';
    }

    function escaparAtributo(texto) {
        return String(texto || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
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
                        const aplicaA = normalizarAlcanceDescuento(d.aplica_a);

                        // 🔹 verificar si ya estaba aplicado (por localStorage)
                        let checked = descuentosAplicados.some(
                            x => x.id_descuento == d.id_descuento
                        );

                        html += `
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               ${checked ? 'checked' : ''}
                               data-id="${Number(d.id_descuento)}"
                               data-nombre="${escaparAtributo(d.nombre)}"
                               data-tipo="${escaparAtributo(d.tipo)}"
                               data-valor="${Number(d.valor)}"
                               data-aplica-a="${aplicaA}"
                               onchange="toggleDescuento(this)">

                        <label class="form-check-label">
                            ${d.nombre}
                            (${d.tipo === 'PORCENTAJE'
                                ? d.valor + '%'
                                : 'Gs. ' + d.valor.toLocaleString()})
                            <small class="text-muted">- ${etiquetaAlcanceDescuento(aplicaA)}</small>
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

    function toggleDescuento(checkbox) {
        const id = Number(checkbox.dataset.id || 0);
        const nombre = checkbox.dataset.nombre || '';
        const tipo = checkbox.dataset.tipo || '';
        const valor = Number(checkbox.dataset.valor || 0);
        const aplicaA = normalizarAlcanceDescuento(checkbox.dataset.aplicaA);

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
                    aplica_a: aplicaA,
                    base_aplicada: 0,
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

    function validarDetallePresupuesto() {
        if (!Array.isArray(detalleServicios) || detalleServicios.length === 0) {
            alert('Debe agregar al menos un servicio');
            return false;
        }

        for (let i = 0; i < detalleServicios.length; i++) {
            const item = detalleServicios[i];
            const cantidad = Number(item.cantidad || 0);
            const precio = Number(item.precio_base || 0);

            if (!Number.isFinite(cantidad) || cantidad <= 0) {
                alert('La cantidad del detalle debe ser mayor a cero');
                renderDetalle();
                return false;
            }

            if (!Number.isFinite(precio) || precio < 0) {
                alert('El precio del detalle no puede ser negativo');
                renderDetalle();
                return false;
            }
        }

        return true;
    }


    document.querySelector('.FormularioAjax')
        .addEventListener('submit', prepararEnvioPresupuesto, true);


    function prepararEnvioPresupuesto(e) {
        if (!contextoPresupuestoSeleccionado()) {
            if (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
            alert('Debe completar el origen del presupuesto antes de guardar');
            return false;
        }

        if (!validarDetallePresupuesto()) {
            if (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
            return false;
        }

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

    document.addEventListener('submit', function(e) {

        const form = e.target.closest('.FormularioAjax');
        if (!form) return;

        // Esperar respuesta AJAX (tu sistema ya lo maneja internamente)
        setTimeout(() => {

            try {

                // buscar última respuesta guardada por tu sistema
                let res = window.lastAjaxResponse || null;

                if (!res) return;

                // SI EL CONTROLADOR DEVUELVE LIMPIAR
                if (res.Alerta === "limpiar") {

                    console.log("🧹 Limpiando formulario presupuesto...");

                    if (typeof limpiarFormularioPresupuesto === "function") {
                        limpiarFormularioPresupuesto();
                    }
                }

            } catch (err) {
                console.error("Error limpieza:", err);
            }

        }, 800); // pequeño delay para esperar respuesta

    });
</script>
