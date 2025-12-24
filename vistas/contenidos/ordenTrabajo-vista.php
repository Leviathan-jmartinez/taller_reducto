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
s