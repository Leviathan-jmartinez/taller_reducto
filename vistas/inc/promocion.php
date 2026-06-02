<script>
    const SERVERURL = "<?php echo SERVERURL; ?>";
    const PROMOCION_STORAGE_BASE = 'promo_articulos';

    function storageKeyPromo() {
        const modo = window.PROMOCION_FORM_MODO || 'crear';
        const id = window.PROMOCION_FORM_ID || 'nuevo';
        return modo === 'editar' ? `${PROMOCION_STORAGE_BASE}_editar_${id}` : `${PROMOCION_STORAGE_BASE}_crear`;
    }

    function articulosPromoGuardados() {
        return JSON.parse(localStorage.getItem(storageKeyPromo())) || [];
    }

    function guardarListaArticulosPromo(articulos) {
        localStorage.setItem(storageKeyPromo(), JSON.stringify(articulos));
    }

    function sincronizarArticulosPromoDesdeVista() {
        const lista = document.getElementById('articulos_seleccionados');
        if (!lista) return;

        const articulos = Array.from(lista.querySelectorAll('li')).map(li => {
            const input = li.querySelector('input[name="articulos[]"]');
            return {
                id: input ? input.value : '',
                descripcion: (li.firstChild ? li.firstChild.textContent : '').trim()
            };
        }).filter(a => a.id);

        guardarListaArticulosPromo(articulos);
    }

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

        let articulos = articulosPromoGuardados();

        // evitar duplicados
        if (articulos.find(a => a.id == id)) {
            return;
        }

        articulos.push({
            id: id,
            descripcion: descripcion
        });

        guardarListaArticulosPromo(articulos);
    }

    /* ================= QUITAR ARTÍCULO ================= */
    function quitarArticuloPromo(id) {

        let el = document.getElementById('articulo_' + id);
        if (el) el.remove();

        let articulos = articulosPromoGuardados();
        articulos = articulos.filter(a => a.id != id);

        guardarListaArticulosPromo(articulos);
    }

    function restaurarArticulosPromo() {
        localStorage.removeItem(PROMOCION_STORAGE_BASE);

        if (window.PROMOCION_FORM_MODO === 'editar') {
            sincronizarArticulosPromoDesdeVista();
        }

        let articulos = articulosPromoGuardados();

        articulos.forEach(a => {
            agregarArticuloPromo(a.id, a.descripcion);
        });
    }

    window.addEventListener('load', restaurarArticulosPromo);


    document.addEventListener('ajax:limpiar', e => {

        if (e.detail.modulo === 'promociones') {

            // 1. LocalStorage propio
            localStorage.removeItem(PROMOCION_STORAGE_BASE);
            localStorage.removeItem(storageKeyPromo());

            // 2. Lista visual
            const lista = document.getElementById('articulos_seleccionados');
            if (lista) lista.innerHTML = '';

            // 3. Buscador
            const buscar = document.getElementById('buscar_articulo');
            if (buscar) buscar.value = '';
        }

    });
</script>
