// var date = new Date();
// var current_year = date.getFullYear();
// var current_month = ("0" + (date.getMonth() + 1)).slice(-2);
// var month_max_range = ("0" + (date.getMonth() + 1)).slice(-2) + 3;
// var current_day = ("0" + date.getDate()).slice(-2);
// var today = current_year + "-" + current_month + "-" + current_day;
// var end_date = current_year + "-" + month_max_range + "-" + current_day;
// $(".form_datetime1").datetimepicker({
//   format: "yyyy-mm-dd",
//   autoclose: true,
//   startDate: today,
//   endDate: end_date,
//   todayBtn: true,
//   fontAwesome: "font-awesome",
//   pickerPosition: "bottom",
//   pickTime: false,
//   minView: 2,
// });

function get_subcription_plans() {
  toggle_custom_loader(true, "custom_loader");
  $("#subcription_plans").html("");
  $("#packages_total_amt_div").html(0);
  $("#packages_total_amt").val(0);
  $("#final_total_amt_div").html(0);
  $("#final_total_amt").val(0);

  var subscription_type = $("#subscription_type").val();
  var max_students = $("#max_students").val();
  var add_amount_manual_check = $("#add_amount_manual");
  var add_amount_manual_value = 0;
  if (add_amount_manual_check != null) {
    if ($("#add_amount_manual").is(":checked")) {
      add_amount_manual_value = 1;
    }
  }
  if (add_amount_manual_value == 0 && subscription_type != "") {
    $("#manual_subcription_plans").hide();
    $.ajax({
      url: base_url + "/subscriptions/get_subcription_plans",
      type: "POST",
      data: {
        plan_type: subscription_type,
        max_students: max_students,
      },
      success: function (result) {
        toggle_custom_loader(false, "custom_loader");
        $("#subcription_plans").html(format_subcription_plans(result));
      },
    });
  } else {
    $("#packages_total_amt_div").html(0);
    $("#packages_total_amt").val(0);
    $("#final_total_amt_div").html(0);
    $("#final_total_amt").val(0);
    $("#subcription_plans").html("");
    $("#manual_subcription_plans").show();
    toggle_custom_loader(false, "custom_loader");
  }
}

function format_subcription_plans(data) {
  var html = "";
  if (data != null) {
    data = JSON.parse(data);
    for (var i = 0; i < data.length; i++) {
      html = html + "<div class='col-12 package_info_card y-1'>";
      if (i == 0) {
        html =
          html +
          "<input type='hidden' name='plan_name' value='" +
          data[i]["plan_name"] +
          "' required />";
      }
      html = html + "<div class='form-check'>";
      html =
        html +
        "<input class='form-check-input' onclick='calculate_amount()' name='check_pkg[]' type='checkbox' value='" +
        data[i]["id"] +
        "' id='" +
        data[i]["id"] +
        "'>";
      html =
        html +
        "<label class='form-check-label' for='" +
        data[i]["id"] +
        "'>" +
        data[i]["module"] +
        " ( " +
        data[i]["plan_name"] +
        " )</label>";
      html = html + "</div>";
      html = html + "</div>";
    }
  }
  return html;
}

// Calculate selected package amount
function calculate_amount() {
  $("#submit_btn_div").html("");
  var checked_pkg = [];
  $.each($("input[name='check_pkg[]']:checked"), function () {
    checked_pkg.push($(this).val());
  });
  var selected_pkgs = checked_pkg.join(",");

  $.ajax({
    url: base_url + "/subscriptions/get_selected_pkgs_amt",
    type: "POST",
    data: {
      selected_pkgs: selected_pkgs,
      no_of_students: $("#max_students").val(),
    },
    success: function (result) {
      result = Number(result).toFixed(2);
      $("#packages_total_amt_div").html(result);
      $("#packages_total_amt").val(result);
      final_total_amount();
    },
  });
}

$(".module_manual_amount").change(function () {
  var total = 0;
  $(".module_manual_amount").each(function () {
    var module_amount = $(this).val();
    if (isNaN(module_amount)) {
      alert("Amount should be numeric value");
    }
    if (module_amount != "") {
      total = Number(total) + Number(module_amount);
    }
  });
  total = Number(total).toFixed(2);
  $("#packages_total_amt_div").html(total);
  $("#packages_total_amt").val(total);
  final_total_amount();
});

$("#discount").on("input", function (e) {
  final_total_amount();
});

// Calculate final amount after discount
function final_total_amount() {
  var total_fee = 0;

  var packages_total_amt = $("#packages_total_amt").val();

  console.log("Package total amount: ", packages_total_amt);

  var discount = $("#discount").val();
  console.log("Discount %: ", discount);

  if (discount > 20) {
    alert("Max discount only upto 20% !!");
    exit();
  }

  var discount_amt = (packages_total_amt * discount) / 100;
  console.log("Discount amount: ", discount_amt);
  $("#total_saved").html(discount_amt);

  total_fee = packages_total_amt - discount_amt;

  total_fee = Number(total_fee).toFixed(2);
  console.log("final total amount: ", total_fee);
  $("#final_total_amt_div").html(total_fee);
  $("#final_total_amt").val(total_fee);

  if (total_fee > 0) {
    $("#submit_btn_div").html("<button class='btn btn-primary'> Add </button>");
  } else {
    $("#submit_btn_div").html("");
  }
}

function format_unbilled_entitites(data) {
  var html = "";
  if (data != null) {
    data = JSON.parse(data);
    for (var i = 0; i < data.length; i++) {
      html = html + "<div class='col-12 package_info_card y-1'>";
      html =
        html +
        "<input name='unbilled_entitites[]' type='hidden' value='" +
        data[i]["module"] +
        "' >";
      html =
        html +
        "<input name='unbilled_entitites_vals[]' type='hidden' value='" +
        data[i]["price"] +
        "' >";
      html = html + "<label>" + "" + data[i]["module"] + "</label>";
      html = html + "<br/><label>" + "" + data[i]["price"] + "</label>";

      html = html + "</div>";
    }
  }
  return html;
}
