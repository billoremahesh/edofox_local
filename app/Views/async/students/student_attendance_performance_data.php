<?php
// Include Service URLs Parameters File
include_once(APPPATH . "Views/service_urls.php");

// Fetch student's data to print at the top of report
$student_name = $student_details['name'];
$student_category = $student_details['caste_category'];
$student_mobile = $student_details['mobile_no'];
$student_rollno = $student_details['roll_no'];
$student_parentno = $student_details['parent_mobile_no'];
$student_previous_marks = $student_details['previous_marks'];
$student_token = $student_details['universal_token'];

$subjectsColorArray = array("#2196f3", "#673ab7", "#009688", "#f44336", "#795548", "#e91e63", "#2196f3", "#673ab7", "#009688", "#f44336", "#795548", "#e91e63");


$data1 = array();
$studentIdObject = array(
    "id" => $student_id,
    "instituteId" => $institute_id,
    "accessType" => $performance_report_type
);
$data["student"] = $studentIdObject;
$data["requestType"] = "ADMIN";

// Date Range Filter
if ($startTime != "" && $endTime != "") {
    $data["startTime"] = $startTime;
    $data["endTime"] = $endTime;
}

$data_string = json_encode($data);
// echo "<p>$data_string</p>";

// Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Username and Password
// curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
// POST ROW data
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string),
    'AuthToken: ' . $student_token
));
// Getting the url from a separate file
curl_setopt($ch, CURLOPT_URL, $fetchStudentPerformanceUrl);
// Execute
$responseDataString = curl_exec($ch);
// Closing
curl_close($ch);

$responseData = json_decode($responseDataString, true);

/*
highlight_string("<?php\n\$data =\n" . var_export($responseData, true) . ";\n?>");
*/
?>



<div class="graph_card p-4" id="performance_analysis_graph"></div>


<div class="graph_card p-4" id="percentwise_performance_analysis_graph"></div>


<div class="graph_card p-4" id="subjectwise_analysis_graph"></div>

<div class="table_div table-responsive">


    <table class="table table-condensed table-hover" id='testsListTable' style="font-size: 14px;">
        <thead style="color: #999">
            <tr>
                <th>id</th>
                <th>#</th>
                <th>Session Name</th>
                <th>Session Date</th>
                <th>Session Time</th>
                <th>Subject</th>
                <th>Classroom</th> 
            </tr>
        </thead>
        <tbody>
            <?php 
            //Defining arrays for charts display
            $test_name_array = array();
            $test_score_array = array();
            $test_total_marks_array = array();
            $test_percent_array = array();
            $all_subjects_array = array();
            $subjectwise_chart_data_array = array();

            $absent_label_text = "<span style='color: red'>ABSENT</span>";

            //Displaying data in table
            foreach ($responseData["exams"] as $key => $test) :
                $test_name = ucwords($test['name']);

                $appeared_on = "";
                if (isset($test['createdDate'])) {
                    $appeared_on =  date("d M Y, H:i A", $test['createdDate'] / 1000);
                } else {
                    $appeared_on =  $absent_label_text;
                }

                $total_marks = "-";
                if (isset($test['totalMarks'])) {
                    $total_marks = $test['totalMarks'];
                }

                $rank = "";

                if (isset($test['rank'])) {
                    if (isset($test['showRank']) && $test['showRank'] == 1) {
                        // Show rank
                        $rank =  $test['rank'];
                        if (isset($test['analysis']) && isset($test['analysis']['studentsAppeared'])) {
                            $rank = $test['rank'] . '/' . $test['analysis']['studentsAppeared'];
                        }
                    } else {
                        // Do not show rank
                        $rank = "-";
                    }
                } else {
                    $rank = $absent_label_text;
                }


                $score = "-";
                $score_text = "-";
                if (isset($test['score'])) {
                    //Pushing in array for charts only when the score exists
                    //That is the student was not absent
                    array_push($test_name_array, $test_name);


                    $score = $test['score'];
                    array_push($test_score_array, $score);
                    array_push($test_total_marks_array, $total_marks);

                    $percent = 0;
                    if ($total_marks != 0) {
                        //To solve the bug of divide by 0 when total marks are 0 for assignments
                        $percent = round($score * 100 / $total_marks, 2);
                    }

                    array_push($test_percent_array, $percent);
                    $score_text = $score . "/" . $total_marks;
                } else {
                    //Student is absent or not attempted the test
                    // array_push($test_score_array, 0);
                    // array_push($test_percent_array, 0);
                    $score_text = $absent_label_text;
                }
                
            ?>
            
            <?php
            endforeach;

            

            /*
highlight_string("<?php\n\$data =\n" . var_export($test_name_array, true) . ";\n?>");
*/
            /*  
highlight_string("<?php\n\$data =\n" . var_export($subjectwise_chart_data_array, true) . ";\n?>");
*/
            ?>

            <?php $sno=0;  foreach($records_table as $key=>$sess_val){ $sno++; ?>
             <tr>
                <td></td>
                <td><?= $key + 1; ?></td>
                <td><?= $sess_val['session_name'] ?></td> 
                <td><?= $sess_val['session_date'] ?></td>
                <td><?= $sess_val['session_time'] ?></td>
                <td><?= $sess_val['session_subject'] ?></td> 
                <td><?= $sess_val['classroom'] ?></td>
              </tr>
              <?php } ?>
        </tbody>
    </table>
