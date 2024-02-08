<?php
session_start();
include("config.php");

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST["add"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $user_id= $_SESSION["id"];
        $name = trim($_POST["name"]);
        $price = preg_replace('/[^0-9.]/', '', $_POST["price"]);
        $coin = $_POST["coin"];
        $description = trim($_POST["description"]);
        $image = $_FILES["image"];
        $image_location = $_FILES["image"]["tmp_name"];
        $image_name = $_FILES["image"]["name"];
        $image_extension = explode(".",$image_name)[1];
        $valid=["jpg","jpge","png"];
        $image_up = "images/products/".$image_name;

        if(in_array($image_extension,$valid)){
            $sqls = "INSERT INTO prod (user_id,name,price,coin,description,image) VALUES (:user_id,:name, :price, :coin, :description,:image_up)";
            $stmt=$con->prepare($sqls);
            $prams=[
                ":user_id"=>$user_id,
                ":name"=>$name,
                ":price"=>$price,
                ":coin"=>$coin,
                ":description"=>$description,
                ":image_up"=>$image_up
            ];
            $stmt->execute($prams);
            
            if(move_uploaded_file($image_location,$image_up)){
                setcookie("added","Y",time()+2);
            }else {
                setcookie("failed","Y",time()+2);
            }
            
        }else{
            setcookie("notValid","y",time()+2);
        }

        header("Location:add.php");
        exit();
    }    
}

if(isset($_POST["add-cart"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{
        $prod_id= $_POST["id"];
        $user_id =$_SESSION["id"];
        $name = trim($_POST["name"]);
        $quantity = $_POST["number"];
        $price = $_POST["price"] * $quantity;
        $coin = $_POST["coin"];
        $sqls = "INSERT INTO cart (prod_id,user_id,name,quantity,price,coin) VALUES (:prod_id,:user_id,:name,:quantity,:price,:coin)";
        $stmt=$con->prepare($sqls);
        $prams=[
            ":prod_id"=>$prod_id,
            ":user_id"=>$user_id,
            ":name"=>$name,
            ":quantity"=>$quantity,
            ":price"=>$price,
            ":coin"=>$coin
        ];
        $stmt->execute($prams);
        header("location: cart.php");
        exit();
    }
}

if(isset($_POST["register"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{
        $ustatus = "user";
        $uimage = "images/img/avatar.jpg";
        $uname = trim($_POST["name"]);
        $uemail = trim($_POST["email"]);
        $upass = $_POST["pass"];
        $uconf =$_POST["conf-pass"];
        $uhashpass = password_hash($upass,PASSWORD_DEFAULT);
        $uv_code = rand(99999,999999);
        $stmt1 = $con->prepare("SELECT email FROM users");
        $stmt1->execute();
        $email = $stmt1->fetchAll();
        $sqls = "INSERT INTO users (status,email,name,password,image,verification_code,verification_status) VALUES (:ustatus, :uemail, :uname, :uhashpass,:uimage,:uv_code,0)";
        $prams=[
            ":ustatus"=>$ustatus,
            ":uemail"=>$uemail,
            ":uname"=>$uname,
            ":uhashpass"=>$uhashpass,
            ":uimage"=>$uimage,
            ":uv_code"=>$uv_code
        ];
        if(!empty($email)){
            foreach($email as $k => $v){
                $emails[]= $v["email"];
            }
        }
        if(($upass==$uconf)){
            if(!empty($email)){
                if(!in_array($uemail,$emails)){
                    $stmt2=$con->prepare($sqls);
                    $stmt2->execute($prams);
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'fakework12345@gmail.com';
                    $mail->Password   = 'dkuceaajxparecdh';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;
                    $mail->setFrom('fakework12345@gmail.com', 'Online Store');
                    $mail->addAddress($uemail);
                    $mail->isHTML(true);
                    $mail->Subject = 'Verification Code';
                    $mail->Body    = "Your Verification Code Is : <b>$uv_code</b><p style='color:red'>Don't Share It</p>";
                    $mail->send();
                    header("location: verification.php?email=$uemail");
                    exit();
                }else{
                    setcookie("found","Y",time()+2);
                }
            }else{
                $stmt2=$con->prepare($sqls);
                $stmt2->execute($prams);
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fakework12345@gmail.com';
                $mail->Password   = 'dkuceaajxparecdh';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;
                $mail->setFrom('fakework12345@gmail.com', 'Online Store');
                $mail->addAddress($uemail);
                $mail->isHTML(true);
                $mail->Subject = 'Verification Code';
                $mail->Body    = "Your Verification Code Is : <b>$uv_code</b><p style='color:red'>Don't Share It</p>";
                $mail->send();
                header("location: verification.php?email=$uemail");
                exit();
            }
            header("location: register.php");
            exit();
        }else{
            setcookie("notmatch","Y",time()+2);
            header("location: register.php");
            exit();
        }
    }
}

if(isset($_POST["bus-register"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $bustatus = "trader";
        $buimage = "images/img/avatar.jpg";
        $buname = trim($_POST["name"]);
        $buemail = trim($_POST["email"]);
        $bupass = $_POST["pass"];
        $buconf =$_POST["conf-pass"];
        $buhashpass = password_hash($bupass,PASSWORD_DEFAULT);
        $buv_code = rand(99999,999999);
        $stmt1 = $con->prepare("SELECT email FROM users");
        $stmt1->execute();
        $email = $stmt1->fetchAll();
        $sqls = "INSERT INTO users (status,email,name,password,image,verification_code,verification_status) VALUES (:bustatus, :buemail, :buname, :buhashpass,:buimage,:buv_code,0)";
        $prams=[
            ":bustatus"=>$bustatus,
            ":buemail"=>$buemail,
            ":buname"=>$buname,
            ":buhashpass"=>$buhashpass,
            ":buimage"=>$buimage,
            ":buv_code"=>$buv_code
        ];
        if(!empty($email)){
            foreach($email as $k => $v){
                $emails[]= $v["email"];
            }
        }
        if($bupass == $buconf){
            if(!empty($email)){
                if(!in_array($buemail,$emails)){
                    $stmt2=$con->prepare($sqls);
                    $stmt2->execute($prams);
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'fakework12345@gmail.com';
                    $mail->Password   = 'dkuceaajxparecdh';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;
                    $mail->setFrom('fakework12345@gmail.com', 'Online Store');
                    $mail->addAddress($buemail);
                    $mail->isHTML(true);
                    $mail->Subject = 'Verification Code';
                    $mail->Body    = "Your Verification Code Is : <b>$buv_code</b><p style='color:red'>Don't Share It</p>";
                    $mail->send();
                    header("location: verification.php?email=$buemail");
                    exit();
                }else{
                    setcookie("found","Y",time()+2);
                }
            }else{
                $stmt2=$con->prepare($sqls);
                $stmt2->execute($prams);
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fakework12345@gmail.com';
                $mail->Password   = 'dkuceaajxparecdh';
                $mail->SMTPSecure = 'ssl';
                $mail->Port       = 465;
                $mail->setFrom('fakework12345@gmail.com', 'Online Store');
                $mail->addAddress($buemail);
                $mail->isHTML(true);
                $mail->Subject = 'Verification Code';
                $mail->Body    = "Your Verification Code Is : <b>$buv_code</b><p style='color:red'>Don't Share It</p>";
                $mail->send();
                header("location: verification.php?email=$buemail");
                exit();
            }
            header("location: bus_register.php");
            exit();
        }else{
            setcookie("notmatch","Y",time()+2);
            header("location: bus_register.php");
            exit();
        }
    }
}
?>