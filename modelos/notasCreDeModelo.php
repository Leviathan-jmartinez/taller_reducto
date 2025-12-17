<?php
if ($peticionAjax) {
    require_once "../config/SERVER.php";
} else {
    require_once "./config/SERVER.php";
}
require_once "mainModel.php";

class notasCreDeModelo extends mainModel
{

    /** Buscar facturas de proveedores por serie, número o proveedor */
    public static function buscarFacturas($buscar)
    {
        $pdo = self::conectar(); // aquí sí funciona porque estamos dentro de la clase

        $sql = "SELECT c.idcompra_cabecera, c.nro_factura, c.nro_timbrado, c.fecha_factura, c.total_compra,
                       p.razon_social AS proveedor
                FROM compra_cabecera c
                INNER JOIN proveedores p ON c.idproveedores = p.idproveedores
                WHERE c.nro_factura LIKE :buscar
                   OR c.nro_timbrado LIKE :buscar
                   OR p.razon_social LIKE :buscar
                ORDER BY c.fecha_factura DESC
                LIMIT 20";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['buscar' => "%$buscar%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Guardar la factura seleccionada en sesión */
    public static function setFacturaSeleccionada($idFactura)
    {
        $pdo = self::conectar();
        $sql = "SELECT c.idcompra_cabecera, c.nro_factura, c.nro_timbrado, c.fecha_factura, 
                       c.idproveedores, p.razon_social, p.ruc
                FROM compra_cabecera c
                INNER JOIN proveedores p ON c.idproveedores = p.idproveedores
                WHERE c.idcompra_cabecera = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idFactura]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($factura) {
            $_SESSION['datos_proveedorNC'] = [
                'ID' => $factura['idproveedores'],
                'RAZON' => $factura['razon_social'],
                'RUC' => $factura['ruc'],
                'NRO_FACTURA' => $factura['nro_factura'],
                'NRO_TIMBRADO' => $factura['nro_timbrado']
            ];
            return true;
        }
        return false;
    }
}
