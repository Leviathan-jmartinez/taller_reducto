<?php
session_start();
$peticionAjax = true;
require_once "../config/APP.php";
require_once "../modelos/mainModel.php";

// proxy class to expose the protected conectar() via a public wrapper
class MainModelProxy extends mainModel
{
    public function conectarPublic()
    {
        return $this->conectar();
    }
}

$mainModel = new MainModelProxy();
$conexion = $mainModel->conectarPublic();

$id = $_POST['idpresupuesto'];

$consulta = $conexion->prepare("
    SELECT d.id_articulo, a.desc_articulo, d.precio
    FROM presupuesto_detalle d
    inner join articulos a on a.id_articulo = d.id_articulo
    WHERE d.idpresupuesto_compra = :id
");
$consulta->execute([":id" => $id]);

$datos = $consulta->fetchAll();

$html = "";

foreach ($datos as $row) {
    $html .= '
        <tr>
            <td>'.$row['id_articulo'].'</td>
            <td>'.$row['desc_articulo'].'</td>
            <td>'.$row['precio'].'</td>            
            <td><input type="number" name="cantidades['.$row['id_articulo'].']" min="0" class="form-control"></td>
        </tr>';
}

echo $html;