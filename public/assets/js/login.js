// Validate Login
function check_valid_login_user(username, password) {
  // validate user using a async promise
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      student: { rollNo: username, password: password },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: root + "login",
      contentType: "application/json",
    })
      .done(function (response) {
        // success logic here
        resolve(JSON.stringify(response));
        // console.log(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}

// Set New Password
function set_new_password(student_token, password, admin_token) {
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      student: {
        token: student_token,
        password: password,
      },
      requestType: 'Admin'
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: root + "changePassword",
      contentType: "application/json",
      beforeSend: function (request) {
        request.setRequestHeader("AuthToken", admin_token);
      },
    })
      .done(function (response) {
        // success logic here
        resolve(JSON.stringify(response));
        // console.log(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}
