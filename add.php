<?php
    session_start();
    include("config.php");

    $token_lifetime = 1800;
    if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['token_time'] = time();
    }
    
    if(!isset($_SESSION["user"])){
        header("Location: sign.php");
        exit();
    }else{ 
        $stmt = $con->prepare("SELECT count(seller_seen) FROM orders WHERE seller_id=:id AND seller_seen=0");
        $stmt->execute([":id"=>$_SESSION["id"]]);
        $not_count=$stmt->fetch()['count(seller_seen)'];
        $error ="";
        if(isset($_COOKIE["notValid"])){
            $error="Not Valid Accept Images That Have An Extension(jpg, jpge, png) Only";
        }

        if(isset($_COOKIE["added"])){
            echo "<script>alert('Product Added Successfully')</script>";
        }elseif(isset($_COOKIE["failed"])){
            echo "<script>alert('Failed To Add')</script>";
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
    <title>online store | add product</title>
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
            <a href="products.php">
                <i class="fa-sharp fa-solid fa-cart-shopping"></i>
                <span>My Products</span>
            </a>
            <a class="active big" href="add.php">
                <i class="fa-solid fa-square-plus"></i>
                <span>Add Product</span>
            </a>
            <a href="bus_orders.php">
                <i class="fa-brands fa-first-order-alt"></i><span id="noti"><?php echo $not_count?></span>
                <span>Orders</span>
            </a>
            <a href="bus_profile.php">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="bus_settings.php">
                <i class="fa-sharp fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
            <a onclick="<?php $_SESSION["logout"]="Y"?>" href="sign.php">
                <i class="fa-sharp fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
        <a title="<?php echo $_SESSION["user"]?>" href="bus_profile.php"><img class="user" src="<?php echo $uimage['image']?>" alt=""></a>
    </div>
    <div class="main">
        <form action="insert.php" method="post" enctype="multipart/form-data">
            <h2>Online Store</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <img src="images/img/logo.jpg" alt="logo">
            <label for="name">Product Name :</label>
            <input type="text" name="name" id="name" required placeholder="Product Name">
            <br>
            <label for="price">Product Price :</label>
            <input type="text" name="price" id="price" required placeholder="Product Price">
            <div>
                <input type="radio" name="coin" id="egp" value="E£" checked>
                <label for="egp">EGP</label>
                <input type="radio" name="coin" id="dolar" value="$">
                <label for="dolar">USD</label>
            </div>
            <label for="description">Description  :</label>
            <input type="text" name="description" id="description" required placeholder="Type Description For The Product">
            <br>
            <input type="file" name="image" accept=".jpg, .jpeg, .png" id="image">
            <label for="image" id="upload">Upload image</label>
            <button type="submit" name="add">Add product</button>
            <span class="error"><?php echo $error ?></span>
            <br>
        </form>
    </div>
</body>
</html>
<?php }?>