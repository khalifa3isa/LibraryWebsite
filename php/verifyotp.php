<?php

session_start();
error_reporting( E_ALL );
ini_set( "display_errors", 1 );

function decode_data($encoded_data, $key) {
    $decoded_data = base64_decode($encoded_data);
    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($decoded_data, 0, $ivlen);
    $hmac = substr($decoded_data, $ivlen, 32);
    $ciphertext = substr($decoded_data, $ivlen + 32);
    $calcmac = hash_hmac('sha256', $ciphertext, $key, true);

    if (!hash_equals($hmac, $calcmac)) {
      throw new Exception('HMAC validation failed');
    }
    
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
  }
  
$name = $_POST["name"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$pass = decode_data($_POST["pass"], '?MOU631');
$profile = "student";
$actualotp = decode_data($_POST["realotp"], '?MOU631');
$otp = $_POST["otp"];

if($otp == $actualotp){
  $url = 'http://localhost/librasys/php/signup.php';
  $data = array('name' => $name, 'phone' => $phone, 'email' => $email, 'profile' => $profile, 'pass' => $pass);
  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
    ),
  );

  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);

  if(strpos($result, "Duplicate entry") !== false){
    header("Location: ../templates/signup-error.html");
    exit();
  }
  else{

    $_SESSION["email"] = $email;
    
    if($profile == "student"){
      header("Location: ../php/dashboard.php");
      exit();
      #echo $result;
    }
  }
  
}else{
    echo 'fail';
}
?>