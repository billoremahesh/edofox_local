//var app = angular.module("app", ['angular-img-dl', 'ngFileUpload']);
var app = angular.module("app", []);

var domain = "dev.edofox.com";

//var host = "http://" + domain + ":8080/edofox";
var host = "https://" + domain + ":8443/edofox";

// var apachehost = window.location.protocol + "//" + window.location.host + "/";
var apachehost = window.location.protocol + "//" + window.location.host + "/edofox_admin/public/";


var root = host + "/service/";
var rootAdmin = host + "/admin/";
var superAdmin = host + "/super/";

var websocketDomain = "dev.edofox.com";

app.service('userService', function ($http, $q) {

    var response = {};

    this.showResponse = function ($scope, successMsg, successLink) {
        $scope.showProgress = false;
        if ($scope.response == null) {
            $scope.response = {};
            $scope.response.status = -111;
            $scope.response.responseText = "Error connecting server ..";
            $("#errorModal").modal('show');
            return;
        }
        // console.log("Response :" + $scope.response.status + " msg:" + successMsg);
        //$scope.response.status = response.status;
        if ($scope.response.status == 200) {
            if (successMsg == "") {
                return;
            }
            /*if(successLink!= null && successLink != "") {
                $scope.successLink = successLink;
            } else {
                $scope.successLink = "#main";
            }*/
            //localStorage.erpEmployee = null;
            $scope.successMsg = successMsg;
            // console.log("Response Text:" + $scope.response.responseText);
            $("#successModal").show();
            $("#successModal").modal('show');
            //console.log("Response :" + $scope.response.reseponseText);
        } else {
            $("#errorModal").modal('show');
        }
        //console.log("Response :" + $scope.response.reseponseText);
    }

    this.showLoading = function ($scope) {
        // console.log("Showing loader..");
        $scope.showProgress = true;
        // console.log("Loaded loader..");
    }

    this.initLoader = function ($scope) {
        $scope.showProgress = false;
        // console.log("Hiding loader..");

    }

    this.validationError = function ($scope, msg) {
        $scope.errorText = msg;
        $("#warningModal").modal('show');
    }

    this.close = function (url) {
        $("#successModal").modal('hide');
        if (url != null && url != "") {
            window.location.href = url;
        }
    }

    this.callService = function ($scope, method) {

        var request = $scope.dataObj;

        var defer = $q.defer();

        $http.get(apachehost + "API/authenticate/student_token")
            .then(function mySuccess(response) {
                var resp = response.data;
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.student_token != null) {
                    var res = $http.post(root + method, request, {
                        headers: { 'AuthToken': resp.data.student_token }
                    });
                    res.success(function (data, status, headers, config) {
                        response = data;
                        defer.resolve(response);
                    });
                    res.error(function (data, status, headers, config) {
                        response = data;
                        defer.resolve(response);
                        // console.log("Error :" + status + ":" + JSON.stringify(data) + ":" + JSON.stringify(headers))
                    });

                } else {
                    alert("Some error authenticating your request. Please logout, clear your browser cache and try again.");
                }

            }, function myError(response) {

                defer.resolve(response);
                alert("Connection Error while authenticating your request. Please logout, clear your browser cache and try again.");

            });

        response = defer.promise;
        return $q.when(response);
    }

    this.callGetService = function (method) {

        var defer = $q.defer();

        $http.get(root + method)
            .then(function mySuccess(response) {

                defer.resolve(response);

            }, function myError(response) {

                defer.resolve(response);

            });

        response = defer.promise;
        return $q.when(response);
    }

    this.callAdminService = function ($scope, method) {

        var request = $scope.dataObj;

        var defer = $q.defer();

        $http.get(apachehost + "API/authenticate/admin_token")
            .then(function mySuccess(response) {
                var resp = response.data;
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    var res = $http.post(rootAdmin + method, request, {
                        headers: { 'AuthToken': resp.data.admin_token }
                    });
                    res.success(function (data, status, headers, config) {
                        response = data;
                        defer.resolve(response);
                        //console.log("Result :" + JSON.stringify(data) + ":" + JSON.stringify(headers))


                    });
                    res.error(function (data, status, headers, config) {
                        response = data;
                        defer.resolve(response);
                        // console.log("Error :" + status + ":" + JSON.stringify(data) + ":" + JSON.stringify(headers))
                    });


                } else {
                    alert("Some error authenticating your request. Please logout, clear your browser cache and try again.");
                }

            }, function myError(response) {

                defer.resolve(response);
                alert("Connection Error while authenticating your request. Please logout, clear your browser cache and try again.");

            });

        response = defer.promise;
        return $q.when(response);
    }

    this.getExam = function (testId, studentId) {

        // if posts object is not defined then start the new process for fetch it
        // create deferred object using $q
        var deferred = $q.defer();

        var method = root + "getTest/" + testId + "/" + studentId;
        // console.log(method);

        // get posts form backend
        $http.get(method)
            .then(function (result) {
                // save fetched posts to the local variable
                response = result.data;
                //console.log("Response is - " + JSON.stringify(response));
                // resolve the deferred
                deferred.resolve(response);
            }, function (error) {
                response = error;
                deferred.reject(error);
            });

        // set the posts object to be a promise until result comeback
        response = deferred.promise;
        // in any way wrap the posts object with $q.when which means:
        // local posts object could be:
        // a promise
        // a real posts data
        // both cases will be handled as promise because $q.when on real data will resolve it immediately
        return $q.when(response);
    };

    this.logout = function () {

    }


});

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
};

function get_token() {
    return new Promise(function (resolve, reject) {
        var xhr = $.ajax({
            type: "POST",
            url: apachehost + "API/authenticate/student_token",
            contentType: "application/json",
        })
            .done(function (response) {
                resolve(response);
            })
            .fail(function (jqXHR) {
                // Our error logic here
                reject(jqXHR.responseText);
            });
    });
}

function get_admin_token() {
    return new Promise(function (resolve, reject) {
        var xhr = $.ajax({
            type: "POST",
            url: apachehost + "API/authenticate/admin_token",
            contentType: "application/json",
        })
            .done(function (response) {
                resolve(response);
            })
            .fail(function (jqXHR) {
                // Our error logic here
                reject(jqXHR.responseText);
            });
    });
}