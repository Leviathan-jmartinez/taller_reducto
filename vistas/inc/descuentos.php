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
</script>