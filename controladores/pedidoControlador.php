<?php
if ($peticionAjax) {
    require_once "../modelos/pedidoModelo.php";
} else {
    require_once "./modelos/pedidoModelo.php";
}

class pedidoControlador extends pedidoModelo
{
    /**controlador buscador proveedor */
    public function buscar_proveedor_controlador()
    {
        $proveedor  = mainModel::limpiar_string($_POST['buscar_proveedor']);

        if ($proveedor == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC o RAZON SOCIAL
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar proveedor */
        $datos_proveedor = mainModel::ejecutar_consulta_simple("SELECT * FROM proveedores where ruc like '%$proveedor%' or razon_social like '%$proveedor%' or 
        telefono like '%$proveedor%' order by razon_social desc");

        if ($datos_proveedor->rowCount() >= 1) {
            $datos_proveedor = $datos_proveedor->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
            foreach ($datos_proveedor as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['ruc'] . ' ' . $rows['razon_social'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="agregar_proveedor(' . $rows['idproveedores'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún proveedor en el sistema que coincida con <strong>“' . $proveedor . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**Controlador agregar proveedor */
    public function agregar_proveedor_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_agregar_proveedor']);

        $check_proveedor = mainModel::ejecutar_consulta_simple("select * from proveedores where idproveedores = '$id' ");
        if ($check_proveedor->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido encontrar el proveedor en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_proveedor->fetch();
        }
        /**iniciar sesion para utilizar variables de sesion */
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_proveedor']);
        if (empty($_SESSION['datos_proveedor'])) {
            $_SESSION['datos_proveedor'] = [
                "ID" => $campos['idproveedores'],
                "RUC" => $campos['ruc'],
                "RAZON" => $campos['razon_social'],
                "TELEFONO" => $campos['telefono']
            ];
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor Agregado!",
                "Texto" => "Proveedor agregado correctamente al pedido",
                "Tipo" => "success"
            ];
            echo json_encode($alerta);
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido agregar el proveedor al pedido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
        }
    }
    /**fin controlador */
}
