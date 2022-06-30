$("#institute_overview_table").DataTable({
  stateSave: true,
});

$("#receipients_div").attr("style", "display:none");

function emailTypeChanged() {
  var type = $("#mail_type").val();
  if (type == "Registered") {
    $("#receipients").val("");
    $("#receipients_div").attr("style", "display:none");
  } else {
    $("#receipients_div").attr("style", "");
  }
}

function titleChanged() {
  $("#email_title_preview").text($("#title").val());
}

function buttonChanged() {
  $("#button_title_preview").text($("#btnTitle").val());
  $("#button_title_preview").attr("href", $("#btnUrl").val());
}

function sendEmail() {
  var content = $("#email_content_preview").html();
  content = content.replaceAll("/super_admin", domain + "/super_admin");

  var instituteObj = null,
    studentObj = null;
  if ($("#institute_id").val() != null && $("#institute_id").val() != "") {
    instituteObj = {
      id: $("#institute_id").val(),
    };
    if (
      $("#new_package_id").val() != null &&
      $("#new_package_id").val() != ""
    ) {
      studentObj = {
        currentPackage: {
          id: $("#new_package_id").val(),
        },
      };
    }
  }

  var request = {
    requestType: "InstituteGenericEmail",
    mailer: {
      mailTitle: $("#title").val(),
      subject: $("#subject").val(),
      actionTitle: $("#btnTitle").val(),
      actionUrl: $("#btnUrl").val(),
      additionalMessage: content,
    },
    student: studentObj,
    institute: instituteObj,
  };

  console.log("Content " + request.mailer.additionalMessage);

  if ($("#receipients").val() != null && $("#receipients").val() != "") {
    request.mailer.mailTo = $("#receipients").val();
  }

  if ($("#bcc_list").val() != null && $("#bcc_list").val() != "") {
    request.mailer.bccList = $("#bcc_list").val();
  }

  console.log("Sending Notifications", request);

  $.ajax({
    url: rootAdmin + "sendNotification",
    type: "post",
    dataType: "json",
    contentType: "application/json",
    success: function (response) {
      console.log("Send response", response);

      if (response.status.statusCode != 200) {
        //$scope.error = response.status.responseText;
        //$("#error").text("Error in creating resource ...");
        alert("Error sending notification...");
      } else {
        alert("Notification process started ...");
      }
    },
    data: JSON.stringify(request),
  });
}

DecoupledEditor.create(document.querySelector(".document-editor__editable"), {
  ckfinder: {
    uploadUrl:
      "ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json",
  },
})
  .then((editor) => {
    const toolbarContainer = document.querySelector(
      ".document-editor__toolbar"
    );
    toolbarContainer.appendChild(editor.ui.view.toolbar.element);
    window.editor = editor;

    editor.model.document.on("change:data", (evt, data) => {
      $("#email_content_preview").html(editor.getData());
    });
  })
  .catch((err) => {
    console.error(err);
  });

// Optimized instutute dropdown list
$(".optimized_institute_dropdown").select2({
  ajax: {
    theme: "bootstrap5",
    type: "POST",
    url: base_url + "/institutes/optimized_institute_list",
    // @returns Data to be directly passed into the request.
    data: function (params) {
      var queryParameters = {
        search: params.term, // search term
        page: params.page,
      };
      return queryParameters;
    },
    processResults: function (data, params) {
      console.log(data, params);
      params.page = params.page || 1;
      return {
        results: $.map(data.items, function (item) {
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
  },
  minimumInputLength: 3,
});

$(".optimized_institute_dropdown").change(function () {
  var institute_id = $("#institute_id").val();

  if (institute_id != "") {
    $.ajax({
      url: base_url + "/institutes/institute_classrooms/" + institute_id,
      method: "GET",
      success: function (result) {
        $("#new_package_id").html(format_classroom(result));
      },
    });
  }
});


// Format Classroom
function format_classroom(result) {
  var html = "";
  if (result != null && result.length > 0) {
    last_record = result.length;
    html = html + "<option></option>";
    result.forEach(function (q) {
      html = html + "<option id='" + q.id + "'>" + q.package_name + "</option>";
    });
  } else {
    html = html + "<option></option>";
  }

  return html;
}
