//To fetch chapters for the selected subject
function fetchSubjectChapters(value) {
  // console.log("fetchSubjectChapters " + value);

  $("#subject-loading-div").show();
  $("#chapters-dropdown-div").html("");

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      $("#chapters-dropdown-div").html(this.responseText);
      $("#subject-loading-div").hide();
      $(".js-example-basic-single").select2();
    } else {
      // console.log(this.responseText);
    }
  };
  xmlhttp.open(
    "GET",
    "test_operations/ajax_doubts_fetch_subject_chapters.php?subjectId=" + value,
    true
  );
  xmlhttp.send();
}

//Fetching doubts counts for showing on the tabs
function fetchDoubtsCounts(studentId, instituteId) {
  // console.log("fetchDoubtsCounts " + studentId + "-" + instituteId);

  fetchDifferentDoubtCounts(studentId, instituteId, "unresolved");
  fetchDifferentDoubtCounts(studentId, instituteId, "resolved");
}

//Fetching doubt counts based on resolved or unresolved
function fetchDifferentDoubtCounts(studentId, instituteId, type) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (type === "resolved") {
        $("#resolved-doubts-count").html(this.responseText);
      }

      if (type === "unresolved") {
        $("#unresolved-doubts-count").html(this.responseText);
      }
    } else {
      // console.log(this.responseText);
    }
  };
  xmlhttp.open(
    "GET",
    "test_operations/ajax_doubts_fetch_counts.php?studentId=" +
      studentId +
      "&instituteId=" +
      instituteId +
      "&countType=" +
      type,
    true
  );
  xmlhttp.send();
}

//To fetch all resolved/unresolved doubts records
function fetchDoubts(studentId, instituteId, type) {
  // console.log("fetchDoubts " + studentId + "-" + instituteId+ "-" + type);

  $("#resolved-doubts-list-loading-div").show();
  $("#unresolved-doubts-list-loading-div").show();

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (type === "resolved") {
        $("#resolved-doubts-table-list").html(this.responseText);
        $("#resolved-doubts-list-loading-div").hide();

        //To enable datatable features like search
        $("#cleared_doubts_table").DataTable();
      }

      if (type === "unresolved") {
        $("#unresolved-doubts-table-list").html(this.responseText);
        $("#unresolved-doubts-list-loading-div").hide();

        //To enable datatable features like search
        $("#pending_doubts_table").DataTable();
      }
    } else {
      // console.log(this.responseText);
    }
  };

  xmlhttp.open("POST", "test_operations/ajax_doubts_fetch_doubts.php", true);
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send(
    "studentId=" +
      studentId +
      "&instituteId=" +
      instituteId +
      "&doubtType=" +
      type
  );
}

/**************************************************** */
/**************************************************** */
/**************************************************** */

//Teacher side functions

/**************************************************** */
/**************************************************** */
/**************************************************** */

//Fetching all chapters in the institute for the subject
function fetchAllChapters(subjectId, instituteId, listButton) {
  $("#chapters-loading-div").show();
  $.get(
    "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
    { subjectId: subjectId, instituteId: instituteId },
    function (data) {
      $("#chapters-loading-div").hide();
      $("#doubts-chapters-list-div").html(data);
      $(listButton).addClass("active");
      $(listButton).siblings().removeClass("active");
    }
  );
}

