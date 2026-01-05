<?php
require_once "./controladores/descuentoControlador.php";
$insDescuento = new descuentoControlador();
?>

<div class="container-fluid">

    <h3 class="text-left">
        <i class="fas fa-percent"></i> &nbsp; LISTADO DE DESCUENTOS
    </h3>
    <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
            <li>
                <a href="<?php echo SERVERURL; ?>descuento-nuevo/">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO DESCUENTO
                </a>
            </li>
            <li>
                <a class="active" href="<?php echo SERVERURL; ?>descuento-lista/">
                    <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE DESCUENTOS
                </a>
            </li>
        </ul>
    </div>
    <?php
    echo $insDescuento->listar_descuentos_controlador();
    ?>

</div>