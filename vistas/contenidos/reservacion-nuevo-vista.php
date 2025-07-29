            <!-- Page header -->
            <div class="full-box page-header">
                <h3 class="text-left">
                    <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO
                </h3>
            </div>

            <div class="container-fluid">
                <ul class="full-box list-unstyled page-nav-tabs">
                    <li>
                        <a class="active" href="<?php echo SERVERURL; ?>reservacion-nuevo/"><i class="fas fa-plus fa-fw"></i> &nbsp; NUEVO PEDIDO </a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>reservacion/"><i class="far fa-calendar-alt"></i> &nbsp; RESERVACIONES</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>reservacion-pendiente/"><i class="fas fa-hand-holding-usd fa-fw"></i> &nbsp; PRÉSTAMOS</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>reservacion-lista/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; FINALIZADOS</a>
                    </li>
                    <li>
                        <a href="<?php echo SERVERURL; ?>reservacion-buscar/"><i class="fas fa-search-dollar fa-fw"></i> &nbsp; BUSCAR POR FECHA</a>
                    </li>
                </ul>
            </div>

            <div class="container-fluid">
                <div class="container-fluid form-neon">
                    <div class="container-fluid">
                        <p class="text-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCliente"><i class="fas fa-user-plus"></i> &nbsp; Agregar Proveedor</button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalItem"><i class="fas fa-box-open"></i> &nbsp; Agregar articulo</button>
                        </p>
                        <div>
                            <span class="roboto-medium">PROVEEDOR:</span>
                            <span class="text-danger">&nbsp; <i class="fas fa-exclamation-triangle"></i> Seleccione un Proveedor</span>
                            <form action="" style="display: inline-block !important;">
                                Carlos Alfaro
                                <button type="button" class="btn btn-danger"><i class="fas fa-user-times"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm">
                                <thead>
                                    <tr class="text-center roboto-medium">
                                        <th>#</th>
                                        <th>ITEM</th>
                                        <th>CANTIDAD</th>
                                        <th>ELIMINAR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>1</td>
                                        <td>Silla plastica</td>
                                        <td>7</td>
                                        <td>
                                            <form action="">
                                                <button type="button" class="btn btn-warning">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr class="text-center bg-light">
                                        <td colspan="2"></td>
                                        <td><strong>TOTAL</strong></td>
                                        <td><strong>21 items</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <form action="" autocomplete="off">
                        <br><br><br>
                        <p class="text-center" style="margin-top: 40px;">
                            <button type="reset" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
                            &nbsp; &nbsp;
                            <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
                        </p>
                    </form>
                </div>
            </div>


            <!-- MODAL CLIENTE -->
            <div class="modal fade" id="ModalCliente" tabindex="-1" role="dialog" aria-labelledby="ModalCliente" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalCliente">Agregar Proovedor</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="form-group">
                                    <label for="input_cliente" class="bmd-label-floating">DNI, Nombre, Apellido, Telefono</label>
                                    <input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_cliente" id="input_cliente" maxlength="30">
                                </div>
                            </div>
                            <br>
                            <div class="container-fluid" id="tabla_clientes">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm">
                                        <tbody>
                                            <tr class="text-center">
                                                <td>0000000000 - Nombre del cliente</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-user-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>0000000000 - Nombre del cliente</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-user-plus"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>0000000000 - Nombre del cliente</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-user-plus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún cliente en el sistema que coincida con <strong>“Busqueda”</strong>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                            &nbsp; &nbsp;
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- MODAL ITEM -->
            <div class="modal fade" id="ModalItem" tabindex="-1" role="dialog" aria-labelledby="ModalItem" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalItem">Agregar item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="form-group">
                                    <label for="input_item" class="bmd-label-floating">Código, Nombre</label>
                                    <input type="text" pattern="[a-zA-z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" class="form-control" name="input_item" id="input_item" maxlength="30">
                                </div>
                            </div>
                            <br>
                            <div class="container-fluid" id="tabla_items">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-sm">
                                        <tbody>
                                            <tr class="text-center">
                                                <td>000000000000 - Nombre del item</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-box-open"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>000000000000 - Nombre del item</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-box-open"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>000000000000 - Nombre del item</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-box-open"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>000000000000 - Nombre del item</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary"><i class="fas fa-box-open"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="alert alert-warning" role="alert">
                                <p class="text-center mb-0">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i><br>
                                    No hemos encontrado ningún item en el sistema que coincida con <strong>“Busqueda”</strong>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"><i class="fas fa-search fa-fw"></i> &nbsp; Buscar</button>
                            &nbsp; &nbsp;
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- MODAL AGREGAR ITEM -->
            <div class="modal fade" id="ModalAgregarItem" tabindex="-1" role="dialog" aria-labelledby="ModalAgregarItem" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form class="modal-content FormularioAjax">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalAgregarItem">Selecciona el formato, cantidad de items, tiempo y costo del préstamo del item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_agregar_item" id="id_agregar_item">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="detalle_formato" class="bmd-label-floating">Formato de préstamo</label>
                                            <select class="form-control" name="detalle_formato" id="detalle_formato">
                                                <option value="Horas" selected="">Horas</option>
                                                <option value="Dias">Días</option>
                                                <option value="Evento">Evento</option>
                                                <option value="Mes">Mes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="detalle_cantidad" class="bmd-label-floating">Cantidad de items</label>
                                            <input type="num" pattern="[0-9]{1,7}" class="form-control" name="detalle_cantidad" id="detalle_cantidad" maxlength="7" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="detalle_tiempo" class="bmd-label-floating">Tiempo (según formato)</label>
                                            <input type="num" pattern="[0-9]{1,7}" class="form-control" name="detalle_tiempo" id="detalle_tiempo" maxlength="7" required="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label for="detalle_costo_tiempo" class="bmd-label-floating">Costo por unidad de tiempo</label>
                                            <input type="text" pattern="[0-9.]{1,15}" class="form-control" name="detalle_costo_tiempo" id="detalle_costo_tiempo" maxlength="15" required="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Agregar</button>
                            &nbsp; &nbsp;
                            <button type="button" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>