//Fetching doubt counts based on chapter
function fetchChapterwiseDoubtCount(chapterId, chapterWise = true) {
  // console.log("fetchChapterwiseDoubtCount " + chapterId);

  if (chapterWise) {
    //We want to fetch count chapterwise

    //Show loading
    $("#unresolved-count-loading-div-" + chapterId).show();
    $("#resolved-count-loading-div-" + chapterId).show();

    $.get(
      "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
      { chapterId: chapterId, fetchType: "unresolvedDoubtCount" },
      function (data) {
        $("#unresolved-count-loading-div-" + chapterId).hide();
        $("#unresolved-badge-" + chapterId).show();
        $("#unresolved-badge-" + chapterId).html(data);
      }
    );

    $.get(
      "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
      { chapterId: chapterId, fetchType: "resolvedDoubtCount" },
      function (data) {
        $("#resolved-count-loading-div-" + chapterId).hide();
        $("#resolved-badge-" + chapterId).show();
        $("#resolved-badge-" + chapterId).html(data);
      }
    );
  } else {
    //We want to fetch total counts of the subject
    //Here chapterId will be subject Id

    //Show loading
    $("#unresolved-count-loading-div-0").show();
    $("#resolved-count-loading-div-0").show();

    $.get(
      "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
      { subjectId: chapterId, fetchType: "unresolvedDoubtCount" },
      function (data) {
        $("#unresolved-count-loading-div-0").hide();
        $("#unresolved-badge-0").show();
        $("#unresolved-badge-0").html(data);
      }
    );

    $.get(
      "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
      { subjectId: chapterId, fetchType: "resolvedDoubtCount" },
      function (data) {
        $("#resolved-count-loading-div-0").hide();
        $("#resolved-badge-0").show();
        $("#resolved-badge-0").html(data);
      }
    );
  }
}

//Fetching doubts counts for showing on the tabs
function fetchFilteredDoubtsCount(fetchId, filter, instituteId) {
  // console.log("fetchFilteredDoubtsCount " + fetchId + "-" + instituteId);

  fetchDifferentChapterDoubtsCount(fetchId, filter, instituteId, "unresolved");
  fetchDifferentChapterDoubtsCount(fetchId, filter, instituteId, "resolved");
}

//Fetching doubt counts based on resolved or unresolved
function fetchDifferentChapterDoubtsCount(fetchId, filter, instituteId, type) {
  // console.log("fetchDifferentChapterDoubtsCount " + fetchId + "-" + instituteId + "-" + type);

  $.get(
    "test_operations/ajax_doubts_teacher_fetch_subject_chapters.php",
    {
      fetchId: fetchId,
      filter: filter,
      instituteId: instituteId,
      countType: type,
    },
    function (data) {
      if (type === "resolved") {
        $("#resolved-doubts-count").html(data);
      }

      if (type === "unresolved") {
        $("#unresolved-doubts-count").html(data);
      }
    }
  );
}

//To fetch all resolved/unresolved doubts records for the specific chapter
/**
 *
 * @param {*} fetchId id of the chapter or subject
 * @param {*} filter to fetch chapterwise or subjectwise
 * @param {*} instituteId id of the institute
 * @param {*} type resolved or unresolved doubt type
 */
function fetchFilteredDoubts(fetchId, filter, instituteId, type) {
  // console.log("fetchFilteredDoubts " + fetchId + "-" + instituteId+ "-" + type);

  $("#resolved-doubts-list-loading-div").show();
  $("#unresolved-doubts-list-loading-div").show();

  $.post(
    "test_operations/ajax_doubts_teacher_fetch_doubts.php",
    {
      fetchId: fetchId,
      filter: filter,
      instituteId: instituteId,
      doubtType: type,
    },
    function (data, status) {
      // console.log("Doubts Data: " + data + "\nStatus: " + status);

      if (type === "resolved") {
        $("#resolved-doubts-table-list").html(data);
        $("#resolved-doubts-list-loading-div").hide();

        //To enable datatable features like search
        $("#cleared_doubts_table").DataTable();
      }

      if (type === "unresolved") {
        $("#unresolved-doubts-table-list").html(data);
        $("#unresolved-doubts-list-loading-div").hide();

        //To enable datatable features like search
        $("#pending_doubts_table").DataTable();
      }
    }
  );
}

//Set doubt resolution modal parameters
function setDoubtParams(id) {
  $("#solve_doubt_id").val(id);
}

//Set move to pending doubt modal parameters
function setMoveDoubtParams(id) {
  $("#move_doubt_id").val(id);
}

