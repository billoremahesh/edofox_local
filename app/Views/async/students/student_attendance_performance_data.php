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

<style> 

form {
  width:100%;
  box-sizing:border-box;
  background-color:white;
  box-shadow: 0px 1px 2px 1px rgba(0, 0, 0, 0.4);
  text-align:center;
  position:relative;
  border-radius:2px;
}

form > div {
  color:white;
  padding-top:24px;
  display:block;
  position:absolute;
  top:-4px;
  left:-4px;
  bottom:-4px;
  width:calc(33.33% + 8px);
  background-color:#ed4c05;
  border-radius:2px;
  box-shadow: 0px 1px 2px 1px rgba(0, 0, 0, 0.4);
  z-index:1;
  pointer-events:none;
  transition:transform 0.3s;
}

form::after {
  content:"";
  display:block;
  clear:both;
}

form label {
  float:left;
  width:calc(33.333% - 1px);
  position:relative;
  padding:0px;
  height: 30px;
  line-height: 30px;
  overflow:hidden;
  border-left:solid 1px rgba(0,0,0,0.2);
  transition:color 0.3s;
  cursor:pointer;
  -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
}

form label:first-child {
  border-left:none;
}

form label input {
  position:absolute;
  top:-200%;
}

form label div {
  z-index: 5;
  position: absolute;
  width: 100%;
}

form label.selected {
  color:white;
}
</style>
 

<div class="row ">

<div class="col my-1">
</div>
<div class="col my-1">
</div>

<div class="col my-1">
</div>
<div class="col my-1">
</div>
<div class="col my-1">
<form action="" id="searchTypeToggle">
  <div></div>
  <label class="selected">
    <input type="radio" name="searchtype" onclick="check_selected('all')" data-location="0" value="all">
    <div>All</div>
  </label>
  <label>
    <input type="radio" name="searchtype" onclick="check_selected('regular')" data-location="calc(100% - 8px)" value="regular">
    <div>Regular</div>
  </label>
  <label>
    <input type="radio" name="searchtype" onclick="check_selected('exam')" data-location="calc(200% - 12px)" value="exam">
    <div>Exam</div>
  </label> 
</form>
</div></div>


<div class="table_div table-responsive">


    <table class="table table-condensed table-hover" id='staffListTable' style="font-size: 14px;">
        <thead style="color: #999">
            <tr>
                <th>id</th>
                <th>#</th>
                <th>Session Name</th>
                <th>Session Date</th>
                <th>Session Time</th>
                <th>Session Type</th>
                <th>Classroom</th> 
                <th>Subject</th>
            </tr>
        </thead>
        <tbody>
          
            <?php $sno=1;  foreach($reqular_session as $key=>$sess_val){  ?>
             <tr class="regular" >
               
                <td><?= $sno; ?></td> <td></td>
                <td><?= $sess_val['session_name'] ?></td> 
                <td><?= $sess_val['session_date'] ?></td>
                <td><?= $sess_val['session_time'] ?></td>
                <td>Regular</td>
                <td><?= $sess_val['classroom'] ?></td>
                <td><?= $sess_val['session_subject'] ?></td> 
              </tr>
              <?php $sno++; } ?>

              <?php   foreach($exam as $key=>$sess_val){  ?>
             <tr class="exam" >
               
                <td><?= $sno ?></td>
                 <td></td>
                <td><?= $sess_val['session_name'] ?></td> 
                <td><?= $sess_val['session_date'] ?></td>
                <td><?= $sess_val['session_time'] ?></td>
                <td>Exam</td>
                <td><?= $sess_val['classroom'] ?></td>
                <td></td> 
              </tr>
              <?php  $sno++; } ?> 

        </tbody>
    </table>
</div>



 
 

<script>
    var staffListTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        localStorage.setItem('staff_datatable_search_value', "");
        staffListTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        staffListTable.state.clear(); // 1a - Clear State
        staffListTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var staff_datatable_search_value = localStorage.getItem('staff_datatable_search_value');
        $("#searchbox").val(staff_datatable_search_value);


        staffListTable = $('#staffListTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [7],
                "orderable": false,
            }, {
                "targets": -1,
                "class": 'btn_col'
            }],
            "order": [
                [0, "asc"]
            ],
            dom: 'Bflrtip',
            buttons: [{
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }, {
                "extend": 'excel',
                "titleAttr": 'Excel',
                // not_export class is used to hide excel columns. 
                "exportOptions": {
                    "columns": ':visible:not(.not_to_export)'
                },
                messageTop: "Attendance Report"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Attendance Report",
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).css('font-size', '9px');
                    $(win.document.body).find('td').css('padding', '0px');
                    $(win.document.body).find('td').css('padding-left', '2px');
                }
            }],
            "searching": true,
            "paging": true,
            "pageLength": 20,
            "bLengthChange": false,
            "bInfo": false,
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            stateSaveCallback: function(settings, data) {
                if (data != null && data.search != null && data.search.search != null) {
                    localStorage.setItem('staff_datatable_search_value', data.search.search);
                }else{
                    localStorage.setItem('staff_datatable_search_value', "");
                }
            }
        });

        if (staff_datatable_search_value != '' && staff_datatable_search_value != null) {
            staffListTable.search(staff_datatable_search_value).draw();
        }
        $("#searchbox").keyup(function() {
            staffListTable.search(this.value).draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#staffListTable_filter").prepend($("#staffListTableExportGroup"));
            $("#staffListTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#staffListTable_filter", 1000, 1);
    });


    $(document).ready(function(event){
  $('form input').click(function(event){
    $('form > div').css('transform', 'translateX('+$(this).data('location')+')');
    $(this).parent().siblings().removeClass('selected');
    $(this).parent().addClass('selected');
  });
});


function check_selected(type){ 
   if(type=='exam'){
    $(".regular ").hide();
    $(".exam").show();
   }else if(type=='regular'){
    $(".regular ").show();
    $(".exam").hide();
   }else if(type=='all'){
    $(".regular ").show();
    $(".exam").show();
   }
}
</script>