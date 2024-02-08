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
        $perror ="";
        if(isset($_COOKIE["notValid"])){
            $error="Not Valid Accept Images That Have An Extension(jpg, jpge, png) Only";
        }

        if(isset($_COOKIE["updated"])){
            echo"<script>alert('Updated Password Successfully')</script>";
        }elseif(isset($_COOKIE["notmatch"])){
            $perror = "Password Doesn't Match";
        }elseif(isset($_COOKIE["wrong"])){
            $perror = "Wrong Password";
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
    <title>settings</title>
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
            $data=$con->prepare("SELECT * FROM users WHERE id=:id");
            $data->execute([":id"=>$_SESSION["id"]]);
            $info = $data->fetch();
        ?>
        <div class="nav-list">
            <a href="products.php">
                <i class="fa-sharp fa-solid fa-cart-shopping"></i>
                <span>My Products</span>
            </a>
            <a href="add.php">
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
            <a class="active" href="bus_settings.php">
                <i class="fa-sharp fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
            <a onclick="<?php $_SESSION["logout"]="Y"?>" href="sign.php">
                <i class="fa-sharp fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
        <a title="<?php echo $_SESSION["user"]?>" href="bus_profile.php"><img class="user" src="<?php echo $info['image']?>" alt=""></a>
    </div>
    <h1>Settings</h1>
    <div class='setting'>
    <div class="img">
            <h3>Profile Picture</h3>
            <img src="<?php echo $info['image']?>" alt="">
            <form id="form" method="post" action="up.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <a onclick="return conf()" href="set_default.php" class="del">
                    <i class="fa-solid fa-trash"></i>
                </a>
                <label for="img" class="change">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <input onchange="submit()" type="file" name="img" accept=".jpg, .jpeg, .png" id="img">
                <span class="error"><?php echo $error ?></span>
            </form>
        </div>
        <div class="pers">
            <h3>Personal Information</h3>
            <div>
                <form method="post" action="up.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <label for="name">Name : </label>
                    <input id="name" type="text" name="name" value="<?php echo $info['name']?>"></input>
                    <input id="submit" type="submit" name="ch-name" value="Update">
                    <br>
                    <label for="email">Email : </label>
                    <input id="email" type="text" readonly value="<?php echo $info['email']?>"></input>
                    <br>
                    <label for="status">Status : </label>
                    <input id="status" type="text" readonly value="<?php echo $info['status']?>"></input>
                    <br>
                </form>
            </div>
        </div>
        <div class="sec">
            <h3>Password And Security</h3>
            <div class="pass">
                <p>Change Your Password</p>
                <span onclick="showform()" class="change">Change</span>
            </div>
            <div class="ch-pass">
                <form id="ch-pass" method="post" action="up.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <label for="cur-pass">Current Password : </label>
                    <input id="cur-pass" type="password" name="cur-pass" placeholder="Type Your Current Password" required></input>
                    <i id="icon" onclick="showcur()" class="fa-sharp fa-solid fa-eye-slash"></i>
                    <br>
                    <label for="new-pass">New Password : </label>
                    <input id="new-pass" type="password" name="new-pass" placeholder="Type Your New Password" required></input>
                    <i id="icon-new" onclick="shownew()" class="fa-sharp fa-solid fa-eye-slash"></i>
                    <br>
                    <label for="conf-pass">Confirm Password : </label>
                    <input id="conf-pass" type="password" name="conf" placeholder="Confirm Your New Password" required></input>
                    <i id="icon-conf" onclick="showconf()" class="fa-sharp fa-solid fa-eye-slash"></i>
                    <br>
                    <span id="p-error" class="error"> <?php echo $perror?></span>
                    <input id="submit" type="submit" name="ch-pass" value="Update">
                    <span onclick="hideform()" id="cancel">Cancel</span>
                </form>
            </div>
        </div>
        <div class="other">
            <h3>Other</h3>
            <div>
                <p>Hide Status From Profile</p>
                <label>
                    <input id="hd-status" class="toggle-checkbox" type="checkbox" <?php if(isset($_COOKIE["hd-status"])){echo "checked";} ?> >
                    <div class="toggle-switch"></div>
                </label> 
            </div>
            <div>
                <p>Hide Product From Profile</p>
                <label>
                    <input id="hd-product" class="toggle-checkbox" type="checkbox" <?php if(isset($_COOKIE["hd-product"])){echo "checked";} ?> >
                    <div class="toggle-switch"></div>
                </label> 
            </div>
        </div>
        <div class="support">
            <h3>Support</h3>
            <p onclick="toggle()">Contact Support If You Have Any Problem</p>
            <ul id="ul-help">
                <li><a href="mailto:sherifashraf51203@gmail.com">Send Us</a></li>
            </ul>
        </div>
        <div class="dark">
            <p><i class="fa-solid fa-moon"></i> Dark Mood</p>
            <label>
                <input id="dark-mood" class="toggle-checkbox" type="checkbox" <?php if(isset($_COOKIE["dark-mood"])){echo "checked";} ?>>
                <div class="toggle-switch"></div>
            </label> 
        </div>
    </div>
    <script src="main.js"></script>
    <?php 
    if(isset($_COOKIE["notmatch"])||isset($_COOKIE["wrong"])){
            echo "<script>
                    document.querySelector('.ch-pass').classList.add('block');
                    document.querySelector('.ch-pass').scrollIntoView({behavior:'smooth'});
                </script>";
    }?>
</body>
</html>
<?php }?>