//Submit the resolve doubt form data via ajax
$("#doubt_submit_form").submit(function (e) {
  var formData = new FormData($(this)[0]);

  $("#send_resolution_form_button").prop("disabled", true);
  $("#doubt-resolve-submit-loading-div").show();

  // console.log(formData);
  $.ajax({
    url: "test_operations/resolve_doubt_submit.php",
    type: "POST",
    data: formData,
    success: function (msg) {
      if (msg.trim() != "SUCCESS") {
        alert(msg);

        $("#resolveDoubtModal").modal("hide");

        $("#doubt-resolve-submit-loading-div").hide();
        $("#send_resolution_form_button").prop("disabled", false);
      } else {
        window.location.reload();
      }
    },
    cache: false,
    contentType: false,
    processData: false,
  });

  e.preventDefault();
});

//Move the doubt to pending via ajax
$("#move_to_pending_form").submit(function (e) {
  var formData = new FormData($(this)[0]);

  $("#move_doubt_form_button").prop("disabled", true);
  $("#move-doubt-submit-loading-div").show();

  $.ajax({
    url: "test_operations/resolve_doubt_submit.php",
    type: "POST",
    data: formData,
    success: function (msg) {
      if (msg.trim() != "SUCCESS") {
        alert(msg);

        $("#moveToPendingDoubtModal").modal("hide");

        $("#move-doubt-submit-loading-div").hide();
        $("#move_doubt_form_button").prop("disabled", false);
      } else {
        window.location.reload();
      }
    },
    cache: false,
    contentType: false,
    processData: false,
  });

  e.preventDefault();
});

function doubtModal() {
  $("#add_subject_id").val("");
  $("#doubt_description").val("");
  $("#doubt_type").val("");
  $("#chapter_id").val("");
  $("#doubt_error").text("");
  $("#doubt_progress").text("");
  $("#content_id").val("");
  $("#content_title").text("");
  $("#askNewDoubtModal").modal("show");
}

function raiseContentDoubt(contentId, contentTitle) {
  doubtModal();
  $("#content_id").val(contentId);
  $("#content_title").text(contentTitle);
}

