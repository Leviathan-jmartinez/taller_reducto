<!-- Nav lateral -->
<section class="full-box nav-lateral">
    <div class="full-box nav-lateral-bg show-nav-lateral"></div>
    <div class="full-box nav-lateral-content">
        <figure class="full-box nav-lateral-avatar">
            <i class="far fa-times-circle show-nav-lateral"></i>
            <!--<img src="<?php echo SERVERURL; ?>vistas/assets/avatar/Avatar.png" class="img-fluid" alt="Avatar">-->
            <figcaption class="roboto-medium text-center">
                <?php echo $_SESSION['nombre_str'] . " " . $_SESSION['apellido_str'] ?> <br><small class="roboto-condensed-light"><?php echo $_SESSION['nick_str'] ?></small>
            </figcaption>
        </figure>
        <div class="full-box nav-lateral-bar"></div>
        <nav class="full-box nav-lateral-menu">
            <ul>
                <li>
                    <a href="<?php echo SERVERURL; ?>home/"><i class="fab fa-dashcube fa-fw"></i> &nbsp; Panel Principal</a>
                </li>




                <li>
                    <a href="<?php echo SERVERURL; ?>inventario-nuevo/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Inventarios</a>
                </li>
                <li>
                    <a href="#" class="nav-btn-submenu"><i class="fas fa-shopping-cart"></i> &nbsp; Compras <i class="fas fa-chevron-down"></i></a>
                    <ul>
                        <li>
                            <a href="<?php echo SERVERURL; ?>pedido-lista/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Pedidos</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>presupuesto-lista/"><i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuestos</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>oc-nuevo/"><i class="fas fa-file-invoice fa-fw"></i> &nbsp; Ordenes de Compra</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>factura-nuevo/"><i class="fas fa-shopping-cart fa-fw"></i> &nbsp; Ingreso de Facturas</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>remisiones-buscar/"><i class="fas fa-box fa-fw"></i> &nbsp; Remisiones</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>notas-lista/"><i class="fas fa-file-alt fa-fw"></i> &nbsp; Notas de Crédito y Débito</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="nav-btn-submenu"><i class="fas fa-tools fa-fw"></i> &nbsp; Servicios <i class="fas fa-chevron-down"></i></a>
                    <ul>
                        <li>
                            <a href="<?php echo SERVERURL; ?>servicios-nuevo/"> <i class="fas fa-file-signature fa-fw"></i> &nbsp; Solicitud de Servicios
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>promociones-lista/"> <i class="fas fa-tags fa-fw"></i> &nbsp; Promociones
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>descuentos-lista/"> <i class="fas fa-tags fa-fw"></i> &nbsp; Descuentos
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>presupuestos-lista/"> <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; Presupuestos de Trabajo
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>ordentrabajo-lista/"> <i class="fas fa-clipboard-check fa-fw"></i> &nbsp; Ordenes de Trabajo
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>regservicios-lista/"> <i class="fas fa-cogs fa-fw"></i> &nbsp; Registro de Servicios
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>reclamos-lista/"> <i class="fas fa-exclamation-circle fa-fw"></i> &nbsp; Reclamos
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="nav-btn-submenu"><i class="fas fa-cog fa-fw"></i> &nbsp; Administración <i class="fas fa-chevron-down"></i></a>
                    <ul>
                        <li>
                            <a href="#" class="nav-btn-submenu"><i class="fas fa-users fa-fw"></i> &nbsp; Clientes <i class="fas fa-chevron-down"></i></a>
                            <ul>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>cliente-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; Agregar Cliente</a>
                                </li>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>cliente-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de clientes</a>
                                </li>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>cliente-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar cliente</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nav-btn-submenu"><i class="fas fa-pallet fa-fw"></i> &nbsp; Articulos <i class="fas fa-chevron-down"></i></a>
                            <ul>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>articulo-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; Agregar Articulo</a>
                                </li>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>articulo-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Articulos</a>
                                </li>
                                <li>
                                    <a href="<?php echo SERVERURL; ?>articulo-buscar/"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar Articulo</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>ciudad-lista/"><i class="fas fa-city"></i> &nbsp; Ciudades</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>cargo-lista/"><i class="fas fa-file-invoice"></i> &nbsp; Cargos</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>marca-lista/"><i class="fas fa-tag fa-fw"></i> &nbsp; Marcas</a>
                        </li>
                        <li>
                            <a href="<?php echo SERVERURL; ?>modelo-auto-lista/"><i class="fas fa-list-ul fa-fw"></i> &nbsp; Modelos de Auto</a>
                        </li>
                    </ul>
                </li>
                <?php
                if ($_SESSION['nivel_str'] == 1) { ?>
                    <li>
                        <a href="#" class="nav-btn-submenu"><i class="fas fa-shield-alt fa-fw"></i> &nbsp; Seguridad <i class="fas fa-chevron-down"></i></a>
                        <ul>
                            <li>
                                <a href="<?php echo SERVERURL; ?>usuario-lista/"><i class="fas fa-user fa-fw"></i> &nbsp; Usuarios</a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['nivel_str'] == 1) { ?>
                    <li>
                        <a href="<?php echo SERVERURL; ?>company/"><i class="fas fa-store-alt fa-fw"></i> &nbsp; Empresa</a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</section>