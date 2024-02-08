<?php 
session_start();
include("config.php");

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}

if(isset($_GET["email"])){
    global $email;
    $email = $_GET["email"];
    global $id;
    $id = $_GET["id"];
}

$ferror = "";
$error = "";
if(isset($_COOKIE["notmatch"])){
    $error = "Password Doesn't Match";
}

if (isset($_POST["login"])) {
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $pass = $_POST["pass"];
        $stmt = $con->prepare("SELECT id FROM users WHERE email=:email ");
        $stmt->execute([":email"=>$email]);
        $cur_id=$stmt->fetch()["id"];
        $conf = $_POST["conf-pass"];
        $hashpass = password_hash($pass,PASSWORD_DEFAULT);
        if ($cur_id == $id){
            if(isset($_COOKIE["email"])){
                if ($_COOKIE["email"] == $email){
                    if($pass==$conf){
                        $con->prepare("UPDATE users SET password=:hashpass WHERE id=:id")->execute([":hashpass"=>$hashpass,":id"=>$id]);
                        setcookie("updated","Y",time()+ 2);
                        header("location:sign.php");
                        exit();
                    }else{
                        setcookie("notmatch","Y",time()+ 2);
                        header("location:reset.php?email=$email&id=$id&t=$_SESSION[csrf_token]");
                        exit();
                    }
                }else{
                    echo "Something Went Wrong";
                    exit();
                }
            }else{
                echo "Something Went Wrong";
                exit();
            }
        }else{
            echo "Something Went Wrong";
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
        <form id="register" action="reset.php?email=<?php if(isset($email)){echo $email;}else echo "";?>&id=<?php if(isset($id)){echo $id;}else echo "";?>&t=<?php echo $_SESSION['csrf_token']?>" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <h1>Reset Password</h1>
            <h2 style="color:red;font-size:11px;"><?php echo $ferror ?></h2>
            <input type="hidden" id="email" name="email" value="<?php if(isset($email)){echo $email;}else echo ""; ?>">
            <label for="pass">Password :</label>
            <br>
            <input type="password" id="pass" name="pass" required>
            <i id="icon" onclick="showpass()" class="fa-sharp fa-solid fa-eye-slash"></i>
            <br>
            <label for="conf-pass">Confirm Password :</label>
            <br>
            <input type="password" id="conf-pass" name="conf-pass" required>
            <i id="icon-conf" onclick="showconf()" class="fa-sharp fa-solid fa-eye-slash"></i>
            <span class="error"><?php echo $error ?></span>
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
    <script src="main.js"></script>
</body> 
</html>