<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo COMPANY ?></title>
    <?php include "./vistas/inc/link.php"; ?>
</head>

<body>

    <?php
    $peticionAjax = false;
    require_once "./controladores/vistasControlador.php";
    $IV = new vistasControlador();
    $vistas = $IV->obtenVista_Controlador();
    if ($vistas == "login" || $vistas == "404") {
        require_once "./vistas/contenidos/" . $vistas . "-vista.php";
    } else {
        session_start(['name' => 'STR']);

        $pagina = explode("/", $_GET['vista']);

        require_once "./controladores/loginControlador.php";
        $lc = new loginControlador();
        /** verificamos si no vinene definidos ninguna de estas variables, en ese caso se cierra la sesion */
        if (
            !isset($_SESSION['token_str']) ||
            !isset($_SESSION['nick_str']) ||
            !isset($_SESSION['nivel_str']) ||
            !isset($_SESSION['id_str'])
        ) {
            $lc->forzarCierre_sesion_controlador();
            exit();
        }
    ?>
        <!-- Main container -->
        <main class="full-box main-container">
            <!-- Nav Lateral-->
            <?php include "./vistas/inc/navLateral.php"; ?>
            <!-- Page content -->
            <section class="full-box page-content">
                <?php
                include "./vistas/inc/navBar.php";
                include $vistas;
                ?>

            </section>
        </main>

    <?php
        include "./vistas/inc/logout.php";
    }
    include "./vistas/inc/scripts.php"; ?>

    <script>
        $(document).ready(function() {

            // Hover sobre <li> que tienen <ul>
            $('.nav-lateral-menu li').has('ul').hover(
                function() { // mouse enter
                    $(this).children('ul').stop(true, true).slideDown(200);
                    $(this).children('a').children('i.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                },
                function() { // mouse leave
                    $(this).children('ul').stop(true, true).slideUp(200);
                    $(this).children('a').children('i.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            );

        });
    </script>


</body>

</html>