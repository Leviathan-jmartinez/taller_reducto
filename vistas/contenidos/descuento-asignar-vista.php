<?php
require_once __DIR__ . "/../../controladores/descuentoControlador.php";
$insDescuento = new descuentoControlador();
$descuento = $insDescuento->datos_descuento_controlador($pagina[1]);

if (!$descuento) {
    echo '<div class="alert alert-danger">Descuento no encontrado</div>';
    return;
}
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-user-tag"></i>
        &nbsp; Asignar descuento a clientes
    </h3>
    <div class="mb-3">
        <a href="<?= SERVERURL; ?>descuento-lista/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Descuento:</strong> <?= $descuento['nombre']; ?>
        (<?= $descuento['tipo']; ?> <?= $descuento['valor']; ?>)
    </div>

    <form class="form-neon FormularioAjax"
        action="<?= SERVERURL; ?>ajax/descuentoAjax.php"
        method="POST"
        data-form="save"
        autocomplete="off">

        <input type="hidden" name="accion" value="asignar_descuento_cliente">
        <input type="hidden" name="id_descuento"
            value="<?= $insDescuento->encryption($descuento['id_descuento']); ?>">

        <!-- BUSCAR CLIENTE -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto px-2">Buscar cliente</legend>

            <input type="text"
                id="buscar_cliente"
                class="form-control"
                placeholder="Buscar por CI o nombre"
                onkeyup="buscarClienteDescuento()">
        </fieldset>

        <div class="row">
            <div class="col-md-6">
                <h6>Resultados</h6>
                <div id="resultado_clientes"></div>
            </div>

            <div class="col-md-6">
                <h6>Clientes con descuento</h6>
                <ul class="list-group" id="clientes_asignados"></ul>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar asociaciones
            </button>
        </div>

    </form>
</div>
<?php include_once "./vistas/inc/descuentos.php"; ?>