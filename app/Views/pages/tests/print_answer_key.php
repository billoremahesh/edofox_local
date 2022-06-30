<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/print_answer_key.css?v=20220527'); ?>" rel="stylesheet">

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

        <div class="card p-4">

            <div class="row">
                <div class="col-4 offset-4 text-center">
                    <div class="mb-2">
                        <label class="form-label" for="columns_in_row_to_display">Select columns in each row: </label>
                        <select id="columns_in_row_to_display">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option selected>8</option>
                        </select>
                    </div>
                </div>
                <div class="col-4" style="text-align: right;">
                    <a href="<?= base_url('/tests/add_answer_key/' . $test_id) ?>" class="btn btn-primary">Add Answer Key</a>
                </div>
            </div>
            <div class="row">
                <div id="printable_area">
                    <div class="text-center">
                        <h4><?= $test_details['test_name']; ?></h4>
                    </div>
                    <?php
                    $test_start_date = $test_details['start_date'];
                    $formatted_test_start_date = date("d/m/Y", strtotime(changeDateTimezone($test_start_date)));
                    ?>
                    <h6 class="text-center">Answer Key (<?= $formatted_test_start_date ?>)</h6>
                    <div class="text-center" id="key_loading_div" hidden>
                        <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
                    </div>
                    <div id="answer-key-table"></div>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary text-uppercase hidden" id="print_button" onclick="printContent('printable_area')">Print</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script>
    MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ]
        },
        startup: {
            ready: function() {
                MathJax.startup.defaultReady();
                document.getElementById('render').disabled = false;
            }
        }
    }
</script>
<script id="MathJax-script" defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
        function applyMathJax(input, spanId) {
            output = document.getElementById(spanId);
            if (output == null) {
                // console.log("returning .." + spanId);
                return;
            }
            if (input == null || input.trim().length == 0) {
                output.innerHTML = "";
                return;
            }
            output.innerHTML = input;

            console.log("Reset math jax ..");
            MathJax.texReset();
            MathJax.typesetClear();
            MathJax.typesetPromise()
                .catch(function(err) {
                    console.log("Error -- " + err.message);
                })
                .then(function() {
                    // console.log("Done adding to == > " + spanId);
                });
        }
</script>

<script>
    $(document).ready(function() {
        var test_id = "<?= $test_id ?>";
        var columns_to_display = $("#columns_in_row_to_display").val();
        getAnswerKeyData(columns_to_display, test_id);

        $('#columns_in_row_to_display').change(function() {
            columns_to_display = $("#columns_in_row_to_display").val()
            getAnswerKeyData(columns_to_display, test_id);
        });

    });
</script>

<script>
    //To get answer key data from ajax file for dynamic row setting
    function getAnswerKeyData(columns, test_id) {
        $("#key_loading_div").show();
        $("#print_button").addClass("hidden");
        $("#answer-key-table").html("");

        $.get(base_url + "/tests/ajax_get_answer_key_print", {
            columns: columns,
            test_id: test_id
        }, function(data) {
            // console.log("response", data);
            $("#key_loading_div").hide();
            $("#print_button").removeClass("hidden");

            // $("#answer-key-table").html(data);
            applyMathJax(data, "answer-key-table");
            //To make the table columns equal width based on total table width which is 100%
            $("#answer-key-table td").css("width", 100 / columns + "%");

        });
    }


    //To print the answer key
    function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
    }
</script>