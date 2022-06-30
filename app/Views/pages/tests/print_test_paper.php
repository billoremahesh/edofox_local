<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/print_test_paper.css?v=20210915'); ?>" rel="stylesheet">

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



        <div class="row justify-content-center">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="number-of-columns-dropdown" onchange="reloadPaperUpdatedColumns();">
                    <option value="2">Show questions in two columns</option>
                    <option value="1" <?= (session()->get('columns_in_print') == "1") ? "selected" : ""; ?>>Show questions in one column</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="show-options-dropdown" onchange="reloadPaperUpdatedColumns();">
                    <option value="0">Don't Show Options Text below questions</option>
                    <option value="1" <?= (session()->get('show_options_in_paper') == "1") ? "selected" : ""; ?>>Show Options Text below questions</option>
                </select>
            </div>

            <div class="col-md-3">
                <div class="input-group mb-3">
                    <span class="input-group-text" for="no_of_que_per_page">Number of questions per page</span>
                    <select class="form-select" id="no_of_que_per_page" onchange="fetchPaperForPrint();">
                        <option value="">Auto</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>


            <div class="col-12 text-center my-1">
                <div id="loader"><i class='fas fa-atom fa-spin fa-2x fa-fw'></i></div>
            </div>
        </div>
    </div>

    <div class="container mt-4">

        <div id="paper_print_data">

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
    function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontent;

        window.print();
        document.body.innerHTML = restorepage;
    }

    $(document).ready(function() {

        fetchPaperForPrint(2);

    });


    // Fetching the paper to print
    function fetchPaperForPrint() {
        // console.log("fetchPaperForPrint", type);
        var institute_id = <?= json_encode($instituteID) ?>;
        var test_id = <?= json_encode($test_id) ?>;
        var instituteName = <?= json_encode($instituteName) ?>;
        var show_solutions = <?= json_encode($show_solutions) ?>;
        var columns = $("#number-of-columns-dropdown").val();
        var show_options = $("#show-options-dropdown").val();
        var no_of_que_per_page = $("#no_of_que_per_page").val();
        // Clearing the previous data
        $("#paper_print_data").html("");
        $("#paper_print_data").empty();

        // Showing the loader
        $("#loader").show();

        // Calling the API
        $.post(base_url + "/tests/fetch_test_paper_data", {
                institute_id: institute_id,
                test_id: test_id,
                columns: columns,
                instituteName: instituteName,
                show_solutions: show_solutions,
                show_options: show_options,
                no_of_que_per_page: no_of_que_per_page
            },
            function(data) {
                // console.log(data);

                // Setting the data
                // $("#paper_print_data").html(data);
                applyMathJax(data, "paper_print_data");
                // Hiding the loader
                $("#loader").hide();
            });
    }


    // On change of the dropdown, fetching paper with new columns format
    function reloadPaperUpdatedColumns(value) {
        fetchPaperForPrint(value);
    }
</script>