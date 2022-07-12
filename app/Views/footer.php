<!-- *********************************************************** -->
<!-- container for modal -->
<div id="modal_div">
</div>
<!-- /.container for modal -->
<!-- *********************************************************** -->



<!-- Loading indicator for reevaluate test -->
<div class="spinner-container-with-cancel" style="left: 0%;top:10%;">
  <div class="spinner-sub-container">
    <h3 class="message" style="top: 15%;font-size:16px;"> Generating Result. Please wait...</h3>
    <div class="spinner" style="display: block;margin-top: 17%;">
      <div class="rect1"></div>
      <div class="rect2"></div>
      <div class="rect3"></div>
      <div class="rect4"></div>
      <div class="rect5"></div>
    </div>
    <div id="btn-close-spinner" style="margin-top: 10px;" class="spinner-btn-close">Cancel</div>
  </div>
</div>



</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->





<script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- Datatable JS For Bootstrap 5  -->

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.11.3/af-2.3.7/b-2.0.1/b-colvis-2.0.1/b-html5-2.0.1/b-print-2.0.1/cr-1.5.4/fc-4.0.0/fh-3.2.0/r-2.2.9/sc-2.0.5/sb-1.2.2/datatables.min.js"></script>


<!-- Datepicker -->
<script src="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js'); ?>"></script>

<!-- Select2 JS -->
<script src="<?php echo base_url('assets/plugins/select2-4.0.13/js/select2.min.js'); ?>"></script>

<!-- Toastr JS -->
<script src="<?php echo base_url('assets/plugins/toastr/toastr.min.js'); ?>"></script>


<!-- Snackbar JS -->
<!-- Ref: https://www.polonel.com/snackbar/ -->
<script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>


<!--Start of Tawk.to Script-->
<!-- <script src="<?php echo base_url('assets/tawkto.js'); ?>"></script> -->
<!--End of Tawk.to Script-->


<!-- Jquery HotKeys -->
<!-- Disabled because of bugs found -->
<script src="<?php echo base_url('assets/jquery.jkey.js?v=20220204'); ?>"></script>
<script src="<?php echo base_url('assets/js/hot_keys.js?v=20220204'); ?>"></script>

<!-- Datepick lib -->
<script src="<?php echo base_url('assets/plugins/datepick/picker.js'); ?>"> </script>
<script src="<?php echo base_url('assets/plugins/datepick/picker.date.js'); ?>"> </script>
<script src="<?php echo base_url('assets/plugins/datepick/picker.time.js'); ?>"> </script>

<!-- Awesomplete JS -->
<script src="<?php echo base_url('assets/plugins/awesomplete/awesomplete.js'); ?>" async> </script>

<!-- Angular JS -->
<script src="<?php echo base_url('assets/js/url.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/angular.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
<!-- Common JS -->
<script src="<?php echo base_url('assets/js/common.js?v=20220316'); ?>"></script>

<!-- CKEditor 5  -->
<script src="<?php echo base_url('assets/js/ckeditor.js'); ?>"></script> 
<script src="<?php echo base_url('assets/js/multiselect_dropdown.js'); ?>"></script> 

<script src="https://cdn.syncfusion.com/ej2/20.2.36/ej2-base/dist/global/ej2-base.min.js" type="text/javascript"></script>
<script src="https://cdn.syncfusion.com/ej2/20.2.36/ej2-inputs/dist/global/ej2-inputs.min.js" type="text/javascript"></script> 
<script src="https://cdn.syncfusion.com/ej2/20.2.36/ej2-lists/dist/global/ej2-lists.min.js" type="text/javascript"></script>
<script src="https://cdn.syncfusion.com/ej2/20.2.36/ej2-popups/dist/global/ej2-popups.min.js" type="text/javascript"></script>
<script src="https://cdn.syncfusion.com/ej2/20.2.36/ej2-calendars/dist/global/ej2-calendars.min.js" type="text/javascript"></script>

<script>
  toastr.options = {
    "positionClass": "toast-top-center"
  }
</script>

<script>
  // Editor configuration.
  DecoupledEditor.defaultConfig = {
    toolbar: {
      items: [
        'heading',
        '|',
        'fontFamily',
        'fontSize',
        '|',
        'bold',
        'italic',
        'strikeThrough',
        'underline',
        'blockQuote',
        'code',
        'link',
        '|',
        'alignment',
        '|',
        'bulletedList',
        'numberedList',
        '|',
        'highlight',
        '|',
        'imageUpload',
        '|',
        'insertTable',
        '|',
        'mediaEmbed',
        '|',
        'undo',
        'redo'
      ]
    },
    image: {
      toolbar: [
        'imageStyle:full',
        'imageStyle:side',
        '|',
        'imageTextAlternative'
      ]
    },
    table: {
      contentToolbar: [
        'tableColumn',
        'tableRow',
        'mergeTableCells'
      ]
    },
    colorButton_enable: true,
    // This value must be kept in sync with the language defined in webpack.config.js.
    language: 'en'
  };
</script>


<?php if (isset($graph_script)) : ?>
  <!-- highchart lib -->
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/data.js"></script>
  <script src="https://code.highcharts.com/modules/drilldown.js"></script>

  <!-- highchart export lib -->
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
<?php endif; ?>

<script type="text/javascript">
  <?php if (session()->getFlashdata('toastr_success')) { ?>
    toastr.success("<?php echo session()->getFlashdata('toastr_success'); ?>");
  <?php } else if (session()->getFlashdata('toastr_error')) {  ?>
    toastr.error("<?php echo session()->getFlashdata('toastr_error'); ?>");
  <?php } else if (session()->getFlashdata('toastr_warning')) {  ?>
    toastr.warning("<?php echo session()->getFlashdata('toastr_warning'); ?>");
  <?php } else if (session()->getFlashdata('toastr_info')) {  ?>
    toastr.info("<?php echo session()->getFlashdata('toastr_info'); ?>");
  <?php } ?>
</script>

<script>
  // To initialize tooltip again using method call
  function initializeTooltip() {
    // Ref: https://www.w3schools.com/bootstrap5/bootstrap_tooltip.php
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
  }
  initializeTooltip();
</script>


<script>
  // Change the background color of the body - Dark Mode ON/OFF
  function toggleDarkMode() {
    var element = document.body;
    element.classList.toggle("dark_mode");
  }
</script>

</body>

</html>