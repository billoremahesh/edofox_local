<?php
$sr_no = 1;
$htmlData = '';

foreach ($exams_data as $row) :

    $test_id = $row['test_id'];
    $classroom_name = $row['package_list'];
    $start_date = $row['start_date'];
    $formatted_test_start_date = changeDateTimezone(date("d M Y, h:i A", strtotime($start_date)), "d M Y, h:i A");
    $end_date = $row['end_date'];
    $formatted_test_end_date = changeDateTimezone(date("d M Y, h:i A", strtotime($end_date)), "d M Y, h:i A");
    $htmlData .= '<tr>';
    $htmlData .= '<td>' . $row['test_name'] . '</td>';
    $htmlData .= '<td>' . $classroom_name . '</td>';
    $htmlData .= '<td>' . $formatted_test_start_date . '</td>';
    $htmlData .= '<td>' . $formatted_test_end_date . '</td>';
    $htmlData .= "<td> <input type='checkbox' name='test_ids[]' onclick='selected_test_count();' class='bulk_tests_select' value=" . $row['test_id'] . " > </td>";
    $htmlData .= "</tr>";
    $sr_no++;

endforeach;
echo $htmlData;
