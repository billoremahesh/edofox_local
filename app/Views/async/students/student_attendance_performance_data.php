<?php
// Include Service URLs Parameters File
include_once(APPPATH . "Views/service_urls.php");  

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
   
<div class="graph_card p-4" id="weekly_attendance_report"></div>

<div class="row ">
<div class="col my-1">
    
    <table class="table table-condensed table-hover" id="staffListTable1" style="font-size: 14px;"> 
        <thead>
              <tr>
                <th>id</th>
                <th>#</th>
                <th>Classroom</th>
                <th>Subject</th>
                <th>Attendance</th>
            </tr>
        </thead>
        <body>
            <?php 
            $snos=1;
            foreach($subject_attendanc_present as $key=>$value){ 
                 $absent=0;
                if(in_array($value['subject_id'],$abset_tem)) {
                  $absents = $subject_attend_abs[$value['subject_id']]; 
                  $absent=$absents['present_count'];
                }  
                if(isset($absent) && $absent > 0) {
                    $persn = round(($absent*100)/($value['present_count'] + $absent));
                } else {
                    //As no absent entries found.. student was present 100%
                    $persn = 100;
                }
                

                ?>
            <tr class="<?php if($key % 2 == 0){ echo "odd"; }else{ echo "even"; } ?>" >
                <td><?= $snos; ?></td> <td></td>
                <td><?= $value['package_name'] ?></td>
                <td><?= $value['subject'] ?></td>
                <td class="text-center" ><?php if($persn==100){ ?>
                <i class="fa fa-check" style="color:green;" ></i>
                <?php }else if($persn==0){ ?>
                <i class="fa fa-times" style="color:red;" ></i>
                 <?php }else{ ?>
                  <span style="color:#ff8d00;" ><?php echo $persn.'%'; ?></span>     
                <?php } 
                 ?></td>
            </tr>
            <?php $snos++; } ?>
        </body>
    </table> 
</div>
</div>
<br>
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
                <th>Session Type</th>
                <th>Classroom</th> 
                <th>Subject</th>
                <th>Session Status</th>
            </tr>
        </thead>
        <tbody id="all_data" >
          
            <?php $sno=1;  foreach($reqular_session as $key=>$sess_val){ 
                $ses_date = $sess_val['session_date'];
                $ses_time = $sess_val['session_time'];
                $new_date= date('Y-m-d H:i:s', strtotime("$ses_date $ses_time"));
                ?>
             <tr class="regular" id="regular" >
               
                <td><?= $sno; ?></td> <td></td>
                <td><?= $sess_val['session_name'] ?></td> 
                <td><?= $new_date; ?></td>
                <td>Regular</td>
                <td><?= $sess_val['classroom'] ?></td>
                <td><?= $sess_val['session_subject'] ?></td> 
                <td><?= in_array($sess_val['id'],$attendance_list)?'Present':'Absent'; ?></td>
              </tr>
              <?php $sno++; } ?>

              <?php   foreach($exam as $key=>$sess_val){  ?>
             <tr class="exam" id="exam" >
               
                <td><?= $sno ?></td>
                 <td></td>
                <td><?= $sess_val['session_name'] ?></td> 
                <td><?= $sess_val['session_date'] ?> <?= $sess_val['session_time'] ?></td>
                <td>Exam</td>
                <td><?= $sess_val['classroom'] ?></td>
                <td></td> 
                <td><?php if($sess_val['status']==''){ echo " "; }else{echo $sess_val['status'];} ?></td>
              </tr>
              <?php  $sno++; } ?> 

        </tbody>
    </table>
</div>



 <script>
       Highcharts.chart('weekly_attendance_report', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Weekly Attendance Report'
        },
        xAxis: [{
            categories:<?php echo json_encode(array_reverse($week_date)); ?>,
            crosshair: true
        }],
        yAxis: {
            title: {
                text: 'Weekly Attendance %'
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Weekly Attendance %',
            data: <?php echo json_encode(array_reverse($week_per)); ?>
        }]
    });
 </script>
 

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
                "targets": [6],
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
    console.log(type,'type');  
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







var staffListTable1;

// To reset all the parameters in the datatable which are saved using stateSave
function resetDatatable() {
    // Resetting the filter values
    $("#searchbox").val("");
    localStorage.setItem('staff_datatable_search_value', "");
    staffListTable1.draw();


    // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
    staffListTable1.state.clear(); // 1a - Clear State
    staffListTable1.destroy(); // 1b - Destroy

    setTimeout(function() {
        window.location.reload();
    }, 1000);
}

$(document).ready(function() {

    var staff_datatable_search_value = localStorage.getItem('staff_datatable_search_value');
    $("#searchbox").val(staff_datatable_search_value);


    staffListTable1 = $('#staffListTable1').DataTable({
        stateSave: true,
        "columnDefs": [{
            "targets": [3],
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
        "pageLength": 8,
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
        staffListTable1.search(staff_datatable_search_value).draw();
    }
    $("#searchbox").keyup(function() {
        staffListTable1.search(this.value).draw();
    });

});


function waitForElementToDisplay(selector, time, counter) {
    if (counter > 6) {
        return;
    }
    if (document.querySelector(selector) != null) {
        $("#staffListTable1_filter").prepend($("#staffListTable1ExportGroup"));
        $("#staffListTable1ExportGroup").show();
        return;
    } else {
        setTimeout(function() {
            waitForElementToDisplay(selector, time, counter + 1);
        }, time);
    }
}

$(document).ready(function() {
    waitForElementToDisplay("#staffListTable1_filter", 1000, 1);
});
</script>