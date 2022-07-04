<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<style>
  .intervalInput {
    display: inline-block;
    width: 100%;
    margin-bottom: 15px;
  }

  /* monthpicker.css */

  .monthpicker {
    display: inline-block;
    position: relative;
    font-size: 0.9em;
    vertical-align: middle;
    padding: 6px 15px;
    background-color: #fff;
    border: 1px solid #aaa;
    border-radius: 4px;
    width: 100%;
    color: black;
    height: 30px;
  }

  .monthpicker_selector *::selection {
    background: transparent;
  }

  .monthpicker_input {
    display: inline-block;
    width: 100%;
    height: 100%;
    background-color: #fff;
    padding: 0 5px;
    border: none;
    outline: none;
    cursor: pointer;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    color: #212121;
    font-size: 1em;
    line-height: 1em;
  }

  .monthpicker_input .placeholder {
    color: #444;
    font-size: 1rem;
  }

  .monthpicker_input.active {
    background-color: #d4d4d4;
    color: #000;
  }

  .monthpicker_selector {
    padding: 5px;
    position: absolute;
    top: 100%;
    background-color: #fff;
    min-width: 250px;
    -webkit-box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14),
      0 1px 10px 0 rgba(0, 0, 0, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
    box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12),
      0 2px 4px -1px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
    z-index: 100;
    color: #444;
  }

  .monthpicker_selector>table {
    color: #444;
    width: 100%;
    text-align: center;
    border-spacing: 0;
    border-collapse: collapse;
    font-family: "Century Gothic";
    font-size: 0.9em;
    border-radius: 5px;
  }

  .monthpicker_selector>table tr:first-child td,
  .monthpicker_selector>table tr td {
    padding-top: 8px;
    padding-bottom: 8px;
  }

  .monthpicker_selector>table tr:first-child>td:nth-child(1) {
    text-align: left;
  }

  .monthpicker_selector>table tr:first-child>td:nth-child(2) {
    position: relative;
  }

  .monthpicker_selector>table tr:first-child>td:nth-child(3) {
    text-align: right;
  }

  .monthpicker_selector>table tr:nth-child(2) td {
    width: 33%;
  }

  .yearSwitch {
    padding: 7px 10px;
    color: #000;
    font-weight: bold;
    cursor: pointer;
    font-size: 1.2rem;
  }

  .yearSwitch.off {
    visibility: hidden;
  }

  .yearValue {
    width: 100%;
    height: 100%;
    text-align: center;
    background: none;
    border: none;
    color: #444;
    outline: none;
    font-size: 1.8em;
  }

  /* months */
  .monthpicker_selector .month {
    /* border: 1px solid #007bff; */
    background-color: #fff;
    font-size: 0.9rem;
    cursor: pointer;
    color: #212121;
  }

  .monthpicker_selector .month:hover {
    background-color: #ed4c05;
    opacity: 0.7;
    color: #fff;
  }

  .month.selected {
    background: #ed4c05;
    color: #fff;
    font-weight: bold;
    font-size: 0.9rem;
  }

  .monthpicker_selector .month.off {
    color: #fff;
    background: red;
    opacity: 0.65;
    cursor: not-allowed;
  }

  .monthpicker_selector .month.off:hover {
    background: red;
    opacity: 0.65;
  }

  /* .monthpicker_selector>table tr td:first-child {
      border-left: none;
  }
  
  .monthpicker_selector>table tr td:last-child {
      border-right: none;
  }
  
  .monthpicker_selector>table tr:last-child td {
      border-bottom: none;
  } */
</style>
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/attendance/take_attendance.css?v=20220602'); ?>" rel="stylesheet">

<div id="content">
  <div class="container-fluid mt-4">

    <div class="flex-container-column">
      <div>
        <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
      </div>
      <div class="breadcrumb_div" aria-label="breadcrumb">
        <ol class="breadcrumb_custom">
          <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
          <li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('reports'); ?>">Reports</a></li>
          <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
        </ol>
      </div>
    </div>


    <div class="bg-white rounded shadow p-2" style="margin:auto;">

      <div class="justify-content-between my-1">
        <div class="row row-cols-auto justify-content-center my-1">

          <div class="col my-1">
            <select id="classroom_filter" class="form-select classroom_select2_dropdown" onchange="get_schedule_data()">
              <option value=""> Select Classroom</option>
            </select>
          </div>


          <div class="col my-1">
            <input type="text" id="schedule_start_date" placeholder="Select Date" onchange="get_schedule_data()" />
          </div>


          <div class="col my-1">
            <button class="btn btn-secondary btn-sm text-uppercase" onclick="resetFilterData();" data-toggle='tooltip' title='Reset Filters'>
              Reset
            </button>
          </div>

        </div>
      </div>


      <div id="custom_loader"></div>


      <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm" id="staffListTable" width="100%" cellspacing="0">
          <thead id="header_date">

          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>


    <div>

    </div>


  </div>
</div>




<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>




