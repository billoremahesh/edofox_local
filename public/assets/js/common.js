// function to show add modal
var isModelDialogOpen = false; // it is required for edit show modal also
function show_add_modal(modal_div, modal_id, controller_name) {
  if (isModelDialogOpen == false) {
    isModelDialogOpen = true;
    if (modal_div != "" && modal_id != "" && controller_name != "") {
      $.ajax({
        url: base_url + "/" + controller_name,
        method: "POST",
        data: {},
        success: function (result) {
          $("#" + modal_div).html(result);
          $("#" + modal_id).modal("show");
          isModelDialogOpen = false;
        },
      });
    }
  }
}
// End of function

// function to show edit modal
function show_edit_modal(modal_div, modal_id, controller_name) {
  if (isModelDialogOpen == false) {
    isModelDialogOpen = true;
    if (modal_div != "" && modal_id != "" && controller_name != "") {
      $.ajax({
        url: base_url + "/" + controller_name,
        method: "POST",
        data: {},
        success: function (result) {
          $("#" + modal_div).html(result);
          $("#" + modal_id).modal("show");
          isModelDialogOpen = false;
        },
      });
    }
  }
}
// End of function
function AddModalAsync(modal_div, modal_id, controller_name) {
  if (isModelDialogOpen == false) {
    isModelDialogOpen = true;
    return new Promise(function (resolve, reject) {
      var jsonObjects = {};
      var dataString = JSON.stringify(jsonObjects);
      $.ajax({
        type: "POST",
        data: dataString,
        url: base_url + "/" + controller_name,
        contentType: "application/json",
      })
        .done(function (response) {
          $("#" + modal_div).html(response);
          $("#" + modal_id).modal("show");
          isModelDialogOpen = false;
          resolve("success");
        })
        .fail(function (jqXHR) {
          reject(jqXHR.responseText);
        });
    });
  }
}

function editModalAsync(modal_div, modal_id, controller_name) {
  if (isModelDialogOpen == false) {
    isModelDialogOpen = true;
    return new Promise(function (resolve, reject) {
      var jsonObjects = {};
      var dataString = JSON.stringify(jsonObjects);
      $.ajax({
        type: "POST",
        data: dataString,
        url: base_url + "/" + controller_name,
        contentType: "application/json",
      })
        .done(function (response) {
          $("#" + modal_div).html(response);
          $("#" + modal_id).modal("show");
          isModelDialogOpen = false;
          resolve("success");
        })
        .fail(function (jqXHR) {
          reject(jqXHR.responseText);
        });
    });
  }
}

