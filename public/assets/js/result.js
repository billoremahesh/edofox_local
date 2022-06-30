function applyMathJax() {
  // console.log("Applying math jax ..");
  //Apply math jax after questions are loaded
  MathJax.texReset();
  MathJax.typesetClear();
  MathJax.typesetPromise()
    .catch(function (err) {
      //
      //  If there was an internal error, put the message into the output instead
      //
      //output.innerHTML = '';
      //output.appendChild(document.createElement('pre')).appendChild(document.createTextNode(err.message));
      console.log("Error -- " + err.message);
    })
    .then(function () {
      // console.log("Done adding to == > ");
    });
}

angular
  .module("app")
  .controller(
    "testResults",
    function ($scope, userService, $location, $http, $sce) {
      $scope.response = {};
      $scope.dataObj = {};
      // console.log("Test Reults loaded ..");
      userService.initLoader($scope);
      //$scope.dataObj = {};
      //$scope.user = JSON.parse(localStorage.erpUser);
      //localStorage.erpEmployee = null;
      $scope.student = {};
      $scope.test = {};
      $scope.student.id = localStorage.studentId;
      $scope.test.id = localStorage.testId;
      $scope.student.instituteId = localStorage.instituteId;
      $scope.pageAccess = localStorage.pageAccess;
      $scope.isLoading = true;

      function processStudentResult(response) {
        //$.skylo('end');
        userService.initLoader($scope);
        // console.log(response);
        $scope.isLoading = false;
        $scope.response = response;
        if (
          $scope.response != null &&
          $scope.response.status != null &&
          $scope.response.status.statusCode != 200
        ) {
          $scope.errorText = $scope.response.status.responseText;
          return;
        }

        $scope.response.test.percentageScored =
          (response.test.score * 100) / response.test.totalMarks;
        $scope.response.test.percentageScored =
          Math.round(
            ($scope.response.test.percentageScored + Number.EPSILON) * 100
          ) / 100;

        // console.log("Response");

        if ($scope.response.test.showResult == "Y") {
          //Trust URLs for solution videos
          if (
            $scope.response.lectures != null &&
            $scope.response.lectures.length > 0
          ) {
            $scope.response.lectures.forEach(function (lecture) {
              lecture.lecture.video_url = $sce.trustAsResourceUrl(
                lecture.lecture.video_url
              );
            });
          }

          /**
           * Fetch the time taken for each subject in total for the test
           */
          $http
            .get(
              base_url +
                "/tests/subjectwise_time_taken_tests/" +
                $scope.test.id +
                "/" +
                $scope.student.id
            )
            .then(function (timeTakenResponse) {
              // console.log(timeTakenResponse);
              $scope.timeTakenResponse = timeTakenResponse;

              var dataForChart = [];
              $scope.timeTakenResponse.data.records.forEach(function (data) {
                dataForChart.push({
                  name: data.subject,
                  y: parseInt(data.time_taken),
                });
              });

              //If records fetched, then only show highcharts
              if (dataForChart.length > 0) {
                Highcharts.chart("subjectwise_time_pie_chart", {
                  credits: {
                    enabled: false,
                  },
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: "pie",
                  },
                  title: {
                    text: "Subjectwise Time Distribution",
                  },
                  subtitle: {
                    text: "How did you spend your time?",
                  },
                  tooltip: {
                    pointFormat: "{series.name}: <b>{point.y}(sec)</b>",
                  },
                  plotOptions: {
                    pie: {
                      allowPointSelect: true,
                      cursor: "pointer",
                      dataLabels: {
                        enabled: true,
                        format: "<b>{point.name}</b>: {point.percentage:.1f} %",
                      },
                    },
                  },
                  series: [
                    {
                      name: "Time Taken",
                      colorByPoint: true,
                      data: dataForChart,
                    },
                  ],
                });
              }

              /**
               * Fetch the subjectwise marks and display in pie chart
               */
              var dataForSubjectChart = [];
              $scope.response.test.analysis.subjectAnalysis.forEach(function (
                subjectData
              ) {
                dataForSubjectChart.push({
                  name: subjectData.subject,
                  y: parseInt(subjectData.score),
                });
              });
              // Pushing another chart item to show difference between total marks and score
              dataForSubjectChart.push({
                name: "Difference",
                y:
                  parseInt($scope.response.test.totalMarks) -
                  parseInt($scope.response.test.score),
              });

              //If records fetched, then only show highcharts
              // Condition is checked for greater than 1 because Difference object is pushed in all cases
              if (dataForSubjectChart.length > 1) {
                Highcharts.chart("subjectwise_marks_pie_chart", {
                  credits: {
                    enabled: false,
                  },
                  chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: "pie",
                  },
                  title: {
                    text: "Subjectwise Marks Distribution",
                  },
                  subtitle: {
                    text:
                      "Score: " +
                      $scope.response.test.score +
                      "/" +
                      $scope.response.test.totalMarks,
                  },
                  tooltip: {
                    pointFormat: "{series.name}: <b>{point.y}</b>",
                  },
                  plotOptions: {
                    pie: {
                      allowPointSelect: true,
                      cursor: "pointer",
                      dataLabels: {
                        enabled: true,
                        format: "<b>{point.name}</b>: {point.y}",
                      },
                    },
                  },
                  series: [
                    {
                      name: "Marks",
                      colorByPoint: true,
                      data: dataForSubjectChart,
                    },
                  ],
                });
              }
            });

          /**
           * Fetching subjectwise correctness count for the test
           */
          $http
            .get(
              base_url +
                "/tests/subjectwise_correctness_percentage/" +
                $scope.test.id +
                "/" +
                $scope.student.id
            )
            .then(function (correctnessResponse) {
              // console.log("correctnessResponse:", correctnessResponse);

              $scope.correctnessResponse = correctnessResponse;

              var subjectwiseCorrectnessSubjects = [];
              var subjectwiseCorrectnessPercent = [];
              var subjectwiseWrongnessPercent = [];
              var subjectwiseUnsolvedPercent = [];
              $scope.correctnessResponse.data.records.forEach(function (data) {
                subjectwiseCorrectnessSubjects.push(
                  data.subject +
                    " (" +
                    parseInt(data.correctCount) +
                    "/" +
                    parseInt(data.totalQuestions) +
                    ")"
                );
                subjectwiseCorrectnessPercent.push(parseInt(data.correctness));
                // subjectwiseWrongnessPercent.push(100 - parseInt(data.correctness));
                subjectwiseWrongnessPercent.push(
                  (parseInt(data.wrongCount) / parseInt(data.totalQuestions)) *
                    100
                );
                subjectwiseUnsolvedPercent.push(
                  ((parseInt(data.totalQuestions) -
                    parseInt(data.correctness) +
                    parseInt(data.wrongCount)) /
                    parseInt(data.totalQuestions)) *
                    100
                );
              });

              //If records fetched, then only show highcharts
              if (subjectwiseCorrectnessSubjects.length > 0) {
                Highcharts.chart("subjectwise_correctness_physics_pie_chart", {
                  credits: {
                    enabled: false,
                  },
                  chart: {
                    type: "column",
                  },
                  title: {
                    text: "Subjectwise Correctness",
                  },
                  subtitle: {
                    text: "What percent of your questions are correct?",
                  },
                  xAxis: {
                    categories: subjectwiseCorrectnessSubjects,
                    crosshair: true,
                  },
                  yAxis: {
                    min: 0,
                    max: 100,
                    title: {
                      text: "Percentage",
                    },
                  },
                  tooltip: {
                    headerFormat:
                      '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat:
                      '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                      '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                    footerFormat: "</table>",
                    shared: true,
                    useHTML: true,
                  },
                  plotOptions: {
                    column: {
                      pointPadding: 0.2,
                      borderWidth: 0,
                    },
                  },
                  series: [
                    {
                      name: "Correct",
                      data: subjectwiseCorrectnessPercent,
                      color: "#4caf50",
                    },
                    {
                      name: "Wrong",
                      data: subjectwiseWrongnessPercent,
                      color: "#e57373",
                    },
                    {
                      name: "Unsolved",
                      data: subjectwiseUnsolvedPercent,
                      color: "#b0bec5",
                    },
                  ],
                });
              }
            });
        }
        setTimeout(applyMathJax, 1000);

        $scope.question = $scope.response.test.test[currQ];
      }

      $scope.getTestResult = function () {
        userService.showLoading($scope);
        $scope.dataObj.student = $scope.student;
        $scope.dataObj.test = $scope.test;
        var resultUrlType = localStorage.getItem("resultUrl");
        if (resultUrlType == null || resultUrlType != "Admin") {
          userService
            .callService($scope, "getTestResult")
            .then(function (response) {
              processStudentResult(response);
            });
        } else {
          //Added as admin side also needs to access the student result
          userService
            .callAdminService($scope, "getStudentTestResult")
            .then(function (response) {
              processStudentResult(response);
              ``;
            });
        }
      };

      /** Take seconds and output the minutes and seconds to display to user */
      $scope.secToMin = function (input) {
        var minutes = parseInt(input / 60, 10);
        var seconds = input % 60;

        return minutes + " min" + (seconds ? ", " + seconds + " sec" : "");
      };

      $scope.showAnswer = function (q) {
        var answer = "";
        if (q.correctAnswer.indexOf("option1") >= 0) {
          answer = answer + q.option1 + ",";
        }
        if (q.correctAnswer.indexOf("option2") >= 0) {
          answer = answer + q.option2 + ",";
        }
        if (q.correctAnswer.indexOf("option3") >= 0) {
          answer = answer + q.option3 + ",";
        }
        if (q.correctAnswer.indexOf("option4") >= 0) {
          answer = answer + q.option4 + ",";
        }

        if (answer.length == 0) {
          return q.correctAnswer;
        }

        return answer;
      };

      /**
       * Showing / returning only the correct option number
       */
      $scope.showCorrectAnswerOption = function (q) {
        var answer = "";
        try {
          if (q.correctAnswer.indexOf("option1") >= 0) {
            answer = answer + "1" + ",";
          }
          if (q.correctAnswer.indexOf("option2") >= 0) {
            answer = answer + "2" + ",";
          }
          if (q.correctAnswer.indexOf("option3") >= 0) {
            answer = answer + "3" + ",";
          }
          if (q.correctAnswer.indexOf("option4") >= 0) {
            answer = answer + "4" + ",";
          }

          if (answer.length == 0) {
            return q.correctAnswer;
          }

          //Removed the final comma
          // Ref: https://stackoverflow.com/questions/17720264/remove-last-comma-from-a-string
          answer = answer.replace(/,\s*$/, "");
        } catch (e) {}

        return answer;
      };

      $scope.showMarks = function (q) {
        var marksObtained = q.marks;
        if (marksObtained === undefined) {
          marksObtained = "NA";
        }
        return marksObtained;
      };

      /** Updated by Hemant */
      $scope.showOptionString = function (q) {
        var answer = "";

        if (q === undefined) {
          return "NA";
        }

        if (q.indexOf("option1") >= 0) {
          answer = answer + "Option 1" + ", ";
        }
        if (q.indexOf("option2") >= 0) {
          answer = answer + "Option 2" + ", ";
        }
        if (q.indexOf("option3") >= 0) {
          answer = answer + "Option 3" + ", ";
        }
        if (q.indexOf("option4") >= 0) {
          answer = answer + "Option 4" + ", ";
        }
        if (q.indexOf("option5") >= 0) {
          answer = answer + "Option 5" + ", ";
        }
        if (q.indexOf("option6") >= 0) {
          answer = answer + "Option 6" + ", ";
        }

        if (answer.length == 0) {
          return q;
        }

        //Removed the final comma
        // Ref: https://stackoverflow.com/questions/17720264/remove-last-comma-from-a-string
        answer = answer.replace(/,\s*$/, "");
        return answer;
      };

      $scope.showOptionString1 = function (q) {
        var answer = q;
        if (q == "option1") {
          return "1";
        }
        if (q == "option2") {
          return "2";
        }
        if (q == "option3") {
          return "3";
        }
        if (q == "option4") {
          return "4";
        }

        if (answer === undefined) {
          answer = "NA";
        }
        return answer;
      };

      $scope.showOptionSelectedAnswer = function (q) {
        var answer = "";
        if (q.answer.indexOf("option1") >= 0) {
          answer = answer + q.option1 + ",";
        }
        if (q.answer.indexOf("option2") >= 0) {
          answer = answer + q.option2 + ",";
        }
        if (q.answer.indexOf("option3") >= 0) {
          answer = answer + q.option3 + ",";
        }
        if (q.answer.indexOf("option4") >= 0) {
          answer = answer + q.option4 + ",";
        }

        if (answer.length == 0) {
          return q.answer;
        }
        return answer;
      };

      $scope.showAnswerAvgTime = function (questions, answer) {
        //    	console.log("Question time: ", questions);
        var averageTime = 0;
        var sum = 0;
        var timeSpentArray = [];
        if (answer === "CORRECT") {
          questions.forEach(function (question) {
            if (question.marks > 0 && question.timeSpent) {
              timeSpentArray.push(question.timeSpent);
            }
          });
          if (timeSpentArray.length) {
            sum = timeSpentArray.reduce(function (a, b) {
              return a + b;
            });
            averageTime = sum / timeSpentArray.length;
          }
          //			console.log("time taken: ", timeSpentArray);
        } else if (answer === "WRONG") {
          questions.forEach(function (question) {
            if (question.marks <= 0 && question.answer.length > 0) {
              timeSpentArray.push(question.timeSpent);
            }
          });
          if (timeSpentArray.length) {
            sum = timeSpentArray.reduce(function (a, b) {
              return a + b;
            });
            averageTime = sum / timeSpentArray.length;
          }
          //			console.log("time taken: ", timeSpentArray);
        } else if (answer == "UNATTEMPTED") {
          questions.forEach(function (question) {
            if (question.answer == null || question.answer.length == 0) {
              timeSpentArray.push(question.timeSpent);
            }
          });
          if (timeSpentArray.length) {
            sum = timeSpentArray.reduce(function (a, b) {
              return a + b;
            });
            averageTime = sum / timeSpentArray.length;
          }
        }
        return Number(averageTime.toPrecision(4));
      };

      // To asynchronously Send the doubt raised
      $scope.showAnalysis = function (q) {
        // console.log("Fetching analysis for question", q);

        $scope.dataObj = {
          question: {
            id: q.qn_id,
          },
        };

        userService
          .callService($scope, "getQuestionAnalysis")
          .then(function (response) {
            // console.log("analysis response", response);
            q.analysis = response.question.analysis;
          });
      };

      // To asynchronously Send the doubt raised
      $scope.raiseDoubt = function (q) {
        $("#raiseDoubtModal").modal("show");

        $("#doubt_text").val("");
        $("#doubt_type_dropdown").val("");

        $("#doubtFile").val("");

        $scope.questionData = q;
      };

      /*************************
       * To Send the doubt raised
       */
      $scope.sendDoubt = function (q) {
        get_token()
          .then(function (result) {
            var resp = JSON.parse(result);
            if (
              resp != null &&
              resp.status == 200 &&
              resp.data != null &&
              resp.data.student_token != null
            ) {
              userService.showLoading($scope);
              var questionDataForDoubt = q;
              var doubtText = $("#doubt_text").val();

              //		console.log("Question data: ");
              //		console.log(q);

              $scope.chapter = {};
              if (q.chapterId) {
                $scope.chapter.chapterId = q.chapterId;
              }

              $scope.currentQuestion = {};
              $scope.currentQuestion.id = q.qn_id;
              $scope.currentQuestion.chapter = $scope.chapter;
              if (q.subjectId) {
                $scope.currentQuestion.subjectId = q.subjectId;
              }

              $scope.feedback = {};
              $scope.feedback.feedback = q.doubtText;
              $scope.feedback.type = q.doubtType;
              $scope.currentQuestion.feedback = $scope.feedback;

              $scope.test.currentQuestion = $scope.currentQuestion;
              $scope.dataObj.test = $scope.test;
              $scope.dataObj.student = $scope.student;

              var fd = new FormData();
              fd.append("request", JSON.stringify($scope.dataObj));

              var f = document.getElementById("doubtFile").files[0];
              // console.log("File:", f);
              if (f != null && f != undefined) {
                fd.append("data", f);
              }

              $http
                .post(root + "raiseDoubtWithFile", fd, {
                  transformRequest: angular.identity,
                  headers: {
                    "Content-Type": undefined,
                    AuthToken: resp.data.student_token,
                  },
                })
                .success(function (response) {
                  // console.log("Doubt uploaded!!", response);
                  userService.initLoader($scope);
                  // console.log(response);
                  $scope.responseForDoubt = response;
                  // console.log("Response", response);

                  $("#raiseDoubtModal").modal("hide");
                  if (response.status.statusCode == 200) {
                    alert(
                      "Doubt sent to teacher. You can review all your doubts in the doubt section."
                    );
                  } else {
                    alert(
                      "There was some error: " + response.status.responseText
                    );
                  }

                  //window.location.reload();
                })
                .error(function (err) {
                  // console.log("File upload failed!!", err);
                  $scope.error = "File upload failed ..";
                  $scope.success = "";
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
      };

      $scope.getDifficulty = function (q) {
        if (q.level != null) {
          if (q.level > 3) {
            return "Hard";
          }
          if (q.level > 1) {
            return "Medium";
          }
          return "Easy";
        }
        return "";
      };

      $scope.getTestResult();

      //To jump to a specific question when clicked in to the answer key row
      $scope.jumpToQuestion = function (id) {
        $("html, body").animate(
          {
            scrollTop: $("#question_display_card_" + id).offset().top,
          },
          2000
        );
      };

      //Setting tooltip
      $('[data-toggle="tooltip"]').tooltip();

      $scope.calculateMarks = function () {
        // console.log("calculateMarks");
        var marks = 0;
        if (
          $scope.response != null &&
          $scope.response.test != null &&
          $scope.response.test.answerFiles != null &&
          $scope.response.test.answerFiles.length > 0
        ) {
          if (
            $scope.response.test.analysis != null &&
            $scope.response.test.analysis.mcqScore != null
          ) {
            marks = $scope.response.test.analysis.mcqScore;
          }

          $scope.response.test.answerFiles.forEach(function (file) {
            if (file.correctionMarks != null) {
              marks = marks + file.correctionMarks;
            }
          });
          $scope.response.test.score = marks;
        } else if ($scope.response != null && $scope.response.test != null) {
          $scope.response.test.test.forEach(function (q) {
            if (q.answerFiles != null && q.answerFiles.length > 0) {
              var qmarks = 0;
              q.answerFiles.forEach(function (ans) {
                qmarks = qmarks + ans.correctionMarks;
              });
              q.marks = qmarks;
              if (q.marks != null) {
                marks = marks + q.marks;
              }
            }
          });
          $scope.response.test.score = marks;
        }

        return marks;
      };

      $scope.updateScoreModal = function () {
        $("#updateScoreModal").modal("show");
        $scope.updateScoreProgress = "";
      };

      $scope.updateScore = function () {
        $scope.dataObj = {
          test: {
            id: $scope.response.test.id,
            solvedCount: $scope.response.test.solvedCount,
            correctCount: $scope.response.test.correctCount,
            score: $scope.response.test.score,
          },
          student: {
            id: localStorage.studentId,
            teacherId: teacherID,
          },
          requestType: "Admin",
        };

        $scope.updateScoreProgress = "Saving ..";

        userService
          .callAdminService($scope, "updateScore")
          .then(function (response) {
            // console.log("Done!", response);
            if (response != null && response.status.statusCode == 200) {
              $scope.updateScoreProgress = "Updated score successfully!";
              $("#updateScoreModal").modal("hide");
            } else {
              $scope.updateScoreProgress = "Failed to update score..";
            }
          });
      };

      // To show the dropdown for marks with dynamic steps
      if (isNaN(localStorage.getItem("stepForMarks"))) {
        $scope.stepForMarks = 1;
      } else {
        $scope.stepForMarks = localStorage.getItem("stepForMarks");
      }

      $scope.range = function (min, max, step) {
        // console.log(parseFloat(step));
        step = parseFloat(step) || 1;
        var input = [];
        for (var i = min; i <= max; i += step) {
          input.push(Number(i.toPrecision(3)));
        }
        return input;
      };

      /**
       * To save the stepForMarks value in localstorage to fetch onload later
       * To solve the bug where fractional values in dropdowns were blank on reload of page
       * @param {*} stepForMarks
       */
      $scope.saveStepsBetweenMarks = function (stepForMarks) {
        // console.log("stepForMarks", stepForMarks);
        localStorage.setItem("stepForMarks", stepForMarks);
      };

      $scope.updateEvaluation = function (answer) {
        $scope.calculateMarks();

        var teacher = null;
        if (teacherID != null && teacherID > 0) {
          console.log("Adding teacher ID as " + teacherID);
          teacher = { id: teacherID };
        }

        $scope.dataObj = {
          question: {
            id: answer.id,
            marks: answer.correctionMarks,
            weightage: $scope.question.marks,
          },
          test: {
            score: $scope.response.test.score,
          },
          student: teacher,
          requestType: "Admin",
        };

        userService
          .callAdminService($scope, "updateEvaluation")
          .then(function (response) {
            // console.log("Done!", response);
            if (response != null && response.status.statusCode == 200) {
              // console.log("Updated evaluation ..");
              Snackbar.show({ text: "Marks updated." });

              if (response.status.responseText != "OK") {
                $scope.question.evaluated = parseInt(
                  response.status.responseText
                );
              } else {
                $scope.question.evaluated = 2;
              }
              //$scope.$apply();
            } else {
              alert("Failed to update score..Please try again");
            }
          });
      };

      var currQ = 0;

      /**
       * Jumps to a question on click of the question number
       * @param {*} qNo
       */
      $scope.jumpToQuestionEvaluation = function (question) {
        var i = 0;
        for (i = 0; i < $scope.response.test.test.length; i++) {
          var q = $scope.response.test.test[i];
          if (question.qn_id == q.qn_id) {
            currQ = i;
            $scope.question = $scope.response.test.test[currQ];
            console.log("Foundn at " + i);
            return;
          }
        }
      };

      $scope.evaluationStatus = function (q) {
        if (q.evaluated == null || q.evaluated == 0) {
          if (q.answerFiles == null || q.answerFiles.length == 0) {
            return "btn-light";
          }
          return "btn-secondary";
        }
        if (q.evaluated == 2) {
          return "btn-success";
        }
        return "btn-warning";
      };

      /**
       * Filter to only show the description questions
       * @param {*} q
       * @returns
       */
      $scope.descriptiveFilter = function (q) {
        return q.type == "DESCRIPTIVE";
      };

      $scope.nextEvaluation = function () {
        if ($scope.response.test.test.length > currQ) {
          var temp = currQ + 1;
          var tempQ = $scope.response.test.test[temp];
          if ($scope.descriptiveFilter(tempQ)) {
            currQ++;
            $scope.question = $scope.response.test.test[currQ];
          }
        }
      };

      $scope.previousEvaluation = function () {
        if (currQ > 0) {
          var temp = currQ - 1;
          var tempQ = $scope.response.test.test[temp];
          if ($scope.descriptiveFilter(tempQ)) {
            currQ--;
            $scope.question = $scope.response.test.test[currQ];
          }
        }
      };
    }
  );

angular
  .module("app")
  .controller(
    "testAnalysis",
    function ($scope, userService, $location, $interval) {
      // console.log("Test analysis loaded ..");

      $scope.selectdTest = null;
      var checkServicesSuccess;

      // testId = localStorage.getItem("testIdAnalysis");
      testId = localStorage.getItem("testid_result");
      //testId = 7;
      if (testId == null) {
        alert("Please select a Test first!");
        return;
      } else {
        $scope.selectdTest = testId;
      }
      // console.log("Test ID from localstorage:" + testId);

      $scope.test = {};
      $scope.students = [];

      var datatableInit = false;
      var dt, dt1;

      $scope.loadData = function () {
        $scope.dataObj = {
          test: {
            id: $scope.selectdTest,
          },
        };
        $scope.students = [];
        userService
          .callAdminService($scope, "getTestAnalysis")
          .then(function (response) {
            // console.log(response);

            if (!response) {
              $("#question-analysis-loading").hide();
              alert(
                "There was some error loading Test Questions analysis. Please reload and try again."
              );
              return;
            }

            $scope.test = response.test;
            $("#question-analysis-loading").hide();
            $("#test-analysis-overview-block").show();

            //Chaning the page <title></title> dynamically from here
            //Setting the page title with test name
            //To Give this file name to exported excel as well
            document.title = $scope.test.name + " - Result Analysis - Edofox";

            setTimeout(applyMathJax, 1000);

            $("#test-analysis-loading").hide();

            // Loading chart for test Summary
            if (response.test.analysis) {
              Highcharts.chart("test_summary_graph_div", {
                chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: "pie",
                },
                credits: {
                  enabled: false,
                },
                exporting: {
                  menuItemDefinitions: {
                    // Custom definition
                    labelHideDataTable: {
                      onclick: function () {
                        $(".highcharts-data-table").html("");
                      },
                      text: "Hide data table",
                    },
                  },
                  buttons: {
                    contextButton: {
                      menuItems: [
                        {
                          textKey: "printChart",
                          onclick: function () {
                            this.print();
                          },
                        },
                        "downloadPNG",
                        "downloadJPEG",
                        "downloadPDF",
                        "downloadSVG",
                        "downloadCSV",
                        "downloadXLS",
                        {
                          textKey: "viewData",
                          onclick: function () {
                            this.viewData();
                          },
                        },
                        "label",
                        "labelHideDataTable",
                      ],
                    },
                  },
                },
                title: {
                  text: "Test Summary",
                  useHTML: true,
                  style: {
                    fontFamily: "Verdana",
                    textAlign: "center",
                    fontWeight: "bold",
                    textDecoration: "underline",
                  },
                },
                tooltip: {
                  pointFormat: "{series.name}: <b>{point.y}</b>",
                },
                plotOptions: {
                  pie: {
                    allowPointSelect: true,
                    cursor: "pointer",
                    dataLabels: {
                      enabled: true,
                      format: "<b>{point.name}</b>: {point.y}",
                      style: {
                        color:
                          (Highcharts.theme &&
                            Highcharts.theme.contrastTextColor) ||
                          "black",
                      },
                      connectorColor: "silver",
                    },
                  },
                },
                series: [
                  {
                    name: "Share",
                    data: [
                      {
                        name: "Average Attempted",
                        color: "#64b5f6",
                        y: Math.round(response.test.analysis.averageAttempted),
                      },
                      {
                        name: "Average Correct",
                        color: "#aed581",
                        y: Math.round(response.test.analysis.averageCorrect),
                      },
                      {
                        name: "Average Wrong",
                        color: "#e57373",
                        y: Math.round(
                          response.test.analysis.averageAttempted -
                            response.test.analysis.averageCorrect
                        ),
                      },
                    ],
                  },
                ],
              });

              // Students Present Graph
              Highcharts.chart("students_present_graph_div", {
                chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: "pie",
                },
                credits: {
                  enabled: false,
                },
                exporting: {
                  menuItemDefinitions: {
                    // Custom definition
                    labelHideDataTable: {
                      onclick: function () {
                        $(".highcharts-data-table").html("");
                      },
                      text: "Hide data table",
                    },
                  },
                  buttons: {
                    contextButton: {
                      menuItems: [
                        {
                          textKey: "printChart",
                          onclick: function () {
                            this.print();
                          },
                        },
                        "downloadPNG",
                        "downloadJPEG",
                        "downloadPDF",
                        "downloadSVG",
                        "downloadCSV",
                        "downloadXLS",
                        {
                          textKey: "viewData",
                          onclick: function () {
                            this.viewData();
                          },
                        },
                        "label",
                        "labelHideDataTable",
                      ],
                    },
                  },
                },
                title: {
                  text: "Students Presenty",
                  useHTML: true,
                  style: {
                    fontFamily: "Verdana",
                    textAlign: "center",
                    fontWeight: "bold",
                    textDecoration: "underline",
                  },
                },
                tooltip: {
                  pointFormat: "{series.name}: <b>{point.y}</b>",
                },
                plotOptions: {
                  pie: {
                    allowPointSelect: true,
                    cursor: "pointer",
                    dataLabels: {
                      enabled: true,
                      format: "<b>{point.name}</b>: {point.y}",
                      style: {
                        color:
                          (Highcharts.theme &&
                            Highcharts.theme.contrastTextColor) ||
                          "black",
                      },
                      connectorColor: "silver",
                    },
                  },
                },
                series: [
                  {
                    name: "Share",
                    data: [
                      {
                        name: "Students Absent",
                        color: "#e57373",
                        y: Math.round(response.test.analysis.studentsAbsent),
                      },
                      {
                        name: "Students Present",
                        color: "#aed581",
                        y: Math.round(response.test.analysis.studentsAppeared),
                      },
                    ],
                  },
                ],
              });

              // Adding datatable for the Question wise analysis
              if (dt1 != null) {
                dt1.destroy();
              }
              try {
                setTimeout(function () {
                  dt1 = $("#test_ques_table").DataTable({
                    columnDefs: [
                      {
                        targets: [1],
                        visible: false,
                        searchable: false,
                      },
                    ],
                    dom: "Blfrtip",
                    buttons: [
                      {
                        extend: "excel",
                        exportOptions: {
                          columns: ":visible",
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
                }, 1000);
              } catch (error) {
                console.log("Datatable error: ", error);
                alert(
                  "There was an error initializing the Question Analysis table. " +
                    error.message
                );
              }
            }
          });
      };

      // Saving settings of the dynamic subjects for ranking in test analysis
      $scope.subjectsOrderDropdown = null;
      $scope.testAnalysisSettingsError = null;
      $scope.saveTestAnalysisSettings = function () {
        // var subjectsOrder = $("#subjects-rank-order-dropdown").val();
        // console.log("$scope.subjectsOrderDropdown", $scope.subjectsOrderDropdown);
        // console.log("$scope.subjects", $scope.subjects.length);
        // console.log("$scope.subjectsOrderDropdown.length", $scope.subjectsOrderDropdown.length);

        if ($scope.subjectsOrderDropdown) {
          if ($scope.subjectsOrderDropdown.length != $scope.subjects.length) {
            $scope.testAnalysisSettingsError =
              "Please select all the subjects for ranking order";
            return;
          }

          $scope.subjectsOrder = $scope.subjectsOrderDropdown.join();
        }

        // console.log("$scope.subjectsOrder", $scope.subjectsOrder);

        $scope.testAnalysisSettingsError = null;

        $scope.showStudents();
        $("#settingsModal").modal("hide");
      };

      function createDataTable(editedPrintTitle, response) {
        if (dt != null) {
          return;
        }
        var response_test_id = response.test.id;

        // Check in array dynmic columns
        var total_columns = JSON.parse(response.dtColumns).length;
        // There are 6 columns for each subjects in result set 
        // Score, Solved, Unsolved, Correct, Incorrect, Deduction 
        var subjects_length = 6 * $scope.subjects.length;
        default_dt_index = total_columns - (subjects_length + 6);
        console.log(default_dt_index);
        dt = $("#test_student_table").DataTable({
          data: JSON.parse(response.dtRows),
          columns: JSON.parse(response.dtColumns),
          destroy: true,
          pageLength: 50,
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
              title: messageTopDataExcel + 6,
            },
            {
              extend: "print",
              exportOptions: {
                columns: ":visible",
              },
              title: tableTopData,
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
          fnRowCallback: function (nRow, aData, iDisplayIndex) {
            // console.log(aData);
            modifyRow(nRow, response_test_id);
            return nRow;
          },
          paging: true,
          colReorder: true,
        });
        console.log("table created ..");

        //Hide details by default
        $scope.toggleDetails(default_dt_index);
      }

      function getValue(value) {
        return value != null ? value : "";
      }

      // This function added to add link on student name in test result page
      function modifyRow(row, test_id) {
        $(row)
          .children()
          .each(function (index, td) {
            if (index === 3) {
              var student_selected_options_url =
                apachehost + "/tests/student_test_options";
              var td_val = $(td).text();
              $(td).html(
                "<a href= ' " +
                  student_selected_options_url +
                  "/" +
                  test_id +
                  "/" +
                  td_val +
                  " ' target='_blank'>" +
                  td_val +
                  "</a>"
              );
            }
          });
      }

      $scope.showDetails = false;

      $scope.toggleDetails = function (default_dt_index) {
        if (dt == null) {
          return;
        }
        if (!$scope.showDetails) {
          //Hide/Show detail columns
          var startIndex = default_dt_index + $scope.subjects.length;
          for (
            var i = startIndex;
            i < startIndex + 5 * $scope.subjects.length;
            i++
          ) {
            dt.column(i).visible(false);
          }
        } else {
          var startIndex = default_dt_index + $scope.subjects.length;
          for (
            var i = startIndex;
            i < startIndex + 5 * $scope.subjects.length;
            i++
          ) {
            dt.column(i).visible(true);
          }
        }
      };

      $scope.subjectsOrder = null;
      $scope.showStudents = function () {
        var requestType;
        if ($scope.showAbsent) {
          requestType = "SHOW_ABSENT";
        }
        $scope.dataObj = {
          test: {
            id: $scope.selectdTest,
          },
          requestType: requestType,
          sortFilter: $scope.subjectsOrder,
        };

        // if (dt != null) {
        //     //dt.clear().draw();
        //     dt.destroy();
        // }

        console.log("Calling test results ..");

        userService
          .callAdminService($scope, "getTestResults")
          .then(function (response) {
            // console.log(response);
            if (!response) {
              $("#student-list-loading").hide();
              alert(
                "There was some error loading Students analysis. Please reload and try again."
              );
              return;
            }

            if (response.status.statusCode != 200) {
              $("#student-list-loading").hide();
              alert(response.status.responseText);
              return;
            }

            $scope.subjects = response.test.subjects;

            //console.log($scope.subjects);
            //console.log(response.students);

            //Old code removed as adding students dynamically in datatable as rows
            //$scope.students = response.students;

            $("#student-list-loading").hide();
            $("#show-details-student-list-div").show();

            // Custom title for print page of the table
            const testStartDate = new Date(response.test.startDate);
            const month = testStartDate.toLocaleString("default", {
              month: "long",
            });
            const testStartDateString =
              testStartDate.getUTCDate() +
              " " +
              month +
              " " +
              testStartDate.getUTCFullYear();
            var editedPrintTitle =
              response.test.name + " Result (" + testStartDateString + ")";

            if (dt == null) {
              setTimeout(function () {
                createDataTable(editedPrintTitle, response);
              }, 1000);
            } else {
              //Datatable is already ready..just fill the data
              dt.clear().draw();
              dt.rows.add(JSON.parse(response.dtRows)).draw();
              // console.log(JSON.parse(response.dtRows));
            }

            // Loading highcharts for top 10 students
            var top10Students = [];

            // Getting names of top 10 students for x axis

            var noOfStudentsInGraph = 10;
            if (response.students.length < 10) {
              noOfStudentsInGraph = response.students.length;
            }

            for (var i = 0; i < noOfStudentsInGraph; i++) {
              top10Students.push(response.students[i].name);
            }

            var highchartsSeriesObject = [];

            response.test.subjects.forEach((element) => {
              // console.log(element);

              var subjectwiseMarks = [];
              for (var j = 0; j < noOfStudentsInGraph; j++) {
                response.students[j].analysis.subjectScores.forEach(
                  (subjectwiseAnalysisObject) => {
                    if (subjectwiseAnalysisObject.subject === element) {
                      subjectwiseMarks.push(subjectwiseAnalysisObject.score);
                    }
                  }
                );
              }

              var tempDataObject = {
                name: element,
                data: subjectwiseMarks,
              };
              highchartsSeriesObject.push(tempDataObject);
            });

            // console.log("highchartsSeriesObject", highchartsSeriesObject);

            Highcharts.chart("top_score_students_graph_div", {
              chart: {
                type: "column",
              },
              credits: {
                enabled: false,
              },
              exporting: {
                menuItemDefinitions: {
                  // Custom definition
                  labelHideDataTable: {
                    onclick: function () {
                      $(".highcharts-data-table").html("");
                    },
                    text: "Hide data table",
                  },
                },
                buttons: {
                  contextButton: {
                    menuItems: [
                      {
                        textKey: "printChart",
                        onclick: function () {
                          this.print();
                        },
                      },
                      "downloadPNG",
                      "downloadJPEG",
                      "downloadPDF",
                      "downloadSVG",
                      "downloadCSV",
                      "downloadXLS",
                      {
                        textKey: "viewData",
                        onclick: function () {
                          this.viewData();
                        },
                      },
                      "label",
                      "labelHideDataTable",
                    ],
                  },
                },
              },
              title: {
                text: "Top 10 Students",
                useHTML: true,
                style: {
                  fontFamily: "Verdana",
                  textAlign: "center",
                  fontWeight: "bold",
                  textDecoration: "underline",
                },
              },
              xAxis: {
                categories: top10Students,
                crosshair: true,
              },
              yAxis: {
                title: {
                  text: "Marks",
                },
                stackLabels: {
                  enabled: true,
                  style: {
                    fontWeight: "bold",
                    color:
                      (Highcharts.theme && Highcharts.theme.textColor) ||
                      "gray",
                  },
                },
              },
              legend: {
                // align: 'right',
                // x: -30,
                // verticalAlign: 'top',
                // y: 25,
                // floating: true,
                backgroundColor:
                  (Highcharts.theme && Highcharts.theme.background2) || "white",
                borderColor: "#CCC",
                borderWidth: 1,
                shadow: false,
              },
              tooltip: {
                headerFormat: "<b>{point.x}</b><br/>",
                pointFormat:
                  "{series.name}: {point.y}<br/>Total: {point.stackTotal}",
              },
              plotOptions: {
                series: {
                  stacking: "normal",
                },
              },
              series: highchartsSeriesObject,
            });

            // Checking whether services have received successful response
            // To calculate and show count of students above average marks and so on
            checkServicesSuccess = $interval(function () {
              // console.log("Checking whether services got response");

              if ($scope.students && $scope.subjectAnalysis && $scope.test) {
                // console.log("services got response $scope.students", $scope.students);
                // console.log("services got response $scope.subjectAnalysis", $scope.subjectAnalysis);

                // Stop the timer
                $interval.cancel(checkServicesSuccess);
                checkServicesSuccess = null;

                // Do the calculations here
                for (var i = 0; i < $scope.subjectAnalysis.length; i++) {
                  var subjectAverageMarks =
                    $scope.subjectAnalysis[i].analysis.subAvg;

                  // console.log("subjectAverageMarks", subjectAverageMarks);

                  var aboveSubjectAverageScoringCount = 0;
                  var aboveTestAverageScoringCount = 0;
                  // Looping through the students array
                  for (var j = 0; j < $scope.students.length; j++) {
                    $scope.students[j].analysis.subjectScores.forEach(function (
                      subjectScore
                    ) {
                      if (
                        subjectScore.subject ===
                          $scope.subjectAnalysis[i].subject &&
                        subjectScore.score >= subjectAverageMarks
                      ) {
                        aboveSubjectAverageScoringCount++;
                      }
                    });

                    // Check if student scored in total above exam average score
                    if (
                      $scope.students[j].analysis.score >=
                      $scope.test.analysis.averageScore
                    ) {
                      aboveTestAverageScoringCount++;
                    }
                  }

                  $scope.subjectAnalysis[
                    i
                  ].analysis.aboveSubjectAverageScoringCount = aboveSubjectAverageScoringCount;
                  $scope.test.analysis.aboveTestAverageScoringCount =
                    aboveTestAverageScoringCount;
                }
              }

              // console.log("post processing $scope.subjectAnalysis", $scope.subjectAnalysis);
            }, 2000);
          });

        fetchSubjectAnalysis();
      };

      /**
       * To fetch Subjectwise analysis of test
       */
      function fetchSubjectAnalysis() {
        $scope.dataObj = {
          test: {
            id: $scope.selectdTest,
          },
          columnSequence: $scope.subjectsOrder,
        };

        userService
          .callAdminService($scope, "getSubjectTestAnalysis")
          .then(function (response) {
            // console.log(response);

            if (response) {
              $("#subjectwise-analysis-div").show();
              $("#subjectwise-analysis-loading").hide();

              $scope.subjectAnalysis = response.test.test;
            }
          });
      }

      $scope.showResults = function (selectTestID) {
        // console.log($scope.selectdTest);
        $scope.loadData();
        $scope.showStudents();
      };

      $scope.loadData();
      $scope.showStudents();

      $scope.sendNotification = function () {
        var requestType = "SMS";
        // var additionalMessage = $("#additional_msg").val();
        var additionalMessage = null;
        var mailer = null;
        if (additionalMessage != null && additionalMessage != "") {
          mailer = {
            additionalMessage: additionalMessage,
          };
        }
        $scope.dataObj = {
          test: {
            id: $scope.selectdTest,
          },
          mailer: mailer,
          requestType: requestType,
        };

        $scope.sendProgress = "Sending SMS/Email ..";
        userService
          .callAdminService($scope, "getTestResults")
          .then(function (response) {
            // console.log("SMS response", response);
            if (response != null) {
              if (response.status.statusCode == 200) {
                $scope.sendProgress =
                  "SMS/Email process started successfully. Please check after sometime";
              } else {
                $scope.sendProgress = response.status.responseText;
              }
            } else {
              $scope.sendProgress =
                "Some error while connecting to the server ..";
            }
          });
      };
    }
  );

app.filter("toMinSec", function () {
  return function (input) {
    var minutes = parseInt(input / 60, 10);
    var seconds = input % 60;

    return (
      minutes + " minutes" + (seconds ? " and " + seconds + " seconds" : "")
    );
  };
});
