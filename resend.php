<?php 
session_start();
include("config.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
if (isset($_GET['id'])) {
    global $id;
    $id = $_GET['id'];
}
$email = $_GET["email"];
$name = $_GET["name"];
$v_code = rand(99999,999999);
$con->prepare("UPDATE users SET verification_code=:v_code WHERE email=:email ")->execute([":v_code"=>$v_code,":email"=>$email]);
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'fakework12345@gmail.com';
$mail->Password   = 'dkuceaajxparecdh';
$mail->SMTPSecure = 'ssl';
$mail->Port       = 465;
$mail->setFrom('fakework12345@gmail.com', 'Online Store');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Verification Code';
$mail->Body    = "Your Verification Code Is : <b>$v_code</b><p style='color:red'>Don't Share It</p>";
$mail->send();
setcookie("resent","Y", time()+ 2);
if($name=="p"){
    header("location:pass_verification.php?email=$email&id=$id&t=$_SESSION[csrf_token]");
    exit();
}else if($name=="u"){
    header("location:verification.php?email=$email&t=$_SESSION[csrf_token]");
    exit();
}
?>