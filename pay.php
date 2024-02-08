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
        if(isset($_GET['id'])){
            $id = $_GET["id"];
        }

        $stmt = $con->prepare("SELECT * FROM cart WHERE id=:id");
        $stmt->execute(["id"=>$id]);
        $row = $stmt->fetch();
        $stmt1 = $con->prepare("SELECT image,user_id,description FROM prod where id=:id");
        $stmt1->execute([":id"=>$row["prod_id"]]);
        $data = $stmt1->fetch();
        $stmt2 = $con->prepare("SELECT email FROM users WHERE id=:id");
        $stmt2->execute([":id"=>$data["user_id"]]);
        $trader = $stmt2->fetch()["email"];
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
    <title>payment</title>
</head>
<?php include("cookies.php");?>
<body>
    <h1 class="first">Pay Product</h1>
    <div class="payment">
        <div class='info'>
            <img src="<?php echo $data["image"] ?>" alt="" width="200px">
            <span><?php echo $row["coin"].$row["price"] ?></span>
            <p>Name: <?php echo $row["name"] ?></p>
            <p>Quantity: <?php echo $row["quantity"] ?></p>
            <p id="description"><?php echo $data["description"] ?></p>
            <hr>
            <div id="paypal-payment-button"></div>
            <hr>
            <p>For More Details About The Product Before You Pay Contact Seller From <a href="mailto:<?php echo $trader ?>">Here</a></p>
            <div class="back">
                <a href="cart.php">Back</a>
            </div>
        </div>
    </div>
    <script src="https://www.paypal.com/sdk/js?client-id=Aalvt9XbzpQn8vvZmJp1zQesSfyYkme7Ttk7UCv-6te1jS_YKH0R1jxFxJjuUh5bQ1Tjs004GDpbEG0G&disable-funding=credit,card"></script>

    <script>
        paypal.Buttons({
            style: {
                layout:  'vertical',
                color:   'gold',
                shape:   'pill',
                label:   'pay'
            },
    createOrder: function (data, actions) {
        return actions.order.create({
            purchase_units : [{
                amount: {
                    value: '<?php echo $row["price"]?>'
                }
            }]
        });
    },
    onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST','add_order.php',true);
            xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
            xhr.onreadystatechange= function(){
                if(this.readyState===4 && this.status===200){
                    window.location.replace("http://localhost/online_store/success_pay.php");
                }
            };
            xhr.send("type=one&id=<?php echo $id;?>");
        })
    },
    onCancel: function (data) {
        window.location.replace("http://localhost/online_store/oncancel_pay.php");
    }
}).render('#paypal-payment-button');
    </script>
</body>
</html>
<?php }?>