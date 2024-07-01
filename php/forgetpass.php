<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$email = $_POST["email"];
$pass = $_POST["password"];
$profile = "student";

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'librasys';

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
    
if ($profile == "student") {
    $sql = "SELECT * FROM student WHERE student.semail = '$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
} 

if(mysqli_num_rows($result) != 1){
    header("Location: ../templates/forgetpassword-error.html");
    exit();
}

function encode_data($data, $key) {
    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext, $key, true);
    return base64_encode($iv . $hmac . $ciphertext);
  }
  
  function send_email($user, $pwd, $recipient, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pwd;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($user);
        $mail->addAddress($recipient);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Email sending failed. Error: " . $mail->ErrorInfo;
    }
}

$otp = rand(111111, 999999);
$msg = "Hi,\n\n" . "Your OTP for LibraSys password change is: " . $otp . "\n\nThanks & regards,\nLibraSys";

$user = 'tutorlinkcare@gmail.com';
$pwd = 'ylaabjsusptjebxa';
$recipient = $email;
$subject = 'LibraSys OTP';

send_email($user, $pwd, $recipient, $subject, $msg);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>TutorLink Forget Password - Verify</title>
        <link rel="stylesheet" type="text/css" href="../css/style.css">
    </head>
    <body>
    <br><br><br><br>
    <div class="header">
        <a href="./index.html"><img src="../static/logo.jpeg" alt="Logo" class="logo"></a>
      </div>
	<div class="container">
		<form method="post" action="./verifyotp_changepass.php">
			<h1>Verify</h1>
            <input type="hidden" name="email" id="email" value="<?php echo $email; ?>">
            <input type="hidden" name="profile" id="profile" value="<?php echo $profile; ?>">
            <input type="hidden" name="pass" id="pass" value="<?php echo encode_data($pass, '?MOU631'); ?>">
            <input type="hidden" name="realotp" id="realotp" value="<?php echo encode_data($otp, '?MOU631'); ?>">
            <label for="otp">OTP</label>
			<input type="text" id="otp" name="otp" required>
			<input type="submit" value="Verify">
    </body>
</html>