// Load Subject Chapters
function load_subject_chapters(subject_id) {
  // validate user using a async promise
  return new Promise(function (resolve, reject) {
    var xhr = $.ajax({
      type: "POST",
      data: { subject_id: subject_id },
      url: base_url + "/subjects/load_subject_chapters",
      contentType: "application/json",
    })
      .done(function (response) {
        // success logic here
        resolve(JSON.stringify(response));
        // console.log(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}

//To validate photo / image in modal
function validateFile(file) {
  //Validating file type
  var ext = $(file).val().split(".").pop().toLowerCase();
  if ($.inArray(ext, ["png", "jpg", "jpeg"]) == -1) {
    alert("File type is invalid. Please upload png, jpg, jpeg file only!");
    $(file).val("");
    return;
  }
}

//To validate solution PDF in modal
function validatePDFFile(file) {
  //Validating file type
  var ext = $(file).val().split(".").pop().toLowerCase();
  if ($.inArray(ext, ["pdf"]) == -1) {
    alert("File type is invalid. Please upload PDF file only!");
    $(file).val("");
    return;
  }
}

// Add Multiple file input with remove option

$(document).ready(function () {
  // Add new file structure file_structure_append_div
  $(".add_file_structure").click(function () {
    // Finding total number of file_structure_append_divs added
    var total_file_structure_append_div = $(
      ".file_structure_append_div"
    ).length;

    // last <div> with file_structure_append_div class id
    var lastid = $(".file_structure_append_div:last").attr("id");
    var split_id = lastid.split("_");
    var nextindex = Number(split_id[3]) + 1;

    var max = 10;
    // Check total number file_structure_append_divs
    if (total_file_structure_append_div < max) {
      // Adding new div container after last occurance of file_structure_append_div class
      $(".file_structure_append_div:last").after(
        "<div class='file_structure_append_div' id='file_structure_div_" +
          nextindex +
          "'></div>"
      );

      // Adding file_structure_append_div to <div>
      $("#file_structure_div_" + nextindex).append(
        "<div class='file_structure_append_subdiv row'><div class='col-md-4'><input type='text' class='form-control' placeholder='Enter a file name' name='upload_doc_names[]' id='txt_" +
          nextindex +
          "'></div><div class='col-md-6'><input type='file'  name='upload_documents[]' id='txt_" +
          nextindex +
          "'></div><div class='col-md-2' onclick='remove_structure(" +
          nextindex +
          ")'><span class='action_button_plus_custom'><i id='remove_" +
          nextindex +
          "' class='fas fa-trash remove_file_structure'></i></span></div></div>"
      );
    } else {
      alert("Exceed max number of file structure elements.");
    }
  });

  // Remove file_structure_append_div
  $(".container").on("click", ".remove_file_structure", function () {
    var id = this.id;
    var split_id = id.split("_");
    var deleteindex = split_id[1];
    // Remove <div> with id
    $("#div_" + deleteindex).remove();
  });
});

// Remove file_structure_append_div
function remove_structure(remove_id) {
  $("#file_structure_div_" + remove_id).remove();
}

//Ref: https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie
function createCookie(name, value, hours) {
  var expires;
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime() + hours * 60 * 60 * 1000);
    expires = "; expires=" + date.toGMTString();
  } else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
  // console.log(name + "=" + value + expires + "; path=/");
}

//Getting cookie value
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
}

//remove query param from string
function removeParam(key, sourceURL) {
  var rtn = sourceURL.split("?")[0],
    param,
    params_arr = [],
    queryString = sourceURL.indexOf("?") !== -1 ? sourceURL.split("?")[1] : "";
  if (queryString !== "") {
    params_arr = queryString.split("&");
    for (var i = params_arr.length - 1; i >= 0; i -= 1) {
      param = params_arr[i].split("=")[0];
      if (param === key) {
        params_arr.splice(i, 1);
      }
    }
    rtn = rtn + "?" + params_arr.join("&");
  }
  return rtn;
}

// Function to check/uncheck all perms for add/edit role
function checkAll(formname, checktoggle) {
  var checkboxes = new Array();
  checkboxes = document[formname].getElementsByTagName("input");

  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].type == "checkbox") {
      checkboxes[i].checked = checktoggle;
    }
  }
}
// End of function

// Call the dataTables jQuery plugin
$(document).ready(function () {
  var custom_data_tble = document.getElementById("custom_data_tble");
  if (custom_data_tble != null) {
    $("#custom_data_tble").DataTable({
      order: [0, "asc"],
      dom: "Bflrtip",
      buttons: ["excel"],
      pageLength: 50,
      stateSave: true,
      language: {
        search: "",
      },
    });

    // Moved Datatable Search box and Page length option
    $("#custom_data_tble_filter input").attr("placeholder", "Search");

    $("#dataTables_search_box_div").html($("#custom_data_tble_filter"));
    $("#dataTables_length_div").html($("#custom_data_tble_length"));
    waitForElementToDisplay("#custom_data_tble_filter", 1000, 1);
  }
});

function waitForElementToDisplay(selector, time, counter) {
  if (counter > 6) {
    return;
  }
  if (document.querySelector(selector) != null) {
    $("#custom_data_tble_filter").prepend($("#custom_data_tbleExportGroup"));
    $("#custom_data_tbleExportGroup").show();
    return;
  } else {
    setTimeout(function () {
      waitForElementToDisplay(selector, time, counter + 1);
    }, time);
  }
}

function dtExport(sContainerName, sType) {
  var sButtonName = "";
  switch (sType) {
    case "excel":
      sButtonName = "buttons-excel";
      break;
    case "pdf":
      sButtonName = "buttons-pdf";
      break;
  }

  $("#" + sContainerName + " ." + sButtonName).click();
}

/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the parameters to add to the url
 * @param {string} [method=post] the method to use on the form
 */

function post_form(path, params, method = "post") {
  // The rest of this code assumes you are not using a library.
  // It can be made less verbose if you use one.
  const form = document.createElement("form");
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement("input");
      hiddenField.type = "hidden";
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }

  document.body.appendChild(form);
  // console.log(form);
  form.submit();
}

