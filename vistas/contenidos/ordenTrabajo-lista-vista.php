<div class="container-fluid">
    <h3>
        <i class="fas fa-tools"></i> &nbsp; ORDEN DE TRABAJO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/ordenTrabajo-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; NUEVA ORDEN DE TRABAJO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/ordenTrabajo-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE ORDENES DE TRABAJO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>/ordenTrabajo-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ORDENES DE TRABAJO POR FECHA
            </a>
        </li>
    </ul>
</div>
<div class="container-fluid">
    <?php
    require_once "./controladores/ordenTrabajoControlador.php";
    $insOT = new ordenTrabajoControlador();
    echo $insOT->paginador_ot_controlador(
        $pagina[1],
        15,
        $_SESSION['nivel_str'],
        $pagina[0],
        "",
        ""
    );
    ?>
</div>
<!-- MODAL ASIGNAR TÉCNICO -->
<!-- MODAL ASIGNAR -->
<div class="modal fade" id="modalAsignarTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form class="FormularioAjax"
                action="<?= SERVERURL ?>ajax/ordenTrabajoAjax.php"
                method="POST"
                data-form="update">

                <div class="modal-header">
                    <h5 class="modal-title">Asignar equipo y técnico</h5>
                    <button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="accion" value="asignar_equipo">
                    <input type="hidden" name="id_ot" id="modal_id_ot">

                    <div class="form-group">
                        <label>Equipo de trabajo</label>
                        <select name="idtrabajos" id="modal_idtrabajos" class="form-control" required>
                            <option value="">Seleccione equipo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Técnico responsable</label>
                        <select name="tecnico_responsable" id="modal_tecnico" class="form-control" required>
                            <option value="">Seleccione un técnico</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Asignar</button>
                </div>

            </form>

        </div>
    </div>
</div>


<?php include_once "./vistas/inc/ordenTrabajoJS.php"; ?>