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
    $stmt->execute([":id" => $_SESSION["id"]]);
    $not_count = $stmt->fetch()['count(buyer_seen)'];
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
        <title>My cart</title>
    </head>
    <?php
    include("cookies.php");
    if ($not_count == 0) {
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
            $img = $con->prepare("SELECT image FROM users WHERE id=:id");
            $img->execute([":id" => $_SESSION["id"]]);
            $uimage = $img->fetch();
            ?>
            <div class="nav-list">
                <a class="active" href="cart.php">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>My Cart</span>
                </a>
                <a href="shop.php">
                    <i class="fa-solid fa-shop"></i>
                    <span>Shop</span>
                </a>
                <a href="orders.php">
                    <i class="fa-brands fa-first-order-alt"></i><span id="noti"><?php echo $not_count ?></span>
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
                <a onclick="<?php $_SESSION["logout"] = "Y" ?>" href="sign.php">
                    <i class="fa-sharp fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
            <a title="<?php echo $_SESSION["user"] ?>" href="profile.php"><img class="user" src="<?php echo $uimage['image'] ?>" alt=""></a>
        </div>
        <h1 class="big">All Reserved Products</h1>
        <div class="cart">
            <div class='res-products'>
                <?php

                $res = $con->prepare("SELECT * FROM cart WHERE user_id=:id ORDER BY id DESC");
                $res->execute([":id" => $_SESSION["id"]]);
                while ($row = $res->fetch()) {
                    $image = $con->prepare("SELECT image FROM prod where id=:id");
                    $image->execute([":id" => $row["prod_id"]]);
                    $img = $image->fetch();
                    echo "<table onclick=window.location.assign('http://localhost/online_store/pay.php?id=$row[id]')>
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
                        <td onclick='return conf()'><a href='delete_cart.php?id=$row[id]&name=1'  class='del'>Delete</a></td>
                    </tr>
                    <tr>
                        <td><button class='pay' name='pay' type='submit'><a href='pay.php?id=$row[id]'>Pay Only</a></button></td>
                    </tr>
                </table>";
                }
                $stmt2 = $con->prepare("SELECT price FROM cart WHERE user_id=:id");
                $stmt2->execute([":id" => $_SESSION["id"]]);
                $prices = $stmt2->fetchAll();
                $total = 0;
                $arr = [];
                foreach ($prices as $cart) {
                    $arr[] = $cart['price'];
                }
                foreach ($arr as $k => $price) {
                    $total += $price;
                }
                if ($total == 0) {
                    echo "
                    <style>
                        div.pay-all{
                            display:none;
                        }
                    </style>
                ";
                }
                ?>
                <hr>
                <div class="pay-all">
                    <span>Pay All</span>
                    <div id='paypal-payment-button'></div>
                </div>
            </div>
        </div>
        <script src="main.js"></script>
        <script src="https://www.paypal.com/sdk/js?client-id=Aalvt9XbzpQn8vvZmJp1zQesSfyYkme7Ttk7UCv-6te1jS_YKH0R1jxFxJjuUh5bQ1Tjs004GDpbEG0G&disable-funding=credit,card"></script>
        <script>
            paypal.Buttons({
                style: {
                    layout: 'vertical',
                    color: 'gold',
                    shape: 'pill',
                    label: 'pay'
                },
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?php echo $total ?>'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', 'add_order.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (this.readyState === 4 && this.status === 200) {
                                window.location.replace("http://localhost/online_store/success_pay.php");
                            }
                        };
                        xhr.send("type=all");
                    })
                },
                onCancel: function(data) {
                    window.location.replace("http://localhost/online_store/oncancel_pay.php");
                }
            }).render('#paypal-payment-button');
        </script>
    </body>

    </html>
<?php } ?>