// Custom Loader
function toggle_custom_loader(state, elementID) {
  if (state) {
    $("#" + elementID).html(
      "<div class='mt-4 text-center'><i class='fas fa-atom fa-spin fa-2x fa-fw'></i><span class='sr-only'>Loading...</span></div>"
    );
  } else {
    $("#" + elementID).html("");
  }
}
var dtt = null;
// Create Datatable Using JS
function createCustomDataTable(
  editedPrintTitle,
  response,
  table_id = "test_student_table"
) {
  if (dtt != null) {
    dtt.destroy();
  }

  dtt = $("#" + table_id).DataTable({
    data: JSON.parse(response.dtRows),
    columns: JSON.parse(response.dtColumns),
    destroy: true,
    columnDefs: [
      {
        targets: 0,
        searchable: true,
        orderable: true,
      },
    ],
    dom: "Blfrtip",
    buttons: [
      {
        extend: "excel",
        exportOptions: {
          columns: ":visible",
        },
        messageTop: editedPrintTitle,
      },
      {
        extend: "print",
        exportOptions: {
          columns: ":visible",
        },
        title: editedPrintTitle,
        customize: function (win) {
          $(win.document.body).find("h1").css("text-align", "center");
          $(win.document.body).css("font-size", "9px");
          $(win.document.body).find("td").css("padding", "0px");
          $(win.document.body).find("td").css("padding-left", "2px");
        },
      },
      {
        extend: "colvis",
        //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
        text: "Toggle Columns",
      },
    ],
    paging: false,
    colReorder: true,
  });
  console.log(dtt);
  console.log("table created ..");
}

// This function is added to make datatable export work for all pages

function newexportaction(e, dt, button, config) {
  var self = this;
  var oldStart = dt.settings()[0]._iDisplayStart;
  dt.one("preXhr", function (e, s, data) {
    // Just this once, load all data from the server...
    data.start = 0;
    data.length = 2147483647;
    dt.one("preDraw", function (e, settings) {
      // Call the original action function
      if (button[0].className.indexOf("buttons-excel") >= 0) {
        $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)
          ? $.fn.dataTable.ext.buttons.excelHtml5.action.call(
              self,
              e,
              dt,
              button,
              config
            )
          : $.fn.dataTable.ext.buttons.excelFlash.action.call(
              self,
              e,
              dt,
              button,
              config
            );
      }
      dt.one("preXhr", function (e, s, data) {
        // DataTables thinks the first item displayed is index 0, but we're not drawing that.
        // Set the property to what it was before exporting.
        settings._iDisplayStart = oldStart;
        data.start = oldStart;
      });
      // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
      setTimeout(dt.ajax.reload, 0);
      // Prevent rendering of the full data to the DOM
      return false;
    });
  });
  // Requery the server with the new one-time export settings
  dt.ajax.reload();
}
var typingTimer; //timer identifier
$(document).ready(function () {
  // Setup before functions for awesomplete_input
  // It is required for search bar link route search
  // https://stackoverflow.com/questions/4220126/run-javascript-function-when-user-finishes-typing-instead-of-on-key-up

  var doneTypingInterval = 500; //time in ms, 1/2 seconds for example

  var awesomplete_input = new Awesomplete(
    document.querySelector("#search_route_links"),
    {
      filter: () => {
        // We will provide a list that is already filtered ...
        return true;
      },
      sort: false, // ... and sorted.
      list: [],
    }
  );

  $("#search_route_links").on("awesomplete-selectcomplete", function () {
    window.location.href = this.value;
  });

  // Global Search Route links based on user input
  $("#search_route_links").on("propertychange input", function (e) {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function () {
      /* Code goes here */
      // Clear search button show
      $(".clear_search_routes").css("display", "block");
      // hide search menu list
      $(".suggestion-search-menu").removeClass("open");
      var search_str = $("#search_route_links").val();
      var request = {
        search: search_str,
      };

      // it shold not be empty
      if (search_str != "") {
        var search_routes_url = base_url + "/linkRoutes/search_routes";
        // Search route with promise
        search_routes(search_routes_url, request)
          .then(function (result) {
            // $("#list_route_links").html(format_search_results(result));
            // $(".suggestion-search-menu").addClass("open");

            var data_result = JSON.parse(result);
            const suggestion_list = [];
            $.each(data_result.items, function (objIndex, obj) {
              var anc_link = {
                label: obj.name,
                value: obj.route_link,
              };
              suggestion_list.push(anc_link);
            });
            awesomplete_input.list = suggestion_list;
          })
          .catch(function (error) {
            // An error occurred
            console.log("Exception: " + error);
          });
      }
    }, doneTypingInterval);
  });
});

// On keydown, clear the countdown
$("#search_route_links").on("keydown", function () {
  clearTimeout(typingTimer);
});

// Clear Search Routes
$(".clear_search_routes").click(function () {
  $("#search_route_links").val("");
  $(".clear_search_routes").css("display", "none");
  // Closing the search results menu
  $(".suggestion-search-menu").removeClass("open");
});

