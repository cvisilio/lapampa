<?php

const CAPTCHA_SITE_KEY = "6Lcu_f0rAAAAAGrOvrlyOTMsc3CIKNCSApor-PnW";

const CAPTCHA_SECRET_KEY = "6Lcu_f0rAAAAAPZdz1kW8y10_UFcj-LSXBxME30n";

$data = array();

if (isset($_POST['captcha'])) {
    $response = $_POST['captcha'];
	$secret_key = CAPTCHA_SECRET_KEY;
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $payload = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$response.'&remoteip='.$remoteip);
    $data = json_decode($payload, TRUE);
	
	if($data['success']===TRUE)
	 {
	 // $_SESSION['uno']=$data['challenge_ts'];
	  session_start();
	  require_once("config/db.php");
	  require_once("classes/Login.php");
	  $login = new Login();
	 }
		
	header('Content-Type: application/json');
    die(json_encode($data));
}?>