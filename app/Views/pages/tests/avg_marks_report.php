<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/reports'); ?>"> Reports </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4">

            <div class="row">
                <div class="col-6">
                    <select class="form-select form-select-sm classroom_dropdown_multiple" id="classrooms_dropdown_filter" multiple style="width: 100%;" name="test_package_filter">
                        <?php
                        foreach ($classroom_list as $classroom) :
                        ?>
                            <option value="<?= $classroom['id']; ?>"><?= $classroom['package_name']; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>

                <div class="col-2">
                    <button onclick="load_classroom_tests();" class="btn btn-primary">Fetch Tests</button>
                </div>

                <div class="col-2">
                    <button type="button" class="btn btn-success" onclick="generate_tests_result();">
                        Generate Result
                    </button>
                </div>



                <div class="col-12 my-2">
                    <div class="text-center">
                        <p class="badge bg-info fw-bold fs-6" id="total_classrooms">No. of test selected : <span id="no_of_tests">0</span></p>
                    </div>
                </div>


                <div class="col-12 text-left my-2">
                    <div id="test_selected">

                    </div>
                </div>

            </div>

            <!-- Loader Div -->
            <div id="custom_loader"></div>

            <!-- Tests Content -->
            <div class="mt-4 collapse" id="load_classroom_tests_div">

                <table class="table table-condensed table-hover" id='tests_table'>
                    <thead>
                        <tr>
                            <th> Test Name </th>
                            <th> Classrooms </th>
                            <th> Start Date </th>
                            <th> End Date </th>
                            <th style="width:5%;"><input type="checkbox" id="selectAll" /> Select All Visible </th>
                        </tr>
                    </thead>
                    <tbody id="tests_table_tbody">
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 collapse" id="load_tests_results_div">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="test_student_table">
                        <thead></thead>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>




<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    $(document).ready(function() {
        $(".classroom_dropdown_multiple").select2({
            closeOnSelect: true
        });
    });
</script>


<script>
    function load_classroom_tests() {
        toggle_custom_loader(true, "custom_loader");
        var test_filter_parameters = {
            instituteID: "<?= $instituteID ?>",
            classrooms: $('#classrooms_dropdown_filter').val()
        };
        $.ajax({
            url: base_url + "/tests/load_classroom_tests",
            type: 'POST',
            dataType: "html",
            data: JSON.stringify(test_filter_parameters),
            contentType: 'application/json',
            success: function(response) {
                toggle_custom_loader(false, "custom_loader");
                $("#load_classroom_tests_div").collapse("show");
                $("#load_tests_results_div").collapse("hide");
                if ($.fn.DataTable.isDataTable('#tests_table')) {
                    $('#tests_table').dataTable().fnDestroy();
                }
                $("#tests_table_tbody").html(response);
                var tests_table = $('#tests_table').DataTable({
                    "paging": false,
                    "columnDefs": [{
                        "targets": [4],
                        "orderable": false,
                    }],
                    "order": []
                });

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }
</script>



<script>
    // For selecting all blocked students
    $('#selectAll').click(function(e) {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        selected_test_count();
    });
</script>

<script>
    // Selected test count
    function selected_test_count() {
        var checkedValue = [];
        // var inputElements = document.getElementsByClassName('bulk_tests_select');
        // let length = inputElements.length;
        let length = $('.bulk_tests_select:checkbox:checked').length;
        $("#no_of_tests").html(length);
    }
</script>


<script>
    // Generate results for selected tests
    function generate_tests_result() {


        var checkboxes = document.getElementsByClassName('bulk_tests_select');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Select aleast one test.");
        } else {

            // Check selected checkboxes
            var checkedValue = [];
            var inputElements = document.getElementsByClassName('bulk_tests_select');

            for (var i = 0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    checkedValue.push(inputElements[i].value);
                }
            }

            var test_ids = checkedValue.toString();

            toggle_custom_loader(true, "custom_loader");
            get_admin_token().then(function(result) {
                    var resp = JSON.parse(result);
                    if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                        var dataObj = {
                            searchFilter: test_ids,
                            requestType: 'IGNORE_UNATTEMPTED' //ONLY to ignore unattempted tests. By default NULL
                        };
                        dataString = JSON.stringify(dataObj);
                        Snackbar.show({
                            pos: 'top-center',
                            text: 'Generating result... please wait'
                        });

                        // Show Tests selected on Generate result header 
                        $.ajax({
                            type: 'post',
                            url: base_url + '/tests/get_test_names',
                            data: {
                                test_ids: test_ids
                            },
                            success: function(result) {
                                $("#test_selected").html("Average Analysis Result For the tests: " + result);


                                $.ajax({
                                    type: 'POST',
                                    data: dataString,
                                    beforeSend: function(request) {
                                        request.setRequestHeader("AuthToken", resp.data.admin_token);
                                    },
                                    contentType: "application/json",
                                    url: rootAdmin + 'getAverageAnalysis',
                                    success: function(response) {
                                        toggle_custom_loader(false, "custom_loader");
                                        if (response.status.statusCode == 200) {
                                            console.log(response);
                                            $("#load_classroom_tests_div").collapse("hide");
                                            $("#load_tests_results_div").collapse("show");
                                            var export_title = $("#test_selected").html();
                                            createCustomDataTable(export_title, response);
                                        } else {
                                            console.log(response.status.responseText);
                                            Snackbar.show({
                                                pos: 'top-center',
                                                text: response.status.responseText
                                            });
                                        }
                                        selectedQuestion = null;
                                    }
                                });

                            }
                        });


                    } else {
                        alert("Some error authenticating your request. Please clear your browser cache and try again.");
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    console.log("Error: ", error);
                });

        }


    }
</script>