<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";

    function buscarClienteDescuento() {
        let txt = document.getElementById("buscar_cliente").value.trim();

        if (txt.length < 2) {
            document.getElementById("resultado_clientes").innerHTML = "";
            return;
        }

        let datos = new FormData();
        datos.append("buscar_cliente", txt);

        fetch(SERVERURL + "ajax/descuentoAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(html => {
                document.getElementById("resultado_clientes").innerHTML = html;
            });
    }

    function agregarClienteDescuento(id, nombre) {
        let lista = document.getElementById("clientes_asignados");

        if (document.getElementById("cli_" + id)) return;

        let li = document.createElement("li");
        li.className = "list-group-item d-flex justify-content-between align-items-center";
        li.id = "cli_" + id;
        li.innerHTML = `
    ${nombre}
    <input type="hidden" name="clientes[]" value="${id}">
    <button type="button" class="btn btn-sm btn-danger"
        onclick="quitarClienteDescuento(${id})">
        <i class="fas fa-times"></i>
    </button>
    `;
        lista.appendChild(li);
    }

    function quitarClienteDescuento(id) {
        let el = document.getElementById("cli_" + id);
        if (el) el.remove();
    }


    function eliminarClienteDescuento(id_descuento, id_cliente) {

        let formData = new FormData();
        formData.append("accion", "eliminar_cliente_descuento");
        formData.append("id_descuento", id_descuento);
        formData.append("id_cliente", id_cliente);

        fetch("<?= SERVERURL ?>ajax/descuentoAjax.php", {
                method: "POST",
                body: formData
            })
            .then(resp => resp.json())
            .then(data => {

                if (typeof window.alertas_ajax === "function") {
                    window.alertas_ajax(data);
                } else {
                    alert(data.Titulo + ": " + data.Texto);
                }

                if (data.Tipo === "success") {
                    location.reload();
                }
            })
            .catch(err => console.error(err));
    }
</script>