const formulario_ajax = document.querySelectorAll(".FormularioAjax");

async function solicitarMotivoAnulacion(opciones = {}) {
    const titulo = opciones.titulo || 'Motivo de anulacion';
    const texto = opciones.texto || 'Ingrese una observacion breve para registrar la anulacion.';

    const result = await Swal.fire({
        title: titulo,
        text: texto,
        type: 'question',
        input: 'textarea',
        inputAttributes: {
            autocapitalize: 'sentences',
            maxlength: '255'
        },
        inputPlaceholder: 'Escriba aqui el motivo de anulacion...',
        showCancelButton: true,
        confirmButtonColor: '#008000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!String(value || '').trim()) {
                return 'Debe ingresar la observacion o motivo de anulacion';
            }
            return null;
        }
    });

    if (!result.value) {
        return null;
    }

    return String(result.value).trim();
}

window.solicitarMotivoAnulacion = solicitarMotivoAnulacion;

async function enviar_formulario_ajax(e) {
    if (this.dataset.customSubmit === "true") {
        return;
    }
    e.preventDefault();
    e.stopImmediatePropagation();
    let data = new FormData(this);
    if (e.submitter && e.submitter.name) {
        data.append(e.submitter.name, e.submitter.value);
    }
    let method = this.getAttribute("method");
    let action = this.getAttribute("action");
    let tipo = this.getAttribute("data-form");

    let encabezados = new Headers();
    let config = {
        method: method,
        headers: encabezados,
        mode: 'cors',
        cache: 'no-cache',
        body: data
    }
    let text_alerta;

    if (tipo === "save") {
        text_alerta = "Los datos serán registrados en el sistema";
    } else if (tipo === "delete") {
        text_alerta = "Los datos serán eliminados del sistema";
    } else if (tipo === "update") {
        text_alerta = "Los datos del sistema serán actualizados";
    } else if (tipo === "search") {
        
        fetch(action, config)
            .then(respuesta => respuesta.json())
            .then(respuesta => {
                return alertasAjax(respuesta, this);
            });

        return;
    } else if (tipo === "loans") {
        text_alerta = "Desea remover los datos seleccionados";
    } else {
        text_alerta = "Desea realizar la operación solicitada";
    }
    if (this.dataset.anulacion === "true") {
        const motivo = await solicitarMotivoAnulacion({
            titulo: this.dataset.anulacionTitulo || 'Motivo de anulacion',
            texto: text_alerta
        });

        if (motivo === null) {
            return;
        }

        data.set('observacion_anulacion', motivo);

        fetch(action, config)
            .then(respuesta => respuesta.json())
            .then(respuesta => {
                return alertasAjax(respuesta, this);
            });
        return;
    }

    Swal.fire({
        title: '¿Estas seguro?',
        text: text_alerta,
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#008000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            fetch(action, config)
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alertasAjax(respuesta, this);
                });
        }
    });

}

formulario_ajax.forEach(formularios => {
    formularios.addEventListener("submit", enviar_formulario_ajax);
});

function alertasAjax(alerta, form = null) {
    if (alerta.Alerta === "simple") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.Alerta === "recargar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value) {
                location.reload();
            }
        });
    } else if (alerta.Alerta === "limpiar") {

        if (alerta.PostAccion === "generar_ot_reclamo") {
            Swal.fire({
                title: alerta.Titulo,
                text: 'Diagnostico registrado. El reclamo fue marcado como valido. Desea generar la OT ahora?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#008000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, generar OT',
                cancelButtonText: 'No, despues'
            }).then((confirmacion) => {
                if (confirmacion.value && typeof crearOTReclamo === "function") {
                    setTimeout(() => {
                        crearOTReclamo(alerta.id_diagnostico, alerta.idreclamo_servicio);
                    }, 150);
                    return;
                }

                if (form) {
                    form.reset();
                    document.dispatchEvent(new CustomEvent('ajax:limpiar', {
                        detail: {
                            modulo: form.dataset.modulo || null
                        }
                    }));
                }
            });

            return;
        }

        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value) {
                if (form) {
                    form.reset();

                    // 🔔 aviso genérico
                    document.dispatchEvent(new CustomEvent('ajax:limpiar', {
                        detail: {
                            modulo: form.dataset.modulo || null
                        }
                    }));
                }
            }
        });
    } else if (alerta.Alerta === "redireccionar") {
        window.location.href = alerta.URL;

    } else if (alerta.Alerta === "confirmar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            showCancelButton: true,
            confirmButtonText: 'Sí, revertir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.value) {

                let form = document.querySelector(".FormularioAjax");
                let data = new FormData(form);
                data.append("confirmar_reversion", "1");

                fetch(form.getAttribute("action"), {
                    method: form.getAttribute("method"),
                    body: data
                })
                    .then(r => r.json())
                    .then(resp => alertasAjax(resp));
            }
        });
    } else if (alerta.Alerta === "limpiar") {
        localStorage.removeItem('presupuesto_servicio_tmp');
        document.querySelector(".FormularioAjax").reset();
    } else if (alerta.Alerta === "redireccionar_confirmado") {
        Swal.fire({
            title: alerta.Titulo || "Proceso completado",
            text: alerta.Texto || "",
            type: alerta.Tipo || "success",
            confirmButtonText: "Aceptar"
        }).then(() => {
            window.location.href = alerta.URL;
        });
    }


}
