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
    public function articulo_controlador()
    {
        session_start(['name' => 'STR']);
        ini_set('log_errors', 1);
        ini_set('error_log', '/path/to/php-error.log');
        // AGREGAR ARTÍCULO
        if (isset($_POST['id_agregar_articulo'])) {

            $id = mainModel::limpiar_string($_POST['id_agregar_articulo']);
            $cantidad = mainModel::limpiar_string($_POST['detalle_cantidad']);

            $check_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE id_articulo='$id' AND estado=1");
            if ($check_articulo->rowCount() <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "No se encontró el artículo", "Tipo" => "error"]));

            $campos = $check_articulo->fetch();
            if ($cantidad == "" || !is_numeric($cantidad) || intval($cantidad) <= 0)
                die(json_encode(["Alerta" => "simple", "Titulo" => "Error!", "Texto" => "Cantidad inválida", "Tipo" => "error"]));

            if (empty($_SESSION['datos_articulo'][$id])) {
            $_SESSION['datos_articulo'][$id] = [
                "ID" => $campos['id_articulo'],
                "codigo" => $campos['codigo'],
                "descripcion" => $campos['desc_articulo'],
                "cantidad" => $cantidad
            ];
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo agregado!",
                "Texto" =>  "El articulo ha sido agregado",
                "Tipo" => "success"
            ];
            echo json_encode($alerta);
            exit();
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" =>  "El articulo que intenta agregar ya se encuentra agregado",
                "Tipo" => "error"
            ];
            echo json_encode($alerta);
            exit();
        }
        }

        // BUSCAR ARTÍCULO (HTML)
        if (isset($_POST['buscar_articulo'])) {
            $articulo = mainModel::limpiar_string($_POST['buscar_articulo']);
            if ($articulo == "") return '<div class="alert alert-warning">Debes introducir código o descripción</div>';

            $id_proveedor = $_SESSION['datos_proveedor']['ID'];
            if (!isset($_SESSION['datos_proveedor']['ID'])) {
                echo json_encode([
                    "Alerta" => "simple",
                    "Titulo" => "Error!",
                    "Texto" => "No se ha seleccionado un proveedor",
                    "Tipo" => "error"
                ]);
                exit();
            }
            $datos_articulo = mainModel::ejecutar_consulta_simple("SELECT * FROM articulos WHERE (codigo like '%$articulo%' OR desc_articulo like '%$articulo%') AND estado=1 AND idproveedores='$id_proveedor' ORDER BY desc_articulo DESC");

            if ($datos_articulo->rowCount() >= 1) {
                $tabla = '<div class="table-responsive"><table class="table table-hover table-bordered table-sm"><tbody>';
                foreach ($datos_articulo->fetchAll() as $rows) {
                    $tabla .= '<tr class="text-center">
                    <td>' . $rows['codigo'] . ' - ' . $rows['desc_articulo'] . '</td>
                    <td style="width:100px;"><input type="number" id="cantidad_' . $rows['id_articulo'] . '" class="form-control form-control-sm" value="1" min="1"></td>
                    <td><button type="button" class="btn btn-primary btn-sm" onclick="agregar_articulo(' . $rows['id_articulo'] . ')"><i class="fas fa-plus-circle"></i></button></td>
                </tr>';
                }
                $tabla .= '</tbody></table></div>';
                return $tabla;
            } else return '<div class="alert alert-warning">No se encontraron artículos que coincidan</div>';
        }
    }




    /**controlador eliminar articulo */
    public function eliminar_articulo_controlador()
    {
        $id  = mainModel::limpiar_string($_POST['id_eliminar_articulo']);
        session_start(['name' => 'STR']);
        unset($_SESSION['datos_articulo'][$id]);
        if (empty($_SESSION['datos_articulo'][$id])) {
            $alerta = [
                "Alerta" => "recargar",
                "Titulo" => "Articulo removido!",
                "Texto" => "Los datos del articulo fueron removidos correctamente",
                "Tipo" => "success"
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado!",
                "Texto" => "No hemos podido remover los datos del articulo",
                "Tipo" => "error"
            ];
        }
        echo json_encode($alerta);
    }
    /**fin controlador */
}
