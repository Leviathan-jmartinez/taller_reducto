<?php
if ($peticionAjax) {
    require_once "../modelos/ordencompraModelo";
} else {
    require_once "./modelos/ordencompraModelo.php";
}

class pedidoControlador extends pedidoModelo
{
    /**controlador buscar pedido */
    public function buscar_presupuesto_controlador()
    {
        $pedidoCompra  = mainModel::limpiar_string($_POST['buscar_presupuesto']);

        if ($pedidoCompra == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC, RAZON SOCIAL o NUMERO DE PEDIDO
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datosPedido = mainModel::ejecutar_consulta_simple("select pc.idpedido_cabecera as idpedido_cabecera, pc.id_usuario as id_usuario, pc.fecha as fecha, pc.estado as estadoPe, pc.id_proveedor as id_proveedor, pc.updated as updated, pc.updatedby as updatedby, 
        p.idproveedores as idproveedores, p.id_ciudad as id_ciudad, p.razon_social as razon_social, p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, p.estado as estadoPro
        from pedido_cabecera pc 
        inner join proveedores p on p.idproveedores = pc.id_proveedor 
        where (idpedido_cabecera like '%$pedidoCompra%' or razon_social like '%$pedidoCompra%' or ruc like '%$pedidoCompra%') and pc.estado = '1'
        order by idpedido_cabecera desc");

        if ($datosPedido->rowCount() >= 1) {
            $datosPedido = $datosPedido->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>
                        <tr class="text-center">
                            <th>Número de Pedido</th>
                            <th>Proveedor</th>
                            <th></th>
                        </tr>';
            foreach ($datosPedido as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['idpedido_cabecera'] . '</td>
                            <td>' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_pedidoPre(' . $rows['idpedido_cabecera'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún pedido en el sistema que coincida con <strong>“' . $pedidoCompra . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */
}
