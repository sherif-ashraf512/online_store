<?php
    session_start();

    $token_lifetime = 1800;
    if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['token_time'] = time();
    }
    
    if(!isset($_SESSION["user"])){
        header("Location: sign.php");
        exit();
    }else{  
?>
<?php

include("config.php");

$id = $_GET["id"];
$sqls = $con->prepare("SELECT * FROM prod WHERE id=:id");
$sqls->execute([":id"=>$id]);
$data = $sqls->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link rel="stylesheet" href="master.css">
    <title>confirm purchase</title>
</head>
<?php include("cookies.php");?>
<body>
    <div class="confirm">
        <form action="insert.php" method="post">
            <h2>Product purchase Confirmation</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <h3><?php echo $data['name']?></h3>
            <label style="font-weight:bold;" for="number">Quantity :</label >
            <input type="number" name="number" id="number" value="1" step="1" min="1" max="100">
            <input type="hidden" name="id" value="<?php echo $data['id']?>" readonly>
            <input type="hidden" name="name" value="<?php echo $data['name']?>" readonly>
            <input type="hidden" name="price" value="<?php echo $data['price']?>" readonly>
            <input type="hidden" name="coin" value="<?php echo $data['coin']?>" readonly>
            <br>
            <button type="submit" name="add-cart">Confirm Adding Product</button>
            <br>
            <a href="shop.php">Go Back</a>
        </form>
    </div>
</body>
</html>
<?php }?>