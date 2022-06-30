var app = angular.module("registration", []);

//var host = "http://localhost:8080/edo-service";
//var host = "https://dev.edofox.com:8443/edofox";
//var host = "https://test.edofox.com:8443/edofox";
//var root = host + "/service/";
//var rootAdmin = host + "/admin/";

//Very IMP. To be changed as per the requirement
//var instituteId = 3;

//Firebase stuff

// Initialize Firebase
var config = {
  apiKey: "AIzaSyBwcVNYR_KzLq7J_l3DAucPptrARoXnXIA",
  authDomain: "edofox-151105.firebaseapp.com",
  databaseURL: "https://edofox-151105.firebaseio.com",
  projectId: "edofox-151105",
  storageBucket: "edofox-151105.appspot.com",
  messagingSenderId: "909874731632",
};
firebase.initializeApp(config);

/*window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sign-in-button', {
  'size': 'invisible',
  'callback': function(response) {
    // reCAPTCHA solved, allow signInWithPhoneNumber.
    onSignInSubmit();
  }
});*/

//Register controller

app.controller("phoneNumberVerification", function ($scope, $http) {
  console.log("Verification controller called ..");

  window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
    "recaptcha-container"
  );

  $scope.user = {
    phone: "",
    code: "",
  };

  $scope.verified = "N";

  $scope.submit = function () {
    console.log($scope.phoneNumber);
    firebase
      .auth()
      .signInWithPhoneNumber($scope.user.phone, recaptchaVerifier)
      .then(function (confirmationResult) {
        // SMS sent. Prompt user to type the code from the message, then sign the
        // user in with confirmationResult.confirm(code).
        window.confirmationResult = confirmationResult;
        console.log(confirmationResult);
        $scope.verified = "Y";
        console.log("Verified .." + $scope.verified);
        $scope.$apply();
      })
      .catch(function (error) {
        // Error; SMS not sent
        // ...
        console.log("Error!" + error);

        window.recaptchaVerifier.render().then(function (widgetId) {
          grecaptcha.reset(widgetId);
        });
      });
  };

  $scope.verify = function () {
    window.confirmationResult
      .confirm($scope.user.code)
      .then(function (result) {
        // User signed in successfully.
        var user = result.user;
        alert("Verified!");
        // ...
      })
      .catch(function (error) {
        // User couldn't sign in (bad verification code?)
        // ...
        console.log(error);
      });
  };
});

var referral;

