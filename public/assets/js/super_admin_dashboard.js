function weekly_tests_chart(chart_year) {
  $.ajax({
    url: base_url + "/charts/weekly_tests_chart",
    method: "POST",
    data: {
      chart_year: chart_year,
    },
    success: function (result) {
      $("#weekly_tests_chart_div").html(result);
      generate_weekly_tests_chart();
    },
  });
}

function weekly_student_logins_chart(chart_year) {
  $.ajax({
    url: base_url + "/charts/weekly_student_logins_chart",
    method: "POST",
    data: {
      chart_year: chart_year,
    },
    success: function (result) {
      $("#weekly_student_logins_chart_div").html(result);
      generate_weekly_student_logins_chart();
    },
  });
}

$(document).ready(function () {
  var todays_date = new Date();
  var current_year = todays_date.getFullYear();
  weekly_tests_chart(current_year);
  weekly_student_logins_chart(current_year);
});
