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
    /**Controlador eliminar proveedor */
    public function eliminar_proveedor_controlador()
    {
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_proveedor']);
        if (empty($_SESSION['datos_proveedor'])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Proveedor removido!",
                "Texto" => "Los datos del Proveedor fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del Proveedor",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
    /**controlador buscador articulo */
    public function buscar_articulo_controlador()
    {
        $articulo  = mainModel::limpiar_string($_POST['buscar_articulo']);

        if ($articulo == "") {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    Debes introducir el RUC o RAZON SOCIAL
                                </p>
                            </div>';
            exit();
        }
        /**seleccionar articulo */
        $datos_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos where (codigo like '%$articulo%' or desc_articulo like '%$articulo%')
        and (estado = 1 )
        order by desc_articulo desc");

        if ($datos_articulo->rowCount() >= 1) {
            $datos_articulo = $datos_articulo->fetchAll();
            $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
            foreach ($datos_articulo as $rows) {
                $tabla .= '
                        <tr class="text-center">
                            <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="modal_agregar_articulo(' . $rows['id_articulo'] . ')"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>';
            }
            $tabla .= '</tbody></table></div>';
            return $tabla;
        } else {
            return '        <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún articulo en el sistema que coincida con <strong>“' . $articulo . '”</strong>
                                </p>
                            </div>';
        }
    }
    /**fin controlador */

    /**controlador agregar articulo */
    public function agregar_articulo_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_agregar_articulo']);

        $check_articulo = mainModel::ejecutar_consulta_simple("select * from articulos where id_articulo = '$id' and estado = 1");
        if ($check_articulo->rowCount() <= 0) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido encontrar el articulo en el sistema",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $campos = $check_articulo->fetch();
        }
        $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);

        if (mainModel::verificarDatos("[0-9]{1,7}", $cantidad)) {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "El formato del campo CANTIDAD no es válido",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
         session_start(['name' => 'STR']);
          
         if (empty($_SESSION['datos_articulo'][$id])) {
            # code...
         } else {
            # code...
         }
         
    }
    /**fin controlador */
}
