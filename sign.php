<?php
session_start();
if (isset($_SESSION["logout"])) {
    session_destroy();
}
global $hashpass;
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}
include("config.php");

if (isset($_SESSION["user"])) {
    if ($_SESSION["status"] == "user") {
        header("Location: shop.php");
        exit();
    } else {
        header("Location: products.php");
        exit();
    }
}
if (isset($_GET["email"])) {
    global $uemail;
    $uemail = $_GET["email"];
}
$error = "";
if (isset($_COOKIE['notfound'])) {
    $error = "Wrong Password Or Email";
    echo $hashpass;
}
if (isset($_COOKIE['verified'])) {
    echo "<script>alert('Verified Successfully')</script>";
}
if (isset($_COOKIE['updated'])) {
    echo "<script>alert('Password Reset Successfully')</script>";
}
if (isset($_COOKIE['notverified'])) {
    $error = "Invalid Email <a href='resend.php?name=u&email=$uemail'>Verify?</a>";
}

if (isset($_POST["login"])) {
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        echo "Something Went Wrong";
        exit();
    }else{ 
        $email = $_POST["email"];
        $pass = $_POST["pass"];
        $sqls = $con->prepare("SELECT id, email, name, password, status, verification_status FROM users  WHERE email = :email LIMIT 1");
        $sqls->execute([":email"=>$email]);
        $row = $sqls->fetch();
        $name = $row["name"];
        $status = $row["status"];
        $v_status = $row["verification_status"];
        $id = $row["id"];
        $stored_pass = $row["password"];
        if (password_verify($pass, $stored_pass)) {
            if ($v_status == 1) {
                $_SESSION["user"] = $name;
                $_SESSION["status"] = $status;
                $_SESSION["email"] = $email;
                $_SESSION["id"] = $id;
            } else {
                setcookie("notverified", "Y", time() + 2);
                header("location: sign.php?email=$email");
                exit();
            }
        } else {
            setcookie("notfound", "Y", time() + 2);
        }
        header("location: sign.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="master.css">
    <title>sign in</title>
</head>

<body>
    <h1 class="first">Online Store</h1>
    <div class='log-user'>
        <form action="sign.php" method="post">
            <h1>Sign In</h1>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="email">Email :</label>
            <br>
            <input type="email" id="email" name="email"
                value="<?php if (isset($_COOKIE['notverified'])) {
                    echo $uemail;
                } ?>" required>
            <br>
            <label for="pass">Password :</label>
            <br>
            <input type="password" id="pass" name="pass" required>
            <i id="icon" onclick="showpass()" class="fa-sharp fa-solid fa-eye-slash"></i>
            <br>
            <span class="error">
                <?php echo $error ?>
            </span>
            <br>
            <div class="help">
                <p onclick="toggle()" id="p-help">Need Help?</p>
                <ul id="ul-help">
                    <li><a href="forgot.php">forgot Your Password?</a></li>
                </ul>
            </div>
            <button name="login" class="log-btn" type="submit">Continue</button>
        </form>
        <div style="width: 90%;">
            <h5>New To Our Store?</h5>
        </div>
        <div class="new">
            <a href="register.php">Create Your Account</a>
        </div>
    </div>
    <?php include("footer.php") ?>
    <script src="main.js"></script>
</body>

</html>