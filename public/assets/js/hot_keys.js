/**
 * Add New Test - Hot Key (E + T)
 * Open the add new test modal
 */
// $(document).jkey("ctrl+alt+t", function () {
//   show_add_modal("modal_div", "add_test_modal", "tests/add_test_modal");
// });

/**
 * Add New Student - Hot Key (E + S)
 * Open the add new student modal
 */
// $(document).jkey("ctrl+alt+s", function () {
//   show_add_modal(
//     "modal_div",
//     "add_student_modal",
//     "Students/add_student_modal"
//   );
// });

/**
 * Add New Classroom - Hot Key (E + C)
 * Open the add new classroom modal
 */
// $(document).jkey("ctrl+alt+c", function () {
//   show_add_modal(
//     "modal_div",
//     "add_classroom_modal",
//     "classrooms/add_classroom_modal/classrooms"
//   );
// });


// Shortcut key for nav searchbox foucus
$(document).jkey("ctrl+/", function () {
  $("#search_route_links").focus();
});
