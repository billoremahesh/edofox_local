var success = 0, failed = 0;

// Compress and Parse OMR
function parse_omr(test_id, omr_file, accessType, prefix,instituteId, requestType, timeout, skipCompress) {
  return new Promise(function (resolve, reject) {
    compressImage(omr_file, skipCompress).then(function (file) {
      var fd = new FormData();
      fd.append("data", file);
      var testId = test_id;
      if (testId != null && testId != "") {
        var request = {
          test: {
            id: testId,
          },
          student: {
            accessType: accessType,
            referrer: prefix,
            instituteId: instituteId
          },
          requestType: requestType
        };
        fd.append("request", JSON.stringify(request));
      }
      upload_omr(fd, timeout)
        .then(function (result) {
          console.log("completed omr parse", result);
          resolve(result);
        })
        .catch(function (error) {
          console.log("Exception:", error);
          var result = {
            status: {
              statusCode: -111,
              responseText: 'Could not connect to server..Please reupload the file and try again ..'
            }
          };
          resolve(result);
        });
    });
  });
}

// Parse Compressed OMR
function upload_omr(fd, timeout) {
  if(!timeout) {
    timeout = 10000;
  }
  console.log("TImeout is " + timeout);
  return new Promise(function (resolve, reject) {
    var xhr = $.ajax({
      type: "POST",
      data: fd,
      url: root + "parseOmr",
      cache: false,
      contentType: false,
      processData: false,
      timeout: timeout
    })
      .done(function (response) {
        resolve(response);
      })
      .fail(function (jqXHR) {
        reject(jqXHR.responseText);
      });
  });
}

function updateStatusCount(status) {
  if(status == 'success') {
    success++;
    $("#" + status + "_count").text("Success : " + success);
  } else {
    failed++;
    $("#" + status + "_count").text("Failed : " + failed);
  }
  
}

function addSuccess() {
    success++;
    $("#success_count").text("Success : " + success);
    failed--;
    $("#failed_count").text("Failed : " + failed);
}

// Format reponse
function format_omr_response(msg, i, file_name, preview, upload_single) {
  console.log(msg);
  var html = "";
  if (msg != null && msg.test != null && msg.student != null) {
    if(msg.student.username == null) {
      msg.student.username = 'NA';
    }
    if(msg.student.name == null) {
      msg.student.name = 'NA';
    }
    if(msg.student.rollNo == null) {
      msg.student.rollNo = 'NA';
    }
    if(!upload_single) {
      html = html + "<tr id='tr" + i + "' class='table-light' style=''>";
      updateStatusCount('success');
    } else {
      $("#tr" + i).attr("style", "");
    }
    html = html + "<td>" + msg.student.username + "</td>";
    html = html + "<td>" + msg.student.name + "</td>";
    html = html + "<td>" + msg.student.rollNo + "</td>";
    html = html + "<td>" + msg.test.solvedCount + "</td>";
    html = html + "<td>" + file_name + "</td>";
    html =
      html +
      "<td><a href='" +
      msg.test.omrSheetImageUrl +
      "' class='not_to_export' target='_blank' > Uploaded OMR Image </a> <span class='not_to_display'>" +
      msg.test.omrSheetImageUrl +
      "<span></td>";
    html = html + "<td></td>";
    if(msg.test.solutionUrl != null) {
      html = html + "<td><a href='" +
      msg.test.solutionUrl +
      "' class='not_to_export' target='_blank' > Scanned OMR Output </a> <span class='not_to_display'>" +
      msg.test.solutionUrl +
      "<span></td>";
    } else {
      html = html + "<td></td>";
    }

    
    if(preview) {
      if (msg != null && msg.test != null && msg.test.test != null) {
        // for(var i = 0; i < msg.test.noOfQuestions; i++) {
        //   html = html + "<th>Q.No." + element.questionNumber + "</th>";
        // }
        msg.test.test.forEach(element => {
          html = html + "<td>" + element.answer + "</td>";
        });
      }
    }
    
    if(!upload_single) {
      html = html + "</tr>";
    }
  } else {
    if(!upload_single) {
      updateStatusCount('failed');
      html = html + "<tr id='tr" + i + "' class='table-danger not_to_export not_to_print' style='color:red'>";
    }
    html = html + "<td>" + msg.status.responseText + "</td>";
    html = html + "<td></td>";
    html = html + "<td></td>";
    html = html + "<td></td>";
    html = html + "<td>" + file_name + "</td>";
    html = html + "<td></td>";
    html =
      html +
      "<td><button type='button' class='btn btn-primary' onclick='reuplod_omr_modal(" +
      i +
      ");'> Reupload OMR </button></td>";
      html = html + "<td></td>";
      if(preview) {
        if (msg != null && msg.test != null && msg.test.noOfQuestions != null) {
          for(var i = 0; i < msg.test.noOfQuestions; i++) {
            html = html + "<td></td>";
          }
          // msg.test.test.forEach(element => {
          //   html = html + "<td>" + element.questionNumber + "</td>";
          // });
        } else if (noOfQuestions != null) {
          for(var i = 0; i < noOfQuestions; i++) {
            html = html + "<td></td>";
          }
        }
      }
      if(!upload_single) {
        html = html + "</tr>";
      }
  }
  return html;
}

