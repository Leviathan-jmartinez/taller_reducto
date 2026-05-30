<?php
if (!mainModel::tienePermiso('servicio.insumo.crear')) {
    echo '<div class="alert alert-danger">Acceso no autorizado</div>';
    return;
}
?>

<style>
    .salida-consumible-bg {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
    }

    .salida-consumible-bg fieldset {
        background-color: #ffffff;
        border-radius: 4px;
    }

    .salida-consumible-bg .table,
    .salida-consumible-bg .table-responsive {
        background-color: #ffffff;
    }

    .salida-consumible-bg h3 {
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid form-neon salida-consumible-bg">

    <h3>
        <i class="fas fa-box-open"></i>
        &nbsp; SALIDA DE CONSUMIBLES OPERATIVOS
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>registro-insumos/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>registro-insumos-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>

    <form class="FormularioAjax"
        action="<?= SERVERURL ?>ajax/salidaInsumoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="registrar_salida_consumible">
        <input type="hidden" name="consumibles_json" id="consumibles_json">

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Datos de la salida</legend>

            <div class="row">
                <div class="col-md-4">
                    <label>Empleado responsable</label>

                    <input type="hidden"
                        name="idempleado"
                        id="idempleado">

                    <input type="text"
                        id="buscar_empleado"
                        class="form-control"
                        placeholder="Buscar empleado..."
                        onkeyup="buscarEmpleado(this.value)">

                    <div id="resultado_empleado" class="mt-2"></div>
                </div>

                <div class="col-md-5">
                    <label>Observación</label>
                    <input type="text"
                        name="observacion"
                        class="form-control"
                        placeholder="Ej: Entrega para uso operativo del taller">
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Buscar insumo</legend>

            <div class="row mb-2">
                <div class="col-md-6">
                    <input type="text"
                        id="buscar_consumible"
                        class="form-control"
                        placeholder="Buscar insumo: WD-40, trapo, limpiador..."
                        onkeyup="buscarConsumible()">
                </div>
            </div>

            <div id="resultado_consumibles"></div>
        </fieldset>

        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Detalle de consumibles</legend>

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Consumible</th>
                            <th>Stock disponible</th>
                            <th>Cantidad salida</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="detalle_consumibles">
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No hay consumibles agregados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Al registrar la salida, se descontará stock de los insumos seleccionados.
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-info btn-raised">
                <i class="fas fa-save"></i> &nbsp; Registrar salida
            </button>

            <button type="button"
                class="btn btn-secondary btn-raised"
                onclick="limpiarSalidaConsumible()">
                <i class="fas fa-times"></i> &nbsp; Cancelar
            </button>
        </div>

    </form>
</div>

<script>
    let timerBuscarEmpleado = null;
    const SERVERURL = "<?php echo SERVERURL; ?>";

    let detalleConsumibles = [];
    let timerBuscarConsumible = null;

    function buscarEmpleado(texto) {

        const resultado = document.getElementById('resultado_empleado');

        if (!resultado) return;

        texto = (texto || '').trim();

        clearTimeout(timerBuscarEmpleado);

        if (texto.length < 2) {
            resultado.innerHTML = '';
            return;
        }

        timerBuscarEmpleado = setTimeout(() => {

            fetch(SERVERURL + 'ajax/salidaInsumoAjax.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        accion: 'buscar_empleado',
                        texto: texto
                    })
                })
                .then(r => r.text())
                .then(html => {
                    resultado.innerHTML = html;
                });

        }, 300);
    }

    function seleccionarEmpleado(id, nombre) {

        document.getElementById('idempleado').value = id;

        document.getElementById('buscar_empleado').value = nombre;

        document.getElementById('resultado_empleado').innerHTML = '';
    }

    function buscarConsumible() {
        const input = document.getElementById('buscar_consumible');
        const resultado = document.getElementById('resultado_consumibles');

        if (!input || !resultado) return;

        let texto = input.value.trim();
        clearTimeout(timerBuscarConsumible);

        if (texto.length < 2) {
            resultado.innerHTML = '';
            return;
        }

        timerBuscarConsumible = setTimeout(() => {
            fetch(SERVERURL + 'ajax/salidaInsumoAjax.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        accion: 'buscar_consumible',
                        texto: texto
                    })
                })
                .then(r => r.text())
                .then(html => {
                    resultado.innerHTML = html;
                });
        }, 300);
    }

    function agregarConsumible(id, descripcion, stock) {
        let existe = detalleConsumibles.find(i => i.id_articulo == id);

        if (existe) {
            alert('El consumible ya fue agregado');
            return;
        }

        stock = parseFloat(stock);

        if (stock <= 0) {
            alert('Sin stock disponible');
            return;
        }

        detalleConsumibles.push({
            id_articulo: id,
            descripcion: descripcion,
            stock: stock,
            cantidad: 1
        });

        renderConsumibles();
    }

    function renderConsumibles() {
        const tbody = document.getElementById('detalle_consumibles');

        if (!tbody) return;

        tbody.innerHTML = '';

        if (detalleConsumibles.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No hay consumibles agregados
                    </td>
                </tr>`;
            return;
        }

        detalleConsumibles.forEach((item, index) => {
            let tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${item.descripcion}</td>

                <td class="text-center">${item.stock}</td>

                <td>
                    <input type="number"
                           min="1"
                           step="1"
                           class="form-control form-control-sm text-center"
                           value="${item.cantidad}"
                           oninput="cambiarCantidadConsumible(this, ${index})">
                </td>

                <td class="text-center">
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            onclick="quitarConsumible(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    function cambiarCantidadConsumible(input, index) {
        let item = detalleConsumibles[index];
        let cantidad = parseFloat(input.value);

        if (isNaN(cantidad) || cantidad <= 0) {
            cantidad = 1;
        }

        if (cantidad > item.stock) {
            alert('Stock insuficiente');
            cantidad = item.stock;
            input.value = cantidad;
        }

        item.cantidad = cantidad;
    }

    function quitarConsumible(index) {
        detalleConsumibles.splice(index, 1);
        renderConsumibles();
    }

    function limpiarSalidaConsumible() {
        detalleConsumibles = [];

        const form = document.querySelector('.FormularioAjax');
        if (form) form.reset();

        const consumiblesJson = document.getElementById('consumibles_json');
        const buscarInput = document.getElementById('buscar_consumible');
        const resultado = document.getElementById('resultado_consumibles');

        const idEmpleado = document.getElementById('idempleado');
        const buscarEmpleadoInput = document.getElementById('buscar_empleado');
        const resultadoEmpleado = document.getElementById('resultado_empleado');

        if (consumiblesJson) consumiblesJson.value = '';
        if (buscarInput) buscarInput.value = '';
        if (resultado) resultado.innerHTML = '';

        if (idEmpleado) idEmpleado.value = '';
        if (buscarEmpleadoInput) buscarEmpleadoInput.value = '';
        if (resultadoEmpleado) resultadoEmpleado.innerHTML = '';

        const fecha = document.querySelector('[name="fecha"]');
        if (fecha) fecha.value = '<?= date('Y-m-d') ?>';

        renderConsumibles();
    }

    const formSalidaConsumible = document.querySelector('.FormularioAjax');

    if (formSalidaConsumible) {
        formSalidaConsumible.addEventListener('submit', function(e) {
            const consumiblesJson = document.getElementById('consumibles_json');

            if (detalleConsumibles.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un consumible');
                return;
            }

            if (consumiblesJson) {
                consumiblesJson.value = JSON.stringify(detalleConsumibles);
            }
        }, true);
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            const btnConfirm = e.target.closest('.swal2-confirm');

            if (!btnConfirm) return;

            setTimeout(function() {
                const titulo = document.querySelector('.swal2-title');

                if (titulo && titulo.textContent.includes('Salida registrada')) {
                    limpiarSalidaConsumible();
                }
            }, 300);
        });
    });
</script>