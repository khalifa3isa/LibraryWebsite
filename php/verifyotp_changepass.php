<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );

$dsn = 'mysql:host=localhost;dbname=librasys';
$username = 'root';
$password = 'root';
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try {
    $pdo = new PDO($dsn, $username, $password, $options);
  } catch(PDOException $e) {
    echo 'Error connecting to database: ' . $e->getMessage();
    exit();
  }

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
  
$email = $_POST["email"];
$pass = decode_data($_POST["pass"], '?MOU631');
$profile = "student";
$actualotp = decode_data($_POST["realotp"], '?MOU631');
$otp = $_POST["otp"];

if($otp == $actualotp){
    if($profile == 'student'){
        $stmt = $pdo->prepare("UPDATE student SET student.spass = '$pass' WHERE student.semail = '$email'");
    }

    try {
        $stmt->execute();
        header("Location: ../templates/password-updated.html");
        exit();
      } catch(PDOException $e) {
        echo 'Error inserting data: ' . $e->getMessage();
        exit();
      }
}else{
    echo 'fail';
}
?>