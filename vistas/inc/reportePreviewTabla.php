<div class="container-fluid mt-3">
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

                    if (json.error) {
                        tbody.innerHTML = '<tr><td class="text-center text-danger">' + json.error + '</td></tr>';
                        btnPdf.classList.add("d-none");
                        return;
                    }

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td class="text-center">Sin registros</td></tr>';
                        btnPdf.classList.remove("d-none");
                        return;
                    }

                    const keys = Object.keys(data[0]);
                    const headerRow = document.createElement("tr");

                    keys.forEach(function(key) {
                        const th = document.createElement("th");
                        th.textContent = key.replace(/_/g, " ").toUpperCase();
                        headerRow.appendChild(th);
                    });

                    thead.appendChild(headerRow);

                    data.forEach(function(row) {
                        const tr = document.createElement("tr");

                        keys.forEach(function(key) {
                            const td = document.createElement("td");
                            td.textContent = formatValue(row[key], key);
                            tr.appendChild(td);
                        });

                        tbody.appendChild(tr);
                    });

                    btnPdf.classList.remove("d-none");
                })
                .catch(function() {
                    thead.innerHTML = "";
                    tbody.innerHTML = '<tr><td class="text-center text-danger">No se pudo previsualizar el informe</td></tr>';
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
