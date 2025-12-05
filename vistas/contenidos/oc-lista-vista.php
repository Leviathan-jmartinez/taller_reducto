            <!-- Page header -->
            <div class="full-box page-header">
                <h3 class="text-left">
                    <i class="fas fa-file-invoice-dollar fa-fw"></i> &nbsp; LISTADO DE ORDENES DE COMPRA
                </h3>
            </div>

            <div class="container-fluid">
                <ul class="full-box list-unstyled page-nav-tabs">
                    <li>
                        <a href="<?php echo SERVERURL; ?>oc-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; GENERAR ORDEN DE COMPRA</a>
                    </li>
                    <li>
                        <a class="active" href="<?php echo SERVERURL; ?>oc-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTADOS DE ORDENES</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>oc-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
                    </li>
                </ul>
            </div>

            <div class="container-fluid">
                <?php
                require_once "./controladores/ordencompraControlador.php";
                $ins_ordencompra = new ordencompraControlador();
                echo $ins_ordencompra->paginador_ordencompra_controlador($pagina[1], 15, $_SESSION['nivel_str'], $pagina[0], "","");
                ?>
            </div>