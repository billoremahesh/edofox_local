<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/crop_image.css?v=20210915'); ?>" rel="stylesheet">

<script>
    var testId = "<?= $testId; ?>";
    var questionId = "<?= $questionId; ?>";
    var url = "<?= $url ?>";
    console.log("URL is " + url);
</script>

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="container text-center">

            <h3 class="text-center text-muted">Crop image</h3>

            <div id="resizer-demo"></div>

            <button class="btn btn-primary resizer-result">SAVE CROPPED IMAGE</button>

        </div>


        <div id="reviewQuestion" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">

                    <div class="modal-header">
                        <h6 class="modal-title"> Crop successful for </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>


                    <div class="modal-body">
                        <img src="" id="reviewImage" class="img-fluid">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="goBack()">OK</button>

                    </div>
                </div>

            </div>
        </div>

        <div class="spinner-container-with-cancel" style="left: 0%;top:10%;" id="loader">
            <div class="spinner-sub-container">
                <h3 class="message" style="top: 15%;font-size:16px;"> Cropping image .. Please wait...</h3>
                <br>
                <div class="spinner" style="display: block;margin-top: 17%;">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>

            </div>
        </div>

        <!-- Include Footer -->
        <?php include_once(APPPATH . "Views/footer.php"); ?>



        <script src="<?php echo base_url('assets/js/croppie.js'); ?>"></script>
        <script src="<?php echo base_url('assets/js/demo.js?ver=20211008'); ?>"></script>

        <script>
            Demo.init();
        </script>

        <script type="text/javascript">
            //Show loader
            function toggleSpinner(toggle) {
                var x = document.getElementById("loader");
                if (toggle === "show") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            }
        </script>

        <script type="text/javascript">
            function goBack() {
                localStorage.parseQueFocus = questionId;
                window.history.back();
            }
        </script>