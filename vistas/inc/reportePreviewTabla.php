<div class="container-fluid mt-3">
    <div id="resumenReportePreview" class="row mb-3 d-none"></div>

    <div class="table-responsive">
        <table class="table table-dark table-sm" id="tablaReportePreview">
            <thead class="text-center"></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    (function() {
        const form = document.getElementById("formPreview");
        const btnPdf = document.getElementById("btnPdf");
        const tabla = document.getElementById("tablaReportePreview");
        const resumenBox = document.getElementById("resumenReportePreview");

        if (!form || !btnPdf || !tabla) {
            return;
        }

        const thead = tabla.querySelector("thead");
        const tbody = tabla.querySelector("tbody");

        const estadoMap = {
            pedidos: {
                0: "Anulado",
                1: "Pendiente",
                2: "Procesado"
            },
            presupuestos_compra: {
                0: "Anulado",
                1: "Pendiente",
                2: "OC generada"
            },
            ordenes_compra: {
                0: "Anulado",
                1: "Pendiente",
                2: "Procesado"
            },
            compras: {
                0: "Anulado",
                1: "Activo",
                2: "Procesado"
            },
            libro_compras: {
                0: "Anulado",
                1: "Activo"
            },
            recepcion_servicio: {
                0: "Anulado",
                1: "Recepcionado",
                2: "En proceso",
                3: "Finalizado"
            },
            presupuesto_servicio: {
                0: "Anulado",
                1: "Pendiente",
                2: "Aprobado",
                3: "OT generada",
                4: "Facturado"
            },
            orden_trabajo: {
                0: "Anulada",
                1: "Activa",
                2: "Servicio registrado",
                3: "Pendiente completar"
            },
            registro_servicio: {
                0: "Anulado",
                1: "Registrado",
                2: "Facturado",
                3: "Con Reclamo"
            }
        };

        const formatValue = function(value, key) {
            if (value === null || value === undefined || value === "") {
                return "-";
            }

            if (key === "estado") {
                const modulo = form.querySelector('[name="modulo"]')?.value || "";
                const mapa = estadoMap[modulo] || {};

                if (Object.prototype.hasOwnProperty.call(mapa, value)) {
                    return mapa[value];
                }
            }

            return String(value);
        };

        const resumenLabels = {
            total: "Total",
            registrados: "Registrados",
            facturados: "Facturados",
            con_reclamo: "Con reclamo",
            anulados: "Anulados",
            cantidad_items: "Items",
            cantidad_repuestos: "Repuestos",
            cantidad_insumos: "Insumos",
            total_repuestos: "Total repuestos",
            total_insumos: "Total insumos",
            total_importe: "Importe total",
            promedio_importe: "Promedio"
        };

        const isMoneyKey = function(key) {
            return ["total_importe", "promedio_importe", "total_repuestos", "total_insumos"].indexOf(key) !== -1;
        };

        const formatResumenValue = function(value, key) {
            const numberValue = Number(value || 0);

            if (isMoneyKey(key)) {
                return numberValue.toLocaleString("es-PY", {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            return numberValue.toLocaleString("es-PY");
        };

        const compactColumns = {
            registro_servicio: [{
                    key: "idregistro_servicio",
                    label: "Registro"
                },
                {
                    key: "fecha_ejecucion",
                    label: "Fecha"
                },
                {
                    key: "estado",
                    label: "Estado"
                },
                {
                    key: "cliente",
                    label: "Cliente"
                },
                {
                    key: "vehiculo",
                    label: "Vehiculo"
                },
                {
                    key: "tecnico",
                    label: "Tecnico"
                },
                {
                    key: "cantidad_repuestos",
                    label: "Repuestos"
                },
                {
                    key: "cantidad_insumos",
                    label: "Insumos"
                },
                {
                    key: "total",
                    label: "Total"
                }
            ]
        };

        const getColumns = function(data) {
            const modulo = form.querySelector('[name="modulo"]')?.value || "";

            if (compactColumns[modulo]) {
                return compactColumns[modulo];
            }

            return Object.keys(data[0]).map(function(key) {
                return {
                    key: key,
                    label: key.replace(/_/g, " ").toUpperCase()
                };
            });
        };

        const renderResumen = function(resumen) {
            if (!resumenBox) {
                return;
            }

            const modulo = form.querySelector('[name="modulo"]')?.value || "";

            resumenBox.innerHTML = "";

            if (modulo === "registro_servicio" || !resumen || Object.keys(resumen).length === 0) {
                resumenBox.classList.add("d-none");
                return;
            }

            Object.keys(resumen).forEach(function(key) {
                const col = document.createElement("div");
                col.className = "col-sm-6 col-md-3 col-lg-2 mb-2";

                col.innerHTML =
                    '<div class="alert alert-info mb-0 py-2">' +
                    '<small class="d-block">' + (resumenLabels[key] || key.replace(/_/g, " ")) + '</small>' +
                    '<strong>' + formatResumenValue(resumen[key], key) + '</strong>' +
                    '</div>';

                resumenBox.appendChild(col);
            });

            resumenBox.classList.remove("d-none");
        };

        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const fd = new FormData(form);

            fetch("<?= SERVERURL ?>ajax/reportesAjax.php", {
                    method: "POST",
                    body: fd
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(json) {
                    const data = json.data || [];
                    thead.innerHTML = "";
                    tbody.innerHTML = "";
                    renderResumen(json.resumen || {});

                    if (json.error) {
                        tbody.innerHTML = '<tr><td class="text-center text-danger">' + json.error + '</td></tr>';
                        renderResumen({});
                        btnPdf.classList.add("d-none");
                        return;
                    }

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td class="text-center">Sin registros</td></tr>';
                        btnPdf.classList.remove("d-none");
                        return;
                    }

                    const columns = getColumns(data);
                    const headerRow = document.createElement("tr");

                    columns.forEach(function(column) {
                        const th = document.createElement("th");
                        th.textContent = column.label;
                        headerRow.appendChild(th);
                    });

                    thead.appendChild(headerRow);

                    data.forEach(function(row) {
                        const tr = document.createElement("tr");

                        columns.forEach(function(column) {
                            const td = document.createElement("td");
                            td.textContent = formatValue(row[column.key], column.key);
                            tr.appendChild(td);
                        });

                        tbody.appendChild(tr);
                    });

                    btnPdf.classList.remove("d-none");
                })
                .catch(function() {
                    thead.innerHTML = "";
                    tbody.innerHTML = '<tr><td class="text-center text-danger">No se pudo previsualizar el informe</td></tr>';
                    renderResumen({});
                    btnPdf.classList.add("d-none");
                });
        });

        btnPdf.addEventListener("click", function() {
            const pdfForm = document.createElement("form");
            const fd = new FormData(form);

            pdfForm.action = "<?= SERVERURL ?>ajax/reportesAjax.php";
            pdfForm.method = "POST";
            pdfForm.target = "_blank";
            pdfForm.className = "d-none";

            fd.delete("modulo");
            fd.append("accion", form.dataset.pdfAction);

            fd.forEach(function(value, key) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = key;
                input.value = value;
                pdfForm.appendChild(input);
            });

            document.body.appendChild(pdfForm);
            pdfForm.submit();
            pdfForm.remove();
        });
    })();
</script>
