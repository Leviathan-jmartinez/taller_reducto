<script>
    /* ================== GUARDAR ESTADO ================== */
    function guardarEstadoRecepcion() {
        localStorage.setItem('rec_id_cliente', document.getElementById('id_cliente').value);
        localStorage.setItem('rec_cliente_nombre', document.getElementById('cliente_nombre').value);
        localStorage.setItem('rec_id_vehiculo', document.getElementById('id_vehiculo').value);
        localStorage.setItem('rec_vehiculo_desc', document.getElementById('vehiculo_desc').value);
    }

    /* ================== RESTAURAR ESTADO ================== */
    function restaurarEstadoRecepcion() {
        if (localStorage.getItem('rec_id_cliente')) {
            document.getElementById('id_cliente').value =
                localStorage.getItem('rec_id_cliente');

            document.getElementById('cliente_nombre').value =
                localStorage.getItem('rec_cliente_nombre') || '';
        }

        if (localStorage.getItem('rec_id_vehiculo')) {
            document.getElementById('id_vehiculo').value =
                localStorage.getItem('rec_id_vehiculo');

            document.getElementById('vehiculo_desc').value =
                localStorage.getItem('rec_vehiculo_desc') || '';
        }
    }

    /* ================== LIMPIAR ESTADO ================== */
    function limpiarEstadoRecepcion() {
        localStorage.removeItem('rec_id_cliente');
        localStorage.removeItem('rec_cliente_nombre');
        localStorage.removeItem('rec_id_vehiculo');
        localStorage.removeItem('rec_vehiculo_desc');
    }

    /* Restaurar automáticamente al cargar */
    window.addEventListener('load', restaurarEstadoRecepcion);


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

        guardarEstadoRecepcion();

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

        guardarEstadoRecepcion();

        $('#modalVehiculo').modal('hide');
    }


    function limpiarFormularioRecepcion() {

        /* ================= FORM ================= */
        const form = document.querySelector('.FormularioAjax');
        if (form) {
            form.reset();
        }

        /* ================= CAMPOS MANUALES ================= */
        document.getElementById('id_cliente').value = '';
        document.getElementById('cliente_nombre').value = '';

        document.getElementById('id_vehiculo').value = '';
        document.getElementById('vehiculo_desc').value = '';

        /* ================= TABLAS DE BÚSQUEDA ================= */
        const tablaClientes = document.getElementById('tabla_clientes');
        if (tablaClientes) tablaClientes.innerHTML = '';

        const tablaVehiculos = document.getElementById('tabla_vehiculos');
        if (tablaVehiculos) tablaVehiculos.innerHTML = '';

        /* ================= LOCAL STORAGE ================= */
        limpiarEstadoRecepcion();

        /* ================= FECHA POR DEFECTO ================= */
        const fecha = document.querySelector('[name="fecha_ingreso"]');
        if (fecha) {
            fecha.value = new Date().toISOString().slice(0, 16);
        }
    }
</script>