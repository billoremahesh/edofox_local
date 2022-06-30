/**
 * To reevaluate result and return data
 * USES JS PROMISE
 */
function revaluateResult(test_id, student_id) {
  // REF: https://stackoverflow.com/questions/14220321/how-to-return-the-response-from-an-asynchronous-call
  return new Promise(function (resolve, reject) {
    get_admin_token()
      .then(function (result) {
        var resp = JSON.parse(result);
        if (
          resp != null &&
          resp.status == 200 &&
          resp.data != null &&
          resp.data.admin_token != null
        ) {
          var obj = {
            test: {
              id: test_id,
            },
          };
          if (student_id != null) {
            obj.student = {
              id: student_id,
            };
          }
          var myJSON1 = JSON.stringify(obj);

          var url = rootAdmin + "revaluateResult";
          fetch(url, {
            method: "POST", // or 'PUT'
            body: myJSON1, // data can be `string` or {object}!
            headers: {
              "Content-Type": "application/json",
              AuthToken: resp.data.admin_token,
            },
          })
            .then((res) => res.json())
            .then((response) => {
              // console.log('Success:', JSON.stringify(response));

              resolve(JSON.stringify(response));
            })
            .catch((error) => {
              console.error("Error:", error);
              reject(error);
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

/**
 * To submit all ongoing tests from admin side
 */
function submitOngoingTests(test_id) {
  return new Promise(function (resolve, reject) {
    var dataString = {
      test_id: test_id,
    };
    $.ajax({
      type: "POST",
      data: dataString,
      url: base_url + "/tests/submit_ongoing_tests",
    })
      .done(function (response) {
        // success logic here
        resolve(response);
      })
      .fail(function (jqXHR) {
        // Our error logic here
        reject(jqXHR.responseText);
      });
  });
}

function toggleTestValue(element, toggleValue, test_id) {
  // console.log(element, toggleValue, test_id);

  var url = base_url + "/tests/update_test_properties";

  Snackbar.show({ text: "Updating configuration ... Please wait." });

  var dataString = "update=" + toggleValue + "&test_id=" + test_id;
  $.ajax({
    type: "POST",
    data: dataString,
    url: url,
    success: function (data) {
      console.log(data);

      if (data != 1) {
        Snackbar.show({
          text: "There was some error. Please reload and try again.",
        });
      } else {
        $(element).toggleClass("active-icon");

        Snackbar.show({ text: "Test configuration updated successfully." });
      }
    },
  });
}
