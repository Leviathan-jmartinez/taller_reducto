<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    /* ================= BUSCAR ARTÍCULOS ================= */
    function buscarArticuloPromo() {
        let txt = document.getElementById('buscar_articulo').value.trim();

        if (txt.length < 2) {
            document.getElementById('resultado_articulos').innerHTML = '';
            return;
        }

        let datos = new FormData();
        datos.append("buscar_articulo", txt);

        fetch(SERVERURL + "ajax/promocionAjax.php", {
                method: "POST",
                body: datos
            })
            .then(r => r.text())
            .then(r => {
                document.getElementById('resultado_articulos').innerHTML = r;
            });
    }


    /* ================= AGREGAR ARTÍCULO ================= */
    function agregarArticuloPromo(id, descripcion) {

        if (document.getElementById('articulo_' + id)) {
            return;
        }

        let li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.id = 'articulo_' + id;

        li.innerHTML = `
        ${descripcion}
        <input type="hidden" name="articulos[]" value="${id}">
        <button type="button"
        class="btn btn-sm btn-danger"
        onclick="quitarArticuloPromo(${id})">
            <i class="fas fa-times"></i>
        </button>
    `;

        document.getElementById('articulos_seleccionados').appendChild(li);

        guardarArticulosPromo(id, descripcion);
    }

    function guardarArticulosPromo(id, descripcion) {

        let articulos = JSON.parse(localStorage.getItem('promo_articulos')) || [];

        // evitar duplicados
        if (articulos.find(a => a.id == id)) {
            return;
        }

        articulos.push({
            id: id,
            descripcion: descripcion
        });

        localStorage.setItem('promo_articulos', JSON.stringify(articulos));
    }

    /* ================= QUITAR ARTÍCULO ================= */
    function quitarArticuloPromo(id) {

        let el = document.getElementById('articulo_' + id);
        if (el) el.remove();

        let articulos = JSON.parse(localStorage.getItem('promo_articulos')) || [];
        articulos = articulos.filter(a => a.id != id);

        localStorage.setItem('promo_articulos', JSON.stringify(articulos));
    }

    function restaurarArticulosPromo() {

        let articulos = JSON.parse(localStorage.getItem('promo_articulos')) || [];

        articulos.forEach(a => {
            agregarArticuloPromo(a.id, a.descripcion);
        });
    }

    window.addEventListener('load', restaurarArticulosPromo);


    document.addEventListener('ajax:limpiar', e => {

        if (e.detail.modulo === 'promociones') {

            // 1. LocalStorage propio
            localStorage.removeItem('promo_articulos');

            // 2. Lista visual
            const lista = document.getElementById('articulos_seleccionados');
            if (lista) lista.innerHTML = '';

            // 3. Buscador
            const buscar = document.getElementById('buscar_articulo');
            if (buscar) buscar.value = '';
        }

    });
</script>