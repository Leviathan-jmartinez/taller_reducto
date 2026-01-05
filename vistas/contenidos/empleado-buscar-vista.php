<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR EMPLEADO
    </h3>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-nuevo/">
                <i class="fas fa-user-plus fa-fw"></i> &nbsp; AGREGAR EMPLEADO
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>empleado-lista/">
                <i class="fas fa-users fa-fw"></i> &nbsp; LISTA DE EMPLEADOS
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>empleado-buscar/">
                <i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR EMPLEADO
            </a>
        </li>
    </ul>
</div>

<?php if (!isset($_SESSION['busqueda_empleado']) || empty($_SESSION['busqueda_empleado'])) { ?>

    <!-- CONTENT BUSQUEDA -->
    <div class="container-fluid">
        <form class="form-neon FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="default"
            autocomplete="off">

            <input type="hidden" name="modulo" value="empleado">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="bmd-label-floating">
                                ¿Qué empleado estás buscando?
                            </label>
                            <input type="text"
                                class="form-control"
                                name="busqueda_inicial"
                                maxlength="40">
                        </div>
                    </div>

                    <div class="col-12">
                        <p class="text-center" style="margin-top: 40px;">
                            <button type="submit" class="btn btn-raised btn-info">
                                <i class="fas fa-search"></i> &nbsp; BUSCAR
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php } else { ?>

    <!-- ELIMINAR BUSQUEDA -->
    <div class="container-fluid">
        <form class="FormularioAjax"
            action="<?php echo SERVERURL; ?>ajax/buscadorAjax.php"
            method="POST"
            data-form="search"
            autocomplete="off">

            <input type="hidden" name="modulo" value="empleado">
            <input type="hidden" name="eliminar_busqueda" value="eliminar">

            <div class="container-fluid">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-6">
                        <p class="text-center" style="font-size: 20px;">
                            Resultados de la búsqueda
                            <strong>“<?php echo $_SESSION['busqueda_empleado']; ?>”</strong>
                        </p>
                    </div>

                    <div class="col-12">
                        <p class="text-center" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-raised btn-danger">
                                <i class="far fa-trash-alt"></i> &nbsp; ELIMINAR BÚSQUEDA
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- RESULTADOS -->
    <div class="container-fluid">
        <?php
        require_once "./controladores/empleadoControlador.php";
        $ins_empleado = new empleadoControlador();

        echo $ins_empleado->paginador_empleados_controlador(
            $pagina[1],
            15,
            $_SESSION['nivel_str'],
            $pagina[0],
            $_SESSION['busqueda_empleado']
        );
        ?>
    </div>

<?php } ?>