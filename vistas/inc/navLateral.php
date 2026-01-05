<!-- Nav lateral -->
<section class="full-box nav-lateral">
    <div class="full-box nav-lateral-bg show-nav-lateral"></div>
    <div class="full-box nav-lateral-content">

        <figure class="full-box nav-lateral-avatar">
            <i class="far fa-times-circle show-nav-lateral"></i>
            <figcaption class="roboto-medium text-center">
                <?= $_SESSION['nombre_str'] . " " . $_SESSION['apellido_str'] ?><br>
                <small class="roboto-condensed-light"><?= $_SESSION['nick_str'] ?></small>
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
                <?php if (mainModel::tienePermiso('inventario.ver')) { ?>
                    <li>
                        <a href="<?= SERVERURL; ?>inventario/">
                            <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Inventarios
                        </a>
                    </li>
                <?php } ?>

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
                                <li><a href="<?= SERVERURL; ?>oc-lista/"><i class="fas fa-file-invoice fa-fw"></i> &nbsp; Ordenes de Compra</a></li>
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
                        </ul>
                    </li>
                <?php } ?>

                <!-- SERVICIOS -->
                <?php if (mainModel::puedeVerMenu('servicio')) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-tools fa-fw"></i> &nbsp; Servicios
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>
                            <?php if (mainModel::tienePermiso('servicio.recepcion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>recepcionServicio-nuevo/"><i class="fas fa-file-signature fa-fw"></i> &nbsp; Solicitud de Servicios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.promocion.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>promocion-nuevo/"><i class="fas fa-tags fa-fw"></i> &nbsp; Promociones</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('servicio.descuento.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>descuento-nuevo/"><i class="fas fa-tags fa-fw"></i> &nbsp; Descuentos</a></li>
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
                    mainModel::tienePermiso('cliente.ver') ||
                    mainModel::tienePermiso('inventario.ver') ||
                    mainModel::tienePermiso('empresa.ver')
                ) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-cog fa-fw"></i> &nbsp; Administración
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>

                            <?php if (mainModel::tienePermiso('cliente.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>cliente-lista/"><i class="fas fa-users fa-fw"></i> &nbsp; Clientes</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('inventario.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>articulo-lista/"><i class="fas fa-pallet fa-fw"></i> &nbsp; Articulos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('empresa.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>company/"><i class="fas fa-store-alt fa-fw"></i> &nbsp; Empresa</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('proveedor.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>proveedor-nuevo/"><i class="fas fa-truck fa-fw"></i> &nbsp; Proveedores</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('sucursal.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>sucursal-nuevo/"><i class="fas fa-city fa-fw"></i> &nbsp; Sucursales</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('vehiculo.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>vehiculo-lista/"><i class="fas fa-car fa-fw"></i> &nbsp; Vehículos</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('empleado.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>empleado-lista/"><i class="fas fa-user fa-fw"></i> &nbsp; Empleados</a></li>
                            <?php } ?>
                            <?php if (mainModel::tienePermiso('equipo.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>empleado-equipo/"><i class="fas fa-users fa-fw"></i> &nbsp; Equipos</a></li>
                            <?php } ?>

                        </ul>
                    </li>
                <?php } ?>

                <!-- SEGURIDAD -->
                <?php if (mainModel::puedeVerMenu('seguridad')) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu">
                            <i class="fas fa-shield-alt fa-fw"></i> &nbsp; Seguridad
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul>
                            <?php if (mainModel::tienePermiso('seguridad.usuarios.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>usuario-lista/"><i class="fas fa-user fa-fw"></i> &nbsp; Usuarios</a></li>
                            <?php } ?>

                            <?php if (mainModel::tienePermiso('seguridad.roles.ver')) { ?>
                                <li><a href="<?= SERVERURL; ?>rol-permisos/"><i class="fas fa-key fa-fw"></i> &nbsp; Roles y Permisos</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</section>