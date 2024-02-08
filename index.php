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
        <title>online store</title>
    </head>
    <body>
    <div class="nav">
        <div class="nav-list">
            <span>Online Store</span>
            <div class="wel">
                <a href="sign.php">Sign In | </a>
                <a href="register.php">Sign Up</a>
            </div>
        </div>
    </div>
    <h1 class="big write">Welcome To Our Store</h1>
    <div class='welcome'>
        <h2></h2> <!-- fill "You can Buy Or Sell Anything From Our Store" By JS -->
        <div>
            <div class="img">
                <a href="register.php">
                    <img src="images/img/buyer.jpg" alt="logo">
                </a>
            </div>
            <div class="p" onclick="window.location.assign('http://localhost/online_store/register.php')">
                <p>IF You Want To Buy Anything From Our Store You Can Create Account From <a href="register.php">Here</a></p>
            </div>
        </div>
        <div>
            <div class="img">
                <a href="bus_register.php">
                    <img src="images/img/seller.png" alt="logo">
                </a>
            </div>
            <div class="p" onclick="window.location.assign('http://localhost/online_store/bus_register.php')">
                <p>IF You Want To Sell Anything From Our Store You Can Create A Business Account From <a href="bus_register.php">Here</a></p>
            </div>
        </div>
        <div>
            <div class="img">
                <a href="sign.php"> 
                    <img src="images/img/sign.jpg" alt="logo">
                </a>
            </div>
            <div class="p" onclick="window.location.assign('http://localhost/online_store/sign.php')">
                <p>IF You Already Have An Account You Can Sign In From <a href="sign.php">Here</a></p>
            </div>
        </div>
        <div>
            <div class="img">
                <a href="mailto:sherifashraf51203@gmail.com" target="_blank">
                    <img src="images/img/support.webp" alt="logo">
                </a>
            </div>
            <div class="p" onclick="window.location.assign('mailto:sherifashraf51203@gmail.com')">
                <p>IF You Have Any Problem Or Question Contact Support From <a href="mailto:sherifashraf51203@gmail.com" target="_blank">Here</a></p>
            </div>
        </div>
        <div class="imag">
            <img src="images/img/logo.jpg" alt="logo">
        </div>
    </div>
    <?php include("footer.php"); ?>
    <script src="main.js"></script>
    </body>

    </html>