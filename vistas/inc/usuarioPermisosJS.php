<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    document.addEventListener('DOMContentLoaded', function() {

        const selectRol = document.getElementById('id_rol');
        const contenedor = document.getElementById('contenedor_permisos');

        if (!selectRol) {
            console.error('ERROR: no existe el select #id_rol');
            return;
        }

        /* =========================
           CARGAR PERMISOS
        ========================= */
        function cargarPermisosRol(idRol) {

            if (!idRol) {
                contenedor.innerHTML =
                    '<div class="alert alert-info">Seleccione un rol para ver los permisos</div>';
                return;
            }

            // Loader
            contenedor.innerHTML =
                '<div class="text-center">Cargando permisos...</div>';

            let data = new FormData();
            data.append('accion', 'permisos_por_rol');
            data.append('id_rol', idRol);

            fetch(SERVERURL + 'ajax/rolesAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(r => {

                    if (r.includes("Acceso") || r.includes("Error")) {
                        console.warn("⚠ Problema en respuesta:", r);
                    }

                    contenedor.innerHTML = r;

                    // 🔥 activar eventos luego de render
                    activarEventosPermisos();

                })
                .catch(err => {
                    console.error('❌ Fetch error:', err);

                    contenedor.innerHTML =
                        '<div class="alert alert-danger">Error al cargar permisos</div>';
                });
        }

        /* =========================
           EVENTOS CHECKBOX
        ========================= */
        function activarEventosPermisos() {

            // ✔ seleccionar todo por módulo
            document.querySelectorAll('.check-modulo').forEach(check => {

                check.addEventListener('change', function() {

                    let target = this.dataset.target;

                    document.querySelectorAll('.permiso-' + target)
                        .forEach(p => p.checked = this.checked);

                });

            });

            // ✔ sincronizar módulo con hijos
            document.querySelectorAll('.permiso-item').forEach(item => {

                item.addEventListener('change', function() {

                    let grupo = this.dataset.grupo;
                    let items = document.querySelectorAll('.permiso-' + grupo);
                    let checkModulo = document.querySelector('#check_' + grupo);

                    let todosMarcados = true;

                    items.forEach(i => {
                        if (!i.checked) todosMarcados = false;
                    });

                    if (checkModulo) {
                        checkModulo.checked = todosMarcados;
                    }

                });

            });
        }

        /* =========================
           EVENTO CAMBIO DE ROL
        ========================= */
        selectRol.addEventListener('change', function() {
            cargarPermisosRol(this.value);
        });

    });
        


</script>