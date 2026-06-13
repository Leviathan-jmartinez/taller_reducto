<?php
if (!headers_sent()) {
    header('Location: ' . SERVERURL . 'cliente-nuevo/');
    exit();
}
?>
<script>
    window.location.href = "<?= SERVERURL ?>cliente-nuevo/";
</script>
<noscript>
    <meta http-equiv="refresh" content="0;url=<?= SERVERURL ?>cliente-nuevo/">
</noscript>
