app.controller('mainCtrl', ['$rootScope', '$scope', '$http', '$element', '$interval', 'userService', 'preloader', '$sce', function ($rootScope, $scope, $http, $element, $interval, userService, preloader, $sce) {


    //To launch test in full screen on first click in the test
    $scope.launchFullScreen = function () {
        // https://stackoverflow.com/questions/19355370/how-to-open-a-web-page-automatically-in-full-screen-mode
        // console.log("launchFullScreen");
        var element = document.documentElement;
        if (element.requestFullScreen) {
            element.requestFullScreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullScreen) {
            element.webkitRequestFullScreen();
        }
    }


    /**
     * Showing test instructions in a modal 
     */
    if (localStorage.testInstructions != null) {
        $scope.testInstructions = $sce.trustAsHtml(localStorage.testInstructions);
        // console.log("localStorage.testInstructions",localStorage.testInstructions);
    } else {
        // Hide the instructions button
        $(".btn-instructions").hide();
    }


    $scope.testName = localStorage.testName;
    $scope.studentName = localStorage.studentName;

    var connectivityInterval;
    var setTimeInterval;

    //$scope.imageCachedData1 = [ "http://172.104.47.180:8080/edofox/service/getImage/19/question", "","http://172.104.47.180:8080/edofox/service/getImage/20/question"];

    function showError() {

        swal({
            title: "Error",
            text: "Hold on! There is a glitch in the test. Please restart your test.",
            icon: "error",
            timer: 5000,
            buttons: false,
            closeOnEsc: false,
            closeOnClickOutside: false
        })
            .then(() => {

                window.location.href = "index.php";

            });

    }

    var studentId;
    var testId;

    try {

        studentId = parseInt(localStorage.studentId);
        testId = parseInt(localStorage.testId);

        $scope.studentId = studentId;

    } catch (e) {

    }

    if (studentId == null || testId == null) {
        showError();
        return;
    }

    //123-456
    var key = studentId + "-" + testId;

    if (localStorage.getItem(key) == null) {
        //No test found
        showError();
        return;

    }

    /*$scope.op1 = false, $scope.op2 = false, $scope.op3 = false, $scope.op4 = false, $scope.op5 = false;

    function checkOptions() {
        $scope.op1 = false;
        $scope.op2 = false;
        $scope.op3 = false;
        $scope.op4 = false;

        if ($scope.exam.type == 'MULTIPLE' && $scope.exam.answer != null) {
            if ($scope.exam.answer.indexOf('option1') >= 0) {
                $scope.op1 = true;
            }
            if ($scope.exam.answer.indexOf('option2') >= 0) {
                $scope.op2 = true;
            }
            if ($scope.exam.answer.indexOf('option3') >= 0) {
                $scope.op3 = true;
            }
            if ($scope.exam.answer.indexOf('option4') >= 0) {
                $scope.op4 = true;
            }
            if ($scope.exam.answer.indexOf('option5') >= 0) {
                $scope.op5 = true;
            }
        }
        console.log($scope.op1, $scope.op2, $scope.op3, $scope.op4);
    }*/

    var backupQuestion = {};

    var questionTime = 0;

    $scope.Question = function (index, saveAnswer) { //get question at index

        if (index < $scope.data.test.length && index >= 0) {
            if ($scope.exam != null && !saveAnswer) {
                if (backupQuestion.timeSpent == null) {
                    backupQuestion.timeSpent = 0;
                }
                //Save time
                backupQuestion.timeSpent = backupQuestion.timeSpent + questionTime;
                //Save review state
                backupQuestion.flagged = $scope.exam.flagged;
                //Save question if response is cleared
                if ($scope.exam.answer == null && $scope.exam.type != 'MATCH') {
                    //Save for other question types as well 08/07/21
                    clearResponse(backupQuestion);

                } else if ($scope.exam.type == 'MATCH' && !matchQuestionSolved($scope.exam)) {
                    //Clear response for MATCH question 08/07/21
                    clearResponse(backupQuestion);
                } else if ((!$scope.exam.saved && isQuestionAnswered($scope.exam)) || isAnswerChanged($scope.exam, backupQuestion)) {
                    // When option is selected, and navigated to another question without saving the answer,
                    // then show an alert
                    //Only show alert if not subjective test
                    if ($scope.data.testUi != 'DESCRIPTIVE') {
                        // alert("WARNING!!! Your answer was not saved. If you want to save your answers, you need to click on SAVE & NEXT.");
                        $.snackbar({
                            content: "WARNING!!! Your answer was not saved. If you want to save your answers, you need to click on SAVE & NEXT.",
                            timeout: 10000
                        });
                    }

                }
                //Changes for subjective exam, as there is no SAVE AND NEXT BUTTON
                if ($scope.exam.answersUploaded != null) {
                    backupQuestion.answersUploaded = $scope.exam.answersUploaded;
                }

                if ($scope.exam.uploadInProgress != null) {
                    backupQuestion.uploadInProgress = $scope.exam.uploadInProgress;
                    backupQuestion.filesToUpload = $scope.exam.filesToUpload;
                }
                //flush all answers / revert the changes
                $scope.data.test[$scope.data.i] = backupQuestion;
                backupQuestion = {};
                /*$scope.exam.answer = null;
                $scope.exam.op1 = null; $scope.exam.op2 = null; $scope.exam.op3 = null; $scope.exam.op4 = null;
                if ($scope.exam.complexOptions != null && $scope.exam.complexOptions.length > 0) {

                    question.complexOptions.forEach(function (op) {
                        if (op.matchOptions != null && op.matchOptions.length > 0) {
                            op.matchOptions.forEach(function (match) {
                                match.selected = false;
                            });
                        }
                    });
                }*/
                if ($scope.exam.timeSpent == null) {
                    $scope.exam.timeSpent = 0;
                }
                $scope.exam.timeSpent = $scope.exam.timeSpent + questionTime;
                // console.log("Setting timeSpent " + $scope.data.test[$scope.data.i].timeSpent);

                //Save time spent to the server
                saveTimeSpent();

            } else if ($scope.exam != null) {
                $scope.exam.saved = true;
                // console.log("Saved!!");
                if ($scope.exam.timeSpent == null) {
                    $scope.exam.timeSpent = 0;
                }
                $scope.exam.timeSpent = $scope.exam.timeSpent + questionTime;
                // console.log("Setting timeSpent 2 " + $scope.exam.timeSpent);
            }
            $scope.data.i = index;



            var timeTakenToLoadQuestion = 0;
            //Calculate the time to load image to understand network performance
            var calculateImageLoadingTime = $interval(function () {
                timeTakenToLoadQuestion++;
            }, 1000);

            //Stopping the timer before loading the question image
            //Very important when network speed is slow
            $interval.cancel(setTimeInterval);
            setTimeInterval = null;

            $('#questions-image-div p').show();
            $('#questions-image-div img').hide();
            /**
             * To show a loading text when the images are loading
             * https://github.com/desandro/imagesloaded
             */
            $('#questions-image-div').imagesLoaded()
                .always(function (instance) {
                    // console.log('all images loaded');
                })
                .done(function (instance) {

                    //Resuming the timer of exam after image is loaded.
                    if (setTimeInterval === null) {
                        setTimeInterval = $interval(setTime, 1000);
                    }

                    // console.log('all images successfully loaded');

                    $('#questions-image-div img').show();
                    $('#questions-image-div p').hide();

                    $interval.cancel(calculateImageLoadingTime);

                    if ($scope.exam != null && $scope.exam.ttl == null) {
                        $scope.exam.ttl = timeTakenToLoadQuestion;
                    }

                })
                .fail(function () {

                    // console.log('all images loaded, at least one is broken');
                    $('#questions-image-div p').show();
                    $('#questions-image-div img').hide();

                    $interval.cancel(calculateImageLoadingTime);

                    if ($scope.exam != null && $scope.exam.ttl == null) {
                        $scope.exam.ttl = timeTakenToLoadQuestion;
                    }

                })
                .progress(function (instance, image) {

                    var result = image.isLoaded ? 'loaded' : 'broken';
                    // console.log('image is loading in progress');
                    // console.log('image is ' + result + ' for ' + image.img.src);
                    // console.log('Loading Instance is: ', instance);

                    $('#questions-image-div p').show();
                    $('#questions-image-div img').hide();
                });





            $scope.exam = $scope.data.test[$scope.data.i];
            backupQuestion = angular.copy($scope.exam, backupQuestion);
            $scope.exam.visited = true;

            backupQuestion.visited = true;
            // console.log("Got question" + JSON.stringify($scope.exam));
            questionTime = 0;
            saveToLocal();
            //Display currentQuestion
            applyMathJax($scope.exam);
            //Add delay if type changed
            //If type changed..add delay
            //if ($scope.data.i == 0 || backupQuestion == null || $scope.exam == null || backupQuestion.type != $scope.exam.type) {
            // console.log("adding delay...");
            setTimeout(function () {
                applyMathJax($scope.exam);
            }, 500);
            //}


        } else if (index == $scope.data.test.length && saveAnswer) {
            $scope.exam.saved = true;
            backupQuestion = $scope.exam;
            // console.log("Saved last!!");
            saveToLocal();
        }

        //$scope.$apply();

    }

    init();


    $scope.updateAns = function () { //submit the answer when question is attempted and color in the corresponding button in navigation
        if ($scope.data.clock != 0) {
            saveToLocal();
            // console.log("data updated ...." + localStorage.getItem(key));
            addQuestionActivity();
        } else {
            swal("Answers won't be submitted as the test is ended");
        }

    }

    function updateExamBoolean(answer, value) {
        if (answer.indexOf('option1') >= 0) {
            $scope.exam.op1 = value;
        }
        if (answer.indexOf('option2') >= 0) {
            $scope.exam.op2 = value;
        }
        if (answer.indexOf('option3') >= 0) {
            $scope.exam.op3 = value;
        }
        if (answer.indexOf('option4') >= 0) {
            $scope.exam.op4 = value;
        }
        if (answer.indexOf('option5') >= 0) {
            $scope.exam.op5 = value;
        }
    }

    function updateAnswerBoolean(answer, value) {
        if (answer.answer.indexOf('option1') >= 0) {
            answer.op1 = value;
        }
        if (answer.answer.indexOf('option2') >= 0) {
            answer.op2 = value;
        }
        if (answer.answer.indexOf('option3') >= 0) {
            answer.op3 = value;
        }
        if (answer.answer.indexOf('option4') >= 0) {
            answer.op4 = value;
        }
        if (answer.answer.indexOf('option5') >= 0) {
            answer.op5 = value;
        }
    }


    $scope.questionTypeDisplay = function (question) {
        if (question != null && question.type != null) {
            if (question.type == 'SINGLE') {
                return "Single correct"
            }
            if (question.type == 'MULTIPLE') {
                return "Multiple correct"
            }
            if (question.type == 'NUMBER') {
                return "Numeric answer"
            }
            if (question.type == 'MATCH') {
                return "Match the columns"
            }
            return "";
        }
        return "";
    }




    $scope.addAnswer = function (answer) {
        if ($scope.exam.answer == null) {
            $scope.exam.answer = "";
        }
        // console.log($scope.exam.answer + " -- " + $scope.exam.answer.indexOf(answer));
        if ($scope.exam.answer.indexOf(answer) >= 0) {
            $scope.exam.answer = $scope.exam.answer.replace(answer + ",", "");
            updateExamBoolean(answer, false);
        } else {
            $scope.exam.answer = $scope.exam.answer + answer + ",";
            updateExamBoolean(answer, true);
        }
        // console.log("Answer - " + $scope.exam.answer);

        if ($scope.exam.answer.trim().length < 3) {
            $scope.exam.answer = null;
            //Call server as answer is technically cleared (Even though not with Clear button)
            saveAnswer();
        }
        saveToLocal();
        addQuestionActivity(null);
    }


    $scope.checkIfRemoved = function () {
        if ($scope.exam.answer != null && $scope.exam.answer.trim() == "") {
            $scope.exam.answer = null;
            saveToLocal();
            addQuestionActivity('Cleared');
        }
    }



    function findPosition(question) {
        var i = 0;
        for (i = 0; i < $scope.data.test.length; i++) {
            var q = $scope.data.test[i];
            if (q.qn_id == question.qn_id) {
                return i;
            }
        }
    }

    $scope.setQuestion = function (question) {
        // console.log(question.qn_id);
        //var pos = $scope.data.test.map(function(e) { return question.qn_id; }).indexOf(question.qn_id);
        var pos = findPosition(question);
        // console.log(pos);
        if (pos != null && pos >= 0) {
            $scope.Question(pos);
        }

        addQuestionActivity('Navigate');

    }

    $scope.jumpTo = function (subject) {
        if ($scope.data == null || $scope.data.test == null) {
            return;
        }
        $scope.data.test.every(function (question, index) {
            if (question.subject == subject) {
                $scope.setQuestion(question);
                return false;
            } else {
                return true;
            }
        });
        addQuestionActivity('Navigate');
    }

    $scope.jumpToSection = function (section) {
        if ($scope.data == null || $scope.data.test == null) {
            return;
        }
        $scope.data.test.every(function (question, index) {
            if (question.section == section) {
                $scope.setQuestion(question);
                return false;
            } else {
                return true;
            }
        });
        addQuestionActivity('Navigate');
    }


    $scope.questionStyle = function (question) {

        if (question == null) {
            return;
        }

        var style = "";
        if ($scope.exam.qn_id == question.qn_id) {
            style = "background-position: -57px -127px;width: 49px;height: 49px;color:#fff;";
        }

        if (question.flagged == 1 && question.saved) {
            style = "background-position: -108px -122px;height: 49px;color:#fff;";
            // console.log("Only flagged!" + question.id);
        } else if (question.visited) {
            style = "background-position: -57px -127px;color:#fff;";
            // console.log("Only visited!" + question.id);
        }

        if (question.answer != null && question.answer.length > 0 && question.saved) {
            if (question.flagged == 1) {
                style = "background-position: -66px -178px;color:#fff;";
                // console.log("Answered and reviewed!" + question.id);
            } else {
                style = "background-position: -4px -5px;color:#fff;";
                // console.log("Answered!" + question.id);
            }
        } else if (question.complexOptions != null && question.complexOptions.length > 0) {
            question.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        if (match.selected && question.saved) {
                            if (question.flagged == 1) {
                                style = "background-position: -66px -178px;color:#fff;";
                            } else {
                                style = "background-position: -4px -5px;color:#fff;";
                            }

                        }
                    });
                }

            });

        }

        return style;

    }

    $scope.questionsStatusClassName = function (question) {

        if (question == null) {
            return;
        }

        var className = "";
        if ($scope.exam.qn_id == question.qn_id) {
            className = "questions_not_answered";
        }

        if (question.flagged == 1 && question.saved) {
            className = "questions_marked_for_review";
            //console.log("Only flagged!");
        } else if (question.visited) {
            className = "questions_not_answered";
        }

        if (question.answer != null && question.answer.length > 0 && question.saved) {
            if (question.flagged == 1) {
                className = "questions_answered_and_marked_for_review";
                //console.log("Answered and reviewed!");
            } else {
                className = "questions_answered";
            }
        } else if (question.complexOptions != null && question.complexOptions.length > 0) {
            question.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        if (match.selected && question.saved) {
                            if (question.flagged == 1) {
                                className = "questions_answered_and_marked_for_review";
                            } else {
                                className = "questions_answered";
                            }

                        }
                    });
                }

            });

        }

        return className;

    }



    // Added By @RushiB For Subjective Exam Question Status 
    // Date 2021/06/04
    $scope.subjectiveQuestionsStatusClassName = function (question) {

        if (question == null) {
            return;
        }

        var className = "";
        if ($scope.exam.qn_id == question.qn_id) {
            className = "questions_not_answered";
        }

        if (question.flagged == 1 && question.saved) {
            className = "questions_marked_for_review";

        } else if (question.visited) {
            className = "questions_not_answered";
        }

        if (question.type == "DESCRIPTIVE") {
            if (question.answersUploaded != null && question.answersUploaded > 0) {
                if (question.flagged == 1) {
                    className = "questions_answered_and_marked_for_review";
                } else {
                    className = "questions_answered";
                }
            }
        } else {
            if (question.answer != null && question.answer.length > 0 && question.saved) {
                if (question.flagged == 1) {
                    className = "questions_answered_and_marked_for_review";
                    //console.log("Answered and reviewed!");
                } else {
                    className = "questions_answered";
                }
            } else if (question.complexOptions != null && question.complexOptions.length > 0) {
                question.complexOptions.forEach(function (op) {
                    if (op.matchOptions != null && op.matchOptions.length > 0) {
                        op.matchOptions.forEach(function (match) {
                            if (match.selected && question.saved) {
                                if (question.flagged == 1) {
                                    className = "questions_answered_and_marked_for_review";
                                } else {
                                    className = "questions_answered";
                                }

                            }
                        });
                    }

                });
            }
        }


        return className;

    }

    $scope.questionStyleRegular = function (question) {

        if (question == null) {
            return;
        }

        var style = "";
        if ($scope.exam.qn_id == question.qn_id) {
            style = "background-color:#ccc";
        }

        if (question.flagged == 1) {
            style = "background-color:#ffb4b4";
            // console.log("YEs!");
        } else if (question.answer != null) {
            style = "background-color:#5fcf80";
        }
        return style;


    }

    function clearResponse(question) {
        if (question == null) {
            return;
        }
        question.answer = null;
        question.op1 = false;
        question.op2 = false;
        question.op3 = false;
        question.op4 = false;
        if (question.complexOptions != null && question.complexOptions.length > 0) {
            question.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        match.selected = false;
                    });
                }

            });
        }
    }

    $scope.clear = function () {

        clearResponse($scope.exam);
        clearResponse(backupQuestion);

        saveToLocal();
        // console.log("Saved cleared response ..");
        saveAnswer();

        addQuestionActivity('Cleared');
    }

    function prepareRequest() {
        $scope.dataObj = {
            student: {
                id: localStorage.studentId
            },
            test: angular.copy($scope.data)
        };
        $scope.dataObj.test.device = device;
        $scope.dataObj.test.deviceInfo = deviceInfo;

        // console.log($scope.dataObj);
        var answered = [];
        $scope.data.test.forEach(function (question) {
            if (question.answer != null) {
                if (question.flagged == undefined) {
                    question.flagged = 0;
                }
                if (question.answer instanceof String) {
                    if (question.answer.trim().length > 0) {
                        answered.push(question);
                    } else if (question.type == 'MATCH') {
                        answered.push(question);
                    }
                } else {
                    answered.push(question);
                }

            } else if (question.flagged == 1) {
                answered.push(question);
            } else if (question.type == 'MATCH') {
                if (question.flagged == undefined) {
                    question.flagged = 0;
                }
                answered.push(question);
            }
        });
        $scope.dataObj.test.test = answered;
        // console.log($scope.dataObj);
    }

    $scope.sendData = function () { //function to end test and send the necessary data back o the server. called after end test button click.


        if (navigator.onLine) {

            prepareRequest();

            userService.callService($scope, "saveTest").then(function (response) {
                // console.log(response);
                if (response.status.statusCode != 200) {
                    swal("Error", response.status.responseText, "error");
                    return;
                }

                //Time spent for last question service called moved here to avoid concurrency 09/07/2021
                if ($scope.exam.timeSpent == null) {
                    $scope.exam.timeSpent = 0;
                }
                $scope.exam.timeSpent = $scope.exam.timeSpent + questionTime;
                saveTimeSpent();


                $scope.data.submitted = true;
                $scope.data.clock = 0;
                $scope.data.min = 0;
                $scope.data.sec = 0;
                saveToLocal();

                /**
                 * Remove OK button from the sweetalert dialogue
                 * Reference: 
                 * https://sweetalert.js.org/docs/#methods
                 * https://sweetalert.js.org/docs/#buttons
                 */

                swal({
                    title: "Test submitted successfully!",
                    text: "Please wait...",
                    icon: "success",
                    timer: 3000,
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                })
                    .then(() => {

                        window.location.href = "result.html";

                    });



            }).catch(function (error) {

                swal("Error", error + ". Please be patient. We have saved all your answers. Please reload and try again.", "error");

            });

        } else {

            swal("Network Issue", "Please check your internet connnectivity and submit again.", "error");

        }

    }


    $scope.range = function (subject) {
        var input = [];
        $scope.data.test.forEach(function (question) {
            if (question.subject == subject) {
                input.push(question);
            }

        });
        return input;

    }

    $scope.rangeSection = function (section) {
        var input = [];
        $scope.data.test.forEach(function (question) {
            if (question.section == section) {
                input.push(question);
            }

        });
        return input;

    }


    //Section wise summary

    function matchQuestionSolved(question) {
        var solved = false;

        if (question.complexOptions != null && question.complexOptions.length > 0) {

            question.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        if (match.selected) {
                            solved = true;
                        }
                    });
                }

            });
        }
        return solved;
    }


    $scope.getUnAnswered = function (section) {

        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (section == null || question.section == section) {

                if (question.visited && !question.flagged) {
                    if (question.type == 'MATCH') {
                        if (!matchQuestionSolved(question)) {
                            count++;
                        }
                    } else if (question.answer == null || question.answer.trim().length == 0) {
                        count++;
                    }
                }

                /*if (!question.saved) {
                    count++;
                } else {
                    if (question.type == 'MATCH') {
                        if (!matchQuestionSolved(question)) {
                            count++;
                        }
                    } else if (question.answer == null || question.answer.trim().length == 0) {
                        count++;
                    }
                }*/

            }

        });
        return count;
    }

    $scope.getUnAnsweredJee = function () {
        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (question.visited && !question.flagged) {
                if (question.type == 'MATCH') {
                    if (!matchQuestionSolved(question)) {
                        count++;
                    }
                } else if (question.answer == null || question.answer.trim().length == 0) {
                    count++;
                }
            }
            /*else {
                           
                       }*/
        });
        return count;
    }

    function isAnswerChanged(question, ref) {
        var answerChanged = false;
        if (question == null || ref == null) {
            return false;
        }
        if (question.answer == null && ref.answer != null) {
            return true;
        }
        if (question.answer != null && ref.answer == null) {
            return true;
        }
        if (question.answer != null && ref.answer != null && question.answer.trim() != ref.answer.trim()) {
            return true;
        } else if (question.type == 'MATCH') {
            if (question.complexOptions != null && question.complexOptions.length > 0 && ref.complexOptions != null && ref.complexOptions.length > 0) {
                question.complexOptions.forEach(function (op) {
                    if (op.matchOptions != null && op.matchOptions.length > 0) {
                        ref.complexOptions.forEach(function (refOp) {
                            if (refOp.optionName == op.optionName && refOp.matchOptions != null && refOp.matchOptions.length > 0) {
                                op.matchOptions.forEach(function (match) {
                                    refOp.matchOptions.forEach(function (refOpMatch) {
                                        if (match.optionName == refOpMatch.optionName) {
                                            if (match.selected != refOpMatch.selected) {
                                                answerChanged = true;
                                            }

                                        }
                                    });

                                });

                            }

                        });

                    }

                });
            }
        }
        return answerChanged;
    }

    function isQuestionAnswered(question) {
        if (question == null) {
            return false;
        }
        if (question.answer != null && question.answer.trim().length > 0) {
            return true;
        } else if (question.type == 'MATCH' && matchQuestionSolved(question)) {
            return true;
        }
        return false;
    }

    $scope.getAnswered = function (section) {

        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (section == null || question.section == section) {
                /*if (question.saved) {
                    if (question.answer != null && question.answer.trim().length > 0) {
                        count++;
                    } else if (question.type == 'MATCH' && matchQuestionSolved(question)) {
                        count++;
                    }
                }*/
                if (question.saved && !question.flagged) {
                    if (isQuestionAnswered(question)) {
                        count++;
                    }
                }
            }

        });
        return count;
    }

    $scope.getAnsweredJee = function () {

        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (question.saved && !question.flagged) {
                if (question.answer != null && question.answer.trim().length > 0) {
                    count++;
                } else if (question.type == 'MATCH' && matchQuestionSolved(question)) {
                    count++;
                }
            }
        });
        return count;
    }

    $scope.getReviewedJee = function () {

        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (question.flagged == 1 && question.saved) {
                if (question.answer == null || question.answer.trim().length == 0) {
                    count++;
                } else if (question.type == 'MATCH' && !matchQuestionSolved(question)) {
                    count++;
                }
                //count++;
            }
        });
        return count;
    }

    $scope.getReviewed = function (section) {

        var count = 0;
        $scope.data.test.forEach(function (question) {
            /*if (section == null || question.section == section) {
                if (question.flagged == 1 && question.saved) {
                    count++;
                }
            }*/
            if (question.flagged == 1 && question.saved) {
                if (question.answer == null || question.answer.trim().length == 0) {
                    count++;
                } else if (question.type == 'MATCH' && !matchQuestionSolved(question)) {
                    count++;
                }
                //count++;
            }

        });
        return count;
    }

    $scope.getReviewedAnswered = function (section) {
        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (section == null || question.section == section) {
                if (question.flagged == 1) {
                    if (question.answer != null && question.answer.trim().length > 0) {
                        count++;
                    } else if (question.type == 'MATCH' && matchQuestionSolved(question)) {
                        count++;
                    }
                }
            }

        });
        return count;
    }

    $scope.getUnVisited = function (section) {
        /*if (section == null) {
            return;
        }*/
        var count = 0;
        $scope.data.test.forEach(function (question) {
            if (section == null || question.section == section) {
                if (!question.visited) {
                    count++;
                }
            }

        });
        return count;
    }

    var submitting = false;

    /**
     * Checking connectivity with server
     */
    function checkConnectivityWithServer() {
        /**
         * Call service to check if the test has connection with the java server
         */
        userService.callGetService("ping").then(function (response) {

            // console.log("RESPONSE: ", response);  
            if (response.status === 200) {

                if ($scope.data.min < 0 || submitting) {

                    /**
                     * Cancel the 10 seconds timer if the clock is less than 0 minute
                     */
                    $interval.cancel(connectivityInterval);
                    connectivityInterval = null;

                    /**
                     * Cancel the clock timer of per second if clock is less than 0 minute
                     */
                    $interval.cancel(setTimeInterval);
                    setTimeInterval = null;

                    /**
                     * submitting test
                     */
                    $scope.sendData();

                }

                if (!submitting) {
                    // console.log("SWAL STATUS: ", swal.getState());
                    if (swal.getState().isOpen) {
                        swal.close();
                        if (setTimeInterval === null) {
                            setTimeInterval = $interval(setTime, 1000);
                        }
                    }
                    //Call save test activity to add 'RESUMED' entry
                    addStudentActivity('RECONNECTED');
                }

            } else {

                //Block UI only if offline conduction is false //13 Jan 21
                if ($scope.data.offlineConduction == 1) {
                    $.snackbar({
                        content: "Your internet connection is unstable..please check"
                    });
                    return;
                }
                $interval.cancel(setTimeInterval);
                setTimeInterval = null;
                submitting = false;
                swal({
                    title: "Error",
                    text: "You don't have network connectivity. The test will resume after the network connection is restored.",
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });

            }

        }).catch(function (error) {

            //Block UI only if offline conduction is false //13 Jan 21
            if ($scope.data.offlineConduction == 1) {
                return;
            }

            console.log("ERROR: ", error);

            swal({
                title: "Error",
                text: error,
                icon: "error",
                buttons: false,
                closeOnEsc: false,
                closeOnClickOutside: false
            });

        });
    }

    function setTime() { //function to set time and display clock for the test. scope variables sec,min,hour and localstorage variables

        // console.log("Timer called. Setting time.");

        if ($scope.data.clock != 0) { //sec,min,hour
            if ($scope.data.sec == 0) {
                $scope.data.min--;
                $scope.data.sec = 60;
            }
            if ($scope.data.min < 0) {
                $scope.data.submissionType = 'timeout';
                checkConnectivityAndSubmitTest();

            }
            $scope.data.sec--;
            saveToLocal();
        }
        questionTime++;
    }
    $scope.send_alert = function () {
        swal({
            title: "Warning",
            text: "Are you sure you want submit the test?",
            icon: "warning",
            dangerMode: true,
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: "Yes, End Test!",
                    value: true,
                    visible: true,
                    //   className: "",
                    closeModal: false
                }
            }
        })
            .then((value) => {
                if (value) {
                    $scope.data.submissionType = 'manual';
                    submitting = true;
                    checkConnectivityAndSubmitTest();

                } else {
                    // console.log("cancelling submit from sweet alert ..");
                    //If modal closed
                    submitting = false;
                }
            });

    }

    /**
     * Check connectivity with server before submitting the test
     */
    function checkConnectivityAndSubmitTest() {

        $interval.cancel(setTimeInterval);
        setTimeInterval = null;

        userService.callGetService("ping").then(function (response) {

            // console.log("RESPONSE: ", response);  
            if (response.status === 200) {

                $scope.sendData();

            } else {

                swal({
                    title: "Error",
                    text: "You don't have network connectivity. The test will resume after the network connection is restored.",
                    buttons: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false
                });

            }

        }).catch(function (error) {

            console.log("ERROR: ", error);

            swal({
                title: "Error",
                text: error,
                icon: "error",
                buttons: false,
                closeOnEsc: false,
                closeOnClickOutside: false
            });

        });

    }


    $scope.Flag = function () {

        if ($scope.data.clock != 0) {
            if ($scope.exam.flagged == 0) {
                // console.log("Flagging the question ..");
                $scope.exam.flagged = 1;
            } else {
                $scope.exam.flagged = 0;
            }
            saveToLocal();

        } else {
            swal("Answers won't be submitted as the test is ended");
        }

    }

    $scope.saveAndReview = function () {
        if ($scope.data.clock != 0) {

            numericValueAdjustment();
            // if not defined then set 0
            /*if ($scope.exam.flagged == null) {
                $scope.exam.flagged = 0;
            }

            if ($scope.exam.flagged == 0) {*/

            if ($scope.exam.answer == null || $scope.exam.answer.trim().length == 0) {
                // alert("Please select a choice!");

                $.snackbar({
                    content: "Please select a choice!"
                });
                return;
            } else if ($scope.exam.type == 'MATCH' && !matchQuestionSolved($scope.exam)) {
                // alert("Please select a choice!");

                $.snackbar({
                    content: "Please select a choice!"
                });
                return;
            } else if (!passesAdditionalCriteria()) {
                return;
            }
            // console.log("Flagging the question ..");
            $scope.exam.flagged = 1;
            /*} else {
                $scope.exam.flagged = 0;
            }*/

            saveAnswer();

            saveToLocal();

            addQuestionActivity('Save and Review');

            $scope.Question($scope.data.i + 1, true);

        } else {
            swal("Answers won't be submitted as the test is ended");
        }

    }

    $scope.showMin = function (min) {
        //console.log(Math.floor(min / 60));
        var hours = Math.floor(min / 60);
        var minutes = min % 60;
        return hours + ":" + minutes;
    }

    function passesAdditionalCriteria() {
        //Check for JEE new format constraint
        var result = true;
        if ($scope.exam != null && $scope.exam.answer != null && !$scope.exam.saved) {
            if ($scope.data != null && $scope.data.jeeNewFormatSections != null && $scope.data.jeeNewFormatSections.length > 0) {
                //JEE format sections are available
                var maxNumericQuestions = 5;
                if ($scope.data.jeeMaxNumeric != null) {
                    maxNumericQuestions = $scope.data.jeeMaxNumeric;
                }
                $scope.data.jeeNewFormatSections.forEach(function (jeeSection) {
                    if ($scope.exam.section == jeeSection && result) {
                        var answeredCount = $scope.getAnswered($scope.exam.section);
                        console.log("answered count " + answeredCount);
                        if (answeredCount >= maxNumericQuestions) {
                            $.snackbar({
                                content: "You have already attempted " + maxNumericQuestions + " questions from this section. Please clear one of the answers before solving this one."
                            });
                            result = false;
                        }

                    }
                });
            }
        }
        return result;
    }

    function numericValueAdjustment() {
        try {
            // console.log($("#numerical_input").val());
            if ($scope.exam.type == 'NUMBER' && ($scope.exam.answer == null || $scope.exam.answer.trim().length == 0)) {
                if ($("#numerical_input") && $("#numerical_input").val()) {
                    $scope.exam.answer = $("#numerical_input").val();
                }
            }
        } catch (e) {
            console.log(e);
        }
    }

    $scope.saveAndNext = function () {

        numericValueAdjustment();

        if ($scope.exam.type == 'MATCH' && !matchQuestionSolved($scope.exam)) {
            // alert("Please select a choice!");

            $.snackbar({
                content: "Please select a choice!"
            });
            return;
        } else if ($scope.exam.type != 'MATCH' && ($scope.exam.answer == null || $scope.exam.answer.trim().length == 0)) {
            // alert("Please select a choice!");

            $.snackbar({
                content: "Please select a choice!"
            });
            return;
        } else if (!passesAdditionalCriteria()) {
            return;
        }
        saveAnswer();
        addQuestionActivity('Save and Next');
        $scope.Question($scope.data.i + 1, true);
    }

    $scope.reviewAndNext = function () {

        if ($scope.data.clock != 0) {

            if (!passesAdditionalCriteria()) {
                return;
            }

            numericValueAdjustment();

            // if not defined then set 0
            if ($scope.exam.flagged == null) {
                $scope.exam.flagged = 0;
            }

            if ($scope.exam.flagged == 0) {
                // console.log("Flagging the question ..");
                $scope.exam.flagged = 1;
            } else {
                $scope.exam.flagged = 0;
            }

            // console.log("Saving answer for " + $scope.exam.id);
            saveAnswer();

            saveToLocal();

            addQuestionActivity('Review and Next');

            if ($scope.exam.flagged == 1) {
                // console.log("Current -" + $scope.data.i);
                $scope.Question($scope.data.i + 1, true);
            }

        } else {
            swal("Answers won't be submitted as the test is ended");
        }

    }
    //new code for flagging ends here

    function saveToLocal() {
        localStorage.setItem(key, JSON.stringify($scope.result));
        //console.log("Saved .. " + localStorage.getItem(key));
    }

    function saveTimeSpent() {
        //$scope.exam.answer = null;
        saveAnswer(true);
    }

    function saveAnswer(saveTimeTakenOnly) {



        if (!$scope.data.submitted) {
            // console.log("Also saving to server ..");

            var answerRequest = {
                test: {
                    "id": $scope.data.id
                },
                student: {
                    "id": studentId
                }
            }

            var question = $scope.exam;
            if (question.answer != null) {
                if (question.flagged == undefined) {
                    question.flagged = 0;
                }
                if (question.answer instanceof String) {
                    if (question.answer.trim().length > 0) {
                        answerRequest.question = question;
                    } else if (question.type == 'MATCH') {
                        answerRequest.question = question;
                    }
                } else {
                    answerRequest.question = question;
                }

            } else if (question.flagged == 1) {
                answerRequest.question = question;
            } else if (question.type == 'MATCH') {
                if (question.flagged == undefined) {
                    question.flagged = 0;
                }
                answerRequest.question = question;
            } else if (question.flagged == 0) {
                answerRequest.question = question;
            } else if (question.answer == null) {
                answerRequest.question = question;
            }

            answerRequest.test.minLeft = $scope.data.min;
            answerRequest.test.secLeft = $scope.data.sec;
            // console.log("Saving answer request", $scope.exam, answerRequest);
            $scope.dataObj = answerRequest;

            if (saveTimeTakenOnly) {
                $scope.dataObj.requestType = 'SAVE_TIME';
            }

            userService.callService($scope, "saveAnswer").then(function (response) {

                // console.log("Answer saved successfully for " + $scope.exam.id);

                if (response == null || response.status == null) {
                    $.snackbar({
                        content: "Could not save answer to the server..Please check your internet connection X",
                        style: "toast",
                        timeout: 0
                    });
                    // Changed color of the snackbar toast to RED
                    $(".toast").css("background-color", "#f44336");
                }
                if (response.status.statusCode != 200) {
                    if (response.status.statusCode == -101) {

                        // Showing a SWAL with a countdown timer in it and then submitting the test
                        // Ref: https://stackoverflow.com/questions/36888070/sweet-alert-display-countdown-in-alert-box
                        // Inititalizing SWAL related variables
                        var
                            closeSwalInMillis = 8,
                            errorSwalTimer,
                            swalDispayText = response.status.responseText + ". Submitting your test in #1 Seconds.";

                        // Displaying the SWAL
                        swal({
                            title: "Error!",
                            text: swalDispayText.replace(/#1/, closeSwalInMillis),
                            icon: "error",
                            timer: closeSwalInMillis * 1000,
                            buttons: false,
                            closeOnEsc: false,
                            closeOnClickOutside: false
                        })
                            .then(() => {
                                // Submit the test here
                                // console.log("SWAL DONE");
                                $scope.data.submissionType = 'rulebreak';
                                checkConnectivityAndSubmitTest();

                            });

                        // Displaying the timer of the SWAL
                        errorSwalTimer = setInterval(function () {

                            closeSwalInMillis--;

                            if (closeSwalInMillis < 0) {

                                clearInterval(errorSwalTimer);
                            }

                            $('.swal-text').text(swalDispayText.replace(/#1/, closeSwalInMillis));

                        }, 1000);

                    } else {
                        $.snackbar({
                            content: "Error saving your answer..Please check your internet connection"
                        });
                    }
                }
            }).catch(function (error) {
                console.log("Some error in saving the answer .. ", error);
                $.snackbar({
                    content: "Could not save answer to the server..Please check your internet connection X",
                    style: "toast",
                    timeout: 0
                });
                // Changed color of the snackbar toast to RED
                $(".toast").css("background-color", "#f44336");
            });
        }
    }

    function getServerResponse() {

        $scope.dataObj = {
            "test": {
                "id": testId
            },
            "student": {
                "id": studentId
            }
        }
        userService.callService($scope, "getSolved").then(function (response) {
            // console.log(response);
            if (response.status.statusCode == 200) {
                if (response.test != null) {

                    //Update time if server time left is less than local time
                    if ($scope.data.min == null || $scope.data.min > response.test.minLeft) {
                        $scope.data.min = response.test.minLeft;
                        $scope.data.sec = response.test.secLeft;
                        //alert($scope.data.min + ":" + $scope.data.sec);
                    }

                    if (response.test.test != null && response.test.test.length > 0) { //Changed on 08/07/21

                        var answered = $scope.getAnswered(null);
                        var reviewedAndAnswered = $scope.getReviewedAnswered(null);
                        if (reviewedAndAnswered == null) {
                            reviewedAndAnswered = 0;
                        }
                        var total = 0;
                        if (answered != null) {
                            total = answered + reviewedAndAnswered;
                        } else {
                            answered = $scope.getAnsweredJee();
                        }

                        // console.log("Total - " + total + " against " + response.test.solvedCount);
                        //if (response.test.solvedCount > total) { //Changed on 08/07/21
                        if (response.test.test.length > 0) {
                            //Update UI from server
                            for (i = 0; i < $scope.data.test.length; i++) {
                                var q = $scope.data.test[i];
                                response.test.test.forEach(function (answered) {
                                    if (q.qn_id == answered.id) {
                                        q.answer = answered.answer;
                                        q.flagged = answered.flagged;
                                        q.timeSpent = answered.timeSpent;
                                        if ((q.answer != null && q.answer.length > 0) || (answered.complexOptions != null && answered.complexOptions.length > 0) || q.flagged == 1) {
                                            q.saved = true;
                                        }
                                        q.visited = true;
                                        if (backupQuestion.qn_id == answered.id) {
                                            backupQuestion.answer = answered.answer;
                                            backupQuestion.flagged = answered.flagged;
                                            backupQuestion.timeSpent = answered.timeSpent;
                                            backupQuestion.saved = q.saved;
                                            // console.log("Backup question found ..");
                                        }
                                        if ($scope.exam.qn_id == answered.id) {
                                            $scope.exam.answer = answered.answer;
                                            $scope.exam.flagged = answered.flagged;
                                            $scope.exam.saved = q.saved;
                                            //$scope.exam.timeSpent = answered.timeSpent;
                                            // console.log("Current question found ..");
                                        }
                                        // console.log("Updated the answer ..." + q.answer);
                                        if (q.answer != null && q.answer.indexOf(",") > 0) {
                                            updateAnswerBoolean(q, true);
                                            if (backupQuestion.qn_id == answered.id) {
                                                updateAnswerBoolean(backupQuestion, true);
                                            }
                                            if ($scope.exam.qn_id == answered.id) {
                                                updateAnswerBoolean($scope.exam, true);
                                            }
                                        } else if (answered.complexOptions != null && answered.complexOptions.length > 0) {
                                            q.complexOptions.forEach(function (co) {
                                                answered.complexOptions.forEach(function (ao) {
                                                    if (co.optionName == ao.optionName) {
                                                        co.matchOptions.forEach(function (matchCo) {
                                                            ao.matchOptions.forEach(function (matchAo) {
                                                                if (matchCo.optionName == matchAo.optionName) {
                                                                    matchCo.selected = matchAo.selected;
                                                                }
                                                            });
                                                        });
                                                    }
                                                });

                                            });
                                            if (backupQuestion.qn_id == answered.id) {
                                                backupQuestion.complexOptions = q.complexOptions;
                                            }
                                            if ($scope.exam.qn_id == answered.id) {
                                                $scope.exm.complexOptions = q.complexOptions;
                                            }
                                        }
                                        //For subjective exams
                                        q.answersUploaded = answered.filesUploaded;
                                    }
                                });

                            }

                        }
                    }
                    saveToLocal();
                }
            }

        }).catch(function (error) {
            console.log("Network is not stable please come back later with better connectivity.");
        });

    }

    function getUploadedAnswers(questionId) {

        $scope.dataObj = {
            "test": {
                "id": testId
            },
            "student": {
                "id": studentId
            },
            "question": {
                "id": questionId
            }
        }
        userService.callService($scope, "getUploadedAnswers").then(function (response) {
            console.log(response);
            $scope.answerFiles = [];
            if (response.status.statusCode == 200) {
                if (response.test != null && response.test.answerFiles != null && response.test.answerFiles.length > 0) {
                    $scope.answerFiles = response.test.answerFiles;
                }
                $scope.loadAnswersProgress = "";
            } else {
                $scope.loadAnswersProgress = "There was an error fetching your answers ..";
            }

        }).catch(function (error) {
            console.log("Network is not stable please come back later with better connectivity.");
            $scope.loadAnswersProgress = "There was an error fetching your answers ..";
        });

    }

    $scope.loadAnswers = function (questionId) {
        $("#uploadedAnswersModal").modal('show');
        $scope.loadAnswersProgress = "Loading your answers ..";
        getUploadedAnswers(questionId);
    }

    $scope.uploadModal = function () {
        $("#uploadAnswersModal").modal('show');
        $("#answerFiles").val("");
        $scope.uploadError = "";
    }

    $scope.upload = function (question) {

        var formData = new FormData($("#uploadAnswersForm")[0]);

        var files = $("#answerFiles")[0].files;
        if (files.length == 0) {
            $scope.uploadError = "Please select at least one image file for upload ..";
            return;
        }


        //$scope.uploadError = "Starting upload ..";

        var count = 1;
        //New Approach
        //Add to AWS first and then save URL

        $("#uploadAnswersModal").modal('hide');

        question.uploadInProgress = true;
        question.filesToUpload = files.length;

        $.snackbar({
            content: "Your answers are being uploaded .. You can continue browsing the question paper .."
        });

        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            var promise = addPhoto(testId + "_" + studentId + "_" + question.qn_id + "_" + new Date().getTime() + "_" + count + "_" + file.name, "answerFilesEdofox", file);
            promise.then(function (data) {
                $scope.dataObj = {
                    test: { id: testId },
                    student: { id: studentId },
                    question: { id: question.qn_id },
                    filePath: data.Location
                };

                userService.callService($scope, "saveSubjectiveAnswer").then(function (response) {

                    if (response == null || response.status == null || response.status.statusCode != 200) {
                        $.snackbar({
                            content: "Problem saving answer to the sever..Please check your internet connection"
                        });
                    } else {
                        if (question.answersUploaded == null) {
                            question.answersUploaded = 0;
                        }
                        question.answersUploaded = question.answersUploaded + 1;

                        if (question.answersUploaded >= question.filesToUpload) {
                            question.uploadInProgress = false;
                            question.filesToUpload = 0;
                        }

                        var pos = findPosition(question);
                        $scope.data.test[pos].answersUploaded = question.answersUploaded;
                        $scope.data.test[pos].uploadInProgress = question.uploadInProgress;

                        saveToLocal();

                        //Update item at position
                        //$scope.$apply();

                    }
                }).catch(function (error) {
                    console.log("Some error in saving the answer .. ", error);
                    $.snackbar({
                        content: "Could not save answer to the sever..Please check your internet connection"
                    });
                });


            }, function (err) {

                $.snackbar({
                    content: "Could not upload file..Please check your internet connection"
                });

            });

        };





        // $.ajax({
        //     url: root + 'uploadAnswers',
        //     type: 'POST',
        //     xhr: function () {
        //         var myXhr = $.ajaxSettings.xhr();
        //         return myXhr;
        //     },
        //     success: function (data) {

        //         console.log("Upload response", data);
        //         if (data != null && data.statusCode == 200) {
        //             $scope.uploadError = "Uploaded answers successfully!";
        //             $("#uploadAnswersModal").modal('hide');
        //             if ($scope.exam.answersUploaded == null) {
        //                 $scope.exam.answersUploaded = 0;
        //             }
        //             $scope.exam.answersUploaded = $scope.exam.answersUploaded + 1;
        //         } else {
        //             $scope.uploadError = "Some error in uploading answers.. Please check your files and try again..";
        //         }
        //         console.log("Uploaded Count", $scope.data);

        //     },
        //     data: formData,
        //     cache: false,
        //     contentType: false,
        //     processData: false
        // });
    }

    window.mobilecheck = function () {
        var check = false;
        (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    };

    var device = 'web';
    if (window.mobilecheck()) {
        device = 'mobile';
    }

    var deviceInfo = window.navigator.userAgent;

    function init() {

        //Check connectivity before staring the test
        checkConnectivityWithServer();

        // console.log(key + ":" + localStorage.getItem(key));

        $scope.result = JSON.parse(localStorage.getItem(key));
        $scope.data = $scope.result.test;
        //        console.log($scope.data.test);    


        //     cached images URLS   
        /*$scope.imageCachedData = {};
        $scope.imageCachedData1 = [];
        angular.forEach($scope.data.test, function (value, key) {
            var id = value.qn_id;
            $scope.imageCachedData[id] = {
                questionImageUrl: value.questionImageUrl,
                option1ImageUrl: value.option1ImageUrl,
                option2ImageUrl: value.option2ImageUrl,
                option3ImageUrl: value.option3ImageUrl,
                option4ImageUrl: value.option4ImageUrl
            }

            if (value.questionImageUrl != "") {
                $scope.imageCachedData1.push(value.questionImageUrl);
            }


            if (value.option1ImageUrl != "") {
                $scope.imageCachedData1.push(value.option1ImageUrl);
            }

            if (value.option2ImageUrl != "") {
                $scope.imageCachedData1.push(value.option2ImageUrl);
            }

            if (value.option3ImageUrl != "") {
                $scope.imageCachedData1.push(value.option3ImageUrl);
            }

            if (value.option4ImageUrl != "") {
                $scope.imageCachedData1.push(value.option4ImageUrl);
            }


        });



        //console.log($scope.imageCachedData); 
        console.log($scope.imageCachedData1);

*/


        //Index is used for keeping track of current question
        if ($scope.data.i == null) { //first time execution of the test 
            $scope.data.i = 0;
            $scope.data.clock = 1;
        }

        // console.log("Data found now ..");
        // console.log($scope.data);

        $scope.Question($scope.data.i);

        setTimeInterval = $interval(setTime, 1000);
        connectivityInterval = $interval(checkConnectivityWithServer, 10000);

        if ($scope.data.sec == null || $scope.data.min == null || $scope.data.sec == 'null' || $scope.data.min == 'null') {
            if ($scope.data.minLeft != null && $scope.data.secLeft != null) {
                $scope.data.sec = $scope.data.secLeft;
                $scope.data.min = $scope.data.minLeft;
                // console.log("Set min left and seconds left ...", $scope.data);
            } else {
                $scope.data.sec = $scope.data.duration % 60;
                $scope.data.min = parseInt($scope.data.duration / 60);
                // console.log("Normal route ...", $scope.data);
            }
            $scope.data.clock = 1;
        }


        if ($scope.data.submitted == true) {
            window.location.href = "result.html";
            checkConnectivityAndSubmitTest();
            return;
        }

        //Get server response to update the local storage if required
        getServerResponse();

        //Update device info
        if ($scope.data != null) {
            $scope.data.device = device;
            $scope.data.deviceInfo = deviceInfo;
        }

    }


    function addStudentActivity(type) {

        $scope.dataObj = {
            test: {
                id: testId,
                device: device,
                deviceInfo: deviceInfo
            },
            student: {
                id: studentId
            },
            requestType: type
        }
        // console.log("Saving student activity ..", $scope.dataObj);
        userService.callService($scope, "saveTestActivity").then(function (response) {
            // console.log("Saved activity", response);
            //$scope.$apply();

        }).catch(function (error) {
            console.log("Error!" + error);
        });
    }

    function addQuestionActivity(type) {

        if (type == null) {
            if ($scope.exam.answer == null) {
                type = 'Cleared';
            } else {
                type = $scope.exam.answer;
            }

        }
        var currQ = {
            id: $scope.exam.qn_id,
            questionNumber: $scope.exam.questionNumber
        }

        $scope.dataObj = {
            test: {
                id: testId,
                device: device,
                deviceInfo: deviceInfo
            },
            student: {
                id: studentId
            },
            question: currQ,
            requestType: type
        }
        //console.log("Saving student activity ..", $scope.dataObj);
        userService.callService($scope, "saveQuestionActivity").then(function (response) {
            //console.log("Saved activity", response);
            //$scope.$apply();

        }).catch(function (error) {
            console.log("Error!" + error);
        });
    }

    $scope.getHtml = function (html) {
        // console.log(html);
        return $sce.trustAsHtml(html);
    };

    //   load Cached Images  
    /*preloader.preloadImages($scope.imageCachedData1)
        .then(function () {
                // Loading was successful.
                console.log("Loading of cache images successful");
            },
            function () {
                // Loading failed on at least one image.
                console.log("Loading of cache images failed");
            });*/




    //Catching when user goes to another window
    //And submitting the test after the user goes away for more than 30 seconds
    var setWindowBlurTimeInterval;
    window.addEventListener('blur', function () {
        // console.log('BLURRED...');

        /**
         * Start the timer to count blur 
         */
        //If the on test pause time out has been set, then run the below code and auto submit test when the student goes away from the test window above the set pauseTimeout
        if ($scope.data.hasOwnProperty('pauseTimeout')) {

            if (setWindowBlurTimeInterval === null) {
                const blurAllowedTime = $scope.data.pauseTimeout * 1000;
                setWindowBlurTimeInterval = $interval(submitTestAfterBlur, blurAllowedTime);
            }

        }
        //Add student test activity
        addStudentActivity('LEFT');

    }, false);

    window.addEventListener('focus', function () {
        // console.log('NOW IN FOCUS***');

        /**
         * Cancel the timer 
         */

        if ($scope.data.hasOwnProperty('pauseTimeout')) {
            $interval.cancel(setWindowBlurTimeInterval);
            setWindowBlurTimeInterval = null;

            swal({
                title: "Warning",
                text: "You clicked away from the Exam Window. The test will be automatically submitted if you do not stay in the Exam Window.",
                buttons: false,
                closeOnEsc: true,
                closeOnClickOutside: true
            });
        }

        addStudentActivity('JOINED');

        //TODO: record how many times this method is called and note it down
    }, false);



    //Submitting the test after window is blurred for a long tim
    function submitTestAfterBlur() { //function to submit the test if the user has blurred/gone away from the test window

        // console.log("submitTestAfterBlur called.");

        // alert("You clicked away from the Test Window for a long time. The test will be automatically submitted now.");
        $scope.data.submissionType = 'rulebreak';
        checkConnectivityAndSubmitTest();
    }


    //Changes for remote proctoring

    var width = 120; // We will scale the photo width to this
    var height = 0; // This will be computed based on the input stream

    var streaming = false;

    var video = null;
    var canvas = null;
    var photo = null;
    var startbutton = null;

    function startup() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        //photo = document.getElementById('photo');
        //startbutton = document.getElementById('startbutton');

        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        })
            .then(function (stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function (err) {
                console.log("An error occurred: " + err);
                alert("You have not enabled your camera. This will impact your evaluation and result.");
            });

        video.addEventListener('canplay', function (ev) {
            if (!streaming) {
                height = video.videoHeight / (video.videoWidth / width);

                if (isNaN(height)) {
                    height = width / (4 / 3);
                }

                video.setAttribute('width', width);
                video.setAttribute('height', height);
                canvas.setAttribute('width', width);
                canvas.setAttribute('height', height);
                streaming = true;
            }
        }, false);

        // startbutton.addEventListener('click', function (ev) {
        //     takepicture();
        //     ev.preventDefault();
        // }, false);

        //clearphoto();
    }


    function clearphoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        //photo.setAttribute('src', data);
    }

    function takepicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            //photo.setAttribute('src', data);

            canvas.toBlob(function (blob) {
                //console.log("Uploading to server ..", blob);
                //Upload file to server
                var promise = addPhoto(testId + "_" + studentId + new Date().getTime() + ".jpg", "proctoring", blob);
                promise.then(
                    function (data) {
                        //console.log("Response", data);

                        if (data != null) {

                            $scope.dataObj = {
                                student: {
                                    id: studentId,
                                    proctorImageRef: data.Location
                                },
                                test: {
                                    id: testId
                                }
                            }

                            userService.callService($scope, "saveProctorImage").then(function (response) {
                                //console.log("Saved proctor URL");

                            }).catch(function (error) {
                                $scope.photoUploadProgress = "There was some error uploading your photo .. Please try again";
                                console.log("Error!" + error);
                            });


                        } else {
                            $.snackbar({
                                content: "Your internet connection is unstable..please check"
                            });
                        }


                    },
                    function (err) {
                        $.snackbar({
                            content: "Your internet connection is unstable..please check"
                        });
                    }
                );


                // var formData = new FormData();
                // formData.append('studentId', studentId);
                // formData.append('testId', testId);
                // formData.append('data', blob);

                // $.ajax({
                //     url: root + 'uploadProctorImage',
                //     data: formData,
                //     type: 'POST',
                //     contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                //     processData: false, // NEEDED, DON'T OMIT THIS
                //     success: function (response) {
                //         console.log("Success ..", response);
                //     },
                //     error: function (error) {
                //         console.log("Error ..", error);
                //     }
                // });

            });


        } else {
            clearphoto();
        }
    }

    if ($scope.data.testUi == 'PROCTORING') {
        setInterval(function () {
            //console.log("Taking picture ..");
            takepicture();
        }, 50000);

        console.log("Starting camera for remote proctoring ..");

        startup();
    }


}]);


