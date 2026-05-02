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

        //guardarEstadoRecepcion();

        $('#modalVehiculo').modal('hide');
    }

    function cargarRecepcionDesdeReclamo(data) {

        document.getElementById('origen').value = 'RECLAMO';
        document.getElementById('idreclamo_servicio').value = data.idreclamo;

        // autocompletar
        document.getElementById('id_cliente').value = data.id_cliente;
        document.getElementById('cliente_nombre').value = data.cliente;

        document.getElementById('id_vehiculo').value = data.id_vehiculo;
        document.getElementById('vehiculo_desc').value = data.vehiculo;

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
        document.getElementById('btnBuscarCliente').disabled = true;
        document.getElementById('btnBuscarVehiculo').disabled = true;

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

    function limpiarFormularioRecepcion() {

        const form = document.querySelector('.FormularioAjax');
        if (form) {
            form.reset();
        }

        document.getElementById('id_cliente').value = '';
        document.getElementById('cliente_nombre').value = '';

        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';
        document.getElementById('origen').value = 'NORMAL';
        document.getElementById('idreclamo_servicio').value = '';
        document.getElementById('btnBuscarCliente').disabled = false;
        document.getElementById('btnBuscarVehiculo').disabled = false;
        document.getElementById('alerta_reclamo').style.display = 'none';

        const tablaClientes = document.getElementById('tabla_clientes');
        if (tablaClientes) tablaClientes.innerHTML = '';

        const tablaVehiculos = document.getElementById('tabla_vehiculos');
        if (tablaVehiculos) tablaVehiculos.innerHTML = '';

        const tablaReclamos = document.getElementById('tabla_reclamos');
        if (tablaReclamos) tablaReclamos.innerHTML = '';

    }
</script>
