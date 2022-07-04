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

  .z-depth-0 {
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
  }

  .z-depth-1 {
    -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
      0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
  }

  .z-depth-2 {
    -webkit-box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14),
      0 1px 10px 0 rgba(0, 0, 0, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
    box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12),
      0 2px 4px -1px rgba(0, 0, 0, 0.3);
  }

  .z-depth-3 {
    -webkit-box-shadow: 0 8px 17px 2px rgba(0, 0, 0, 0.14),
      0 3px 14px 2px rgba(0, 0, 0, 0.12), 0 5px 5px -3px rgba(0, 0, 0, 0.2);
    box-shadow: 0 8px 17px 2px rgba(0, 0, 0, 0.14),
      0 3px 14px 2px rgba(0, 0, 0, 0.12), 0 5px 5px -3px rgba(0, 0, 0, 0.2);
  }

  .z-depth-4 {
    -webkit-box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14),
      0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -7px rgba(0, 0, 0, 0.2);
    box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14),
      0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -7px rgba(0, 0, 0, 0.2);
  }

  .z-depth-5,
  .modal {
    -webkit-box-shadow: 0 24px 38px 3px rgba(0, 0, 0, 0.14),
      0 9px 46px 8px rgba(0, 0, 0, 0.12), 0 11px 15px -7px rgba(0, 0, 0, 0.2);
    box-shadow: 0 24px 38px 3px rgba(0, 0, 0, 0.14),
      0 9px 46px 8px rgba(0, 0, 0, 0.12), 0 11px 15px -7px rgba(0, 0, 0, 0.2);
  }
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
            <input type="text" id="startDate" placeholder="Select Month" onchange="get_schedule_data()" />
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
                                <thead id="header_date" >
                                  
                                </thead>
                                <tbody>
                                </tbody>
                </table></div>
    </div>
    

    <div >

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
    minViewMode: "months"
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
    var schedule_start_date = $("#startDate").val();
    console.log(schedule_start_date, 'schedule_start_date')
    console.log(classroom_name, 'classroom_name');
    console.log(classroom, 'classroom');

    if (classroom == '' || schedule_start_date == '') {
      toggle_custom_loader(false, "custom_loader");
      $("#error_message").html("<div class='default_card'><h4>Please select the classroom</h4></div>");
    } else {

      localStorage.setItem('schedule_classroom_filter_val', classroom);
      localStorage.setItem('schedule_classroom_filter_text', classroom_name);
      localStorage.setItem('schedule_start_date_filter_val', schedule_start_date);
      jQuery.ajax({
        url: base_url + '/reports/fetch_student_attendance',
        type: 'POST',
        dataType: 'json',
        data: {
          attendance_month: schedule_start_date,
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

<!--- view attendance -->

<script>
  var scheduleId = "";
  var attendance_date = "";
  var institute_id = "";
  console.log(attendance_date);

  $(document).ready(function() {


    $(".select_all_students").click(function() {
      $(".stu_atten_checkbox").each(function() {
        this.checked = true;
      });
      calculate_stu_cnt();
    });

    $(".unselect_all_students").click(function() {
      $(".stu_atten_checkbox").each(function() {
        this.checked = false;
      });
      calculate_stu_cnt();
    });



  });


  $(document).ready(function() {
    // get_attendance_data();
  });

  function search_students() {
    var search_val = $("#student_search_filter").val();
    if (search_val != '') {
      $(".attendance_ckboxs").removeClass("d-block");
      $(".attendance_ckboxs").addClass("d-none");
      $('.attendance_ckboxs:contains(' + search_val + ')').removeClass("d-none");
      $('.attendance_ckboxs:contains(' + search_val + ')').addClass("d-block");
    } else {
      $(".attendance_ckboxs").removeClass("d-none");
      $(".attendance_ckboxs").addClass("d-block");
    }
  }


  function calculate_stu_cnt() {
    console.log("call func");
    // Count of all checkboxes with class my_class
    total_count = $('input.stu_atten_checkbox').length;
    // Count of checkboxes which are checked
    total_checked_count = $('input.stu_atten_checkbox:checked').length;
    $('#present_students_cnt').html(total_checked_count);
    $('#absent_students_cnt').html((total_count - total_checked_count));
    console.log(total_checked_count);
  }
</script>


<script>
  function format_attendance_data(data) { 
    console.log(data,'data'); 
    var html = "";
    if (data != null) {
      var absent_students = 0;
      var present_students = 0;
      colspan = data.classes_schedule.length +1;
      html = html + `<tr style="background-color: #ed4c05 !important;" class="text-white" ><th colspan="`+colspan+`" class="text-center" >STUDENT MONTHLY ATTENDANCE REPORT</th></tr> ` 
      html = html +  ` <tr style="background-color: #f5f7f9;" ><th>Student Name</th>`
      $.each(data.classes_schedule, function(objIndex, obj) { 
        html = html +  `<th class="text-center" >`+obj.Date+`</th> `;
      });
      html = html +  ` </tr> ` 
     
      $.each(data.student, function(objIndex, obj) {
        
        html = html +  ` <tr>` 
        html = html +  `<td>`+obj.name+`<br><span style="font-style: italic;font-size: 12px" >(`+obj.roll_no+`)</span></td>`;
     
            $.each(data.classes_schedule, function(objIndex, classes_obj) {
              if(data.attendance_arr[obj.student_id] !=undefined){ 
              if(data.attendance_arr[obj.student_id][classes_obj.Date]){
                attend_cls = data.attendance_arr[obj.student_id][classes_obj.Date]; 
                per = parseInt(attend_cls.attendance)*100/parseInt(classes_obj.totalSession); 
                html = html + `<td class="text-center" >`+per+`%</td>`;
              }else{
                html = html + `<td class="text-center" >0%</td>`;
              }
              }
            });
          
        html = html +  ` </tr> `
      });
    

      
      // html = html + "<div class='d-flex justify-content-center' style='position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: center;'><button class='btn btn-primary submit_btn' onclick='upload_attendance_submit();' type='button'>Submit</button></div>";

      total_students = data.classes_schedule.length;
      $("#total_students_count").html(total_students);
      $("#present_students_cnt").html(present_students);
      $("#absent_students_cnt").html(absent_students);
    }
    return html;
  }
</script>

<script>
  function upload_attendance_submit() {

    var student_attendance_data_arr = [];
    var outArray = $('.stu_present_ids').toArray();
    outArray.forEach(function(i, key) {
      key = key + 1;
      obj = {};
      var student_id = $(i).val();
      if ($("#stu_present_check_" + student_id).is(":checked")) {
        var present = true;
      } else {
        var present = false;
      }
      obj['id'] = student_id;
      obj['present'] = present;
      student_attendance_data_arr.push(obj);
    })

    // console.log(student_attendance_data_arr);

    var request = {
      institute: {
        id: institute_id
      },
      schedule: {
        id: scheduleId,
        date: attendance_date
      },
      students: student_attendance_data_arr
    };

    callAdminServiceJSONPost("updateAttendance", request).then(function(response) {
        if (response.statusCode > 0) {
          Snackbar.show({
            pos: 'top-center',
            text: 'Attendance added successfully'
          });

          window.location = base_url + "/attendance/overview/" + encrypt_session_id + '/' + attendance_date;
          // window.location.reload();

        } else {
          Snackbar.show({
            pos: 'top-center',
            text: 'Some error occured in fetching student data'
          });
        }
      })
      .catch(function(error) {
        Snackbar.show({
          pos: 'top-center',
          text: 'Error in service call'
        });
      });
  }

  function sendAbsentNotification() {
    if (scheduleDataId == null) {
      alert("Please take attendance first in order to send notifications");
      return;
    }
    var request = {
      schedule: {
        id: scheduleDataId
      },
      requestType: 'AbsentStudent'
    };

    callAdminServiceJSONPost("sendNotification", request).then(function(response) {
        console.log("Response", response);
        if (response != null && response.status.statusCode > 0) {
          Snackbar.show({
            pos: 'top-center',
            text: 'SMS/Email process started successfully'
          });
          //window.location.reload();
        } else {
          Snackbar.show({
            pos: 'top-center',
            text: 'Some error sending SMS/Email. Please try again'
          });
        }
      })
      .catch(function(error) {
        Snackbar.show({
          pos: 'top-center',
          text: 'Could not connect with server.. Please try again'
        });
      });
  }
</script>
<script>
  // monthpicker.js
  "use strict";
  var Monthpicker = (function() {
    function c(a, b) {
      this.selectedMonth = this.selectedYear = this.currentYear = null;
      this.id = c.next_id++;
      c.instances[this.id] = this;
      this.original_input = a;
      this.InitOptions(b);
      this.InitValue();
      this.Init();
      this.RefreshInputs();
    }
    c.Get = function(a) {
      if ("undefined" === typeof a.parentElement.dataset.mp)
        throw "Unable to retrieve the Monthpicker of element " + a;
      return c.instances[a.parentElement.dataset.mp];
    };
    c.prototype.InitValue = function() {
      var a = new Date();
      this.currentYear = a.getFullYear();
      var b = !1;
      this.original_input.value.match("[0-9]{1,2}/[0-9]{4}") &&
        ((b = this.original_input.value.split("/")),
          (this.selectedMonth = parseInt(b[0])),
          (this.currentYear = this.selectedYear = parseInt(b[1])),
          (b = !0));
      this.opts.allowNull ||
        b ||
        ((this.selectedMonth = a.getMonth()),
          (this.selectedYear = a.getFullYear()),
          null !== this.bounds.min.year &&
          (this.selectedYear < this.bounds.min.year ?
            ((this.selectedYear = this.bounds.min.year),
              (this.selectedMonth = this.bounds.min.month ?
                this.bounds.min.month :
                1)) :
            this.selectedYear == this.bounds.min.year &&
            this.selectedMonth < this.bounds.min.month &&
            (this.selectedMonth = this.bounds.min.month)),
          null !== this.bounds.max.year &&
          (this.selectedYear > this.bounds.max.year ?
            ((this.selectedYear = this.bounds.max.year),
              (this.selectedMonth = this.bounds.max.month ?
                this.bounds.max.month :
                12)) :
            this.selectedYear == this.bounds.max.year &&
            this.selectedMonth > this.bounds.max.month &&
            (this.selectedMonth = this.bounds.max.month)),
          (this.currentYear = this.selectedYear));
    };
    c.prototype.InitOptions = function(a) {
      this.opts = c._clone(c.defaultOpts);
      this.MergeOptions(a);
      this.EvaluateOptions();
    };
    c.prototype.UpdateOptions = function(a) {
      this.MergeOptions(a);
      this.EvaluateOptions();
      this.RefreshUI();
    };
    c.prototype.MergeOptions = function(a) {
      if (a)
        for (var b in a) this.opts[b] = a[b];
    };
    c.prototype.EvaluateOptions = function() {
      var a = {
        min: {
          year: null,
          month: null
        },
        max: {
          year: null,
          month: null
        }
      };
      if (null !== this.opts.minValue || null !== this.opts.minYear)
        if (null !== this.opts.minValue && null !== this.opts.minYear) {
          var b = this.opts.minValue.split("/"),
            c = parseInt(this.opts.minYear),
            d = parseInt(b[1]);
          c > d ?
            ((a.min.year = c), (a.min.month = 1)) :
            ((a.min.year = d), (a.min.month = parseInt(b[0])));
        } else
          null !== this.opts.minValue ?
          ((b = this.opts.minValue.split("/")),
            (a.min.year = parseInt(b[1])),
            (a.min.month = parseInt(b[0]))) :
          ((a.min.year = parseInt(this.opts.minYear)), (a.min.month = 1));
      if (null !== this.opts.maxValue || null !== this.opts.maxYear)
        null !== this.opts.maxValue && null !== this.opts.maxYear ?
        ((b = this.opts.maxValue.split("/")),
          (c = parseInt(this.opts.maxYear)),
          (d = parseInt(b[1])),
          c < d ?
          ((a.max.year = c), (a.max.month = 12)) :
          ((a.max.year = d), (a.max.month = parseInt(b[0])))) :
        null !== this.opts.maxValue ?
        ((b = this.opts.maxValue.split("/")),
          (a.max.year = parseInt(b[1])),
          (a.max.month = parseInt(b[0]))) :
        ((a.max.year = parseInt(this.opts.maxYear)), (a.max.month = 12));
      this.bounds = a;
    };
    c.prototype.RefreshInputs = function() {
      this.selectedYear && this.selectedMonth ?
        ((this.original_input.value =
            (10 > this.selectedMonth ?
              "0" + this.selectedMonth :
              this.selectedMonth.toString()) +
            "/" +
            this.selectedYear),
          (this.input.innerHTML =
            this.opts.monthLabels[this.selectedMonth - 1] +
            " " +
            this.selectedYear)) :
        (this.input.innerHTML =
          '<span class="placeholder">' +
          this.original_input.placeholder +
          "</span>");
    };
    c.prototype.RefreshUI = function() {
      this.UpdateCalendarView();
      null !== this.currentYear &&
        (this.year_input.innerHTML = this.currentYear.toString());
      this.UpdateYearSwitches();
    };
    c.prototype.InitIU = function() {
      this.parent = document.createElement("div");
      this.parent.classList.add("monthpicker");
      this.parent.tabIndex = -1;
      var a = getComputedStyle(this.original_input, null);
      /*this.parent.style.width=a.getPropertyValue("width");*/
      "auto" ===
      this.parent.style.width &&
        (this.parent.style.width =
          0 === this.original_input.offsetWidth ?
          "100px" :
          this.original_input.offsetWidth + "px");
      this.original_input.parentElement.insertBefore(
        this.parent,
        this.original_input
      );
      this.parent.appendChild(this.original_input);
      this.original_input.style.display = "none";
      this.input = document.createElement("div");
      this.input.classList.add("monthpicker_input");
      this.input.style.height = a.getPropertyValue("height");
      "auto" === this.input.style.height &&
        (this.input.style.height =
          0 === this.original_input.offsetHeight ?
          "" :
          this.original_input.offsetHeight + "px");
      /*this.input.style.padding=a.getPropertyValue("padding");this.input.style.border=a.getPropertyValue("border");*/
      this.parent.appendChild(
        this.input
      );
      this.selector = document.createElement("div");
      this.selector.classList.add("monthpicker_selector");
      this.selector.style.display = "none";
      for (
        var a =
          "<table><tr><td><span class='yearSwitch down'><i class='fas fa-chevron-left'></i></span></td><td><div class='yearValue'>" +
          this.currentYear +
          "</div> </td><td><span class='yearSwitch up'><i class='fas fa-chevron-right'></i></span> </td></tr> ",
          b = 0; 4 > b; b++
      )
        var c = 3 * b,
          d = this.opts.monthLabels.slice(c, c + 3),
          a =
          a +
          ("<tr><td class='month month" +
            (c + 1) +
            "' data-m='" +
            (c + 1) +
            "'>" +
            d[0] +
            "</td><td class='month month" +
            (c + 2) +
            "' data-m='" +
            (c + 2) +
            "'>" +
            d[1] +
            "</td><td class='month month" +
            (c + 3) +
            "' data-m='" +
            (c + 3) +
            "'>" +
            d[2] +
            "</td></tr>");
      this.selector.innerHTML = a + "</table>";
      this.parent.appendChild(this.selector);
    };
    c.prototype.Init = function() {
      this.InitIU();
      this.year_input = this.selector.querySelector(".yearValue");
      this.parent.dataset.mp = this.id.toString();
      this.parent.addEventListener(
        "focusin",
        function() {
          c.instances[this.dataset.mp].Show();
        },
        !0
      );
      this.parent.addEventListener(
        "focusout",
        function() {
          c.instances[this.dataset.mp].Hide();
        },
        !0
      );
      this.parent
        .querySelector(".yearSwitch.down")
        .addEventListener("click", function() {
          c.instances[this.closest(".monthpicker").dataset.mp].PrevYear();
        });
      this.parent
        .querySelector(".yearSwitch.up")
        .addEventListener("click", function() {
          c.instances[this.closest(".monthpicker").dataset.mp].NextYear();
        });
      for (
        var a = this.parent.querySelectorAll(
            ".monthpicker_selector>table tr:not(:first-child) td.month"
          ),
          b = 0; b < a.length; b++
      )
        a[b].addEventListener("click", function() {
          this.classList.contains("off") ||
            c.instances[this.closest(".monthpicker").dataset.mp].SelectMonth(
              this.dataset.m
            );
        });
    };
    c.prototype.SelectMonth = function(a) {
      var b = parseInt(a);
      if (isNaN(b)) throw "Selected month is not a number : " + a;
      if (1 > b || 12 < b)
        throw "Month is out of range (should be in [1:12], was " + a + ")";
      this.selectedMonth = b;
      this.selectedYear = this.currentYear;
      this.RefreshUI();
      this.RefreshInputs();
      this.ReleaseFocus();
      if (null !== this.opts.onSelect) this.opts.onSelect();
    };
    c.prototype.UpdateCalendarView = function() {
      for (
        var a = this.selector.querySelectorAll(".month"), b = 0; b < a.length; b++
      )
        a[b].classList.remove("selected");
      null !== this.selectedYear &&
        this.currentYear === this.selectedYear &&
        a[this.selectedMonth - 1].classList.add("selected");
      for (b = 0; b < a.length; b++) a[b].classList.remove("off");
      if (
        null !== this.bounds.min.year &&
        this.currentYear <= this.bounds.min.year
      )
        for (b = 1; b < this.bounds.min.month; b++) a[b - 1].classList.add("off");
      if (
        null !== this.bounds.max.year &&
        this.currentYear >= this.bounds.max.year
      )
        for (b = 12; b > this.bounds.max.month; b--)
          a[b - 1].classList.add("off");
    };
    c.prototype.ReleaseFocus = function() {
      this.parent.blur();
    };
    c.prototype.Show = function() {
      this.RefreshUI();
      this.selector.style.display = "block";
    };
    c.prototype.Hide = function() {
      null !== this.selectedYear && (this.currentYear = this.selectedYear);
      this.selector.style.display = "none";
    };
    c.prototype.ShowYear = function(a) {
      this.currentYear = a;
      this.RefreshUI();
    };
    c.prototype.UpdateYearSwitches = function() {
      var a = this.selector.querySelector(".yearSwitch.down"),
        b = this.selector.querySelector(".yearSwitch.up");
      null !== this.bounds.min.year && this.currentYear <= this.bounds.min.year ?
        a.classList.add("off") :
        a.classList.remove("off");
      null !== this.bounds.max.year && this.currentYear >= this.bounds.max.year ?
        b.classList.add("off") :
        b.classList.remove("off");
    };
    c.prototype.PrevYear = function() {
      this.ShowYear(this.currentYear - 1);
    };
    c.prototype.NextYear = function() {
      this.ShowYear(this.currentYear + 1);
    };
    c._clone = function(a) {
      var b;
      if (null == a || "object" != typeof a) return a;
      if (a instanceof Date) return (b = new Date()), b.setTime(a.getTime()), b;
      if (a instanceof Array) {
        b = [];
        for (var e = 0, d = a.length; e < d; e++) b[e] = c._clone(a[e]);
        return b;
      }
      if (a instanceof Object) {
        b = {};
        for (e in a) a.hasOwnProperty(e) && (b[e] = c._clone(a[e]));
        return b;
      }
      throw Error("Unable to copy obj! Its type isn't supported.");
    };
    c.next_id = 1;
    c.instances = [];
    c.defaultOpts = {
      minValue: null,
      minYear: null,
      maxValue: null,
      maxYear: null,
      monthLabels: "Jan Feb Mar Apr May Jun Jui Aug Sep Oct Nov Dec".split(" "),
      onSelect: null,
      onClose: null,
      allowNull: !0
    };
    return c;
  })();
  window.jQuery &&
    (window.jQuery.fn.Monthpicker = function(c, a) {
      var b;
      if ("undefined" === typeof c || "object" === typeof c) b = "ctor";
      else if ("string" === typeof c && "option" === c) b = "option";
      else {
        console.error("Error : Monthpicker - bad argument (1)");
        return;
      }
      window.jQuery(this).each(function(e, d) {
        switch (b) {
          case "ctor":
            "INPUT" != d.tagName ||
              ("text" != d.getAttribute("type") && null !== d.getAttribute("type")) ?
              console.error("Monthpicker must be called on a text input") :
              new Monthpicker(d, c);
            break;
          case "option":
            "INPUT" != d.tagName ||
              ("text" != d.getAttribute("type") && null !== d.getAttribute("type")) ?
              console.error("Monthpicker must be called on a text input") :
              Monthpicker.Get(d).UpdateOptions(a);
        }
      });
    });

  $("#startDate").Monthpicker({
    onSelect: function() {
      get_schedule_data();
    }
  });
</script>