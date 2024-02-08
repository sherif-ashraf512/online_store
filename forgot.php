<?php
    session_start();

    include("config.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    
    $error = "";
    if(isset($_COOKIE['notfound'])){
        $error ="User Not Found Check Your Email";
    }

    if(isset($_POST["login"])){
        if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
            $error = "Something Went Wrong";
            exit();
        }else{
            global $email;
            $email = $_POST["email"];
            $sqls = $con->prepare("SELECT email,id FROM users  WHERE email = :email");
            $sqls->execute([":email"=>$email]);
            $count = $sqls->rowCount();
            $row = $sqls->fetch();
            $id =$row["id"];
            if($count > 0){
                $stmt = $con->prepare("SELECT verification_code FROM users WHERE email=:email ");
                $stmt->execute([":email"=>$email]);
                $v_code = $stmt->fetch()["verification_code"];
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
                setcookie("email","$email",time()+ 600);
                header("location:pass_verification.php?email=$email&id=$id&t=$_SESSION[csrf_token]");
                exit();
            }else{
                setcookie("notfound","Y",time()+2);
                header("Location: forgot.php");
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="master.css">
    <title>reset password</title>
</head>

<body>
    <h1 class="first">Online Store</h1>
    <div class='log-user'>
        <form action="forgot.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <h1>Reset Password</h1>
            <label for="email">Email :</label>
            <br>
            <input type="email" id="email" name="email" required>
            <br>
            <button name="login" class="log-btn" type="submit">Continue</button>
        </form>
        <div class="new sign">
            <a class="sign" href="sign.php">Sign In</a>
        </div>
        <div style="width: 90%;">
            <h5>New To Our Store?</h5>
        </div>
        <div class="new">
            <a href="register.php">Create Your Account</a>
        </div>
    </div> 
    <?php include("footer.php")?>
</body> 
</html>