<?php
session_start();
include("config.php");

$token_lifetime = 1800;
if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}

if (!isset($_SESSION["user"])) {
    header("Location: sign.php");
    exit();
} else {
    $stmt = $con->prepare("SELECT count(buyer_seen) FROM orders WHERE buyer_id=:id AND buyer_seen=0");
    $stmt->execute([":id"=>$_SESSION["id"]]);
    $not_count=$stmt->fetch()['count(buyer_seen)'];
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
        <title>shop</title>
    </head>
    <?php 
        include("cookies.php");
        if($not_count==0){
            echo "<style>
            .nav .nav-list a #noti{
                display:none;
            }
            </style>";
        }
    ?>
    <body>
    <div class="nav">
        <?php
            $img=$con->prepare("SELECT image FROM users WHERE id=:id");
            $img->execute([":id"=>$_SESSION["id"]]);
            $uimage = $img->fetch();
        ?>
        <div class="nav-list">
            <a href="cart.php">
                <i class="fa-solid fa-cart-plus"></i>
                <span>My Cart</span>
            </a>
            <a class="active sml" href="shop.php">
                <i class="fa-solid fa-shop"></i>
                <span>Shop</span>
            </a>
            <a href="orders.php">
                <i class="fa-brands fa-first-order-alt"></i><span id="noti"><?php echo $not_count?></span>
                <span>Orders</span>
            </a>
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="settings.php">
                <i class="fa-sharp fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
            <a onclick="<?php $_SESSION["logout"]="Y"?>" href="sign.php">
                <i class="fa-sharp fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
        <a title="<?php echo $_SESSION["user"]?>" href="profile.php"><img class="user" src="<?php echo $uimage['image']?>" alt=""></a>
    </div>
        <h1 class="big">All Available Products</h1>
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass fa-beat-fade"></i>
            <input onkeyup="search()" class="search-input" type="text" placeholder="search">
        </div>
        <div class='products'>
            <?php

            include("config.php");

            $res = $con->prepare("SELECT * FROM prod");
            $res->execute();
            while ($row = $res->fetch()) {
                echo "<div class='card'>";
                echo "<img src='$row[image]' alt=''>";
                echo "<div class='card-body' style='text-align:center;'>";
                echo "<h4>$row[name]</h4>";
                echo "<p id='price'>$row[coin]$row[price]</p>";
                echo"<p id='description'>$row[description]</p>";
                echo "<a href='val.php?id=$row[id]' class='add' style='width:90%; background-color:limegreen;'>Add To Cart</a>";
                echo "</div>";
                echo "</div>";
            }

            ?>
            <script src="main.js"></script>
    </body>

    </html>
<?php } ?>