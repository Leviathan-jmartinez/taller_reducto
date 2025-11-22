<?php
if ($peticionAjax) {
    require_once "../modelos/presupuestoModelo.php";
} else {
    require_once "./modelos/presupuestoModelo.php";
}

class presupuestoControlador extends presupuestoModelo {}
