<script>
    function buscarProducto() {
        const q = document.getElementById('buscar_producto').value.trim();

        if (q === '') {
            alert('Ingrese un criterio de búsqueda');
            return;
        }

        const datos = new FormData();
        datos.append('accion', 'buscar_producto');
        datos.append('termino', q);

        fetch('<?= SERVERURL ?>ajax/transferenciaAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(data => {
                let html = '<table class="table table-sm table-hover">';
                html += `
            <thead>
                <tr>
                    <th>Producto</th>
                    <th width="100">Stock</th>
                    <th width="120">Cantidad</th>
                    <th width="80"></th>
                </tr>
            </thead><tbody>
        `;

                if (data.length === 0) {
                    html += `
                <tr>
                    <td colspan="4" class="text-danger text-center">
                        No se encontraron productos
                    </td>
                </tr>
            `;
                } else {
                    data.forEach(p => {
                        html += `
                            <tr>
                                <td>${p.desc_articulo}</td>
                                <td class="text-center">${p.stockDisponible}</td>
                                <td>
                                    <input type="number"
                                        id="cant_${p.id_articulo}"
                                        class="form-control form-control-sm"
                                        min="0.01"
                                        step="0.01">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success"
                                            onclick="agregarProducto(${p.id_articulo}, '${p.desc_articulo}', ${p.stockDisponible})">
                                        +
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                }

                html += '</tbody></table>';
                document.getElementById('resultado_busqueda').innerHTML = html;
            });
    }



    function agregarProducto(id, desc, stock) {
        const input = document.getElementById('cant_' + id);
        const cant = parseFloat(input.value);

        if (!cant || cant <= 0) {
            alert('Cantidad inválida');
            return;
        }

        if (cant > stock) {
            alert('La cantidad supera el stock disponible');
            return;
        }

        // evitar duplicados
        if (document.getElementById('prod_' + id)) {
            alert('El producto ya fue agregado');
            return;
        }

        const tbody = document.getElementById('detalle_productos');
        const tr = document.createElement('tr');
        tr.id = 'prod_' + id;

        tr.innerHTML = `
        <td>${desc}</td>
        <td>
            ${cant}
            <input type="hidden" name="productos[${id}]" value="${cant}">
        </td>
        <td class="text-center">
            <button type="button"
                    class="btn btn-sm btn-danger"
                    onclick="this.closest('tr').remove()">
                ✖
            </button>
        </td>
    `;

        tbody.appendChild(tr);

        document.getElementById('resultado_busqueda').innerHTML = '';
        document.getElementById('buscar_producto').value = '';
    }



    function buscarSucursalDestino() {
        const q = document.getElementById('buscar_sucursal').value.trim();

        if (q.length < 2) {
            document.getElementById('resultado_sucursal').innerHTML = '';
            return;
        }

        const datos = new FormData();
        datos.append('accion', 'buscar_sucursal_destino');
        datos.append('termino', q);

        fetch('<?= SERVERURL ?>ajax/transferenciaAjax.php', {
                method: 'POST',
                body: datos
            })
            .then(res => res.json())
            .then(data => {
                let html = '<ul class="list-group">';
                if (data.length === 0) {
                    html += `
                <li class="list-group-item text-danger">
                    No se encontraron locales
                </li>`;
                } else {
                    data.forEach(s => {
                        html += `
                    <li class="list-group-item list-group-item-action"
                        onclick="seleccionarSucursal(${s.id_sucursal}, '${s.suc_descri}')">
                        ${s.suc_descri}
                    </li>`;
                    });
                }
                html += '</ul>';
                document.getElementById('resultado_sucursal').innerHTML = html;
            });
    }

    function seleccionarSucursal(id, nombre) {
        document.getElementById('sucursal_destino').value = id;
        document.getElementById('buscar_sucursal').value = nombre;
        document.getElementById('resultado_sucursal').innerHTML = '';
    }

    // buscar mientras escribe
    document.getElementById('buscar_sucursal').addEventListener('keyup', buscarSucursalDestino);
</script>