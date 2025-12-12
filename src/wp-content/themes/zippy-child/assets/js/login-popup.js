jQuery(document).ready(function ($) {
  const $form = $("#student-login-form");
  const $message = $(".student-login-message");
  const ajaxurl = "/wp-admin/admin-ajax.php";

  // Handle student login
  $form.on("submit", function (e) {
    e.preventDefault();

    const data = {
      action: "student_login",
      student_name: $form.find("input[name='student_name']").val(),
      level: $form.find("select[name='level']").val(),
      student_id: $form.find("input[name='student_id']").val(),
    };

    $.post(ajaxurl, data)
      .done(function (response) {
        const color = response.success ? "green" : "red";
        $message.css("color", color).text(response.data.message);

        if (response.success) {
          setTimeout(() => {
            window.location.replace("/lesson-shop");
          }, 1000);
        }
      })
      .fail(function () {
        $message.css("color", "red").text("Something went wrong.");
      });
  });

  // Login button action

  $("a.nav-top-not-logged-in").attr("data-open", "#custom-login-form-popup");
});
