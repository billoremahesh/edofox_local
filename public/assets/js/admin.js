//Show loader
function toggleSpinner(toggle, text) {
  var x = document.getElementById("loader");
  if (toggle === "show") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
  if (text != null) {
    var displayText = document.getElementById("loader_text");
    x.innerHTML = text;
  }
}

app.controller("pdfParser", [
  "$rootScope",
  "$scope",
  "$http",
  "$element",
  "$interval",
  "userService",
  function ($rootScope, $scope, $http, $element, $interval, userService) {
    console.log("PDF parser loaded ..." + testId);

    //var testId = 250;

    /*userService.callService($scope, "parsePdf").then(function (response) {
        console.log(response);
        $scope.status = response.status;

        $scope.response = response;

    }).catch(function (error) {
        console.log("Error!" + error);
    });
*/

    var subject_id = $("#subjectId") != null ? $("#subjectId").val() : null;
    var chapter_id = $("#chapterId") != null ? $("#chapterId").val() : null;
    var section_name = $("#section") != null ? $("#section").val() : null;
    var wt = $("#weightage") != null ? $("#weightage").val() : null;
    var nm = $("#negativeMarks") != null ? $("#negativeMarks").val() : null;

    $scope.request = {
      questionSuffix: "",
      questionPrefix: "",
      buffer: "5",
      pdfType: "SINGLE_COL",
    };

    if (subject_id != null) {
      $scope.request.subject = subject_id;
    }

    if (chapter_id != null) {
      $scope.request.chapter = chapter_id;
    }

    if (section_name != null) {
      $scope.request.section = section_name;
    }

    try {
      if (wt != null) {
        $scope.request.weightage = parseFloat(wt);
      }
      if (nm != null) {
        $scope.request.negativeMarks = parseFloat(nm);
      }

    } catch (e) {
      console.log(e);
    }

    try {
      var cookieVal = getCookie("parse_pdf_" + testId);
      if (cookieVal != null && cookieVal != "") {
        $scope.request = JSON.parse(cookieVal);

        var subject_id = $("#subjectId") != null ? $("#subjectId").val() : null;
        var chapter_id = $("#chapterId") != null ? $("#chapterId").val() : null;
        if (subject_id != null && subject_id > 0) {
          $scope.request.subject = subject_id;
        }
        if (chapter_id != null && chapter_id > 0) {
          $scope.request.chapter = chapter_id;
        }
        //Save request to cookie
        setCookie("parse_pdf_" + testId, JSON.stringify($scope.request), 1);
        $scope.applyChanges(false);
      }
    } catch (e) {
      console.log(e);
    }

    function validateRequest() {
      var appendError = "";
      if ($scope.test != null) {
        appendError =
          ". Also, click on re-apply the filters after setting the values.";
      }
      if ($scope.applyTemplate) {
        return true;
      }
      if ($scope.request.subject == null) {
        alert("Please select a subject" + appendError);
        return false;
      }

      if ($scope.request.section == null) {
        alert("Please enter section name" + appendError);
        return false;
      }
      if ($scope.request.weightage == null) {
        alert("Please enter the weightage" + appendError);
        return false;
      }
      if ($scope.request.negativeMarks == null) {
        alert(
          "Please enter the negative marks (0 if no negative marking)" +
          appendError
        );
        return false;
      }

      return true;
    }

    $scope.typeChanged = function (q) {
      console.log("Type changed", q);
      if (q.type == "MULTIPLE" || q.type == "MATCH") {
        var elementId = "#questionAnswerMultiple" + q.questionNumber;
        if (q.type == "MATCH") {
          elementId = "#questionAnswerMatch" + q.questionNumber;
        }
        //Apply select JS
        setTimeout(function () {
          $(elementId).select2();
        }, 500);
      }
    };

    $scope.columnChanged = function (q) {
      q.matchOptions = [];
      if (q.option1 != null && q.option1 != "") {
        var leftCol = q.option1.split(",");
        if (q.option2 != null && q.option2 != "") {
          var rightCol = q.option2.split(",");
          if (
            rightCol != null &&
            rightCol.length > 0 &&
            leftCol != null &&
            leftCol.length > 0
          ) {
            leftCol.forEach(function (left) {
              rightCol.forEach(function (right) {
                q.matchOptions.push(left + "-" + right);
              });
            });
          }
        }
      }
    };

    $scope.replaceImageModal = function (q) {
      $scope.selectedQuestion = q;
      clearCanvas(false, false);
      $("#imageReplaceModal").modal("show");
    };

    var crop_max_width = 300;
    var crop_max_height = 300;

    var coord = {};

    $scope.saveCrop = function () {
      console.log("Saving crop", coord);
      var request = {};
      if (testId != null && testId > 0) {
        request.test = {
          id: testId
        };
      } else {
        request.filePath = "staff_" + staffId;
        request.test = {};
      }
      request.test.currentQuestion = {
        questionImageUrl: $scope.selectedQuestion.questionImageUrl
      };

      request.x = coord.x;
      request.y = coord.y;
      request.width = coord.w;
      request.height = coord.h;
      $scope.dataObj = request;
      $scope.imageCropProgress = "Saving ..";
      userService.callAdminService($scope, "cropTempQuestion").then(function (response) {
        console.log(response);
        if (response != null && response.question != null && response.question.questionImageUrl != null) {
          $scope.imageCropProgress = "";
          $("#imageCropModal").modal('hide');
          $scope.selectedQuestion.questionImageUrl = response.question.questionImageUrl;
          $scope.$apply();
        } else {
          $scope.imageCropProgress = "Error in saving image.. Try again";
        }
      });
    }

    function canvas(coords) {
      console.log(coords);
      coord = coords;
    }

    $scope.cropImageModal = function (q) {
      $scope.selectedQuestion = q;
      $scope.imageCropProgress = "";
      $("#jcrop, #preview").html("").append("<img class='img-responsive' src=\"" + q.questionImageUrl + "\" alt=\"\" />");
      picture_width = $("#preview img").width();
      picture_height = $("#preview img").height();
      $("#jcrop  img").Jcrop({
        onChange: canvas,
        onSelect: canvas,
        boxWidth: crop_max_width,
        boxHeight: crop_max_height
      });

      $("#imageCropModal").modal("show");
    };

    // Encode Cropper URL
    $scope.encode_cropper_uri = function (q) {
      let test_id = testId;
      let que_no = q.questionNumber;
      let que_imgurl = q.questionImageUrl;
      post_form(base_url + "/tests/cropper", {
        test_id: test_id,
        questionId: que_no,
        url: que_imgurl,
      });
    };

    $scope.parsePdf = function () {
      if ($scope.test != null) {
        $scope.test = null;
        return;
      }

      if (!validateRequest()) {
        return;
      }

      //Save request to cookie
      setCookie("parse_pdf_" + testId, JSON.stringify($scope.request), 1);

      var f = document.getElementById("pdfToParse").files[0];
      console.log("File:", f);
      if (f == null || f == undefined) {
        alert("Please select a file to upload!");
        return;
      }
      var fd = new FormData();
      fd.append("data", f);

      var request = $scope.request;
      if (testId != null && testId > 0) {
        request.test = {
          id: testId,
        };
      } else {
        request.filePath = "staff_" + staffId;
      }
      
      request.institute= {
        id: instituteID
      }
      
      fd.append("request", JSON.stringify(request));

      // fd.append('testId', testId);
      // fd.append('buffer', $scope.request.buffer);
      // fd.append('questionSuffix', $scope.request.questionSuffix);
      // fd.append('questionPrefix', $scope.request.questionPrefix);
      // fd.append('pdfType', $scope.request.pdfType);
      // if ($scope.request.fromQuestion != null) {
      //     fd.append('fromQuestion', $scope.request.fromQuestion);
      // }
      // if ($scope.request.toQuestion != null) {
      //     fd.append('toQuestion', $scope.request.toQuestion);
      // }

      toggleSpinner("show");

      $scope.responseText = null;

      //Authenticate for token
      get_admin_token().then(function (result) {
        var resp = JSON.parse(result);
        if (
          resp != null &&
          resp.status == 200 &&
          resp.data != null &&
          resp.data.admin_token != null
        ) {
          $http
            .post(rootAdmin + "parsePdf", fd, {
              transformRequest: angular.identity,
              headers: {
                "Content-Type": undefined,
                AuthToken: resp.data.admin_token,
              },
            })
            .success(function (response) {
              toggleSpinner("hide");
              console.log("Done!!!", response);
              if (response != null) {
                $scope.test = response.test;
              }

              if (
                response == null ||
                response.test == null ||
                response.test.test == null ||
                response.test.test.length <= 0
              ) {
                $scope.responseText =
                  "Parsing failed. Please make sure your question suffix and prefix are correct and PDF contains valid questions within the given range";
              } else {
                $scope.responseText = "OK";
              }
              $scope.applyChanges(false);
            })
            .error(function (data, status, headers, config) {
              console.log(
                "Error :" +
                status +
                ":" +
                JSON.stringify(data) +
                ":" +
                JSON.stringify(headers)
              );
              toggleSpinner("hide");
              $scope.responseText = "Error connecting server..try again later";
            });
        } else {
          $scope.responseText =
            "Error authenticating the request .. Logout and try again";
        }
      });
    };

    //$scope.applyTemplate = false;

    $scope.applyChanges = function (save) {
      console.log("Applying changes ..", $scope.test);
      if ($scope.test != null) {
        $scope.test.test.forEach(function (q) {
          if (q.type == null) {
            q.type = "SINGLE";
          }
          if (!$scope.applyTemplate) {
            q.section = $scope.request.section;
            q.weightage = $scope.request.weightage;
            q.negativeMarks = $scope.request.negativeMarks;
            q.subjectId = $scope.request.subject;
          }
          if ($scope.request.optionType != "") {
            q.option1 = $scope.request.optionType;
          }
          if (staffId != null && staffId > 0) {
            q.adminId = staffId;
          }
          if ($scope.request.chapter != null) {
            q.chapter = {
              chapterId: $scope.request.chapter,
            };
          }
          if ($scope.request.questionType != null && !$scope.applyTemplate) {
            q.type = $scope.request.questionType;
            $scope.typeChanged(q);
          }
        });
      }
      if (save) {
        setCookie("parse_pdf_" + testId, JSON.stringify($scope.request), 1);
      }
    };

    //console.log("Here!");
    $scope.changeQuestion = function ($event) {
      console.log("Key pressed", $event);
      if ($event.keyCode == "39") {
        $scope.next();
      } else if ($event.keyCode == "37") {
        $scope.previous();
      }
    };

    $scope.viewQuestion = function (question) {
      $scope.selectedQuestion = question;
      $("#viewQuestion").modal("show");
    };

    $scope.next = function () {
      var curIndex = $scope.test.test.indexOf($scope.selectedQuestion);
      if (curIndex < $scope.test.test.length - 1) {
        $scope.selectedQuestion = $scope.test.test[curIndex + 1];
      }
    };

    $scope.previous = function () {
      var curIndex = $scope.test.test.indexOf($scope.selectedQuestion);
      if (curIndex > 0) {
        $scope.selectedQuestion = $scope.test.test[curIndex - 1];
      }
    };

    if (testId != null && testId > 0) {
      $scope.dataObj = {
        test: {
          id: testId,
        },
      };
    } else {
      $scope.dataObj = {
        filePath: "staff_" + staffId,
      };
    }

    userService
      .callAdminService($scope, "loadParsedPdf")
      .then(function (response) {
        console.log(response);
        $scope.test = response.test;
        $scope.test = response.test;
        $scope.applyChanges(false);

        if (
          localStorage.parseQueFocus != null &&
          localStorage.parseQueFocus != ""
        ) {
          //Focus on question div
          // $("html, body").animate(
          //   {
          //     scrollTop: $("#que_div_" + localStorage.parseQueFocus).offset()
          //       .top,
          //   },
          //   2000
          // );
          // localStorage.parseQueFocus = null;

          setTimeout(function () {
            console.log("Scrolling to " + localStorage.parseQueFocus);
            $("html, body").animate(
              {
                scrollTop: $("#que_div_" + localStorage.parseQueFocus).offset()
                  .top,
              },
              2000
            );
            localStorage.parseQueFocus = null;
          }, 1000);
        }
      });

    $scope.saveExam = function () {
      console.log("Saving exam ..");
      if (!validateRequest()) {
        return;
      }

      if ($scope.test != null && $scope.test.test != null) {
        //Convert multi selects into comma separated
        //Also validate if all values are present
        var validation = true;
        $scope.test.test.forEach(function (q) {
          if (q.type == "MULTIPLE" || q.type == "MATCH") {
            if (q.correctAnswer != null) {
              q.correctAnswer = q.correctAnswer.toString();
            }
          }

          if (q.subjectId == null || q.subjectId == '') {

          }
          if (q.section == null || q.section == '') {
            validation = false;
          }
          if (q.weightage == null || q.weightage.toString().trim() == '') {
            validation = false;
          }
          if (q.negativeMarks == null || q.negativeMarks.toString().trim() == '') {
            validation = false;
          }
          if (q.type == null || q.type == '') {
            validation = false;
          }

        });

        if (!validation) {
          alert("Some of the parameters are missing. Please check all the questions.");
          return;
        }

        var student = null;
        if (teacherID != null && teacherID > 0) {
          student = {
            id: teacherID,
          };
        }

        $scope.dataObj = {
          test: $scope.test,
          filePath: "staff_" + staffId,
          student: student,
          institute: {
            id: instituteID,
          },
        };
        console.log("Calling save exam ..", $scope.dataObj);
        toggleSpinner("show", "Saving question paper ... Please wait");
        userService
          .callAdminService($scope, "saveParsedQuestionPaper")
          .then(function (response) {
            console.log("Done!", response);
            //alert("Paper saved successfully!");
            toggleSpinner("hide", "Saving question paper ... Please wait");
            if (response.status.statusCode == 200) {
              // Snakbar Message
              Snackbar.show({
                pos: "top-center",
                text: "Questions uploaded successfully",
              });
              $scope.test = null;
              // document.getElementById("viewQuestions").submit();
            } else {
              alert("Error!");
            }
          });
      }
    };

    $scope.uploadImage = function () {
      console.log("File:", uploadedImage);
      if (uploadedImage == null || uploadedImage == undefined) {
        alert("Please select a file to upload!");
        return;
      }
      // var fd = new FormData();
      // fd.append("file", uploadedImage);
      // var request = {
      //   test: { id: testId },
      //   question: {
      //     questionNumber: $scope.selectedQuestion.questionNumber,
      //     type: "Q",
      //   },
      // };
      // fd.append("request", JSON.stringify(request));

      console.log("Uploading base 64 ..");
      $scope.imageReplaceProgress = "Uploading ..";

      //Upload Image as Base64 //20/11/2021
      $scope.dataObj = {
        test: { id: testId },
        question: {
          questionNumber: $scope.selectedQuestion.questionNumber,
          type: "Q",
          questionImageUrl: uploadedImage,
        },
      };
      userService
        .callAdminService($scope, "uploadQuestionImageBase64")
        .then(function (response) {
          console.log("Done!", response);
          $scope.imageReplaceProgress = "";
          if (
            response == null ||
            response.status == null ||
            response.status.statusCode != 200
          ) {
            if (response != null && response.status != null) {
              $scope.imageReplaceProgress = response.status.responseText;
            } else {
              $scope.imageReplaceProgress =
                "Error in uploading .. Please try again ..";
            }
          }
          if(response.question != null) {
            $scope.selectedQuestion.questionImageUrl = response.question.questionImageUrl;
            console.log("Updated question URL..");
            $scope.$apply();
          }
          //Update test URL of question
          // $scope.test.test.forEach(function (que) {
          //   if (que.questionNumber == $scope.selectedQuestion.questionNumber) {
          //     var questionBasUrl = que.questionImageUrl.split("?")[0];
          //     questionBasUrl = questionBasUrl + "?" + new Date().getTime();
          //     que.questionImageUrl = questionBasUrl;
          //     $scope.$apply();
          //     console.log(
          //       "Updating " + que.questionNumber + "as " + questionBasUrl
          //     );
          //   }
          // });
          // $scope.selectedQuestion = null;
          $("#imageReplaceModal").modal("hide");
        });

      //Authenticate for token
      // get_admin_token().then(function (result) {
      //   var resp = JSON.parse(result);
      //   if (
      //     resp != null &&
      //     resp.status == 200 &&
      //     resp.data != null &&
      //     resp.data.admin_token != null
      //   ) {
      //     $http
      //       .post(rootAdmin + "uploadQuestionImage", fd, {
      //         transformRequest: angular.identity,
      //         headers: {
      //           "Content-Type": undefined,
      //           AuthToken: resp.data.admin_token,
      //         },
      //       })
      //       .success(function (response) {
      //         console.log("Done!!!", response);

      //         $scope.imageReplaceProgress = "";
      //         if (
      //           response == null ||
      //           response.status == null ||
      //           response.status.statusCode != 200
      //         ) {
      //           if (response != null && response.status != null) {
      //             $scope.imageReplaceProgress = response.status.responseText;
      //           } else {
      //             $scope.imageReplaceProgress =
      //               "Error in uploading .. Please try again ..";
      //           }
      //         }

      //         //Update test URL of question
      //         $scope.test.test.forEach(function (que) {
      //           if (
      //             que.questionNumber == $scope.selectedQuestion.questionNumber
      //           ) {
      //             var questionBasUrl = que.questionImageUrl.split("?")[0];
      //             questionBasUrl = questionBasUrl + "?" + new Date().getTime();
      //             que.questionImageUrl = questionBasUrl;
      //             $scope.$apply();
      //             console.log(
      //               "Updating " + que.questionNumber + "as " + questionBasUrl
      //             );
      //           }
      //         });

      //         $scope.selectedQuestion = null;

      //         $("#imageReplaceModal").modal("hide");
      //       })
      //       .error(function (data, status, headers, config) {
      //         //$scope.responseText = "Error connecting server..try again later";
      //       });
      //   } else {
      //     $scope.responseText =
      //       "Error authenticating the request .. Logout and try again";
      //   }
      // });
    };
  },
]);

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
