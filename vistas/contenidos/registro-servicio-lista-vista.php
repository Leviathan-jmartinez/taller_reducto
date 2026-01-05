<div class="container-fluid">
    <h3>
        <i class="fas fa-clipboard-check"></i> &nbsp; REGISTRO DE SERVICIO
    </h3>

    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>/registro-servicio-nuevo/">
                <i class="fas fa-plus fa-fw"></i> &nbsp; REGISTRO DE SERVICIO
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>/registro-servicio-lista/">
                <i class="fas fa-search fa-fw"></i> &nbsp; HISTORIAL DE SERVICIOS
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>registro-servicio-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR
            </a>
        </li>
    </ul>
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