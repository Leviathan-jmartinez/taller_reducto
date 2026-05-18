<!-- Nav lateral -->
<section class="full-box nav-lateral">
    <div class="full-box nav-lateral-bg show-nav-lateral"></div>
    <div class="full-box nav-lateral-content">

        <figure class="full-box nav-lateral-avatar">
            <i class="far fa-times-circle show-nav-lateral"></i>
            <figcaption class="roboto-medium text-center">
                <?= $_SESSION['nombre_str'] . " " . $_SESSION['apellido_str'] ?><br>
                <small class="roboto-condensed-light"> </small>
            </figcaption>
        </figure>

        <div class="full-box nav-lateral-bar"></div>

        <nav class="full-box nav-lateral-menu">
            <ul>
                <!-- PANEL -->
                <li>
                    <a href="<?= SERVERURL; ?>home/">
                        <i class="fab fa-dashcube fa-fw"></i> &nbsp; Panel Principal
                    </a>
                </li>

                <!-- INVENTARIOS -->


                <!-- COMPRAS -->
                <?php if (mainModel::tienePermiso('compra.ver')) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-shopping-cart"></i> &nbsp; Compras
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>
                            <?php if (mainModel::tienePermiso('compra.pedido.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>pedido-lista/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Pedidos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.presupuesto.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>presupuesto-lista/"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuestos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.oc.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>oc-nuevo/"><i class="fas fa-file-invoice fa-fw"></i> &nbsp; Ordenes de Compra</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.factura.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>factura-nuevo/"><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ingreso de Facturas</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.remision.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>remision-nuevo/"><i class="fas fa-box fa-fw"></i> &nbsp; Remisiones</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('compra.nota.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>notasCreDe-nuevo/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Notas de Crédito y Débito</a></li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('compra.transferencia.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>transferencia-nuevo/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Transferencias</a></li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('inventario.ver')) { ?>
                                <li>
                                    <a href="<?= SERVERURL; ?>inventario/">
                                        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Inventarios
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- SERVICIOS -->
                <?php if (mainModel::tienePermiso('servicio.ver')) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-tools fa-fw"></i> &nbsp; Servicios
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>
                            <?php if (mainModel::tienePermiso('servicio.recepcion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>recepcionServicio-nuevo/"><i class="fas fa-file-signature fa-fw"></i> &nbsp; Solicitud de Servicios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.diagnostico.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>diagnostico-servicio-nuevo/"><i class="fas fa-stethoscope fa-fw"></i> &nbsp; Diagnostico</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.promocion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>promocion-nuevo/"><i class="fas fa-tags fa-fw"></i> &nbsp; Promociones</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.descuento.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>descuento-nuevo/"><i class="fas fa-tags fa-fw"></i> &nbsp; Descuentos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.regla_comercial.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>regla-comercial-nuevo/"><i class="fas fa-project-diagram fa-fw"></i> &nbsp; Reglas Comerciales</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.presupuesto.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>presupuesto-servicio-nuevo"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuesto de Trabajo</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.ot.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>ordenTrabajo-nuevo/"><i class="fas fa-clipboard-check fa-fw"></i> &nbsp; Ordenes de Trabajo</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.registro.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>registro-servicio-nuevo/"><i class="fas fa-cogs fa-fw"></i> &nbsp; Registro de Servicios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.reclamo.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>reclamo-servicio-nuevo/"><i class="fas fa-exclamation-circle fa-fw"></i> &nbsp; Reclamos</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>

                <!-- ADMINISTRACIÓN -->
                <?php if (
                    mainModel::tienePermiso('mantenimiento.ver')
                ) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-cog fa-fw"></i> &nbsp; Mantenimiento
                            <i class="fas fa-chevron-down"></i>
                        </a>

                        <ul>
                            <?php if (mainModel::tienePermiso('compra.ver')) { ?>
                                <!-- COMPRAS -->
                                <li>
                                    <a href="#" class="nav-btn-submenu">
                                        <i class="fas fa-cog fa-fw"></i> &nbsp; Compras
                                        <i class="fas fa-chevron-down"></i>
                                    </a>

                                    <ul>
                                        <?php if (mainModel::tienePermiso('sucursal.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>sucursal-nuevo/">
                                                    <i class="fas fa-city fa-fw"></i> &nbsp; Sucursales
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('articulo.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>articulo-nuevo/">
                                                    <i class="fas fa-pallet fa-fw"></i> &nbsp; Artículos
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('proveedor.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>proveedor-nuevo/">
                                                    <i class="fas fa-truck fa-fw"></i> &nbsp; Proveedores
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('servicio.ver') || mainModel::tienePermiso('equipo.crear') || mainModel::tienePermiso('equipo.editar')) { ?>
                                <!-- SERVICIOS -->
                                <li>
                                    <a href="#" class="nav-btn-submenu">
                                        <i class="fas fa-cog fa-fw"></i> &nbsp; Servicios
                                        <i class="fas fa-chevron-down"></i>
                                    </a>

                                    <ul>
                                        <?php if (mainModel::tienePermiso('cliente.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>cliente-nuevo/">
                                                    <i class="fas fa-users fa-fw"></i> &nbsp; Clientes
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('vehiculo.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>vehiculo-nuevo/">
                                                    <i class="fas fa-car fa-fw"></i> &nbsp; Vehículos
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('empleado.ver')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>empleado-nuevo/">
                                                    <i class="fas fa-user fa-fw"></i> &nbsp; Empleados
                                                </a>
                                            </li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('equipo.crear') || mainModel::tienePermiso('equipo.editar')) { ?>
                                            <li>
                                                <a href="<?= SERVERURL; ?>empleado-equipo/">
                                                    <i class="fas fa-users fa-fw"></i> &nbsp; Equipos
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <!-- SEGURIDAD -->
                            <?php if (mainModel::tienePermiso('usuarios.ver')) { ?>
                                <li>
                                    <a href="#" class="nav-btn-submenu">
                                        <i class="fas fa-shield-alt fa-fw"></i> &nbsp; Seguridad
                                        <i class="fas fa-chevron-down"></i>
                                    </a>
                                    <ul>
                                        <?php if (mainModel::tienePermiso('usuarios.ver')) { ?>
                                            <li><a href="<?= SERVERURL; ?>usuario-nuevo/"><i class="fas fa-user fa-fw"></i> &nbsp; Usuarios</a></li>
                                        <?php } ?>

                                        <?php if (mainModel::tienePermiso('roles.ver')) { ?>
                                            <li><a href="<?= SERVERURL; ?>rol-nuevo/"><i class="fas fa-key fa-fw"></i> &nbsp; Roles y Permisos</a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <?php if (
                    mainModel::tienePermiso('reportes.articulos.ver') ||
                    mainModel::tienePermiso('reportes.proveedores.ver') ||
                    mainModel::tienePermiso('reportes.sucursales.ver') ||
                    mainModel::tienePermiso('reportes.clientes.ver') ||
                    mainModel::tienePermiso('reportes.vehiculos.ver') ||
                    mainModel::tienePermiso('reportes.empleados.ver')
                ) { ?>
                <li>
                    <a href="#" class="nav-btn-submenu">
                        <i class="fas fa-chart-bar fa-fw"></i> &nbsp; Informes Referenciales
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul>
                        <?php if (
                            mainModel::tienePermiso('reportes.articulos.ver') ||
                            mainModel::tienePermiso('reportes.proveedores.ver') ||
                            mainModel::tienePermiso('reportes.sucursales.ver')
                        ) { ?>
                            <!-- ================= COMPRAS ================= -->
                            <li>
                                <a href="#" class="nav-btn-submenu">
                                    <i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Referenciales de Compras
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul>
                                    <?php if (mainModel::tienePermiso('reportes.articulos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-articulos/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Artículos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.proveedores.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-proveedores/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Proveedores
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.sucursales.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-sucursales/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Sucursales
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (
                            mainModel::tienePermiso('reportes.clientes.ver') ||
                            mainModel::tienePermiso('reportes.vehiculos.ver') ||
                            mainModel::tienePermiso('reportes.empleados.ver')
                        ) { ?>
                            <!-- ================= SERVICIOS ================= -->
                            <li>
                                <a href="#" class="nav-btn-submenu">
                                    <i class="fas fa-tools fa-fw"></i> &nbsp; Referenciales de Servicios
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul>
                                    <?php if (mainModel::tienePermiso('reportes.clientes.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-clientes/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Clientes
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.vehiculos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-vehiculos/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; VehÃ­culos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.empleados.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-empleados/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Empleados
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>


                <?php if (
                    mainModel::tienePermiso('reportes.pedidos.ver') ||
                    mainModel::tienePermiso('reportes.presupuestos_compra.ver') ||
                    mainModel::tienePermiso('reportes.ordenes_compra.ver') ||
                    mainModel::tienePermiso('reportes.compras.ver') ||
                    mainModel::tienePermiso('reportes.libro_compras.ver') ||
                    mainModel::tienePermiso('reportes.stock.ver') ||
                    mainModel::tienePermiso('reportes.movimientos_stock.ver') ||
                    mainModel::tienePermiso('reportes.recepcion_servicio.ver') ||
                    mainModel::tienePermiso('reportes.presupuesto_servicio.ver') ||
                    mainModel::tienePermiso('reportes.orden_trabajo.ver') ||
                    mainModel::tienePermiso('reportes.registro_servicio.ver')
                ) { ?>
                <li>
                    <a href="#" class="nav-btn-submenu">
                        <i class="fas fa-chart-bar fa-fw"></i> &nbsp; Informes de movimientos
                        <i class="fas fa-chevron-down"></i>
                    </a>

                    <ul>
                        <?php if (
                            mainModel::tienePermiso('reportes.pedidos.ver') ||
                            mainModel::tienePermiso('reportes.presupuestos_compra.ver') ||
                            mainModel::tienePermiso('reportes.ordenes_compra.ver') ||
                            mainModel::tienePermiso('reportes.compras.ver') ||
                            mainModel::tienePermiso('reportes.libro_compras.ver') ||
                            mainModel::tienePermiso('reportes.stock.ver') ||
                            mainModel::tienePermiso('reportes.movimientos_stock.ver')
                        ) { ?>
                            <!-- ================= INFORMES DE COMPRAS ================= -->
                            <li>
                                <a href="#" class="nav-btn-submenu">
                                    <i class="fas fa-file-invoice fa-fw"></i> &nbsp; Informes de Compras
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul>
                                    <?php if (mainModel::tienePermiso('reportes.pedidos.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-pedidos/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Pedidos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.presupuestos_compra.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-presupuestos/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Presupuestos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.ordenes_compra.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-ordenes-compra/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Órdenes de Compra
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.compras.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-compras/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Compras
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.libro_compras.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-LibroCompras/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe Libro de Compras
                                        </a>
                                    </li>
                                    <?php } ?>

                                    <?php if (mainModel::tienePermiso('reportes.stock.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-stock/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Stock
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.movimientos_stock.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-movimientostock/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Movimientos de Stock
                                        </a>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (
                            mainModel::tienePermiso('reportes.recepcion_servicio.ver') ||
                            mainModel::tienePermiso('reportes.presupuesto_servicio.ver') ||
                            mainModel::tienePermiso('reportes.orden_trabajo.ver') ||
                            mainModel::tienePermiso('reportes.registro_servicio.ver')
                        ) { ?>
                            <!-- ================= INFORMES DE SERVICIOS ================= -->
                            <li>
                                <a href="#" class="nav-btn-submenu">
                                    <i class="fas fa-tools fa-fw"></i> &nbsp; Informes de Servicios
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul>


                                    <?php if (mainModel::tienePermiso('reportes.recepcion_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-recepcion-servicio/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Rec. de Servicios
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.presupuesto_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-presupuesto-servicio/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Presupuestos
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.orden_trabajo.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-orden-trabajo/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de OT
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (mainModel::tienePermiso('reportes.registro_servicio.ver')) { ?>
                                    <li>
                                        <a href="<?= SERVERURL; ?>reporte-registro-servicio/">
                                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Informe de Reg. Servicios
                                        </a>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>


                <li>
                    <a href="<?= SERVERURL; ?>public/docs/userManual.pdf" download>
                        <i class="fas fa-question-circle fa-fw"></i> &nbsp; Ayuda
                    </a>
                </li>


            </ul>
        </nav>
    </div>
</section>
