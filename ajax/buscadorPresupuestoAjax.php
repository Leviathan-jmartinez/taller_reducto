<?php
session_start();
$peticionAjax = true;
require_once "../config/APP.php";
require_once "../modelos/mainModel.php";

$busqueda = isset($_POST['busqueda']) ? trim(strip_tags($_POST['busqueda'])) : "";
$_SESSION['busqueda_presupuesto'] = $busqueda;

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

$query = "
SELECT SQL_CALC_FOUND_ROWS 
    pc.idpresupuesto_compra as idpresupuesto_compra, pc.id_usuario as id_usuario, pc.fecha as fecha, 
    pc.estado as estadoPre, pc.idproveedores as idproveedores, p.razon_social as razon_social, 
    p.ruc as ruc, p.telefono as telefono, p.direccion as direccion, p.correo as correo, 
    p.estado as estadoPro, u.usu_nombre as usu_nombre, u.usu_apellido as usu_apellido, 
    u.usu_estado as usu_estado, u.usu_nick as usu_nick, pc.updated as updated, pc.updatedby as updatedby
FROM presupuesto_compra pc
INNER JOIN proveedores p ON p.idproveedores = pc.idproveedores
INNER JOIN usuarios u ON u.id_usuario = pc.id_usuario
WHERE (pc.idpresupuesto_compra LIKE '%$busqueda%'
        OR p.razon_social LIKE '%$busqueda%'
        OR p.ruc LIKE '%$busqueda%')
  AND pc.estado = 1
ORDER BY fecha ASC
LIMIT 0, 50
";

$datos = $conexion->query($query);


if ($datos->rowCount() < 1) { ?>

    <div class="card">
        <div class="card-body text-center text-muted py-5">
            No se encontraron resultados
        </div>
    </div>

<?php exit;
} ?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-hover text-center align-middle oc-table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>

                    <?php while ($row = $datos->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?php echo $row['idpresupuesto_compra']; ?></td>
                            <td><?php echo $row['razon_social']; ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td>—</td>
                            <td>
                                <span class="badge bg-<?php echo ($row['estadoPre'] == 1 ? 'success' : 'warning'); ?>">
                                    <?php echo ($row['estadoPre'] == 1 ? 'Aprobado' : 'Pendiente'); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm">Generar OC</button>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>