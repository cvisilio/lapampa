<?php 
const CAPTCHA_SITE_KEY = "6Lcu_f0rAAAAAGrOvrlyOTMsc3CIKNCSApor-PnW";

require_once("config/db.php");
require_once("classes/Login.php");

// Crear instancia de Login
$login = new Login();

if ($login->isUserLoggedIn()) {
    header("location: index.php");
    exit();
  } 
 else {
 
  $page_title = "SZ | Login";

 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Bootstrap CSS  -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Google Fonts  -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Custom Style  -->
    <link rel="stylesheet" href="style_login.css">
</head>

<body>
    <section class="login-page my-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="login-block mx-auto mb-10">
                        <img class="logo" src="https://factupyme.com.ar/factupyme.png" alt="SZ">
                        <h1>Acceso a SZ</h1>
                        <form id="captchaForm" onSubmit="submitForm();return false">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input name="user_name" id="user_name" type="text" value="" placeholder="Usuario" class="form-control" required>
                                </div>
                            </div>
                            <hr class="hr-xs">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock ti-unlock"></i></span>
                                    <input type="password" name="user_password" id="user_password" value=""  placeholder="Clave" class="form-control" required>
                                   <i id="togglePassword" class="fa fa-eye"></i> 
                                </div>
                            </div>
                            <div class="g-recaptcha" 
                                data-sitekey="<?php echo CAPTCHA_SITE_KEY; ?>"
                                data-callback="recaptchaCheckedCb"
                                data-expired-callback="recaptchaExpireCb"
                                data-theme="light">                            </div>
                            <button class="btn btn-primary btn-block" name="login" type="submit">Acceder</button>
                            <div id="captchaError" class="captcha-error"></div>
                        </form>
                    </div>
                </div>
               
                
             </div>
        </div>
    </section>
    
    
    <!-- Custom Script  -->
    <script src="icaptcha.script.js"></script>
    <!-- jQuery  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- reCAPTCHA v2 Javascript  -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <script>
	
	const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#user_password');

  togglePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    // toggle the eye slash icon
    this.classList.toggle('fa-eye-slash');
});
	
    </script>
       
</body>
</html>
<?php } ?>