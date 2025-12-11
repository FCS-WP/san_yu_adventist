jQuery(document).ready(function ($) {
  $("#student-login-form").on("submit", function (e) {
    e.preventDefault();

    var data = {
      action: "student_login",
      student_name: $("input[name='student_name']").val(),
      level: $("select[name='level']").val(),
      student_id: $("input[name='student_id']").val(),
    };
    var ajaxurl = "/wp-admin/admin-ajax.php";

    $.post(ajaxurl, data, function (response) {
      if (response.success) {
        $(".student-login-message")
          .css("color", "green")
          .text(response.data.message);
      } else {
        $(".student-login-message")
          .css("color", "red")
          .text(response.data.message);
      }
    });
  });
});
