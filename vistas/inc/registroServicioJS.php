<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    
    let timerBuscarOT = null;
    

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

    function textoRegistroServicio(valor) {
        if (valor === null || valor === undefined || valor === '') return '-';
        return valor;
    }

    function htmlRegistroServicio(valor) {
        const div = document.createElement('div');
        div.textContent = textoRegistroServicio(valor);
        return div.innerHTML;
    }

    function numeroRegistroServicio(valor) {
        const numero = Number(valor || 0);
        return numero.toLocaleString('es-PY', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    function fechaRegistroServicio(valor) {
        if (!valor) return '-';
        const fecha = new Date(String(valor).replace(' ', 'T'));
        if (Number.isNaN(fecha.getTime())) return htmlRegistroServicio(valor);
        return fecha.toLocaleDateString('es-PY');
    }

    function estadoRegistroServicio(estado) {
        const estados = {
            '0': '<span class="badge badge-secondary">Anulado</span>',
            '1': '<span class="badge badge-success">Registrado</span>',
            '2': '<span class="badge badge-primary">Facturado</span>',
            '3': '<span class="badge badge-warning">Con Reclamo</span>'
        };
        return estados[String(estado)] || '<span class="badge badge-light">-</span>';
    }

    function verDetalleRegistroServicio(idRegistro) {
        const contenedor = document.getElementById('contenidoDetalleRegistroServicio');
        if (!contenedor) return;

        contenedor.innerHTML = '<div class="text-center text-muted py-4">Cargando detalle...</div>';
        $('#modalDetalleRegistroServicio').modal('show');

        const data = new FormData();
        data.append('accion', 'detalle_registro');
        data.append('id_registro', idRegistro);

        fetch(SERVERURL + 'ajax/registroServicioAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.error) {
                    contenedor.innerHTML = `<div class="alert alert-danger mb-0">${htmlRegistroServicio(res.msg || 'No se pudo cargar el detalle')}</div>`;
                    return;
                }

                const cab = res.cabecera || {};
                const detalle = Array.isArray(res.detalle) ? res.detalle : [];
                let total = 0;
                let filas = '';

                if (detalle.length === 0) {
                    filas = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">Sin detalle registrado</td>
                        </tr>`;
                } else {
                    detalle.forEach(item => {
                        const subtotal = Number(item.subtotal || 0);
                        total += subtotal;
                        filas += `
                            <tr>
                                <td>${htmlRegistroServicio(item.desc_articulo)}</td>
                                <td class="text-center">${htmlRegistroServicio(item.tipo)}</td>
                                <td class="text-center">${numeroRegistroServicio(item.cantidad)}</td>
                                <td class="text-right">${numeroRegistroServicio(item.precio_unitario)}</td>
                                <td class="text-right">${numeroRegistroServicio(subtotal)}</td>
                                <td class="text-center">${htmlRegistroServicio(item.origen)}</td>
                            </tr>`;
                    });
                }

                contenedor.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Registro</small>
                            <strong>#${htmlRegistroServicio(cab.idregistro_servicio)}</strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">OT</small>
                            <strong>#${htmlRegistroServicio(cab.idorden_trabajo)}</strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Fecha</small>
                            <strong>${fechaRegistroServicio(cab.fecha_servicio)}</strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Estado</small>
                            ${estadoRegistroServicio(cab.estado)}
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted d-block">Cliente</small>
                            <strong>${htmlRegistroServicio((cab.nombre_cliente || '') + ' ' + (cab.apellido_cliente || ''))}</strong>
                            <div class="text-muted">${htmlRegistroServicio(cab.doc_number)}</div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted d-block">Vehiculo</small>
                            <strong>${htmlRegistroServicio((cab.mod_descri || '') + ' ' + (cab.placa || ''))}</strong>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted d-block">Registrado por</small>
                            <strong>${htmlRegistroServicio(cab.nombre_usuario)}</strong>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-muted d-block">Kilometraje salida</small>
                            <strong>${htmlRegistroServicio(cab.kilometraje_salida)}</strong>
                        </div>
                        <div class="col-md-8 mb-2">
                            <small class="text-muted d-block">Observacion</small>
                            <span>${htmlRegistroServicio(cab.observacion)}</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Detalle realizado</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-right">Precio</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-center">Origen</th>
                                </tr>
                            </thead>
                            <tbody>${filas}</tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th class="text-right">${numeroRegistroServicio(total)}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>`;
            })
            .catch(() => {
                contenedor.innerHTML = '<div class="alert alert-danger mb-0">No se pudo cargar el detalle</div>';
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


    function limpiarRegistroServicio() {
        

        const idOT = document.getElementById('idorden_trabajo');
        const otNumero = document.getElementById('ot_numero');
        const otCliente = document.getElementById('ot_cliente');
        const otVehiculo = document.getElementById('ot_vehiculo');
        const resultadoOT = document.getElementById('resultado_ot');
        const detalleOT = document.getElementById('detalle_ot');

        if (idOT) idOT.value = '';
        if (otNumero) otNumero.value = '';
        if (otCliente) otCliente.value = '';
        if (otVehiculo) otVehiculo.value = '';
        if (resultadoOT) resultadoOT.innerHTML = '';
        if (detalleOT) {
            detalleOT.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    Seleccione una orden de trabajo
                </td>
            </tr>`;
        }

        const observacion = document.querySelector('[name="observacion"]');
        if (observacion) observacion.value = '';

        setRegistroServicioHabilitado(false);
    }

    const formRegistroServicio = document.getElementById('idorden_trabajo')?.closest('.FormularioAjax');
    if (formRegistroServicio) {
        formRegistroServicio.addEventListener('submit', function(e) {
            const idOT = document.getElementById('idorden_trabajo');
            

            if (!idOT || !idOT.value) {
                e.preventDefault();
                alert('Seleccione una orden de trabajo');
                return;
            }

        }, true);
    }

    document.addEventListener('ajax:limpiar', function(e) {
        if (!e.detail || e.detail.modulo !== 'registro_servicio') return;

        limpiarRegistroServicio();
    });

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.FormularioAjax');
        if (!form || form.dataset.modulo !== 'registro_servicio') return;

        setTimeout(() => {
            const res = window.lastAjaxResponse || null;
            if (res && res.Alerta === 'limpiar') {
                limpiarRegistroServicio();
            }
        }, 800);
    });
</script>
