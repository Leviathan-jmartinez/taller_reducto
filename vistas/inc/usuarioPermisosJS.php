<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    document.addEventListener('DOMContentLoaded', function() {

        const selectRol = document.getElementById('id_rol');
        if (!selectRol) {
            console.error('ERROR: no existe el select #id_rol');
            return;
        }

        function cargarPermisosRol(idRol) {


            if (!idRol) {
                document.getElementById('contenedor_permisos').innerHTML =
                    '<div class="alert alert-info">Seleccione un rol para ver los permisos</div>';
                return;
            }

            let data = new FormData();
            data.append('accion', 'permisos_por_rol');
            data.append('id_rol', idRol);


            fetch(SERVERURL + 'ajax/usuarioAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(r => {
                    
                    document.getElementById('contenedor_permisos').innerHTML = r;
                })
                .catch(err => console.error('Fetch error:', err));
        }

        selectRol.addEventListener('change', function() {
            
            cargarPermisosRol(this.value);
        });

    });
</script>