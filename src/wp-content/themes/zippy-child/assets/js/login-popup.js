document.addEventListener("DOMContentLoaded", function () {
    function waitForForm(callback) {
        const check = setInterval(() => {
            const form = document.querySelector("#student-login-form");
            if (form) {
                clearInterval(check);
                callback(form);
            }
        }, 200);
    }

    waitForForm(function (form) {

        console.log("Student login form FOUND:", form);

        const msg = document.querySelector(".student-login-message");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "student_login");
            formData.append("nonce", studentLogin.nonce);

            console.log("AJAX URL:", studentLogin.ajax_url);
            console.log("Sending data:", Object.fromEntries(formData));

            fetch(studentLogin.ajax_url, {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(response => {

                console.log("AJAX RESPONSE:", response);

                if (response.success) {
                    msg.innerHTML = `<div class="success">Login thành công! Redirect...</div>`;
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 800);
                } else {
                    msg.innerHTML = `<div class="error">${response.data.message}</div>`;
                }

            })
            .catch(err => {
                console.error("AJAX ERROR:", err);
                msg.innerHTML = `<div class="error">Có lỗi xảy ra, vui lòng thử lại.</div>`;
            });
        });
    });
});
