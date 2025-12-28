<div class="container-fluid">
    <h3>
        <i class="fas fa-tools"></i> &nbsp; ORDENES DE TRABAJO
    </h3>
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
<div class="modal fade" id="modalAsignarTecnico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form class="FormularioAjax"
                action="<?= SERVERURL ?>ajax/ordenTrabajoAjax.php"
                method="POST"
                data-form="update">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Asignar técnico
                    </h5>
                    <button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="accion" value="asignar_tecnico">
                    <input type="hidden" name="id_ot" id="modal_id_ot">

                    <label>Técnico</label>
                    <select name="idtrabajos"
                        id="idtrabajos"
                        class="form-control"
                        required>
                        <option value="">Seleccione técnico</option>
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Asignar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php include_once "./vistas/inc/ordenTrabajoJS.php";?>