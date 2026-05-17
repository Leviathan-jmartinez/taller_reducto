<?php

if (!mainModel::tienePermiso('equipo.editar')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-users-cog fa-fw"></i> &nbsp; EQUIPOS DE TRABAJO
    </h3>
</div>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-equipo/">
                <i class="fas fa-users-cog fa-fw"></i> &nbsp; EQUIPOS
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>empleado-equipo-asignar/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; ASIGNAR EMPLEADOS
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <div class="form-neon">
        <?php
        require_once "./controladores/equipoControlador.php";
        $eq = new equipoControlador();

        $equipos = $eq->listar_equipos_controlador();
        ?>

        <form class="FormularioAjax"
            id="formAsignarEquipo"
            action="<?php echo SERVERURL; ?>ajax/equipoAjax.php"
            method="POST"
            data-form="save">

            <div class="form-group">
                <label>Equipo</label>
                <select name="id_equipo" id="idEquipoAsignar" class="form-control" required>
                    <option value="">Seleccione equipo</option>
                <?php foreach ($equipos as $e): ?>
                    <option value="<?= $e['id_equipo']; ?>">
                        <?= $e['nombre'] . ' - ' . $e['descripcion']; ?> (<?= $e['suc_descri']; ?>)
                    </option>
                <?php endforeach; ?>
                </select>
            </div>

            <hr>

            <div class="form-group">
                <label>Buscar empleado</label>
                <input type="text" id="buscarEmpleadoEquipo" class="form-control" placeholder="Nombre, apellido o cedula" disabled>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <h5>Disponibles <span class="badge badge-secondary" id="contadorDisponibles">0</span></h5>
                    <div id="empleadosDisponibles" class="border rounded p-3" style="min-height: 180px;">
                        <p class="text-muted mb-0">Seleccione un equipo para cargar empleados</p>
                    </div>
                </div>

                <div class="col-md-5">
                    <h5>Miembros actuales <span class="badge badge-info" id="contadorMiembros">0</span></h5>
                    <div id="miembrosActuales" class="border rounded p-3" style="min-height: 180px;">
                        <p class="text-muted mb-0">Sin equipo seleccionado</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted" id="contadorSeleccionados">0 empleados seleccionados</span>
                <button class="btn btn-info" id="btnAsignarEquipo" disabled>ASIGNAR SELECCIONADOS</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formAsignarEquipo');
    const equipoSelect = document.getElementById('idEquipoAsignar');
    const buscador = document.getElementById('buscarEmpleadoEquipo');
    const disponibles = document.getElementById('empleadosDisponibles');
    const miembros = document.getElementById('miembrosActuales');
    const contadorDisponibles = document.getElementById('contadorDisponibles');
    const contadorMiembros = document.getElementById('contadorMiembros');
    const contadorSeleccionados = document.getElementById('contadorSeleccionados');
    const btnAsignar = document.getElementById('btnAsignarEquipo');
    let empleados = [];

    function texto(valor) {
        return valor == null ? '' : String(valor);
    }

    function crearBadge(clase, contenido) {
        const badge = document.createElement('span');
        badge.className = 'badge ' + clase + ' ml-2';
        badge.textContent = contenido;
        return badge;
    }

    function crearEmpleadoDisponible(emp) {
        const item = document.createElement('div');
        item.className = 'form-check mb-2 empleado-item';
        item.dataset.search = [
            emp.nombre,
            emp.apellido,
            emp.nro_cedula,
            emp.equipos
        ].map(texto).join(' ').toLowerCase();

        const input = document.createElement('input');
        input.className = 'form-check-input empleado-check';
        input.type = 'checkbox';
        input.name = 'empleados[]';
        input.value = emp.idempleados;
        input.id = 'empleado_equipo_' + emp.idempleados;

        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.setAttribute('for', input.id);
        label.textContent = texto(emp.apellido) + ' ' + texto(emp.nombre);

        if (emp.equipos) {
            label.appendChild(crearBadge('badge-info', emp.equipos));
        } else {
            label.appendChild(crearBadge('badge-secondary', 'Sin equipo'));
        }

        item.appendChild(input);
        item.appendChild(label);
        return item;
    }

    function crearMiembroActual(emp) {
        const item = document.createElement('div');
        item.className = 'mb-2 empleado-item';
        item.dataset.search = [
            emp.nombre,
            emp.apellido,
            emp.nro_cedula,
            emp.equipos
        ].map(texto).join(' ').toLowerCase();

        const nombre = document.createElement('span');
        nombre.textContent = texto(emp.apellido) + ' ' + texto(emp.nombre);
        item.appendChild(nombre);
        item.appendChild(crearBadge('badge-success', 'Asignado'));
        return item;
    }

    function actualizarSeleccionados() {
        const total = form.querySelectorAll('.empleado-check:checked').length;
        contadorSeleccionados.textContent = total + (total === 1 ? ' empleado seleccionado' : ' empleados seleccionados');
        btnAsignar.disabled = total === 0 || equipoSelect.value === '';
    }

    function filtrarEmpleados() {
        const filtro = buscador.value.trim().toLowerCase();
        document.querySelectorAll('.empleado-item').forEach(function (item) {
            item.style.display = item.dataset.search.indexOf(filtro) !== -1 ? '' : 'none';
        });
    }

    function renderizar() {
        disponibles.innerHTML = '';
        miembros.innerHTML = '';

        const empleadosDisponibles = empleados.filter(function (emp) {
            return Number(emp.es_miembro) !== 1;
        });
        const miembrosActuales = empleados.filter(function (emp) {
            return Number(emp.es_miembro) === 1;
        });

        contadorDisponibles.textContent = empleadosDisponibles.length;
        contadorMiembros.textContent = miembrosActuales.length;

        if (empleadosDisponibles.length === 0) {
            disponibles.innerHTML = '<p class="text-muted mb-0">No hay empleados disponibles</p>';
        } else {
            empleadosDisponibles.forEach(function (emp) {
                disponibles.appendChild(crearEmpleadoDisponible(emp));
            });
        }

        if (miembrosActuales.length === 0) {
            miembros.innerHTML = '<p class="text-muted mb-0">Sin miembros asignados</p>';
        } else {
            miembrosActuales.forEach(function (emp) {
                miembros.appendChild(crearMiembroActual(emp));
            });
        }

        buscador.disabled = false;
        filtrarEmpleados();
        actualizarSeleccionados();
    }

    function limpiar(mensaje) {
        empleados = [];
        disponibles.innerHTML = '<p class="text-muted mb-0">' + mensaje + '</p>';
        miembros.innerHTML = '<p class="text-muted mb-0">Sin equipo seleccionado</p>';
        contadorDisponibles.textContent = '0';
        contadorMiembros.textContent = '0';
        contadorSeleccionados.textContent = '0 empleados seleccionados';
        buscador.value = '';
        buscador.disabled = true;
        btnAsignar.disabled = true;
    }

    function cargarEmpleados() {
        if (equipoSelect.value === '') {
            limpiar('Seleccione un equipo para cargar empleados');
            return;
        }

        disponibles.innerHTML = '<p class="text-muted mb-0">Cargando empleados...</p>';
        miembros.innerHTML = '<p class="text-muted mb-0">Cargando miembros...</p>';
        btnAsignar.disabled = true;

        const datos = new FormData();
        datos.append('accion', 'empleados_asignacion');
        datos.append('id_equipo', equipoSelect.value);

        fetch(form.action, {
            method: 'POST',
            body: datos
        })
            .then(function (respuesta) {
                return respuesta.json();
            })
            .then(function (respuesta) {
                if (!respuesta.ok) {
                    limpiar(respuesta.mensaje || 'No se pudieron cargar empleados');
                    return;
                }

                empleados = respuesta.empleados || [];
                renderizar();
            })
            .catch(function () {
                limpiar('No se pudieron cargar empleados');
            });
    }

    equipoSelect.addEventListener('change', cargarEmpleados);
    buscador.addEventListener('input', filtrarEmpleados);
    disponibles.addEventListener('change', actualizarSeleccionados);
});
</script>
