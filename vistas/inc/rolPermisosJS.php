<script>
    document.getElementById('id_rol').addEventListener('change', function() {

        let data = new FormData();
        data.append('accion', 'obtener_permisos_rol');
        data.append('id_rol', this.value);

        fetch(SERVERURL + 'ajax/rolAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('contenedor_permisos').innerHTML = r;
            });
    });
</script>