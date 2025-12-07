<script>
    function buscar_OC() {
        let input_oc = document.querySelector('#input_oc').value;
        input_oc = input_oc.trim();
        if (input_oc != "") {
            let datos = new FormData();
            datos.append("buscar_oc", input_oc);

            fetch("<?php echo SERVERURL ?>ajax/compraAjax.php", {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.text())
                .then(respuesta => {
                    let tabla_articulo = document.querySelector('#tabla_OC');
                    tabla_OC.innerHTML = respuesta;
                });
        } else {
            Swal.fire({
                title: 'Ocurrio un error',
                text: 'Debes introducir el Número de OC o el Nombre del Proveedor',
                type: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    function agregar_OC(idcoseleccionado) {
        if (!idcoseleccionado) return;

        // Crear form dinámico para POST
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SERVERURL ?>ajax/compraAjax.php';

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_oc_seleccionado';
        input.value = idcoseleccionado;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit(); // envía y recarga la página
    }
</script>