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
        include("config.php");
        $res = $con->prepare("SELECT status,email,name,image FROM users WHERE email =:email ");
        $res->execute([":email"=>$_SESSION["email"]]);
        $row=$res->fetch();

        $con->prepare("UPDATE orders SET buyer_seen=1 where buyer_id=:id")->execute([":id"=>$_SESSION["id"]]);
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
    <title>Orders</title>
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
                <a href="shop.php">
                    <i class="fa-solid fa-shop"></i>
                    <span>Shop</span>
                </a>
                <a class="active" href="orders.php">
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
        <h1 class="mid">My Orders</h1>
    <div class="orders">
    <?php
            $res = $con->prepare("SELECT * FROM orders WHERE buyer_id=:id ORDER BY date DESC, id DESC");
            $res->execute([":id"=>$_SESSION["id"]]);
            while($row=$res->fetch()){
                    echo "<table onclick=window.location.assign('http://localhost/online_store/order_details.php?id=$row[id]')>
                    <thead>
                        <th>Image</th>
                        <th>Order Id</th>
                        <th>Product Name</th>
                        <th>N</th>
                        <th>Price</th>
                        <th>View</th>
                    </thead>
                    <tr>
                        <td><img src='$row[image]' alt='' style='width:100px;'></td>
                        <td>$row[id]</td>
                        <td>$row[name]</td>
                        <td>$row[quantity]</td>
                        <td>$row[coin]$row[price]</td>
                        <td><a onclick='order_click()' href='order_details.php?id=$row[id]'>More...</a><td>
                    </tr>
                </table>";
            }
            ?>
        </div>
        <script src="main.js"></script>
</body>
</html>
<?php }?>