</div>





<script>
    // high chart common settings
    Highcharts.setOptions({
        colors: ['#C55A11', '#0070C0', '#555ABE', '#71601C', '#338B8A', '#CA762F', '#AF1A3E', '#D78942', '#ED5E5E', '#B77351', '#FFD301', '#FE0D27', '#3A546A', '#E871FE', '#00FEDE', '#1AFE00'],
        lang: {
            thousandsSep: ','
        }
    });
</script>

<script>
    /**Overall performance charts */
    /*********************** */
    Highcharts.chart('performance_analysis_graph', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Performance'
        },
        xAxis: [{
            categories: <?php echo json_encode(array_reverse($test_name_array)); ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: 'Marks Obtained'
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Marks Obtained',
            data: <?php echo json_encode(array_reverse($test_score_array)); ?>
        }, {
            name: 'Total Test Marks',
            data: <?php echo json_encode(array_reverse($test_total_marks_array)); ?>
        }]
    });


    /**Percentwise performance charts */
    /*********************** */
    Highcharts.chart('percentwise_performance_analysis_graph', {
        title: {
            text: 'Percentwise Performance'
        },
        xAxis: [{
            categories: <?php echo json_encode(array_reverse($test_name_array)); ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: '% Obtained'
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Percentage',
            data: <?php echo json_encode(array_reverse($test_percent_array)); ?>
        }]
    });

    var options = {
        title: {
            text: 'Subjectwise Performance'
        },
        xAxis: [{
            categories: <?php echo json_encode(array_reverse($test_name_array)); ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: 'Marks Obtained'
            }
        },
        credits: {
            enabled: false
        },
        series: [],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    };

    /**Subject wise performance charts */
    /*********************** */
    function search(nameKey, myArray) { //search if score exist for a particular exam
        for (var i = 0; i < myArray.length; i++) {
            if (myArray[i].indexOf(nameKey) == 0) {
                return myArray[i];
            }
        }
    }

    var allTest = <?php echo json_encode(array_reverse($test_name_array)); ?>;
    var subjects = <?php echo json_encode(array_reverse($all_subjects_array)); ?>;
    var scoreArray = <?php echo json_encode(array_reverse($subjectwise_chart_data_array)); ?>;
    for (i = 0, len = subjects.length; i < len; i++) {
        if (options.series.length < len) options.series.push({
            name: '',
            data: ''
        })
        options.series[i].name = subjects[i];
        let subjectArrayScore = scoreArray[subjects[i]];

        let dataArray = [];
        let scoreAdd;
        for (j = 0, len1 = allTest.length; j < len1; j++) {
            scoreAdd = 0;
            var testScoreArray = search(allTest[j], subjectArrayScore);
            if (testScoreArray) {
                var splitScore = testScoreArray.split(':');
                scoreAdd = Number(splitScore[1]);
            }
            dataArray.push(scoreAdd);
        }
        options.series[i].data = dataArray;
    }

    var chart = new Highcharts.Chart('subjectwise_analysis_graph', options);
</script>


<script>
    var student_rollno = "<?= $student_rollno ?>";
    var student_name = "<?= $student_name ?>";
    var student_category = "<?= $student_category ?>";
    var student_parentno = " (Parent) " + "<?= $student_parentno ?>";
    var student_mobile = " (Self) " + "<?= $student_mobile ?>";

    var student_previous_marks = "<?= $student_previous_marks ?>";

    var tableTopData = "<div style='font-size: 14px;'>Roll No: " + student_rollno + " <br>Student's Name: " + student_name + " <br>Category: " + student_category + " <br>Previous Marks: " + student_previous_marks + " <br>Contact Details: " + student_parentno + student_mobile + "</div>";

    var studentListTable = $('#testsListTable').DataTable({
        "lengthChange": false,
        "searching": true,
        "bInfo": false,
        "paging": false,
        "order": [
            [1, "asc"]
        ],
        dom: 'Bfrtip',
        buttons: [{
                extend: 'print',
                exportOptions: {
                    columns: ':visible'
                },
                messageTop: tableTopData,
                customize: function(doc) {
                    $(doc.document.body).find('h1').css('font-size', '15pt');
                    $(doc.document.body).find('h1').css('text-align', 'center');
                    $(doc.document.body).find('h1').css('display', 'none');
                    $(doc.document.body).find('table').css('font-size', '8pt');
                    $(doc.document.body).find('table').addClass('table-bordered');
                    $(doc.document.body).find('td').css('padding', '0pt');
                }
            },
            {
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: 'Toggle Columns'
            }
        ],
        "columnDefs": [{
            "targets": [0],
            "visible": false,
            "searchable": false
        }]
    });


    $("#testsListTable").on("click", "tbody tr", function() {
        var row = studentListTable.row($(this)).data();
        // console.log(row);
        localStorage.setItem("studentId", <?= $student_id ?>);
        localStorage.setItem("testId", row[0]);
        localStorage.setItem("pageAccess", "Temp");
        localStorage.setItem("resultUrl", "Admin");
        var host_urltemp = "<?= HTTPHOST; ?>";
        window.open(host_urltemp + "/result.html", "_blank").focus();
    });
</script>