app.controller("register", function ($scope, $http, $location) {
  //var instituteId = $location.search().institute;

  // console.log("Registration controller called .." + instituteId, $location);

  $scope.submitted = false;

  function getProperString(val) {
    if (val == null) {
      return "";
    }
    // To remove all special characters in the string
    val = val.replace(/[^a-zA-Z0-9 ]/g, "");
    //To make the string uppercase before returning
    return val.toUpperCase();
  }

  //var totalPrice = 0;

  $scope.noPackage = false;

  function getPackages() {
    $scope.noPackage = false;
    var selectedPackages = [];
    $scope.packages.forEach(function (package) {
      if (package.selected) {
        selectedPackages.push(package);
      }
    });
    if (selectedPackages.length == 0) {
      $scope.noPackage = true;
    }
    return selectedPackages;
  }

  $scope.getPrice = function () {
    $scope.totalPrice = 0;
    if ($scope.packages == null) {
      return $scope.totalPrice;
    }
    //if($scope.user.exam)
    $scope.packages.forEach(function (package) {
      if (package.selected) {
        if ($scope.user != null && $scope.user.examMode == "Offline") {
          $scope.totalPrice = $scope.totalPrice + package.offlinePrice;
        } else {
          $scope.totalPrice = $scope.totalPrice + package.price;
        }
      }
    });
    return $scope.totalPrice;
  };

  function makePayment(response) {
    if (response.paymentStatus.statusCode == 200) {
      console.log("Payment URL =>" + response.paymentStatus.paymentUrl);
      if (response.paymentStatus.paymentUrl.indexOf("payu") >= 0) {
        $scope.status = response.paymentStatus;
        $("#payuform").attr("action", response.paymentStatus.paymentUrl);
        console.log("Form action changed", $("#payuform"));
        setTimeout(function () {
          $("#payuform").submit();
        }, 500);
      } else {
        window.location.href = response.paymentStatus.paymentUrl;
      }
    } else {
      console.log("Error ", response);
      //window.location.href = "error.html";
    }
  }

  $scope.progress = false;

  $scope.user = {};

  function checkAdditionalDetails() {
    var additionalDetails = "";
    if ($scope.user.mothername) {
      additionalDetails =
        additionalDetails +
        "Mother Name:" +
        getProperString($scope.user.mothername) +
        " | ";
    }
    if ($scope.prevSchool) {
      additionalDetails =
        additionalDetails +
        "Previous School:" +
        getProperString($scope.prevSchool) +
        " | ";
    }
    if ($scope.mediumOfStudy) {
      additionalDetails =
        additionalDetails + "Medium of study:" + $scope.mediumOfStudy + " | ";
    }
    if ($scope.adharNumber) {
      additionalDetails =
        additionalDetails + "Aadhar number:" + $scope.adharNumber + " | ";
    }

    // For Lion's Club Requirement
    if ($scope.courseYearDetails) {
      additionalDetails =
        additionalDetails +
        "Course and Year:" +
        $scope.courseYearDetails +
        " | ";
    }
    if ($scope.collegeNameAddress) {
      additionalDetails =
        additionalDetails +
        "College Details:" +
        $scope.collegeNameAddress +
        " | ";
    }

    return additionalDetails;
  }

  $scope.submit = function () {
    $scope.submitted = true;

    if ($scope.registerForm != null && !$scope.registerForm.$valid) {
      // console.log("Invalid form ...");
      // console.log($scope.registerForm);
      // Snakbar Message
      Snackbar.show({
        pos: "top-center",
        text: "There is some error in the form. Please check all the fields",
      });
      return;
    }
    $scope.dataObj = {};

    // If full name is not found from UI, then concat full name from below fields
    if ($scope.user.name == undefined || $scope.user.name == "") {
      $scope.user.name =
        getProperString($scope.user.firstName) +
        " " +
        getProperString($scope.user.middleName) +
        " " +
        getProperString($scope.user.lastName);
    }

    // If username/roll no is not found from UI, then take mobile no as username
    if ($scope.user.rollNo == undefined || $scope.user.rollNo == "") {
      $scope.user.rollNo = $scope.user.phone;
    }

    $scope.user.packages = getPackages();

    if ($scope.noPackage) {
      $scope.registerForm.$valid = false;
      return;
    }

    if (
      $scope.user.year != null &&
      $scope.user.month != null &&
      $scope.user.day != null
    ) {
      $scope.user.dob =
        $scope.user.year + "-" + $scope.user.month + "-" + $scope.user.day;
    }

    if ($scope.user.examMode == null) {
      $scope.user.examMode = "Online";
    }

    $scope.dataObj.student = $scope.user;
    if (referral) {
      $scope.dataObj.student.referrer = referral;
    }

    $scope.dataObj.student.additionalDetails = checkAdditionalDetails();

    // console.log($scope.dataObj);

    $scope.progress = true;
    var res = $http.post(root + "registerStudentPackages", $scope.dataObj);
    res.then(function (data, status, headers, config) {
      $scope.progress = false;
      var response = data.data;
      if (response.status.statusCode == 200) {
        console.log("Registered successfully!");
        if ($scope.user.payment == null || !$scope.user.payment.offline) {
          makePayment(response);
        } else {
          // console.log(response);
          // console.log("TOKEN", response.student.token);
          window.location =
            base_url +
            "/registration/signup_response/" +
            response.student.token;
        }
      } else {
        // console.log("Error", response);
        // console.log("Registration error .." + response.status.responseText);
        $scope.errorText = response.status.responseText;
        alert("Error: " + response.status.responseText);
        // Snakbar Message
        Snackbar.show({
          pos: "top-center",
          text: response.status.responseText,
        });
        //window.location.href = "error.html";
      }
      //console.log(response);
    }),
      function (data, status, headers, config) {
        response = data;
        console.log(
          "Error :" +
            status +
            ":" +
            JSON.stringify(data) +
            ":" +
            JSON.stringify(headers)
        );
      };
  };

  $scope.getPackages = function () {
    // console.log(root + "getPackages");
    $scope.dataObj = {
      institute: {
        id: instituteId,
      },
      requestType: "public",
    };
    var res = $http.post(root + "getPackages", $scope.dataObj);
    res.then(function (data, status, headers, config) {
      var response = data.data;
      if (response.status.statusCode == 200) {
        $scope.packages = response.packages;
      }
      // console.log(response);
    }),
      function (data, status, headers, config) {
        response = data;
        console.log(
          "Error :" +
            status +
            ":" +
            JSON.stringify(data) +
            ":" +
            JSON.stringify(headers)
        );
      };
  };

  $scope.getPackages();

  function setPackagePrice(value) {
    $scope.packages.forEach(function (pkg) {
      if (pkg.price > 0) {
        pkg.price = value;
      }
    });
  }

  $scope.searchRegId = function () {
    $scope.dataObj = {
      student: {
        rollNo: $scope.user.rollNo,
      },
    };
    $scope.applicationIdProgress = true;
    console.log("Searching for app ID", $scope.dataObj);
    var res = $http.post(root + "searchForDeeperRegistration", $scope.dataObj);
    res.then(function (data, status, headers, config) {
      var response = data.data;
      console.log(response);
      if (response.status.statusCode == 200) {
        $scope.applicationFound = true;
        if (response.student == null) {
          $scope.applicationIdError =
            "Application ID not found .. Please sign up below";
          $scope.applicationFound = false;
        } else {
          $scope.user = response.student;
          $scope.applicationFound = true;
          if ($scope.user.name != null) {
            $scope.user.firstName = $scope.user.name.split(" ")[0];
            $scope.user.lastName = $scope.user.name.split(" ")[1];
          }

          if ($scope.user.currentPackage.name == "3") {
            setPackagePrice(300);
          } else if ($scope.user.currentPackage.name == "2") {
            setPackagePrice(500);
          } else if ($scope.user.currentPackage.name == "1") {
            setPackagePrice(700);
          }
        }
      } else {
        if (response != null && response.status.responseText != null) {
          $scope.applicationIdError = response.status.responseText;
        } else {
          $scope.applicationIdError =
            "Some error in searching your application ID ..";
        }
      }
      $scope.applicationIdProgress = false;
    }),
      function (data, status, headers, config) {
        response = data;
        console.log(
          "Error :" +
            status +
            ":" +
            JSON.stringify(data) +
            ":" +
            JSON.stringify(headers)
        );
      };
  };

  $scope.newStudent = function () {
    $scope.newForm = true;
  };

  $scope.formatAmount = function (amount) {
    return (Math.round(amount * 100) / 100).toFixed(2);
  };
});
