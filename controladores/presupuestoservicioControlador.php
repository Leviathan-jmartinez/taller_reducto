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

    public function buscar_servicios_controlador()
    {
        $txt = trim($_POST['buscar_servicio'] ?? '');

        if ($txt === '') {
            return '';
        }

        return presupuestoServicioModelo::buscar_servicios_modelo($txt);
    }

    public function promo_articulo_controlador()
    {
        $id = intval($_POST['promo_articulo']);

        return presupuestoServicioModelo::promo_articulo_modelo($id);
    }

    public function descuentos_cliente_controlador()
    {
        $idCliente = intval($_POST['descuentos_cliente']);

        return presupuestoServicioModelo::descuentos_cliente_modelo($idCliente);
    }

    public function guardar_presupuesto_controlador()
    {
        session_start(['name' => 'STR']);

        $datos = [
            'usuario'         => $_SESSION['id_str'],
            'idrecepcion'     => $_POST['idrecepcion'],
            'fecha_venc'      => $_POST['fecha_venc'],

            // 👇 estos vienen de los inputs hidden correctos
            'subtotal'        => $_POST['subtotal_servicios'],
            'total_descuento' => $_POST['total_descuento'],
            'total_final'     => $_POST['total_final'],

            'detalle'         => json_decode($_POST['detalle_json'], true),
            'descuentos'      => json_decode($_POST['descuentos_json'], true)
        ];

        $res = presupuestoServicioModelo::guardar_presupuesto_modelo($datos);
        
        if ($res === true) {
            return json_encode([
                'Alerta' => 'limpiar',
                'Titulo' => 'Presupuesto registrado',
                'Texto'  => 'El presupuesto se guardó correctamente',
                'Tipo'   => 'success'
            ]);
        }

        return json_encode([
            'Alerta' => 'simple',
            'Titulo' => 'Error',
            'Texto'  => $res['msg'] ?? 'No se pudo guardar',
            'Tipo'   => 'error'
        ]);
    }
}
