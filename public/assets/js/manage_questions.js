function addMathJax() {
  MathJax.texReset();
  MathJax.typesetClear();
  MathJax.typesetPromise()
    .catch(function (err) {
      console.log("Error -- " + err.message);
    })
    .then(function () {});
}

function validate_checkbox() {
  var chks = document.getElementsByName("chkboxQues_append[]");
  var hasChecked = false;
  for (var i = 0; i < chks.length; i++) {
    if (chks[i].checked) {
      hasChecked = true;
      break;
    }
  }
  if (hasChecked == false) {
    var elem1 = document.getElementById("empty_option_div");
    elem1.style.color = "#F5484F";
    elem1.style.padding = "8px";

    $("#empty_option_div").html("Please select at least one option.");
    return false;
  }
  return true;
}

function getRequest(last_record) {
  var chapterId = null;
  var subjectId = null;
  var instituteId = null;
  var verifiedCheck = null;
  var lastIndex = 0;
  if (last_record) {
    lastIndex = last_record;
  }

  chapterId = $("#chapterId").val();
  subjectId = $("#subjectId").val();
  instituteId = $("#instituteId").val();
  var difficulty_level = $("#question_difficulty_filter").val();
  var type_filter = $("#question_type_filter").val();
  var verifiedCheck = $("#verifiedQuesFilter").val();

  var request = {
    question: {
      chapter: {
        chapterId: chapterId,
      },
      level: difficulty_level,
      type: type_filter,
      subjectId: subjectId,
      instituteId: instituteId,
      lastIndex: lastIndex,
      status: verifiedCheck,
    },
  };
  return request;
}

function getResponse(test) {
  var html = "";
  if (test != null && test.test.length > 0) {

    test.test.forEach(function (q) {

      html =
        html +
        " <div class='bg-white rounded shadow my-5 p-2'> ";
        
        if(q.verifiedDate){
          var d = new Date(q.verifiedDate);
          formattedVerifiedDate = d.toLocaleString('en-GB');
          html =
          html + "<div> <span class='material-icons text-primary fs-4 p-2' data-bs-toggle='tooltip' title='Verified by "+q.moderatorName+" on "+ formattedVerifiedDate +"'>verified</span></div>";
        }
        
      html = html + "<div class='question_check_div'>";
      
      html =
        html +
        "<div class='text-end'><span class='ques_desc d-inline-block me-2' data-bs-toggle='tooltip' title='Question Type'>Question Type: " +
        q.type +
        "</span> <span class='ques_desc d-inline-block me-2' data-bs-toggle='tooltip' title='Difficulty level'>Difficulty level: " +
        q.level +
        "</span><span><button class='btn btn-sm' onclick=" +
        "show_edit_modal('modal_div','update_question_modal','questionBank/update_question_modal/" +
        q.id +
        "');" +
        " data-bs-toggle='tooltip' title='Update Question'><i class='material-icons material-icon-small text-primary'>edit</i></button></span><span><button class='btn btn-sm' onclick=" +
        "show_edit_modal('modal_div','delete_question_modal','questionBank/delete_question_modal/" +
        q.id +
        "');" +
        " data-bs-toggle='tooltip' title='Delete Question'><i class='material-icons material-icon-small text-danger'>delete</i></button></span></div>";

      if (q.question != null) {
        html = html + "<div class='ques_div'>" + q.question + "</div>";
      }

      if (q.option1 != null && q.option1 != "" && q.option1 != "1)") {
        html =
          html +
          "<div class='ques_div'> 1) " +
          q.option1 +
          " 2) " +
          q.option2 +
          " 3) " +
          q.option3 +
          " 4) " +
          q.option4 +
          "</div>";
      }
      if (q.correctAnswer != null && q.correctAnswer != "") {
        html =
          html +
          "<div class='ques_div' style='color:green'> Correct Answer: " +
          q.correctAnswer +
          "</div>";
      }
      if (q.questionImageUrl != null && q.questionImageUrl != "") {
        html =
          html +
          "<h5 class='text-muted fw-bold text-center'><span class='badge bg-primary'>Question:</span></h5><div class='ques_img_div text-center'><img src='" +
          q.questionImageUrl +
          "' alt='Question Image' class='img-fluid w-100 ques_imgs border border-primary border-2 rounded' style='max-width: 800px;' /></div>";
      }
      if (q.solutionImageUrl != null && q.solutionImageUrl != "") {
        html =
          html +
          "<hr/><h5 class='text-muted fw-bold text-center'><span class='badge bg-secondary'>Solution:</span></h5><div class='soln_img_div text-center'><img src='" +
          q.solutionImageUrl +
          "' alt='Solution Image' class='img-fluid w-100 soln_imgs border border-secondary border-2 rounded' style='max-width: 800px;' /></div>";
      }

      html = html + " <div class='d-flex justify-content-end'> ";
      if(q.createdDate){
        var d = new Date(q.createdDate);
        formattedCreatedDate = d.toLocaleString('en-GB');
        html =
        html + "<div> <span class='material-icons text-secondary fs-4 p-2' data-bs-toggle='tooltip' title='Question Added by "+q.adminName+" on "+ formattedCreatedDate +"'>create_new_folder</span> </div>";
      }
      if(q.updatedDate){
        var d = new Date(q.updatedDate);
        formattedUpdatedDate = d.toLocaleString('en-GB');
        html =
        html + "<div> <span class='material-icons text-secondary fs-4 p-2' data-bs-toggle='tooltip' title='Question Updated by "+q.updatorName+" on "+ formattedUpdatedDate +"'>rate_review</span> </div>";
      }
      html = html + " </div>";


      html = html + "</div></div>";
    });
  } else {
    html =
      html +
      "<hr/><div class='text-danger text-center fw-bold m-0'>No questions match selected filters.</div>";
  }


  return html;
}

function getFormattedChaptersList(result, subject_id) {
  var html = "";
  if (result != null && result.length > 0) {

    html = html + "<div class='row'>";
    result.forEach(function (q) {
      html =
        html +
        "<div class='col-md-6 d-flex my-2'> <a href='" +
        base_url +
        "/questionBank/chapter_questions/" +
        subject_id +
        "/" +
        q.id +
        "' class='chapter_list' data-bs-toggle='tooltip' title='" +
        q.chapter_name +
        "'><span class='text-wrap'>" +
        q.chapter_name +
        "</span></a> <span aria-hidden='true' data-bs-toggle='tooltip' title='Added Questions'>" +
        q.que_count +
        "</span></div> ";
    });
    html = html + "</div>";
  } else {
    html =
      html +
      "<div class='text-danger text-center'>No chapters found matching selected filters.</div>";
  }

  return html;
}
