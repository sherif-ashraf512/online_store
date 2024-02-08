<?php 
session_start();

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}

$email = $_GET["email"];
$error="";
if(isset($_COOKIE["notmatch"])){
    $error = "Invalid Code";
    echo "<style>
            .log-user .verify input{
                outline-color: red;
            }
        </style>";
}

if(isset($_COOKIE["resent"])){
    echo "<script>alert('Code Has Been Sent Successfully')</script>";
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
    <title>Verification</title>
</head>

<body>
    <h1 class="first">Online Store</h1>
    <div class='log-user'>
        <form class="verify" action="up.php?email=<?php echo $email?>" method="post">
            <h2>Verification</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <p>Verification Code has been Sent To Your Email</p>
            <input type="text" id="v-code" name="v-code" placeholder="Type Verification Code" required>
            <span><a href="resend.php?name=u&email=<?php echo $email; ?>">Resend?</a></span>
            <br>
            <span class="error"><?php echo $error ?></span>
            <button name="verify" class="log-btn" type="submit">Verify</button>
        </form>
    </div> 
    <?php include("footer.php")?>
    <script src="main.js"></script>
</body> 
</html>