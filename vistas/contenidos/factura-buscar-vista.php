<div class="container-fluid">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR FACTURA
    </h3>
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>factura-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; INGRESO DE FACTURA</a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>factura-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR</a>
        </li>
    </ul>
</div>

<?php
// Preparar datetime completos para enviar
$fecha_inicio = $_SESSION['fecha_inicio_factura'] ?? '';
$fecha_final  = $_SESSION['fecha_final_factura'] ?? '';
$fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
$fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
?>

<div class="container-fluid">
    <form id="form_busqueda_factura" class="form-neon" method="POST" autocomplete="off">
        <input type="hidden" name="modulo" value="factura">

        <div class="container-fluid">
            <div class="row justify-content-md-center">

                <!-- Fecha Inicio -->
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                    </div>
                </div>

                <!-- Fecha Final -->
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fecha_final">Fecha final</label>
                        <input type="date" class="form-control" name="fecha_final" id="fecha_final" value="<?php echo $fecha_final; ?>">
                    </div>
                </div>

                <!-- Número de factura -->
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="nro_factura">Número de factura</label>
                        <input type="text" class="form-control" name="nro_factura" id="nro_factura" placeholder="Ej: 001-000123">
                    </div>
                </div>
                <?php
                require_once "./controladores/compraControlador.php";
                $ins_compra = new compraControlador();
                $proveedores = $ins_compra->obtenerProveedores();
                ?>
                <!-- Proveedor -->
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="idproveedor">Proveedor</label>
                        <select name="idproveedor" id="idproveedor" class="form-control">
                            <option value="">-- Todos --</option>
                            <?php foreach ($proveedores as $p): ?>
                                <option value="<?php echo $p['idproveedores']; ?>"><?php echo $p['razon_social']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Botón buscar -->
                <div class="col-12 text-center" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-raised btn-info"><i class="fas fa-search"></i> &nbsp; BUSCAR</button>
                    <?php if ($fecha_inicio || $fecha_final): ?>
                        <button type="button" id="btn_eliminar_busqueda" class="btn btn-raised btn-danger"><i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA</button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- Tabla de resultados -->
<div class="container-fluid mt-4" id="tabla_resultados">
    <?php
    if ($fecha_inicio || $fecha_final) {
        require_once "./controladores/compraControlador.php";
        $ins_compra = new compraControlador();
        echo $ins_compra->paginador_factura_controlador(
            $pagina[1] ?? 1,
            15,
            $_SESSION['nivel_str'],
            $pagina[0] ?? 'factura-buscar',
            $fecha_inicio,
            $fecha_final
        );
    }
    ?>
</div>

<script src="<?php echo SERVERURL; ?>vistas/inc/compra.php"></script>