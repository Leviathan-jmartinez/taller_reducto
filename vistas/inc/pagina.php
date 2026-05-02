<?php
if (!isset($pagina) || !is_array($pagina)) {
    $vistaActual = $_GET['vista'] ?? '';
    $pagina = explode('/', trim($vistaActual, '/'));
}

if (empty($pagina[0])) {
    $pagina[0] = 'home';
}

if (!isset($pagina[1]) || $pagina[1] === '' || !is_numeric($pagina[1]) || (int)$pagina[1] < 1) {
    $pagina[1] = 1;
}

return $pagina;
