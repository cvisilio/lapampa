"use strict";

window.onload = function () {
  $("#captchaCheckedTrue").hide();
};

function submitForm() {
  if (grecaptcha.getResponse().length === 0) {
    showError("Captcha Requerido!");
    return;
  }

  var captcha = grecaptcha.getResponse();
  var clave1 = $("#user_password").val();
  var nombre_usuario = $("#user_name").val();
  var login1 =1;
  var params = {
    captcha: captcha, user_name:nombre_usuario,login:login1 ,user_password:clave1
  };
  $.ajax({
    type: "POST",
    url: "https://lapampaperonista.com.ar/sz/verify.php",
    data: params,
    error: function error() {
      showError("Error de Servidor!");
    },
    success: function success(res) {
      if (res.success) {
        //var response = JSON.stringify(res, null, "\t");
        //$("#backendResponse").html(response);
        //$("#captchaForm")[0].reset();
        //grecaptcha.reset();
		window.location.href = "https://lapampaperonista.com.ar/sz/index.php";
      } else {
        showError("reCaptcha Error!");
      }
    }
  });
}

/*function recaptchaCheckedCb() {
  $("#captchaCheckedFalse").hide();
  $("#captchaCheckedTrue").show();
}

function recaptchaExpireCb() {
  $("#captchaCheckedTrue").hide();
  $("#captchaCheckedFalse").show();
}
*/

function showError(msg) {
  $("#captchaError").fadeToggle().text(msg);
  setTimeout(function () {
    $("#captchaError").fadeToggle();
  }, 3000);
}