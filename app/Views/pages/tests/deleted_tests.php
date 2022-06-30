<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/deleted_test_overview.css?v=20210902'); ?>" rel="stylesheet">


<div class="container-fluid">
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

    <div class="card shadow p-4" id="main_div">
        <table class="table table-condensed table-hover" id='manageTest'>

            <tbody class="manageTestBody">
                <?php
           
                $todays_date = date('d-m-Y H:i:s');
                if (!empty($deleted_tests_data)) :
                    $sr_no = 0;
                    foreach ($deleted_tests_data as $data) :
                        $test_id = encrypt_string($data['test_id']);
                        $test_name = $data['test_name'];
                        $test_duration = $data['duration'];
                        $test_duration_in_hours_min = gmdate("H \h\\r:i \m\i\\n", (int)$test_duration);
                        $start_date = $data['start_date'];
                        $formatted_test_start_date = changeDateTimezone(date("d M Y, h:i A", strtotime($start_date)),"d M Y, h:i A");
                        $test_start_date_only_date = changeDateTimezone(date("d-m-Y H:i:s", strtotime($start_date)),"d M Y, h:i A");
                        $end_date = $data['end_date'];
                        $formatted_test_end_date = changeDateTimezone(date("d M Y, h:i A", strtotime($end_date)),"d M Y, h:i A");
                        $active = null;
                        $non_active_test_styling = 'color: #9E9E9E !important;';
                        if (strtotime($todays_date) >= strtotime($test_start_date_only_date) && strtotime($todays_date) <= strtotime($end_date)) {
                            $active = "(ACTIVE)";
                            $non_active_test_styling = null;
                        }
                        $questionsAddedCount = $data['questionsAddedCount'];
                        $totalQuestionsInTest = $data['no_of_questions'];

                        if ($questionsAddedCount == null) {
                            $questionsAddedCount = 0;
                        }

                        $textStylingForNoOfQuestions = null;
                        if ($questionsAddedCount !== $totalQuestionsInTest) {
                            $textStylingForNoOfQuestions = 'color: #FD1D1D;';
                        } else {
                            $textStylingForNoOfQuestions = 'color: #25D366;';
                        }


                        echo "<tr>";
                        echo "<td><h5 style='font-weight: bold;" . $non_active_test_styling . "'>" . ++$sr_no . ". " . $test_name . $active . "</h5>
                            <p title='" . $test_duration . " Sec'>Duration: " . $test_duration_in_hours_min . " &emsp; Questions Added: <span style='" . $textStylingForNoOfQuestions . "'>" . $questionsAddedCount . "/" . $totalQuestionsInTest . "</span></p>
                                 <span style='font-size:10px; color: #a4a4a4'>" . $formatted_test_start_date . " - " . $formatted_test_end_date . "</span></td>";

                        echo "<td><div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button'
									id='dropdownMenu1' data-bs-toggle='dropdown' aria-haspopup='true' aria-expanded='true'><i class='icon-ellipsis-horizontal' aria-hidden='true'></i></button>
								    <ul class='dropdown-menu dropdown-menu-right' aria-labelledby='dropdownMenu1'>
									<li>
                                    <a class='link-danger p-2' href='" . base_url('tests/unable_deleted_test/'.$test_id) . "'> Undo Delete Test</a>
                                    </li>
							        </ul>
							        </div>
						            </td>";
                        echo "</tr>";
                    endforeach;
                endif;
                ?>
            </tbody>
        </table>
        <div class="text-center">
            <a href="<?= base_url('tests'); ?>"> Manage Tests </a>
        </div>
    </div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>


<script>
    $(document).ready(function() {
        var manageTest = $('#manageTest').DataTable({
            "pageLength": 50,
            "columnDefs": [{
                "targets": [1],
                "orderable": false,
            }],
            "order": [],
            dom: 'Bfrtip',
            //         buttons: [{
            //             extend: 'excel',
            //             exportOptions: {
            //                 columns: [0,1,2,3,4,5,6,7,8]
            //                 }
            //         }]
        });
    });