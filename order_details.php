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

        if(isset($_GET["id"])){
            $order_id=$_GET["id"];
            $stmt = $con->prepare("SELECT * FROM orders WHERE id=:id");
            $stmt->execute([":id"=>$order_id]);
            $data = $stmt->fetch();
            $stmt1=$con->prepare("SELECT description FROM prod WHERE id=:prod_id");
            $stmt1->execute([":prod_id"=>$data["prod_id"]]);
            $description = $stmt1->fetch()["description"];
            switch($_SESSION["status"]){
                case("trader"):
                    $buyer_nameStmt=$con->prepare("SELECT name FROM users WHERE id= :buyer_id");
                    $buyer_nameStmt->execute([":buyer_id"=>$data["buyer_id"]]);
                    $buyer_name = $buyer_nameStmt->fetch()["name"];
                    $buyer_emailStmt=$con->prepare("SELECT email FROM users WHERE id= :buyer_id");
                    $buyer_emailStmt->execute([":buyer_id"=>$data["buyer_id"]]);
                    $buyer_email = $buyer_emailStmt->fetch()["email"];
                    $buyer_imageStmt=$con->prepare("SELECT image FROM users WHERE id= :buyer_id");
                    $buyer_imageStmt->execute([":buyer_id"=>$data['buyer_id']]);
                    $buyer_image = $buyer_imageStmt->fetch()["image"];
                    $back="bus_orders.php";
                    echo"
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css'integrity='sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==' crossorigin='anonymous' referrerpolicy='no-referrer' />
                            <link rel='preconnect' href='https://fonts.googleapis.com'>
                            <link href='https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap' rel='stylesheet'>
                            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin=''>
                            <link rel='stylesheet' href='master.css'>
                            <title>Details</title>
                        </head>
                        ";
                        include("cookies.php");
                    echo "
                    <div class='payment'>
                        <div class='info'>
                            <img src='$data[image]' alt='' width='100%'>
                            <p id='description'>$description</p>
                            <p>Order Id: $data[id]</p>
                            <p>Product Name: $data[name]</p>
                            <p>Quantity: $data[quantity]</p>
                            <p>Price: $data[coin]$data[price]</p>
                            <p>Buyer Name: <span id='name'>$buyer_name<img id='user' src='$buyer_image' alt=''></span></p>
                            <p>Buyer Email: $buyer_email</p>
                            <p>Orderd At: $data[date]</p>
                            <hr>
                            <p>Contact Buyer From <a href='mailto:$buyer_email'>Here</a></p>
                            <div class='back'>
                                <a href=$back>Back</a>
                            </div>
                        </div>
                    </div>
                    ";
                break;
                case("user"):
                    $seller_nameStmt=$con->prepare("SELECT name FROM users WHERE id= :seller_id");
                    $seller_nameStmt->execute([":seller_id"=>$data["seller_id"]]);
                    $seller_name = $seller_nameStmt->fetch()["name"];
                    $seller_emailStmt=$con->prepare("SELECT email FROM users WHERE id= :seller_id");
                    $seller_emailStmt->execute([":seller_id"=>$data["seller_id"]]);
                    $seller_email = $seller_emailStmt->fetch()["email"];
                    $seller_imageStmt=$con->prepare("SELECT image FROM users WHERE id= :seller_id");
                    $seller_imageStmt->execute([":seller_id"=>$data["seller_id"]]);
                    $seller_image = $seller_imageStmt->fetch()["image"];
                    $back="orders.php";
                    echo"
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css'integrity='sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==' crossorigin='anonymous' referrerpolicy='no-referrer' />
                            <link rel='preconnect' href='https://fonts.googleapis.com'>
                            <link href='https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;display=swap' rel='stylesheet'>
                            <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin=''>
                            <link rel='stylesheet' href='master.css'>
                            <title>Details</title>
                        </head>
                        ";
                        include("cookies.php");
                    echo "
                    <div class='payment'>
                        <div class='info'>
                            <img src='$data[image]' alt='' width='100%'>
                            <p id='description'>$description</p>
                            <p>Order Id: $data[id]</p>
                            <p>Product Name: $data[name]</p>
                            <p>Quantity: $data[quantity]</p>
                            <p>Price: $data[coin]$data[price]</p>
                            <p>Buyer Name: <span id='name'>$seller_name<img id='user' src='$seller_image' alt=''></span></p>
                            <p>Buyer Email: $seller_email</p>
                            <p>Orderd At: $data[date]</p>
                            <hr>
                            <p>Contact Seller From <a href='mailto:$seller_email'>Here</a></p>
                            <div class='back'>
                                <a href=$back>Back</a>
                            </div>
                        </div>
                    </div>
                    ";
                break;
                default:
                echo "Unknown Value";
            }
        }

    }

?>