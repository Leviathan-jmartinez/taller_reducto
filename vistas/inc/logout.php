<script>
    let btn_salir = document.querySelector(".btn-exit-system");
    btn_salir.addEventListener('click', function(e){
        e.preventDefault();
        Swal.fire({
			title: 'Estas seguro que quieres cerrar la sesión?',
			text: "La sesion actual se cerrará y saldrá del sistema",
			type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si, salir!',
			cancelButtonText: 'No, cancelar'
		}).then((result) => {
			if (result.value) {
				let url ='<?php echo SERVERURL; ?>ajax/loginAjax.php';
                let token = '<?php echo $lc->encryption($_SESSION['token_str'])?>';
                let usuario = '<?php echo $lc->encryption($_SESSION['nick_str'])?>';

                let  datos = new FormData();
                datos.append("token",token);
                datos.append("usuario",usuario);

                fetch(url, {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json())
                .then(respuesta => {
                    return alertasAjax(respuesta);
                });
			}
		});
    });
</script>