
                </div>
            </div>


            <footer>
                <div class="copyright">Copyrights © 2021 Learning Management System</div>
            </footer>

            
        </div>
    </div> 
</div>  
<!-- Image gallery Modal -->
<div class="modal fade modals-tel-theme" id="gallery_images_popup" role="dialog" >
    <div class="modal-dialog w80 h80"> <!---->
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <!-- <button type="button" class="close" id="closeFooterPopup">&times;</button> -->
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Upload Image</h4>
            </div>
            <div class="modal-body p-0">
                <span id="popup_img_div"></span>
            </div>
        </div>

    </div>
</div>

<style type="text/css">

    .w80{ width: 80%; }
    .h80{ height: 80%; }
    .modal-open .modal{}
    .modal .modal-content{height: 100%;}
    .modal .modal-content .modal-body{height: calc(100% - 50px);}
    .modal .modal-content .modal-body #popup_img_div{height: 100%;}
    .modal .modal-content .modal-body #popup_img_div .row{height: 100%;}
    .modal .modal-content .modal-body #popup_img_div .row .col{height: 100%;}
    .modal .modal-content .modal-body #popup_img_div .row .col .tab-content{height: calc(100% - 50px);}
    .modal .modal-content .modal-body #popup_img_div .row .col .tab-content #allMedia{height: 100%;}
    .modal .modal-content .modal-body #popup_img_div .row .col .tab-content #allMedia #search_div{
        max-height: calc(100% - 90px); padding-top: 0; padding-bottom: 0;
    }

</style>

<script type="text/javascript" src="<?= base_url('public/assets/js/jquery-ui.min-1.12.1.js') ?>"></script> <!-- Load jQueryUI -->
<script type="text/javascript" src="<?= base_url('public/assets/js/bootstrap.min.js') ?>"></script> <!-- Load Bootstrap -->
<script type="text/javascript" src="<?= base_url('public/assets/js/enquire.min.js') ?>"></script> <!-- Load Enquire -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/velocityjs/velocity.min.js') ?>"></script> <!-- Load Velocity for Animated Content -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/velocityjs/velocity.ui.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/wijets/wijets.js') ?>"></script> <!-- Wijet -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/codeprettifier/prettify.js') ?>"></script> <!-- Code Prettifier  -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/bootstrap-switch/bootstrap-switch.js') ?>"></script> <!-- Swith/Toggle Button -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') ?>"></script> <!-- Bootstrap Tabdrop -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/iCheck/icheck.min.js') ?>"></script> <!-- iCheck -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/nanoScroller/js/jquery.nanoscroller.min.js') ?>"></script> <!-- nano scroller -->
<script type="text/javascript" src="<?= base_url('public/assets/js/application.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/demo/demo.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/demo/demo-switcher.js') ?>"></script>
<!-- End loading site level scripts -->
<!-- Load pages level scripts-->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/smartmenus/jquery.smartmenus.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/smartmenus/addons/bootstrap/jquery.smartmenus.bootstrap.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/fullcalendar/moment.min.js') ?>"></script> <!-- Moment.js Dependency -->
<script type="text/javascript" src="<?= base_url('public/assets/plugins/switchery/switchery.js') ?>"></script>

<script src="<?= base_url('public/assets/js/bootstrap-notify.js') ?>"></script>

<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/jquery.dataTables.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/dataTables.bootstrap.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/demo/demo-datatables.js') ?>"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/form-stepy/jquery.stepy.js') ?>"></script>
<!-- Stepy Plugin -->
<script type="text/javascript" src="<?= base_url('public/assets/demo/demo-formwizard.js') ?>"></script>
<script src="<?= base_url('public/assets/js/login.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/progress-skylo/skylo.js') ?>"></script>
<!-- Skylo -->
<script type="text/javascript" src="<?= base_url('public/assets/demo/demo-custom-skylo.js') ?>"></script>
<script type="text/javascript" src="<?php bs('/public/assets/plugins/ckeditor/ckeditor.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="<?php bs() ?>public/assets/js/script.js"></script>
<script>
    $(".scroll-top").on("click", function(){
      $('body,html').animate({
          scrollTop: 0
      }, 500);
    });
    $('#gallery_images_popup').modal({show: false,backdrop: 'static', keyboard: false});
    
</script>
<script type="text/javascript">
var dirty = false;
window.onbeforeunload = function() {
    dirty=$('form').length;   
    if (dirty) {
        return  "Do you want to leave this page without saving?";
    }
}
</script>
</body>
</html>
