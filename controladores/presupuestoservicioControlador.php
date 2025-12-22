<?php
if ($peticionAjax) {
    require_once "../modelos/presupuestoservicioModelo.php";
} else {
    require_once "./modelos/presupuestoservicioModelo.php";
}

class presupuestoservicioControlador extends presupuestoservicioModelo
{
    public function datos_recepcion_controlador($id_encriptado)
    {
        $id = mainModel::decryption($id_encriptado);

        if ($id <= 0) {
            return false;
        }

        return presupuestoservicioModelo::datos_recepcion_modelo($id);
    }

    public function buscar_recepciones_controlador()
    {
        $txt = trim($_POST['buscar_recepcion']);
        return presupuestoservicioModelo::buscar_recepciones_modelo($txt);
    }
}