<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
  $("#datepicker").datepicker({
    format: "mm-yyyy",
    viewMode: "months",
    minViewMode: "days"
  });

  var ui_module = "";

  function resetFilterData() {
    localStorage.setItem('schedule_classroom_filter_val', "");
    localStorage.setItem('schedule_classroom_filter_text', "");
    localStorage.setItem('schedule_start_date_filter_val', "");
    localStorage.setItem('schedule_end_date_filter_val', "");
    setTimeout(function() {
      window.location.reload();
    }, 1000);
  }

  $(document).ready(function() {

    var classroom_filter_val = "";
    var classroom_filter_text = "";

    $("#schedule_start_date").flatpickr({
      dateFormat: "Y-m-d",
      onChange: function(selectedDates) {
        $("#schedule_end_date").flatpickr({
          dateFormat: "Y-m-d",
          minDate: new Date(selectedDates),
          maxDate: new Date(selectedDates).fp_incr(6), // add 7 days
        });
      }
    });


    // Optimized classroom dropdown list
    $(".classroom_select2_dropdown").select2({
      ajax: {
        theme: "bootstrap5",
        type: "POST",
        url: base_url + "/classrooms/optimized_classrooms_list",
        // @returns Data to be directly passed into the request.
        data: function(params) {
          var queryParameters = {
            search: params.term, // search term
            page: params.page
          };
          return queryParameters;
        },
        processResults: function(data, params) {
          console.log(data, params);
          params.page = params.page || 1;
          return {
            results: $.map(data.items, function(item) {
              return {
                text: item.name,
                id: item.id,
              };
            }),
            pagination: {
              more: params.page * 30 < data.total_count,
            },
          };
        },
        // The number of milliseconds to wait for the user to stop typing before
        // issuing the ajax request.
        delay: 250,
        dataType: "json",
      }
    });

    if (localStorage.getItem('schedule_end_date_filter_val') != '') {
      $("#schedule_end_date").val(localStorage.getItem('schedule_end_date_filter_val'));
    }

    if (localStorage.getItem('schedule_start_date_filter_val') != '') {
      $("#schedule_start_date").val(localStorage.getItem('schedule_start_date_filter_val'));
    }


    if (localStorage.getItem('schedule_classroom_filter_val') != '') {
      classroom_filter_val = localStorage.getItem('schedule_classroom_filter_val');
      classroom_filter_text = localStorage.getItem('schedule_classroom_filter_text');
      $(".classroom_select2_dropdown").select2("trigger", "select", {
        data: {
          id: classroom_filter_val,
          text: classroom_filter_text
        }
      });
    }

    get_schedule_data();
  });



  function get_schedule_data() {
    $("#error_message").html("");
    toggle_custom_loader(true, "custom_loader");
    var classroom = $("#classroom_filter").val();
    var classroom_name = $("#classroom_filter").select2('data')[0]['text'];
    var schedule_start_date = $("#schedule_start_date").val();
    if (classroom == '' || schedule_start_date == '') {
      toggle_custom_loader(false, "custom_loader");
      $("#error_message").html("<div class='default_card'><h4>Please select the classroom</h4></div>");
    } else {
      localStorage.setItem('schedule_classroom_filter_val', classroom);
      localStorage.setItem('schedule_classroom_filter_text', classroom_name);
      localStorage.setItem('schedule_start_date_filter_val', schedule_start_date);
      jQuery.ajax({
        url: base_url + '/reports/fetch_student_day_attendance',
        type: 'POST',
        dataType: 'json',
        data: {
          attendance_date: schedule_start_date,
          classroom: classroom
        },
        success: function(result) {

          var attendance_data = format_attendance_data(result);
          $("#header_date").html(attendance_data);

          toggle_custom_loader(false, "custom_loader");
          $("#error_message").html("");
        }
      });
    }
  }
</script>



<script>
  function format_attendance_data(data) {
    console.log(data, 'data');
    var html = "";
    if (data != null) {
      var absent_students = 0;
      // var present_students = 0;
      colspan = data.class_sessions.length + 2;
      html = html + `<tr style="background-color: #ed4c05 !important;width:100%;" class="text-white" ><th colspan=` + colspan + ` class="text-center" >STUDENT DAILY ATTENDANCE REPORT</th></tr> `
      html = html + ` <tr style="background-color: #f5f7f9;" ><th style="width: 13%;" >Student Name</th>`;
      if(data.class_sessions.length>0){
      $.each(data.class_sessions, function(objIndex, obj) {
        html = html + `<th class="text-center" >` + obj.title + `</br>` + obj.view_session + `</th> `;
      });
    }else{
      html = html + `<th class="text-center" >No Session</th> `;
    }
      html = html + ` </tr> `

      $.each(data.student, function(objIndex, st_obj) {
        html = html + ` <tr>`
        html = html + `<td style="width: 13%;" >` + st_obj.name + `<br><span style="font-style: italic;font-size: 12px" >(` + st_obj.roll_no + `)</span></td>`;
        if(data.class_sessions.length>0){
        $.each(data.class_sess_check, function(objIndex, obj) {
          if (data.student_attd_session[st_obj.student_id] != undefined) {
            st_attend = data.student_attd_session[st_obj.student_id][obj.id];
            html = html + `<td class="text-center" >` + st_attend.is_present + `</td>`;
          } else {
            html = html + `<td class="text-center" >0</td>`;
          }
        });
      }else{
      html = html + `<th class="text-center" ></th> `;
    }

        html = html + ` </tr> `
      });

    }
    return html;
  }
</script>