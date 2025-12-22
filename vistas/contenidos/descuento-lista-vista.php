<?php
require_once "./controladores/descuentoControlador.php";
$insDescuento = new descuentoControlador();
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-percent"></i> &nbsp; LISTADO DE DESCUENTOS
    </h3>

    <div class="mb-3">
        <a href="<?= SERVERURL; ?>descuento-nuevo/" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo descuento
        </a>
    </div>

    <?php
    echo $insDescuento->listar_descuentos_controlador();
    ?>

</div>