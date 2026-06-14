 <?php
    $pagina = require __DIR__ . '/../inc/pagina.php';
    if (!mainModel::tienePermiso('compra.remision.ver')) {
        echo '<div class="alert alert-danger">Acceso no autorizado</div>';
        return;
    } ?>

 <div class="container-fluid">

 </div>

 <?php
    /* 🔹 Fechas desde sesión (REMISIÓN) */
    $fecha_inicio = $_SESSION['fecha_inicio_remision'] ?? '';
    $fecha_final  = $_SESSION['fecha_final_remision'] ?? '';
    $nro_factura  = $_SESSION['nro_factura_remision'] ?? '';
    $estado       = $_SESSION['estado_remision'] ?? '';
    $ordenRemision = mainModel::cargar_ordenamiento_sesion('remision', ['fecha', 'estado'], 'fecha', 'DESC');

    $fecha_inicio_dt = $fecha_inicio ? $fecha_inicio . ' 00:00:00' : '';
    $fecha_final_dt  = $fecha_final  ? $fecha_final  . ' 23:59:59' : '';
    $busqueda_activa = isset($_SESSION['filtro_remision_activo']) || $fecha_inicio || $fecha_final || $nro_factura || $estado !== '';
    ?>

 <?php if (!$busqueda_activa) { ?>

     <!-- 🔹 FORMULARIO DE BÚSQUEDA -->
     <div class="container-fluid form-neon app-view">
         <h3 class="text-left">
             <i class="fas fa-search fa-fw"></i> &nbsp; REMISIONES - BUSCAR REMISIÓN
         </h3>
         <ul class="full-box list-unstyled page-nav-tabs">
             <li>
                 <a href="<?php echo SERVERURL; ?>remision-nuevo/">
                     <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA REMISIÓN
                 </a>
             </li>
             <li>
                 <a class="active" href="<?php echo SERVERURL; ?>remision-buscar/">
                     <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR REMISIÓN
                 </a>
             </li>
         </ul>
         <form class="form-neon FormularioAjax app-form"
             action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
             method="POST"
             data-form="search"
             autocomplete="off">

             <input type="hidden" name="modulo" value="remision">

             <!-- Fechas completas -->
             <input type="hidden" name="fecha_inicio_dt" value="">
             <input type="hidden" name="fecha_final_dt" value="">

             <div class="container-fluid">
                 <div class="row justify-content-md-center">

                     <div class="col-12 col-md-4">
                         <div class="form-group">
                             <label>Fecha inicial</label>
                             <input type="date"
                                 class="form-control"
                                 name="fecha_inicio"
                                 id="fecha_inicio">
                         </div>
                     </div>

                     <div class="col-12 col-md-4">
                         <div class="form-group">
                             <label>Fecha final</label>
                             <input type="date"
                                 class="form-control"
                                 name="fecha_final"
                                 id="fecha_final">
                         </div>
                     </div>

                     <div class="col-12 col-md-4">
                         <div class="form-group">
                             <label>Nro Factura</label>
                             <input type="text"
                                 class="form-control"
                                 name="nro_factura"
                                 id="nro_factura">
                         </div>
                     </div>

                     <div class="col-12 col-md-4">
                         <div class="form-group">
                             <label>Estado</label>
                             <select class="form-control" name="estado_remision" id="estado_remision">
                                 <option value="">Todos</option>
                                 <option value="1">Activo</option>
                                 <option value="2">Procesado</option>
                                 <option value="0">Anulado</option>
                             </select>
                         </div>
                     </div>

                     <div class="col-12 text-center" style="margin-top: 40px;">
                         <button type="submit" class="btn btn-raised btn-info">
                             <i class="fas fa-search"></i> &nbsp; BUSCAR
                         </button>
                     </div>

                 </div>
             </div>
         </form>
     </div>

 <?php } else { ?>

     <!-- 🔹 FORMULARIO ELIMINAR BÚSQUEDA -->
     <div class="container-fluid">
         <form class="FormularioAjax"
             action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
             method="POST"
             data-form="search"
             autocomplete="off">

             <input type="hidden" name="modulo" value="remision">
             <input type="hidden" name="eliminar_busqueda" value="eliminar">

             <input type="hidden" name="fecha_inicio_dt" value="<?php echo $fecha_inicio_dt; ?>">
             <input type="hidden" name="fecha_final_dt" value="<?php echo $fecha_final_dt; ?>">

             <div class="container-fluid">
                 <div class="row justify-content-md-center">

                     <div class="col-12 col-md-6">
                         <p class="text-center" style="font-size: 20px;">
                             Búsqueda:
                             <strong>
                                 <?php
                                    $criterios = [];

                                    if ($fecha_inicio || $fecha_final) {
                                        $criterios[] = "Fecha: " . ($fecha_inicio ?: 'inicio') . " a " . ($fecha_final ?: 'final');
                                    }

                                    if ($nro_factura) {
                                        $criterios[] = "Factura: " . htmlspecialchars($nro_factura, ENT_QUOTES, 'UTF-8');
                                    }

                                    if (isset($_SESSION['filtro_remision_activo'])) {
                                        $estados = [
                                            ""  => "Todos",
                                            "0" => "Anulado",
                                            "1" => "Activo",
                                            "2" => "Procesado"
                                        ];
                                        $criterios[] = "Estado: " . ($estados[(string)$estado] ?? $estado);
                                    }

                                    echo implode(" | ", $criterios);
                                    ?>
                             </strong>
                         </p>
                     </div>

                     <div class="col-12 text-center" style="margin-top: 20px;">
                         <button type="submit" class="btn btn-raised btn-danger">
                             <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA
                         </button>
                     </div>

                 </div>
             </div>
         </form>
     </div>

     <!-- 🔹 RESULTADOS -->
     <div class="container-fluid">
         <?php
            require_once "./controladores/remisionControlador.php";
            $remision = new remisionControlador();

            $remision->paginador_remision_controlador(
                $pagina[1],
                15,
                $pagina[0],
                $_SESSION['fecha_inicio_remision'] ?? '',
                $_SESSION['fecha_final_remision'] ?? '',
                $_SESSION['nro_factura_remision'] ?? '',
                $_SESSION['estado_remision'] ?? '',
                $ordenRemision['orden'],
                $ordenRemision['direccion']
            );
            ?>
     </div>

 <?php
    } ?>