app.directive("mathjaxBind", function () { //directive to apply mathjax to special characters
    return {
        restrict: "A",
        scope: {
            text: "@mathjaxBind"
        },
        controller: ["$scope", "$element", "$attrs", function ($scope, $element, $attrs) {

            $scope.$watch('text', function (value) {
                var $script = angular.element("<script type='math/tex'>")
                    .html(value == undefined ? "" : value);
                $element.html("");
                $element.append($script);
                MathJax.Hub.Queue(["Reprocess", MathJax.Hub, $element[0]]);
            });
        }]
    };
})
app.directive('dynamic', function ($compile) { //directive to replace special characeters using R.E. special characters
    return {
        restrict: 'A',
        replace: true,
        link: function (scope, ele, attrs) {
            scope.$watch(attrs.dynamic, function (html) {
                if (html == null) {
                    return null;
                }

                html = html.replace(/\$\$([^$]+)\$\$/g, "<span class=\"blue\" mathjax-bind=\"$1\"></span>");
                html = html.replace(/\$([^$]+)\$/g, "<span class=\"red\" mathjax-bind=\"$1\"></span>");
                html = html.replace(/\\\(([^)]+\\)\)/g, "<span class=\"red\" mathjax-bind=\"$1\"></span>");
                ele.html(html);
                $compile(ele.contents())(scope);
            });
        }
    };
});

