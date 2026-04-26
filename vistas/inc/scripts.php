   <!--=============================================
	=            Include JavaScript files           =
	==============================================-->
   <!-- jQuery V3.4.1 -->
   <!--<script src="<?php echo SERVERURL; ?>vistas/js/jquery-3.4.1.min.js"></script>-->

   <!-- jQuery V3.6.0 -->
   <script src="<?php echo SERVERURL; ?>vistas/js/jquery-3.6.0.min.js"></script>

   <!-- jQuery V3.6.0 -->
   <script src="<?php echo SERVERURL; ?>vistas/js/select2.min.js"></script>

   <!-- popper -->
   <script src="<?php echo SERVERURL; ?>vistas/js/popper.min.js"></script>

   <!-- Bootstrap V4.3 -->
   <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.min.js"></script>

   <!-- jQuery Custom Content Scroller V3.1.5 -->
   <script src="<?php echo SERVERURL; ?>vistas/js/jquery.mCustomScrollbar.concat.min.js"></script>

   <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.bundle.min.js"></script>

   <!-- Bootstrap Material Design V4.0 -->
   <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap-material-design.min.js"></script>
   <script>
       $(document).ready(function() {
           $('body').bootstrapMaterialDesign();
       });
   </script>

   <script src="<?php echo SERVERURL; ?>vistas/js/main.js"></script>

   <script src="<?php echo SERVERURL; ?>vistas/js/alertas.js"></script>

   <script>
       function activarSelect2(context = document) {

           $(context).find('.select2').each(function() {

               if (!$(this).hasClass("select2-hidden-accessible")) {

                   $(this).select2({
                       width: '100%',
                       placeholder: "Seleccione una opción",
                       allowClear: true
                   });

               }

           });

       }

       // AUTOFOCUS en buscador
       $(document).on('select2:open', () => {
           setTimeout(() => {
               document.querySelector('.select2-container--open .select2-search__field')?.focus();
           }, 0);
       });

       // INIT
       $(document).ready(function() {
           activarSelect2();
       });
   </script>

   <script>
       $(document).on('click', '#btnCancelar', function() {
           let form = $(this).closest('form');

           // Reset del formulario
           form[0].reset();

           // Reset visual de Select2
           form.find('.select2').val(null).trigger('change');
       });
   </script>