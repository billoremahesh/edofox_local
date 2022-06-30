// Add New Staff
function add_new_staff(
  adminName,
  instituteId,
  email,
  mobile,
  perms,
  classrooms,
  username,
  password
) {
  // add new institute using a async promise
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        adminName: adminName,
        id: instituteId,
        email: email,
        contact: mobile,
        perms: perms,
        classrooms: classrooms.toString(),
        username: username,
        password: password,
      },
    };
    //Load tokens first
    get_admin_token()
      .then(function (result) {
        var resp = JSON.parse(result);
        if (
          resp != null &&
          resp.status == 200 &&
          resp.data != null &&
          resp.data.admin_token != null
        ) {
          var dataString = JSON.stringify(jsonObjects);
          // console.log(dataString);

          var xhr = $.ajax({
            type: "POST",
            data: dataString,
            url: rootAdmin + "createAdmin",
            contentType: "application/json",
            beforeSend: function (formData) {
              formData.setRequestHeader("AuthToken", resp.data.admin_token);
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
        } else {
          alert(
            "Some error authenticating your request. Please clear your browser cache and try again."
          );
        }
      })
      .catch(function (error) {
        // An error occurred
        // alert("Exception: " + error);
      });
  });
}

// Set New Password
function set_new_password(token, password) {
  return new Promise(function (resolve, reject) {
    var jsonObjects = {
      institute: {
        token: token,
        password: password,
      },
    };
    var dataString = JSON.stringify(jsonObjects);
    // console.log(dataString);
    var xhr = $.ajax({
      type: "POST",
      data: dataString,
      url: rootAdmin + "changePassword",
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