app.directive('mathjax', function () {
    return {
        restrict: 'EA',
        link: function (scope, element, attrs) {
            scope.$watch(attrs.ngModel, function () {
                MathJax.Hub.Queue(['Typeset', MathJax.Hub, element.get(0)]);
            });
        }
    };
});

function addMathJax(input, spanId) {
    /*var $el = $('#test_question');
    console.log("Previewing ..", $el);
    $el.empty();
    $el.append($scope.exam.question);
    MathJax.Hub.Queue(['Typeset', MathJax.Hub, $el[0]]);*/

    // console.log("Text=" + input, spanId);

    output = document.getElementById(spanId);
    if (output == null) {
        // console.log("returning .." + spanId);
        return;
    }
    if (input == null || input.trim().length == 0) {
        output.innerHTML = "";
        return;
    }
    output.innerHTML = input;
    //
    //  Reset the tex labels (and automatic equation numbers, though there aren't any here).
    //  Reset the typesetting system (font caches, etc.)
    //  Typeset the page, using a promise to let us know when that is complete
    //
    // console.log("Adding to - " + spanId);
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
            // console.log("Done adding to == > " + spanId);
        });
}


function applyMathJax(exam) {
    addMathJax(exam.question, "questionSpan");
    addMathJax(exam.option1, "option1Span");
    addMathJax(exam.option2, "option2Span");
    addMathJax(exam.option3, "option3Span");
    addMathJax(exam.option4, "option4Span");
    addMathJax(exam.option5, "option5Span");
}


