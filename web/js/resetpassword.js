(function () {
    $(document).ready(function () {
        var password = $("#password");
        var confirmPassword = $("#c_password");
        var errorMsg = $("#errorMsg");

        $("#resetPwdBtn").unbind("click").click(function (e) {
            errorMsg.hide();
            if(password.val() != confirmPassword.val()) {
                e.preventDefault();
                password.focus();
                errorMsg.fadeIn();
            }
        });
    });
})();