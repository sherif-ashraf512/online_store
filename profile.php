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
    <title>Profile</title>
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
            <a href="orders.php">
                <i class="fa-brands fa-first-order-alt"></i><span id="noti"><?php echo $not_count?></span>
                <span>Orders</span>
            </a>
            <a class="active sml" href="profile.php">
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
    <h1>Profile</h1>
    <div class='profile'>
        <div class="prof-info">
            <img src="<?php echo $row["image"] ?>" alt="">
            <div class="info">
                <h3><?php echo $row["name"]?></h3>
                <p id="status">Status : <b><?php echo $row["status"]?></b></p>
                <p>Email : <b><?php echo $row["email"]?></b></p>
            </div>
            <div class=' pro res-products'>
                <h3>Your Reserved Products</h3>
                <?php

                $res = $con->prepare("SELECT * FROM cart WHERE user_id=:id");
                $res->execute([":id"=>$_SESSION["id"]]);
                while($row=$res->fetch()){
                    $image = $con->prepare("SELECT image FROM prod WHERE id=:id");
                    $image->execute([":id"=>$row["prod_id"]]);
                    $img=$image->fetch();
                        echo "<table>
                        <thead>
                            <th>Image</th>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>N</th>
                            <th>Total Price</th>
                            <th>Delete</th>
                        </thead>
                        <tr>
                            <td><img src='$img[image]' alt='' style='width:100px;'></td>
                            <td>$row[prod_id]</td>
                            <td>$row[name]</td>
                            <td>$row[quantity]</td>
                            <td>$row[coin]$row[price]</td>
                            <td><a onclick='return conf()' href='delete_cart.php?id=$row[id]&name=2'  class='del'><i class='fa-solid fa-trash'></i></a></td>
                        </tr>
                </table>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>
<?php }?>