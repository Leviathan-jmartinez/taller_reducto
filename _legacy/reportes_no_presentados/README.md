# Reportes no presentados

Este directorio conserva codigo retirado del flujo visible de la aplicacion para la entrega.

## Informes activos en la aplicacion

- Articulos
- Sucursales
- Proveedores
- Clientes
- Vehiculos
- Empleados
- Pedidos de compra
- Ordenes de compra
- Compras
- Solicitudes de servicios
- Presupuestos de servicios
- Registros de servicios

## Informes retirados del flujo visible

- Marcas
- Categorias
- Usuarios
- Presupuestos de compra
- Libro de compras
- Stock
- Movimientos de stock
- Transferencias
- Ordenes de trabajo
- Kardex de articulo

## Contenido respaldado

- `controladores/reportesControlador.full.php`: copia completa del controlador antes del recorte.
- `modelos/reportesModelo.full.php`: copia completa del modelo antes del recorte.
- `vistas/contenidos/reporte-referenciales-vista.full.php`: copia completa de la vista referencial antes del recorte.
- `vistas/contenidos/reporte-movimientos-vista.full.php`: copia completa de la vista de movimientos antes del recorte.
- `pdf/`: PDFs exclusivos de informes retirados.

## Restauracion

Para recuperar un informe retirado:

1. Revisar el snapshot correspondiente en este directorio.
2. Restaurar la entrada del tipo en `config_referenciales()` o `config_movimientos()`.
3. Restaurar la opcion del selector en la vista correspondiente.
4. Restaurar permisos del menu en `vistas/inc/navLateral.php`.
5. Mover el PDF requerido desde este directorio hacia `pdf/`.

