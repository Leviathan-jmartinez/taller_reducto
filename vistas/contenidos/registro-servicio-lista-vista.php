<div class="container-fluid">
    <h3>
        <i class="fas fa-clipboard-check"></i>
        &nbsp; REGISTRO DE SERVICIOS
    </h3>
</div>

<div class="container-fluid">
    <?php
    require_once "./controladores/registroServicioControlador.php";

    $insRS = new registroServicioControlador();

    echo $insRS->paginador_registro_servicio_controlador(
        $pagina[1],
        15,
        $_SESSION['nivel_str'],
        $pagina[0],
        "",
        ""
    );
    ?>
</div>

<?php include_once "./vistas/inc/registroServicioJS.php"; ?>