app.controller('questionCtrl', ['$rootScope', '$scope', '$http', '$element', '$interval', 'userService', 'preloader', '$sce', '$location', function ($rootScope, $scope, $http, $element, $interval, userService, preloader, $sce, $location) {

    var chapterId = $location.search().chapter;
    var subjectId = $location.search().subject;
    var questionId = $location.search().question;

    /*$rootScope.$watch(function () {
        //console.log("Changed ...");
        MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        return true;
    });*/

    $scope.studentName = getCookie("studentName");
    // console.log("Loaded question page for " + $scope.studentName + " subject " + subjectId + " Q " + questionId);

    /*$scope.$watch(function (scope) {
            //return scope.data.myVar
        },
        function (newValue, oldValue) {
            console.log("Scopes == " +newValue + ":" + oldValue);
        }
    );*/

    $scope.nextQuestion = function (next) {
        var type = null,
            level = null;
        if ($scope.type != null && $scope.type != "") {
            type = $scope.type;
            // console.log("Setting type :" + $scope.type);
        }
        if ($scope.level != null && $scope.level != "") {
            level = $scope.level;
            // console.log("Setting type :" + $scope.type);
        }
        $scope.answer = null;

        // console.log("Scope exam", $scope.exam, "Next", next);
        if (questionId == null && (next || ($scope.exam == null || $scope.exam.subjectId != subjectId || $scope.exam.chapter.chapterId != chapterId))) {
            $scope.dataObj = {
                question: {
                    chapter: {
                        chapterId: chapterId
                    },
                    subjectId: subjectId,
                    type: type,
                    level: level
                }
            };
        } else if (questionId != null) {
            $scope.dataObj = {
                question: {
                    id: questionId
                }
            };
        } else {
            $scope.dataObj = {
                question: {
                    id: $scope.exam.id,
                    chapter: {
                        chapterId: chapterId
                    },
                    subjectId: subjectId
                }
            };
        }

        $scope.dataObj.question.correct = true;

        var type = null;
        if ($scope.exam != null && $scope.exam.type != null) {
            type = $scope.exam.type;
        }

        document.getElementById("solutionSpan").innerHTML = "";

        // console.log("Calling next question ..", $scope.dataObj);
        userService.callService($scope, "getNextQuestion").then(function (response) {

            if (response == null || response.question == null) {
                $scope.exam = null;
                return;
            }
            // console.log(response);
            $scope.status = response.status;

            $scope.exam = response.question;
            localStorage.currentQuestion = JSON.stringify($scope.exam);

            applyMathJax($scope.exam);
            //If type changed..add delay
            if (response.question.type != type) {
                // console.log("adding delay...");
                setTimeout(function () {
                    applyMathJax($scope.exam);
                }, 2000);
            }


            //$scope.$apply();

        }).catch(function (error) {
            console.log("Error!" + error);
        });
    }


    $scope.submitAnswer = function () {
        $scope.dataObj = {
            question: $scope.exam
        };
        userService.callService($scope, "submitAnswer").then(function (response) {
            // console.log(response);
            $scope.status = response.status;

            $scope.answer = response.question;

            addMathJax($scope.answer.solution, "solutionSpan");

        }).catch(function (error) {
            console.log("Error!" + error);
        });
    }

    $scope.getOptionClass = function (val) {

        if ($scope.answer != null && $scope.answer.correctAnswer != null) {
            // console.log("Checking .. " + $scope.answer.correctAnswer.indexOf(val) + " for " + val);
            if ($scope.answer.correctAnswer.indexOf(val) >= 0) {
                return "correct-ans";
            } else {
                return "";
            }
        }
        return "";
    }

    $scope.questionStyle = function (question) {

        if (question == null) {
            return;
        }

        var style = "";
        if ($scope.exam.qn_id == question.qn_id) {
            style = "background-position: -57px -127px;width: 49px;height: 49px;color:#fff;";
        }

        // console.log("Style!" + question.id);
        if (question.flagged == 1 && question.saved) {
            style = "background-position: -108px -122px;height: 49px;color:#fff;";
            // console.log("Only flagged!" + question.id);
        } else if (question.visited) {
            style = "background-position: -57px -127px;color:#fff;";
        }

        if (question.answer != null && question.answer.length > 0 && question.saved) {
            if (question.flagged == 1) {
                style = "background-position: -66px -178px;color:#fff;";
                // console.log("Answered and reviewed!" + question.id);
            } else {
                style = "background-position: -4px -5px;color:#fff;";
            }
        } else if (question.complexOptions != null && question.complexOptions.length > 0) {
            question.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        if (match.selected && question.saved) {
                            if (question.flagged == 1) {
                                style = "background-position: -66px -178px;color:#fff;";
                            } else {
                                style = "background-position: -4px -5px;color:#fff;";
                            }

                        }
                    });
                }

            });

        }

        return style;

    }

    $scope.questionStyleRegular = function (question) {

        if (question == null) {
            return;
        }

        var style = "";
        if ($scope.exam.qn_id == question.qn_id) {
            style = "background-color:#ccc";
        }

        if (question.flagged == 1) {
            style = "background-color:#ffb4b4";
            // console.log("YEs!");
        } else if (question.answer != null) {
            style = "background-color:#5fcf80";
        }
        return style;


    }

    $scope.clear = function () {
        // console.log("Clearing ...");
        $scope.exam.answer = null;
        $scope.exam.op1 = false;
        $scope.exam.op2 = false;
        $scope.exam.op3 = false;
        $scope.exam.op4 = false;
        if ($scope.exam.complexOptions != null && $scope.exam.complexOptions.length > 0) {
            $scope.exam.complexOptions.forEach(function (op) {
                if (op.matchOptions != null && op.matchOptions.length > 0) {
                    op.matchOptions.forEach(function (match) {
                        match.selected = false;
                    });
                }

            });
        }
    }

    function updateExamBoolean(answer, value) {
        if (answer.indexOf('option1') >= 0) {
            $scope.exam.op1 = value;
        }
        if (answer.indexOf('option2') >= 0) {
            $scope.exam.op2 = value;
        }
        if (answer.indexOf('option3') >= 0) {
            $scope.exam.op3 = value;
        }
        if (answer.indexOf('option4') >= 0) {
            $scope.exam.op4 = value;
        }
        if (answer.indexOf('option5') >= 0) {
            $scope.exam.op5 = value;
        }
    }


    $scope.addAnswer = function (answer) {
        if ($scope.exam.answer == null) {
            $scope.exam.answer = "";
        }
        // console.log($scope.exam.answer + " -- " + $scope.exam.answer.indexOf(answer));
        if ($scope.exam.answer.indexOf(answer) >= 0) {
            $scope.exam.answer = $scope.exam.answer.replace(answer + ",", "");
            updateExamBoolean(answer, false);
        } else {
            $scope.exam.answer = $scope.exam.answer + answer + ",";
            updateExamBoolean(answer, true);
        }
        // console.log("Answer - " + $scope.exam.answer);

        if ($scope.exam.answer.trim().length < 3) {
            $scope.exam.answer = null;
        }
        //saveToLocal();
    }

    //console.log(localStorage.currentQuestion);
    if (localStorage.currentQuestion != 'null' && localStorage.currentQuestion != 'undefined' && localStorage.currentQuestion) {
        $scope.exam = JSON.parse(localStorage.currentQuestion);
        $scope.nextQuestion(false);
    } else {
        // console.log("Calling ..");
        $scope.nextQuestion(true);
    }




}]);