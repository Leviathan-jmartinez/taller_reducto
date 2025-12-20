<script>
    function abrirModalFactura() {
        $('#modalFactura').modal('show');
    }

    document.addEventListener('keyup', function(e) {
        if (e.target && e.target.id === 'buscarFactura') {

            let texto = e.target.value.trim();
            if (texto.length < 2) return;

            let data = new FormData();
            data.append('buscar_factura', texto);

            fetch('<?php echo SERVERURL ?>ajax/notasCreDeAjax.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.text())
                .then(html => {
                    document.getElementById('resultadoFacturas').innerHTML = html;
                    console.log('buscando factura:', texto);
                });
        }
    });

    function seleccionarFactura(id) {
        let data = new FormData();
        data.append('seleccionar_factura', id);

        fetch('<?php echo SERVERURL ?>ajax/notasCreDeAjax.php', {
                method: 'POST',
                body: data
            })
            .then(() => location.reload());
    }



    function actualizarItem(index) {

        const fila = document.querySelectorAll('#detalle_nota tr')[index];
        if (!fila) return;

        const cantidad = parseFloat(fila.children[2].querySelector('input').value) || 0;
        const precio = parseFloat(fila.children[3].querySelector('input').value) || 0;

        const totalItem = cantidad * precio;
        const totalCell = document.getElementById('total_item_' + index);

        if (totalCell) {
            totalCell.innerText = totalItem.toLocaleString('es-ES');
        }

        let data = new FormData();
        data.append('accion', 'actualizar_item_nc');
        data.append('index', index);
        data.append('cantidad', cantidad);
        data.append('precio', precio);

        fetch('<?php echo SERVERURL ?>ajax/notasCreDeAjax.php', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(resp => {

                if (resp.status !== 'ok') {
                    alert(resp.msg);
                    return;
                }

                /* ===== FILA ===== */
                if (document.getElementById('exenta_' + index))
                    document.getElementById('exenta_' + index).innerText = resp.fila.exenta;

                if (document.getElementById('iva5_' + index))
                    document.getElementById('iva5_' + index).innerText = resp.fila.iva_5;

                if (document.getElementById('iva10_' + index))
                    document.getElementById('iva10_' + index).innerText = resp.fila.iva_10;

                /* ===== TOTALES ===== */
                document.getElementById('subtotal').value = resp.totales.subtotal;
                document.getElementById('iva_5').value = resp.totales.iva_5;
                document.getElementById('iva_10').value = resp.totales.iva_10;
                document.getElementById('total').value = resp.totales.total;
            });
    }
</script>