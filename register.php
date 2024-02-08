<?php 
session_start();

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}
$error ="";
if(isset($_COOKIE["notmatch"])){
    $error = "\nPassword Doesn't Match";
}elseif(isset($_COOKIE["found"])){
    $error = "Email Already Exists";
}

if(isset($_COOKIE["add"])){
    echo "<script>alert('Created Successfully')</script>";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="master.css">
    <title>register</title>
</head>
<body>
    <h1 class="first">Online Store</h1>
    <div class='register'>
        <form id="register" action="insert.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <h1>Sign Up</h1>
            <label for="name">Name :</label>
            <br>
            <input type="text" id="name" name="name" required placeholder="Type Your First Name And Last Name">
            <br>
            <label for="email">Email :</label>
            <br>
            <input type="email" id="email" name="email" required placeholder="Type A Valid Email">
            <br>
            <label for="pass">Password :</label>
            <br>
            <input type="password" id="pass" name="pass" required>
            <i id="icon" onclick="showpass()" class="fa-sharp fa-solid fa-eye-slash"></i>
            <br>
            <label for="conf-pass">Confirm Password :</label>
            <br>
            <input type="password" id="conf-pass" name="conf-pass" required>
            <i id="icon-conf" onclick="showconf()" class="fa-sharp fa-solid fa-eye-slash"></i>
            <br>
            <button class="log-btn" name="register" type="submit">Create Account</button>
            <span class="error"><?php echo $error ?></span>
        </form>
        <div class="bus">
            <span>Account For Work?</span>
            <a href="bus_register.php">Create A Business Account</a>
        </div>
        <div style="width: 90%;">
            <h5>Already Have Account</h5>
        </div>
        <div class="new">
            <a href="sign.php">Sign In</a>
        </div>
    </div> 
    <?php include("footer.php");
    ?>
    <script src="main.js"></script>
</body>
</html>