function format_omr_response_as_array(msg, i, file_name, preview, upload_single) {
  console.log(msg);
  var array = [];
  if (msg != null && msg.test != null && msg.student != null) {
    if(msg.student.username == null) {
      msg.student.username = 'NA';
    }
    if(msg.student.name == null) {
      msg.student.name = 'NA';
    }
    if(msg.student.rollNo == null) {
      msg.student.rollNo = 'NA';
    }
    // if(!upload_single) {
    //   html = html + "<tr id='tr" + i + "' class='table-light' style=''>";
    //   updateStatusCount('success');
    // } else {
    //   $("#tr" + i).attr("style", "");
    // }
    array[0] = msg.student.username ;
    array[1] = msg.student.name;
    array[2] = msg.student.rollNo;
    array[3] = msg.test.solvedCount;
    array[4] = file_name;
    array[5] = "<a href='" + msg.test.omrSheetImageUrl +
     "' class='not_to_export' target='_blank' > Uploaded OMR Image </a> <span class='not_to_display'>" + msg.test.omrSheetImageUrl +
     "<span>";
    array[6] = '';
    if(msg.test.solutionUrl != null) {
      // html = html + "<td><a href='" +
      // msg.test.solutionUrl +
      // "' class='not_to_export' target='_blank' > Scanned OMR Output </a> <span class='not_to_display'>" +
      // msg.test.solutionUrl +
      // "<span></td>";
      array[7] = "<a href='" + msg.test.solutionUrl + "' class='not_to_export' target='_blank' > Scanned OMR Output </a> <span class='not_to_display'>" + msg.test.solutionUrl + "<span>"
    } else {
      array[7] = '';
    }

    // html = html + "<td>" + + "</td>";
    // html = html + "<td>" +  + "</td>";
    // html = html + "<td>" +  + "</td>";
    // html = html + "<td>" +  + "</td>";
    // html = html + "<td>" +  + "</td>";
    // html =
    //   html +
    //   "<td><a href='" +
    //    +
    //   "' class='not_to_export' target='_blank' > Uploaded OMR Image </a> <span class='not_to_display'>" +
    //   msg.test.omrSheetImageUrl +
    //   "<span></td>";
    // html = html + "<td></td>";
    

    var i = 8;
    if(preview) {
      if (msg != null && msg.test != null && msg.test.test != null) {
        // for(var i = 0; i < msg.test.noOfQuestions; i++) {
        //   html = html + "<th>Q.No." + element.questionNumber + "</th>";
        // }
        msg.test.test.forEach(element => {
          // html = html + "<td>" + element.answer + "</td>";
          array[i] = element.answer;
          i++;
        });
      }
    }

    addSuccess();
    
    // if(!upload_single) {
    //   html = html + "</tr>";
    // }
  } else {
    // if(!upload_single) {
    //   updateStatusCount('failed');
    //   html = html + "<tr id='tr" + i + "' class='table-danger not_to_export not_to_print' style='color:red'>";
    // }

    array[0] = msg.status.responseText ;
    array[1] = '';
    array[2] = '';
    array[3] = '';
    array[4] = file_name;
    array[5] = '';
    array[6] = "<button type='button' class='btn btn-primary' onclick='reuplod_omr_modal(" + i + ");'> Reupload OMR </button>";
    array[7] = '';

    // html = html + "<td>" +  + "</td>";
    // html = html + "<td></td>";
    // html = html + "<td></td>";
    // html = html + "<td></td>";
    // html = html + "<td>" + file_name + "</td>";
    // html = html + "<td></td>";
    // html =
    //   html +
    //   "<td><button type='button' class='btn btn-primary' onclick='reuplod_omr_modal(" +
    //   i +
    //   ");'> Reupload OMR </button></td>";

      //html = html + "<td></td>";
      var i = 8;
      if(preview) {
        if (msg != null && msg.test != null && msg.test.noOfQuestions != null) {
          for(var i = 0; i < msg.test.noOfQuestions; i++) {
            //html = html + "<td></td>";
            array[i] = '';
            i++;
          }
          // msg.test.test.forEach(element => {
          //   html = html + "<td>" + element.questionNumber + "</td>";
          // });
        } else if (noOfQuestions != null) {
          for(var i = 0; i < noOfQuestions; i++) {
            //html = html + "<td></td>";
            array[i] = '';
            i++;
          }
        }
      }
      // if(!upload_single) {
      //   html = html + "</tr>";
      // }
  }
  return array;
}

function format_omr_headers(result) {
  var html = "";
  if (result != null && result.test != null && result.test.test != null) {
    // for(var i = 0; i < result.test.noOfQuestions; i++) {
    //   html = html + "<th>Q.No." + element.questionNumber + "</th>";
    // }
    result.test.test.forEach(element => {
      html = html + "<th>Q.No." + element.questionNumber + "</th>";
    });
  } else if (noOfQuestions != null) {
    for(var i = 0; i < noOfQuestions; i++) {
      html = html + "<th>Q.No." + (i + 1) + "</th>";
    }
  }
  return html;
}
