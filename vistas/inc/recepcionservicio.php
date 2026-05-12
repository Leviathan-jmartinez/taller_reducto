<script>
    function buscarClienteAjax() {
        let txt = document.getElementById('buscar_cliente').value.trim();
        let datos = new FormData();
        datos.append("buscar_cliente", txt);

        fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('tabla_clientes').innerHTML = r;
            });
    }

    function seleccionarCliente(id, nombre, doc) {
        document.getElementById('id_cliente').value = id;
        document.getElementById('cliente_nombre').value = nombre + ' - ' + doc;

        // limpiar vehículo
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';

        //guardarEstadoRecepcion();

        $('#modalCliente').modal('hide');
    }

    function abrirNuevoClienteRecepcion() {
        $('#modalCliente').modal('hide');
        $('#modalNuevoClienteRecepcion').modal('show');
    }

    function abrirNuevoVehiculoRecepcion() {
        let idCliente = document.getElementById('id_cliente').value;
        let clienteNombre = document.getElementById('cliente_nombre').value;

        if (!idCliente) {
            Swal.fire({
                icon: 'warning',
                text: 'Debe seleccionar o cargar un cliente primero'
            });
            return;
        }

        document.getElementById('vehiculo_rapido_cliente').value = idCliente;
        document.getElementById('vehiculo_rapido_cliente_nombre').innerHTML =
            '<strong>Cliente:</strong> ' + clienteNombre;

        $('#modalVehiculo').modal('hide');
        $('#modalNuevoVehiculoRecepcion').modal('show');
    }

    function manejarRespuestaRapida(data) {
        Swal.fire({
            icon: data.Tipo || 'info',
            title: data.Titulo || '',
            text: data.Texto || ''
        });
    }

    function debounceRecepcion(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function ocultarAutocomplete(id) {
        const contenedor = document.getElementById(id);
        if (contenedor) {
            contenedor.innerHTML = '';
            contenedor.style.display = 'none';
        }
    }

    function textoSeguro(valor) {
        const div = document.createElement('div');
        div.textContent = valor || '';
        return div.innerHTML;
    }

    function valorJsSeguro(valor) {
        return String(valor || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }

    function buscarClienteAutocomplete() {
        const input = document.getElementById('cliente_nombre');
        const contenedor = document.getElementById('resultado_clientes_autocomplete');
        const termino = input.value.trim();

        document.getElementById('id_cliente').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';
        ocultarAutocomplete('resultado_vehiculos_autocomplete');

        if (termino.length < 4) {
            ocultarAutocomplete('resultado_clientes_autocomplete');
            return;
        }

        const datos = new FormData();
        datos.append('accion', 'buscar_cliente_autocomplete');
        datos.append('termino', termino);

        contenedor.innerHTML = '<div class="recepcion-autocomplete-empty">Buscando...</div>';
        contenedor.style.display = 'block';

        fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.json())
            .then(clientes => {
                if (!Array.isArray(clientes) || clientes.length === 0) {
                    contenedor.innerHTML = '<div class="recepcion-autocomplete-empty">Sin resultados</div>';
                    contenedor.style.display = 'block';
                    return;
                }

                contenedor.innerHTML = clientes.map(c => `
                    <button type="button" class="recepcion-autocomplete-item"
                        onclick="seleccionarCliente(${Number(c.id_cliente)}, '${valorJsSeguro(c.cliente)}', '${valorJsSeguro(c.doc_number)}')">
                        <span class="recepcion-autocomplete-main">
                            <span class="recepcion-autocomplete-title">${textoSeguro(c.cliente)}</span>
                            <span class="recepcion-autocomplete-meta">${textoSeguro(c.celular_cliente || 'Sin telefono')}</span>
                        </span>
                        <span class="recepcion-autocomplete-badge">${textoSeguro(c.doc_number)}</span>
                    </button>
                `).join('');
                contenedor.style.display = 'block';
            })
            .catch(() => ocultarAutocomplete('resultado_clientes_autocomplete'));
    }

    function buscarVehiculoAutocomplete() {
        const idCliente = document.getElementById('id_cliente').value;
        const input = document.getElementById('vehiculo_desc');
        const contenedor = document.getElementById('resultado_vehiculos_autocomplete');
        const termino = input.value.trim();

        document.getElementById('id_vehiculo').value = '';

        if (!idCliente || termino.length < 4) {
            ocultarAutocomplete('resultado_vehiculos_autocomplete');
            return;
        }

        const datos = new FormData();
        datos.append('accion', 'buscar_vehiculo_autocomplete');
        datos.append('termino', termino);
        datos.append('id_cliente', idCliente);

        contenedor.innerHTML = '<div class="recepcion-autocomplete-empty">Buscando...</div>';
        contenedor.style.display = 'block';

        fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.json())
            .then(vehiculos => {
                if (!Array.isArray(vehiculos) || vehiculos.length === 0) {
                    contenedor.innerHTML = '<div class="recepcion-autocomplete-empty">Sin resultados</div>';
                    contenedor.style.display = 'block';
                    return;
                }

                contenedor.innerHTML = vehiculos.map(v => `
                    <button type="button" class="recepcion-autocomplete-item"
                        onclick="seleccionarVehiculo(${Number(v.id_vehiculo)}, '${valorJsSeguro(v.descripcion)}')">
                        <span class="recepcion-autocomplete-main">
                            <span class="recepcion-autocomplete-title">${textoSeguro(v.marca)} ${textoSeguro(v.modelo)}</span>
                            <span class="recepcion-autocomplete-meta">${textoSeguro(v.color)} ${v.anho ? '- ' + textoSeguro(v.anho) : ''}</span>
                        </span>
                        <span class="recepcion-autocomplete-badge">${textoSeguro(v.placa)}</span>
                    </button>
                `).join('');
                contenedor.style.display = 'block';
            })
            .catch(() => ocultarAutocomplete('resultado_vehiculos_autocomplete'));
    }

    function validarClienteVehiculo() {
        let idCliente = document.getElementById('id_cliente').value;

        if (!idCliente) {
            Swal.fire({
                icon: 'warning',
                text: 'Debe seleccionar un cliente primero'
            });
            return;
        }

        $('#modalVehiculo').modal('show');
    }

    function buscarVehiculoAjax() {
        let txt = document.getElementById('buscar_vehiculo').value.trim();
        let idCliente = document.getElementById('id_cliente').value;

        if (!idCliente) {
            document.getElementById('tabla_vehiculos').innerHTML =
                '<div class="alert alert-warning text-center">Seleccione un cliente</div>';
            return;
        }

        let datos = new FormData();
        datos.append("buscar_vehiculo", txt);
        datos.append("id_cliente", idCliente);

        fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('tabla_vehiculos').innerHTML = r;
            })
            .catch(() => {
                document.getElementById('tabla_vehiculos').innerHTML =
                    '<div class="alert alert-danger text-center">Error al buscar vehículos</div>';
            });
    }

    function seleccionarVehiculo(id, desc) {
        document.getElementById('id_vehiculo').value = id;
        document.getElementById('vehiculo_desc').value = desc;
        ocultarAutocomplete('resultado_vehiculos_autocomplete');

        //guardarEstadoRecepcion();

        $('#modalVehiculo').modal('hide');
    }

    function cargarRecepcionDesdeReclamo(data) {

        // marcar como reclamo
        document.getElementById('origen').value = 'RECLAMO';
        document.getElementById('idreclamo_servicio').value = data.idreclamo_servicio;

        // cliente
        document.getElementById('id_cliente').value = data.id_cliente;
        document.getElementById('cliente_nombre').value = data.cliente;

        // vehículo
        document.getElementById('id_vehiculo').value = data.id_vehiculo;
        document.getElementById('vehiculo_desc').value = data.vehiculo;

        // BLOQUEAR EDICIÓN
        if (document.getElementById('btnBuscarCliente')) {
            document.getElementById('btnBuscarCliente').disabled = true;
        }
        if (document.getElementById('btnBuscarVehiculo')) {
            document.getElementById('btnBuscarVehiculo').disabled = true;
        }
        if (document.getElementById('btnNuevoCliente')) {
            document.getElementById('btnNuevoCliente').disabled = true;
        }
        if (document.getElementById('btnNuevoVehiculo')) {
            document.getElementById('btnNuevoVehiculo').disabled = true;
        }

        // mostrar alerta
        document.getElementById('alerta_reclamo').style.display = 'block';
    }

    function buscarReclamoAjax(txt) {

        let datos = new FormData();
        datos.append("accion", "buscar_reclamo_recepcion");
        datos.append("buscar", txt);

        fetch("<?php echo SERVERURL ?>ajax/reclamoServicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('tabla_reclamos').innerHTML = r;
            });
    }

    function seleccionarReclamo(id) {

        let datos = new FormData();
        datos.append("accion", "obtener_reclamo");
        datos.append("id", id);

        fetch("<?php echo SERVERURL ?>ajax/reclamoServicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.json())
            .then(data => {

                cargarRecepcionDesdeReclamo(data);

                $('#modalReclamo').modal('hide');
            });
    }

    function activarServicioDesdeReclamo() {
        const bloque = document.getElementById('bloque_reclamo_recepcion');
        const input = document.getElementById('buscar_reclamo_recepcion');

        document.getElementById('origen').value = 'RECLAMO';
        bloque.style.display = 'block';
        document.getElementById('alerta_reclamo').style.display = 'block';

        if (!document.getElementById('idreclamo_servicio').value) {
            document.getElementById('id_cliente').value = '';
            document.getElementById('cliente_nombre').value = '';
            document.getElementById('id_vehiculo').value = '';
            document.getElementById('vehiculo_desc').value = '';
            ocultarAutocomplete('resultado_clientes_autocomplete');
            ocultarAutocomplete('resultado_vehiculos_autocomplete');
        }

        bloquearOrigenReclamo(true);

        if (input) {
            input.focus();
        }
    }

    function bloquearOrigenReclamo(bloquear) {
        ['btnNuevoCliente', 'btnNuevoVehiculo'].forEach(id => {
            const boton = document.getElementById(id);
            if (boton) boton.disabled = bloquear;
        });

        document.getElementById('cliente_nombre').disabled = bloquear;
        document.getElementById('vehiculo_desc').disabled = bloquear;
    }

    function pintarDetalleReclamo(data) {
        const prioridad = String(data.prioridad || '-');

        document.getElementById('rec_id').innerText = '#' + (data.idreclamo_servicio || '-');
        document.getElementById('rec_fecha').innerText = data.fecha_reclamo || '-';
        document.getElementById('rec_tipo').innerText = data.tipo_reclamo || '-';
        document.getElementById('rec_garantia').innerText = Number(data.requiere_garantia) === 1 ? 'Si' : 'No';
        document.getElementById('rec_cliente').innerText = data.cliente || '-';
        document.getElementById('rec_vehiculo').innerText = data.vehiculo || '-';
        document.getElementById('rec_descripcion').innerText = data.descripcion || '-';

        const badge = document.getElementById('rec_badge_prioridad');
        badge.innerText = prioridad;
        badge.className = prioridad.toLowerCase() === 'urgente' ? 'badge badge-danger' : 'badge badge-secondary';

        document.getElementById('detalle_reclamo_recepcion').style.display = 'block';
    }

    function cargarRecepcionDesdeReclamo(data) {
        if (!data || !data.idreclamo_servicio) {
            Swal.fire({
                icon: 'warning',
                text: 'No se pudo obtener el reclamo seleccionado'
            });
            return;
        }

        document.getElementById('origen').value = 'RECLAMO';
        document.getElementById('idreclamo_servicio').value = data.idreclamo_servicio;
        document.getElementById('id_cliente').value = data.id_cliente;
        document.getElementById('cliente_nombre').value = data.cliente;
        document.getElementById('id_vehiculo').value = data.id_vehiculo;
        document.getElementById('vehiculo_desc').value = data.vehiculo;

        bloquearOrigenReclamo(true);
        pintarDetalleReclamo(data);
        document.getElementById('alerta_reclamo').style.display = 'block';
    }

    function buscarReclamoAjax(txt) {
        const tabla = document.getElementById('tabla_reclamos');
        if (!tabla) return;

        txt = String(txt || '').trim();

        if (txt.length === 0) {
            tabla.innerHTML = '';
            return;
        }

        tabla.innerHTML = '<div class="alert alert-info mb-0">Buscando reclamos...</div>';

        let datos = new FormData();
        datos.append("accion", "buscar_reclamo_recepcion");
        datos.append("buscar", txt);

        fetch("<?php echo SERVERURL ?>ajax/reclamoServicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {
                tabla.innerHTML = r;
            })
            .catch(() => {
                tabla.innerHTML = '<div class="alert alert-danger mb-0">Error al buscar reclamos</div>';
            });
    }

    function seleccionarReclamo(id) {
        let datos = new FormData();
        datos.append("accion", "obtener_reclamo");
        datos.append("id", id);

        fetch("<?php echo SERVERURL ?>ajax/reclamoServicioAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.json())
            .then(data => {
                cargarRecepcionDesdeReclamo(data);
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    text: 'No se pudo cargar el detalle del reclamo'
                });
            });
    }

    function limpiarReclamoRecepcion() {
        document.getElementById('origen').value = 'NORMAL';
        document.getElementById('idreclamo_servicio').value = '';
        document.getElementById('bloque_reclamo_recepcion').style.display = 'none';
        document.getElementById('detalle_reclamo_recepcion').style.display = 'none';
        document.getElementById('alerta_reclamo').style.display = 'none';

        const input = document.getElementById('buscar_reclamo_recepcion');
        if (input) input.value = '';

        const tabla = document.getElementById('tabla_reclamos');
        if (tabla) tabla.innerHTML = '';

        document.getElementById('id_cliente').value = '';
        document.getElementById('cliente_nombre').value = '';
        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';
        bloquearOrigenReclamo(false);
    }

    function limpiarFormularioRecepcion() {

        const form = document.querySelector('.FormularioAjax');
        if (form) {
            form.reset();
        }

        document.getElementById('id_cliente').value = '';
        document.getElementById('cliente_nombre').value = '';

        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';
        ocultarAutocomplete('resultado_clientes_autocomplete');
        ocultarAutocomplete('resultado_vehiculos_autocomplete');
        document.getElementById('origen').value = 'NORMAL';
        document.getElementById('idreclamo_servicio').value = '';
        if (document.getElementById('btnBuscarCliente')) {
            document.getElementById('btnBuscarCliente').disabled = false;
        }
        if (document.getElementById('btnBuscarVehiculo')) {
            document.getElementById('btnBuscarVehiculo').disabled = false;
        }
        if (document.getElementById('btnNuevoCliente')) {
            document.getElementById('btnNuevoCliente').disabled = false;
        }
        if (document.getElementById('btnNuevoVehiculo')) {
            document.getElementById('btnNuevoVehiculo').disabled = false;
        }
        document.getElementById('alerta_reclamo').style.display = 'none';
        document.getElementById('bloque_reclamo_recepcion').style.display = 'none';
        document.getElementById('detalle_reclamo_recepcion').style.display = 'none';
        bloquearOrigenReclamo(false);

        const tablaClientes = document.getElementById('tabla_clientes');
        if (tablaClientes) tablaClientes.innerHTML = '';

        const tablaVehiculos = document.getElementById('tabla_vehiculos');
        if (tablaVehiculos) tablaVehiculos.innerHTML = '';

        const tablaReclamos = document.getElementById('tabla_reclamos');
        if (tablaReclamos) tablaReclamos.innerHTML = '';

        const inputReclamo = document.getElementById('buscar_reclamo_recepcion');
        if (inputReclamo) inputReclamo.value = '';

        ocultarAutocomplete('resultado_clientes_autocomplete');
        ocultarAutocomplete('resultado_vehiculos_autocomplete');

    }

    document.addEventListener('DOMContentLoaded', function() {
        const formClienteRapido = document.getElementById('formNuevoClienteRecepcion');
        const formVehiculoRapido = document.getElementById('formNuevoVehiculoRecepcion');
        const inputCliente = document.getElementById('cliente_nombre');
        const inputVehiculo = document.getElementById('vehiculo_desc');
        const inputReclamo = document.getElementById('buscar_reclamo_recepcion');

        if (inputCliente) {
            inputCliente.addEventListener('input', debounceRecepcion(buscarClienteAutocomplete, 350));
        }

        if (inputVehiculo) {
            inputVehiculo.addEventListener('input', debounceRecepcion(buscarVehiculoAutocomplete, 350));
            inputVehiculo.addEventListener('focus', function() {
                if (!document.getElementById('id_cliente').value) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Debe seleccionar un cliente primero'
                    });
                    inputCliente.focus();
                }
            });
        }

        if (inputReclamo) {
            inputReclamo.addEventListener('input', debounceRecepcion(function() {
                buscarReclamoAjax(inputReclamo.value.trim());
            }, 350));
        }

        document.addEventListener('ajax:limpiar', function() {
            if (document.getElementById('bloque_reclamo_recepcion')) {
                limpiarFormularioRecepcion();
            }
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#cliente_nombre') && !e.target.closest('#resultado_clientes_autocomplete')) {
                ocultarAutocomplete('resultado_clientes_autocomplete');
            }
            if (!e.target.closest('#vehiculo_desc') && !e.target.closest('#resultado_vehiculos_autocomplete')) {
                ocultarAutocomplete('resultado_vehiculos_autocomplete');
            }
        });

        if (formClienteRapido) {
            formClienteRapido.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                        method: "POST",
                        body: new FormData(formClienteRapido)
                    })
                    .then(r => r.json())
                    .then(data => {
                        manejarRespuestaRapida(data);

                        if (data.Alerta === 'seleccionar_cliente' && data.cliente) {
                            seleccionarCliente(
                                data.cliente.id_cliente,
                                data.cliente.nombre,
                                data.cliente.doc
                            );
                            formClienteRapido.reset();
                            $('#modalNuevoClienteRecepcion').modal('hide');
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            text: 'No se pudo registrar el cliente'
                        });
                    });
            });
        }

        if (formVehiculoRapido) {
            formVehiculoRapido.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch("<?php echo SERVERURL ?>ajax/recepcionservicioAjax.php", {
                        method: "POST",
                        body: new FormData(formVehiculoRapido)
                    })
                    .then(r => r.json())
                    .then(data => {
                        manejarRespuestaRapida(data);

                        if (data.Alerta === 'seleccionar_vehiculo' && data.vehiculo) {
                            seleccionarVehiculo(
                                data.vehiculo.id_vehiculo,
                                data.vehiculo.descripcion
                            );
                            formVehiculoRapido.reset();
                            $('#modalNuevoVehiculoRecepcion').modal('hide');
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            text: 'No se pudo registrar el vehiculo'
                        });
                    });
            });
        }
    });
</script>
