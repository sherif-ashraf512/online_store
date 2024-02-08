<?php
session_start();

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

include("config.php");

date_default_timezone_set('Africa/Cairo');

if(isset($_POST["add"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $id =$_POST["id"];
        $name = trim($_POST["name"]);
        $price = preg_replace('/[^0-9.]/', '', $_POST["price"]);
        $description = $_POST["description"];
        $image = $_FILES["image"];
        $img = $con->prepare("SELECT image FROM prod WHERE id=:id");
        $img->execute([":id"=>$id]);
        $current_image = $img->fetch()["image"];
        $image_location = $_FILES["image"]["tmp_name"];
        $image_name = $_FILES["image"]["name"];
        $image_extension = explode(".",$image_name)[1];
        $valid=["jpg","jpge","png"];
        $image_up = "images/products/".$image_name;
        if($image["size"]==0){
            $image_up = $current_image;
            $image_extension = explode(".",$current_image)[1];
        }else{
            unlink($current_image);
        }
        if(in_array($image_extension,$valid)){
            move_uploaded_file($image_location,$image_up);
                $sqls = "UPDATE prod SET name=:name, price=:price, description=:description,image=:image_up WHERE id=:id";
                $stmt=$con->prepare($sqls);
                $prams=[
                    ":name"=>$name,
                    ":price"=>$price,
                    ":description"=>$description,
                    ":image_up"=>$image_up,
                    ":id"=>$id
                ];
                $stmt->execute($prams);
        }else{
            setcookie("notValid","y",time()+2);
            header("Location: update.php?id=$id");
            exit();
        }
        header("Location: products.php");
        exit();
    }
}

if(isset($_FILES["img"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $status = $_SESSION["status"];
        $image = $_FILES["img"];
        $image_location = $_FILES["img"]["tmp_name"];
        $image_name = $_FILES["img"]["name"];
        $image_extension = explode(".",$image_name)[1];
        $valid=["jpg","jpge","png"];
        $image_up = "images/users/".$image_name;

        if(in_array($image_extension,$valid)){
            $img = $con->prepare("SELECT image FROM users WHERE id=:id");
            $img->execute([":id"=>$_SESSION["id"]]);
            $current_image=$img->fetch()["image"];
            if($current_image !="images/img/avatar.jpg"){
                unlink("$current_image");
            }
            move_uploaded_file($image_location,$image_up);
            $sqls = "UPDATE users SET image=:image_up WHERE id=:id";
            $con->prepare($sqls)->execute([":image_up"=>$image_up,":id"=>$_SESSION["id"]]);
        }else{
            setcookie("notValid","y",time()+2);
        }

        if($status=="user"){
            header("location: settings.php");
            exit();
        }
        if($status== "trader"){
            header("location: bus_settings.php");
            exit();
        }
    }
}

if(isset($_POST["ch-name"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $status = $_SESSION["status"];
        $name = trim($_POST["name"]);

        $sqls = "UPDATE users SET name=:name WHERE id=:id";
        $con->prepare($sqls)->execute([":name"=>$name,":id"=>$_SESSION["id"]]);

        if($status=="user"){
            header("location: settings.php");
            exit();
        }elseif($status== "trader"){
            header("location: bus_settings.php");
            exit();
        }
    }
}

if(isset($_POST["ch-pass"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $status = $_SESSION["status"];
        $email = $_SESSION["email"];
        $Current_PassStmt = $con->prepare("SELECT password FROM users WHERE id=:id");
        $Current_PassStmt->execute([":id"=>$_SESSION["id"]]);
        $Current_Pass= $Current_PassStmt->fetch()["password"];
        $cur_pass = $_POST["cur-pass"];
        $new_pass = $_POST["new-pass"];
        $hashNewPass = password_hash($new_pass,PASSWORD_DEFAULT);
        $conf_pass = $_POST["conf"];
        if(password_verify($cur_pass,$Current_Pass)){
            if($new_pass==$conf_pass){
                $stmt=$con->prepare("UPDATE users SET password='$hashNewPass' WHERE id=:id");
                $stmt->execute([":id"=>$_SESSION["id"]]);
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
                $mail->Subject = 'Security Alert';
                $mail->Body    = "Your <b>Password</b> has been changed AT : ". date("Y-m-d H:i:s")."<p>Isn't You?</p>";
                $mail->send();
                setcookie("updated","Y",time()+ 2);
            }else{
                setcookie("notmatch","Y",time()+ 2);
            }
        }else{
            setcookie("wrong","Y",time()+ 2);
        }
        if($status=="user"){
            header("location: settings.php");
            exit();
        }elseif($status== "trader"){
            header("location: bus_settings.php");
            exit();
        }
    }
}

if(isset($_POST["verify"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $email= $_GET["email"];
        $code = $_POST["v-code"];
        $new_code = rand(99999,999999);
        $stmt= $con->prepare("SELECT verification_code FROM users WHERE email=:email ");
        $stmt->execute([":email"=>$email]);
        $v_code=$stmt->fetch()["verification_code"];
        if($v_code==$code){
            $con->prepare("UPDATE users SET verification_status=1 WHERE email=:email")->execute([":email"=>$email]);
            $con->prepare("UPDATE users SET verification_code=:new_code WHERE email=:email ")->execute([":new_code"=>$new_code,":email"=>$email]);
            setcookie("verified","Y",time()+ 2);
            header("location:sign.php");
            exit();
        }else{
            setcookie("notmatch","Y",time()+ 2);
            header("location: verification.php?email=$email&t=$_SESSION[csrf_token]");
            exit();
        }
    }
}

if(isset($_POST["p-verify"])){
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $email= $_GET["email"];
        $id= $_GET["id"];
        $code = $_POST["v-code"];
        $new_code = rand(99999,999999);
        $stmt= $con->prepare("SELECT verification_code,id FROM users WHERE email=:email ");
        $stmt->execute([":email"=>$email]);
        $data=$stmt->fetch();
        $v_code = $data["verification_code"];
        $cur_id = $data["id"];
        if($cur_id==$id){
            if($v_code==$code){
                $con->prepare("UPDATE users SET verification_code=:new_code WHERE email=:email ")->execute([":new_code"=>$new_code,":email"=>$email]);
                setcookie("email","$email",time()+ 600);
                header("location:reset.php?email=$email&id=$id&t=$_SESSION[csrf_token]");
                exit();
            }else{
                setcookie("notmatch","Y",time()+ 2);
                header("location: pass_verification.php?email=$email&id=$id&t=$_SESSION[csrf_token]");
                exit();
            }
        }else{
            echo "Something Went Wrong";
        }
    }
}

?>