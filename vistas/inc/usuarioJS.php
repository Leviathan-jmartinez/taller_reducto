<script>
    document.addEventListener('click', function(e) {

        if (e.target.closest('.btn-roles')) {

            let btn = e.target.closest('.btn-roles');
            let id = btn.dataset.id;

            // setear ID en el form
            document.getElementById('input_id_usuario').value = id;

            let contenedor = document.getElementById('contenedor_roles_usuario');
            contenedor.innerHTML = "Cargando...";

            let data = new FormData();
            data.append('accion', 'roles_por_usuario');
            data.append('id_usuario', id);

            fetch(SERVERURL + 'ajax/usuarioAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(html => {

                    contenedor.innerHTML = html;

                    // 🔥 IMPORTANTE si usás estilos dinámicos
                    if (typeof activarSelect2 === "function") {
                        activarSelect2(contenedor);
                    }

                    $('#modalRolesUsuario').modal('show');
                });
        }

    });

    document.addEventListener("click", function(e) {

        if (e.target.classList.contains("swal2-confirm")) {

            let modal = document.getElementById('modalRolesUsuario');

            if ($(modal).hasClass('show')) {
                $('#modalRolesUsuario').modal('hide');
            }

        }

    });

    document.addEventListener('click', function(e) {

        if (e.target.closest('.btn-sucursal')) {

            let id = e.target.closest('.btn-sucursal').dataset.id;

            document.getElementById('input_id_usuario_sucursal').value = id;

            let contenedor = document.getElementById('contenedor_sucursal_usuario');
            contenedor.innerHTML = "Cargando...";

            let data = new FormData();
            data.append('accion', 'sucursal_por_usuario');
            data.append('id_usuario', id);

            fetch(SERVERURL + 'ajax/usuarioAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(html => {

                    contenedor.innerHTML = html;

                    if (typeof activarSelect2 === "function") {
                        activarSelect2(contenedor);
                    }

                    $('#modalSucursalUsuario').modal('show');
                });
        }

    });

    document.addEventListener("click", function(e) {

        if (e.target.classList.contains("swal2-confirm")) {

            let modal = document.getElementById('modalSucursalUsuario');

            if ($(modal).hasClass('show')) {
                $('#modalSucursalUsuario').modal('hide');
            }

        }

    });
</script>