// Search routes
function search_routes(search_routes_url, request) {
  return new Promise(function (resolve, reject) {
    $.ajax({
      type: "POST",
      data: JSON.stringify(request),
      url: search_routes_url,
      contentType: "application/json",
    })
      .done(function (response) {
        // console.log("Response", response);
        resolve(response);
      })
      .fail(function (jqXHR) {
        reject(jqXHR.responseText);
      });
  });
}

// Format Navbar Serach Results
function format_search_results(data) {
  var html = "";
  if (data != null) {
    data = JSON.parse(data);
    $.each(data.items, function (objIndex, obj) {
      html = html + "<li id='" + objIndex + "'>";
      if (obj.route_link != "" && obj.route_link != null) {
        html =
          html +
          "<a class='suggestion_link' href='" +
          obj.route_link +
          "'>" +
          obj.name +
          "</a>";
      } else {
        html =
          html +
          "<span class='dropdown-item-text disabled'>" +
          obj.name +
          "</span>";
      }
      html = html + "</li>";
    });
  }
  return html;
}

function callStudentServiceJSONPost(method, request) {
  return new Promise(function (resolve, reject) {
    get_token()
      .then(function (result) {
        var resp = JSON.parse(result);
        if (
          resp != null &&
          resp.status == 200 &&
          resp.data != null &&
          resp.data.student_token != null
        ) {
          $.ajax({
            url: root + method,
            beforeSend: function (request) {
              request.setRequestHeader("AuthToken", resp.data.student_token);
            },
            type: "post",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify(request),
          })
            .done(function (response) {
              resolve(response);
            })
            .fail(function (jqXHR) {
              // Our error logic here
              reject(jqXHR.responseText);
            });
        } else {
          alert(
            "Some error authenticating your request. Please clear your browser cache and try again."
          );
        }
      })
      .catch(function (error) {
        // An error occurred
        //alert("Exception: " + error);
        alert(
          "Connection error while authenticating your request. Please clear your browser cache and try again."
        );
      });
  });
}

function callAdminServiceJSONPost(method, request) {
  return new Promise(function (resolve, reject) {
    get_admin_token()
      .then(function (result) {
        var resp = JSON.parse(result);
        if (
          resp != null &&
          resp.status == 200 &&
          resp.data != null &&
          resp.data.admin_token != null
        ) {
          $.ajax({
            url: rootAdmin + method,
            beforeSend: function (request) {
              request.setRequestHeader("AuthToken", resp.data.admin_token);
            },
            type: "post",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify(request),
          })
            .done(function (response) {
              resolve(response);
            })
            .fail(function (jqXHR) {
              // Our error logic here
              reject(jqXHR.responseText);
            });
        } else {
          alert(
            "Some error authenticating your request. Please clear your browser cache and try again."
          );
        }
      })
      .catch(function (error) {
        // An error occurred
        //alert("Exception: " + error);
        alert(
          "Connection error while authenticating your request. Please clear your browser cache and try again."
        );
      });
  });
}

function generate_unique_identifier(length) {
  var result = "";
  var characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  var charactersLength = characters.length;
  for (var i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

// Convert miliseconds to hour ,minutes ,seconds
function msToTime(duration) {
  var seconds = Math.floor((duration / 1000) % 60);
  var minutes = Math.floor((duration / (1000 * 60)) % 60);
  var hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

  hours = hours < 10 ? "0" + hours : hours;
  minutes = minutes < 10 ? "0" + minutes : minutes;
  seconds = seconds < 10 ? "0" + seconds : seconds;

  return hours + ":" + minutes + ":" + seconds;
}

function secondsToHms(d){
  d = Number(d);
  var h = Math.floor(d / 3600);
  var m = Math.floor(d % 3600 / 60);
  var s = Math.floor(d % 3600 % 60);

  var hDisplay = h > 0 ? h + (h == 1 ? " hour" : " hours, ") : "";
  var mDisplay = m > 0 ? m + (m == 1 ? " minute " : " minutes, ") : "";
  var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
  return hDisplay + mDisplay + sDisplay; 
}

function getDayName(dateStr, locale) {
  var date = new Date(dateStr);
  return date.toLocaleDateString(locale, { weekday: "long" });
}

function formatAMPM(time) {
  const myArray = time.split(":");
  var hours = myArray[0];
  var minutes = myArray[1];
  var ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  // minutes = minutes < 10 ? "0" + minutes : minutes;
  var strTime = hours + ":" + minutes + " " + ampm;
  return strTime;
}
