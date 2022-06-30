app.controller('instructions', ['$rootScope', '$scope', '$http', '$element', '$interval', 'userService', '$sce', function ($rootScope, $scope, $http, $element, $interval, userService, $sce) {

    //localStorage.studentId = 1751;
    //localStorage.testId = 64;

    var student_id = localStorage.getItem("studentId");
    var test_id = localStorage.getItem("testId");
    var student_name = localStorage.getItem("studentName");
    var test_name = localStorage.getItem("testName");

    $scope.packageName = localStorage.getItem("packageName");;

    localStorage.completePayment = "N";


    var keyVal = localStorage.getItem("key");
    // console.log("Key: " + keyVal);

    // console.log("Student ID: " + student_id);
    // console.log("Student Name: " + student_name);
    // console.log("Test ID: " + test_id);
    // console.log("Test Name: " + test_name);

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

    $scope.dataObj = {
        test: {
            id: test_id,
            device: device,
            deviceInfo: deviceInfo
        },
        student: {
            id: student_id
        }
    }


    $scope.examMode = "Online";


    $scope.status = {};

    var location = null;

    $scope.loadingMessage = "Loading test .. Please wait ..";

    $scope.disableStartButton = false;

    $scope.cameraPermitted = false;

    $scope.requestCamera = function () {
        video = document.getElementById('video');
        console.log("Video element", video);

        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        })
            .then(function (stream) {
                $("#camera-feed-block").show();
                $scope.cameraPermitted = true;
                $scope.disableStartButton = false;
                $scope.$apply();
                if (video != null) {
                    video.srcObject = stream;
                    video.play();
                }
                console.log("Camera permitted ..", video);

            })
            .catch(function (err) {
                console.log("An error occurred: " + err);
                alert("Please allow the system to switch on the camera as it is mandatory for this exam.")
            });

        if (video != null) {
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
        }


    }

    var width = 300; // We will scale the photo width to this
    var height = 0; // This will be computed based on the input stream
    var streaming = false;
    $scope.photoUploadProgress = "";

    $scope.takepicture = function () {
        if (!$scope.cameraPermitted) {
            $scope.requestCamera();
        }
        $("#canvas").show();

        canvas = document.getElementById('canvas');
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            //photo.setAttribute('src', data);

            $scope.photoUploadProgress = "Uploading photo ..";

            canvas.toBlob(function (blob) {
                console.log("Uploading to server ..", blob);
                //Upload to server

                // var formData = new FormData();
                // formData.append('studentId', student_id);
                // formData.append('testId', test_id);
                // formData.append('data', blob);


                //Upload photo to AWS
                var promise = addPhoto(student_id + new Date().getTime() + ".jpg", "proctorRef", blob);
                promise.then(
                    function (data) {
                        console.log("Response", data);

                        if (data != null) {

                            $scope.dataObj.student.proctorImageRef = data.Location;

                            //Save proctoring URL for student
                            $scope.photoUploadProgress = "Saving photo ..";

                            userService.callService($scope, "saveProctorImageRef").then(function (response) {
                                $scope.loadingMessage = "";
                                if (response != null && response.status != null && response.status.statusCode == 200) {
                                    $scope.photoUploadProgress = "Uploaded the photo successfully!";
                                    loadTest();
                                    return;
                                } else {
                                    $scope.photoUploadProgress = "There was some error uploading your photo .. Please try again";
                                }


                            }).catch(function (error) {
                                $scope.photoUploadProgress = "There was some error uploading your photo .. Please try again";
                                console.log("Error!" + error);
                            });


                        } else {
                            return alert("There was an error uploading your photo. Please try again");
                        }


                    },
                    function (err) {
                        return alert("There was an error uploading your photo: ", err.message);
                    }
                );


                // $.ajax({
                //     url: root + 'uploadProctorRef',
                //     data: formData,
                //     type: 'POST',
                //     contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                //     processData: false, // NEEDED, DON'T OMIT THIS
                //     success: function (response) {
                //         console.log("Success ..", response);
                //         if (response.status != null && response.status.statusCode == 200) {
                //             $scope.photoUploadProgress = "Uploaded the photo successfully!";
                //         } else {
                //             $scope.photoUploadProgress = response.status.responseText;
                //         }

                //         loadTest();
                //     },
                //     error: function (error) {
                //         console.log("Error ..", error);
                //         $scope.photoUploadProgress = "Error in uploading photo!";
                //     }
                // });

            });


        }
    }

    $scope.loadTestFailed = false;


    function loadTest() {

        $scope.loadTestFailed = false;

        if (location != null) {
            $scope.dataObj.test.locationLat = "" + location.latitude;
            $scope.dataObj.test.locationLong = "" + location.longitude;
        }

        userService.callService($scope, "getTest").then(function (response) {
            $scope.loadingMessage = "";
            if (response == null || response.test == null || response.test.test == null || response.test.test.length == 0) {
                if (response == null || response.status == null) {
                    //Outage
                    $scope.loadingMessage = "Could not connect with server .. Please check your connection";
                    $scope.loadTestFailed = true;
                    return;
                }
                if (response.status == null || response.status.statusCode == 200) {
                    $scope.loadingMessage = "Test not found. Please contact your admin.";
                    return;
                }
            }
            // console.log(response);
            $scope.status = response.status;

            $scope.response = response;

            // If the pauseTimeout value is set, show an alert to student with the warning
            if (response != null && response.test != null && response.test.hasOwnProperty('pauseTimeout')) {
                swal({
                    title: "Note",
                    text: "If you go away from the test window after starting the test (click on another tab/window), the test will be automatically submitted.",
                    buttons: false,
                    closeOnEsc: true,
                    closeOnClickOutside: true
                });
            }

            localStorage.testInstructions = null;
            //custom instructions
            if (response.test != null && response.test.instructions != null) {
                //$("#institute_instructions").attr("style", "");
                //document.getElementById("institute_instructions").innerHTML =  response.test.instructions;
                //console.log("Added instructions", response.test.instructions);
                $scope.trustedHtml = $sce.trustAsHtml(response.test.instructions);

            }


            //Disable start button if proctored exam till camera access is not provided
            if ($scope.response.test != null && $scope.response.test.testUi == 'PROCTORING') {
                //if (!$scope.cameraPermitted) {
                $scope.disableStartButton = true;
                setTimeout(
                    function () {
                        console.log("Requesting camera ..");
                        console.log("streaming false ..");

                        streaming = false;

                        $scope.requestCamera();
                    }, 1000);
                //}

            }

        }).catch(function (error) {
            $scope.loadingMessage = "Some error occurred while loading the test ..";
            $scope.loadTestFailed = true;
            console.log("Error!" + error);
        });
    }

    $scope.startTest = function () {
        if (localStorage.getItem(student_id + "-" + test_id) == null) {
            localStorage.setItem(student_id + "-" + test_id, JSON.stringify($scope.response));
        } else {
            if ($scope.response != null && $scope.response.test != null) {

                var response = JSON.parse(localStorage.getItem(student_id + "-" + test_id));
                var changed = false;    

                if ($scope.response.test.minLeft != null && $scope.response.test.secLeft != null) {
                    response.test.min = $scope.response.test.minLeft;
                    response.test.sec = $scope.response.test.secLeft;
                    if ($scope.response.test.adminReset == 1) {
                        response.test.submitted = false;
                        response.test.clock = 1;
                    }
                    changed = true;
                }

                if($scope.response.test.testUi != response.test.testUi) {
                    response.test.testUi = $scope.response.test.testUi;
                    changed = true;
                }

                if($scope.response.test.jeeMaxNumeric != response.test.jeeMaxNumeric) {
                    response.test.jeeMaxNumeric = $scope.response.test.jeeMaxNumeric;
                    response.test.jeeNewFormatSections = $scope.response.test.jeeNewFormatSections;
                    changed = true;
                }

                if(changed) {
                    localStorage.setItem(student_id + "-" + test_id, JSON.stringify(response));
                }

            }
        }

        // console.log("testInstructions", $("#only-instructions-block").html());
        localStorage.testInstructions = $("#only-instructions-block").html();


        //Check if running on mobile
        if (window.mobilecheck()) {
            // console.log("Here!!!");
            if ($scope.response.test.testUi == 'DESCRIPTIVE') {
                window.location.href = "test_subjective.html";
                return;
            } else if ($scope.response.test.testUi == 'PROCTORING') {
                window.location.href = "test_proctoring.php";
                return;
            }
            window.location.href = "test.html";
            return;
        }


        if ($scope.response.test.testUi == 'JEE') {
            //CET UI
            window.location.href = "test_jee.html";
            // alert("JEE!");
        } else if ($scope.response.test.testUi == 'JEEM' || $scope.response.test.testUi == 'NEET') {
            //JEE Mains UI
            window.location.href = "test_jee_new.html";
        } else if ($scope.response.test.testUi == 'MOBILE') {
            window.location.href = "test.html";
        } else if ($scope.response.test.testUi == 'DESCRIPTIVE') {
            window.location.href = "test_subjective.html";
        } else if ($scope.response.test.testUi == 'PROCTORING') {
            window.location.href = "test_proctoring.php";
            return;
        }
        else {
            window.location.href = "test_jee.html";
        }

    }


    //   console.log("Loaded exam in mobile ==> " + window.mobilecheck());


    $scope.showResult = function () {
        window.location.href = "result.html";
    }

    $scope.pay = function () {
        // console.log($scope.examMode);
        $scope.dataObj = {
            test: {
                id: test_id
            },
            student: {
                id: student_id,
                examMode: $scope.examMode
            }
        }
        userService.callService($scope, "completePayment").then(function (response) {
            // console.log(response);
            if (response.paymentStatus.statusCode != 200) {
                $scope.errorTxt = response.paymentStatus.responseText;
                return;
            }
            localStorage.completePayment = "Y";
            localStorage.setItem("completePayment", "Y");
            window.location.href = response.paymentStatus.paymentUrl;

        }).catch(function (error) {
            console.log("Error!" + error);
        });
    }

    function showPosition(position) {
        console.log(position.coords);
        location = position.coords;
        $scope.loadingMessage = "Sending location .. Please wait ..";
        loadTest();
    }

    function handlePermission() {
        navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
            if (result.state == 'granted') {
                report(result.state);
                navigator.geolocation.getCurrentPosition(showPosition, function () {
                    console.log("Error!");
                }, null);
            } else if (result.state == 'prompt') {
                report(result.state);
                navigator.geolocation.getCurrentPosition(showPosition, function () {
                    console.log("Error!");
                }, null);
            } else if (result.state == 'denied') {
                report(result.state);
            }
            result.onchange = function () {
                report(result.state);
            }
        });
    }

    function report(state) {
        console.log('Permission ' + state);
    }

    $scope.sendLocation = function () {
        handlePermission();
    }

    loadTest();

}]);