function sendDoubt() {
  get_token()
    .then(function (result) {
      var resp = JSON.parse(result);
      if (
        resp != null &&
        resp.status == 200 &&
        resp.data != null &&
        resp.data.student_token != null
      ) {
        var subjectId = $("#add_subject_id").val();

        if (subjectId == null || subjectId == "") {
          subjectId = $("#dlp_subject_id").val();
        }

        if (subjectId == null || subjectId == "") {
          $("#doubt_error").text("Please select a subject of your doubt");
          return;
        }

        var doubtText = $("#doubt_description").val();
        if (doubtText == null || doubtText == "") {
          $("#doubt_error").text("Please enter your doubt details");
          return;
        }

        $("#doubt_error").text("");

        var doubtType = $("#doubt_type").val();
        var chapterId = $("#chapter_id").val();

        if (chapterId == null || chapterId == "") {
          chapterId = $("#dlp_chapter_id").val();
        }

        if (chapterId == "") {
          chapterId = null;
        }

        var feedbackId = $("#content_id").val();
        if (feedbackId == "") {
          feedbackId = null;
        }

        var request = {
          test: {
            currentQuestion: {
              feedback: {
                id: feedbackId,
                type: doubtType,
                feedback: doubtText,
              },
              chapter: {
                chapterId: chapterId,
              },
              subjectId: subjectId,
            },
          },
          student: {
            id: studentId,
            instituteId: instituteId,
          },
          institute: {
            id: instituteId,
          },
        };

        var fd = new FormData();
        fd.append("request", JSON.stringify(request));

        var f = document.getElementById("doubt_photo").files[0];
        // console.log("File:", f);
        if (f != null && f != undefined) {
          fd.append("data", f);
        }

        $("#doubt_progress").text("Sending doubt .. ");

        $.ajax({
          url: root + "raiseDoubtWithFile",
          beforeSend: function (request) {
            request.setRequestHeader("AuthToken", resp.data.student_token);
          },
          type: "POST",
          data: fd,
          success: function (msg) {
            // console.log("Response", msg);
            if (
              msg != null &&
              msg.status != null &&
              msg.status.statusCode == 200
            ) {
              $("#doubt_progress").text("Done!");
              window.location.reload();
            } else {
              $("#doubt_progress").text(
                "Error in sending doubt. Please try again later."
              );
            }
          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#doubt_progress").text(
              "Error in sending doubt.. Please try again "
            );
          },
          cache: false,
          contentType: false,
          processData: false,
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
        "Some error authenticating your request. Please clear your browser cache and try again."
      );
    });
}

//Notifications when doubt is resolved
function removeParam(key, sourceURL) {
  //remove query param from string
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

$(document).ready(function () {
  var url = new URL(window.location.href);
  var drt = url.searchParams.get("drt");
  var doubtId = url.searchParams.get("resolvedId");

  console.log("Doubt resolution param", drt, " AND Doubt ", doubtId);

  if (drt != null && doubtId != null) {
    //console.log('trigger'+ service_arr[1]);
    var feedbackObj = {
      id: doubtId,
    };
    if (drt == "video") {
      feedbackObj = {
        videoId: doubtId,
      };
    } else if (drt == "question") {
      feedbackObj = {
        questionId: doubtId,
      };
    }
    var postdata = {
      feedback: feedbackObj,
      requestType: "DoubtResolved",
    };
    var url = rootAdmin + "sendDoubtNotification";
    $.ajax({
      type: "POST",
      url: url,
      data: JSON.stringify(postdata),
      success: function (result) {
        var alteredURL = removeParam("drt", window.location.href);
        alteredURL = removeParam("resolvedId", alteredURL);
        window.location = alteredURL;
      },
      dataType: "json",
      contentType: "application/json",
    });
  }

  //Resolve doubt using service/AJAX
  $("#resolveDoubtModal").submit(function (evt) {
    //var resourceType = $("#resource_type").val();
    //var video_url = $("#video_url").val();
    evt.preventDefault();
    console.log("Form submit blocked ..");

    var video_url = $("#video_url").val();
    var image = document.getElementById("solution_img").files[0];
    var solution = $("#doubt_resolution_textarea").val();
    var doubtId = $("#doubt_question_id").val();

    var request = {
      feedback: {
        feedbackVideoUrl: video_url,
        feedbackResolutionText: solution,
        resolution: "Resolved",
      },
    };

    //Decide type of doubt
    var type = $("#doubt_question_type").val();
    console.log("Type found is " + type);
    if (type == "video") {
      request.feedback.videoId = doubtId;
    } else if (type == "general") {
      request.feedback.id = doubtId;
    } else {
      request.feedback.questionId = doubtId;
    }

    var fd = new FormData();
    fd.append("file", image);
    fd.append("request", JSON.stringify(request));

    $("#doubt_progress").text("Saving ..");

    console.log("Resolving", request);

    get_admin_token().then(function (result) {
      var resp = JSON.parse(result);
      if (
        resp != null &&
        resp.status == 200 &&
        resp.data != null &&
        resp.data.admin_token != null
      ) {
        token = resp.data.admin_token;
        $.ajax({
          url: rootAdmin + "resolveDoubtWithFile",
          beforeSend: function (request) {
            request.setRequestHeader("AuthToken", token);
          },
          type: "POST",
          data: fd,
          success: function (msg) {
            // console.log("Response", msg);
            if (
              msg != null &&
              msg.status != null &&
              msg.status.statusCode == 200
            ) {
              window.location.reload();
            } else {
              $("#doubt_progress").text(
                "Error in resolving doubt. Please try again later."
              );
            }
          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#doubt_progress").text(
              "Error in resolving doubt.. Please try again "
            );
          },
          cache: false,
          contentType: false,
          processData: false,
        });
      }
    });
  });
});
