const formulario_ajax = document.querySelectorAll(".FormularioAjax");

function enviar_formulario_ajax(e) {
    if (this.dataset.customSubmit === "true") {
        return;
    }
    e.preventDefault();
    e.stopImmediatePropagation();
    let data = new FormData(this);
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
        text_alerta = "Se eliminará el término de busqueda";
    } else if (tipo === "loans") {
        text_alerta = "Desea remover los datos seleccionados";
    } else {
        text_alerta = "Desea realizar la operación solicitada";
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