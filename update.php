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
        $error ="";
        if(isset($_COOKIE["notValid"])){
            $error="Not Valid Accept Images That Have An Extension(jpg, jpge, png) Only";
        }
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
    <title>online store | update product</title>
</head>
<body>
    <?php
    include("config.php");
    $id = $_GET['id'];
    $up = $con->prepare("SELECT * FROM prod WHERE id=:id");
    $up->execute([":id"=>$id]);
    $data = $up->fetch();
    ?>
    <div class="main">

        <form action="up.php" method="post" enctype="multipart/form-data">
            <h2>Online Store</h2>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <img src=<?php echo $data["image"]?> alt="logo">
            <label for="id">Product Id :</label>
            <input type="text" name="id" id="id" value="<?php echo $data["id"]?>" readonly>
            <br>
            <label for="name">Product Name :</label>
            <input type="text" name="name" id="name" value="<?php echo $data["name"]?>">
            <br>
            <label for="price">Product Price :</label>
            <input type="text" name="price" id="price" value="<?php echo $data["price"]?>">
            <br>
            <label for="description">description :</label>
            <input type="text" name="description" id="description" value="<?php echo $data["description"]?>">
            <br>
            <input type="file" name="image" accept=".jpg, .jpeg, .png" id="image">
            <label for="image" id="upload">Update image</label>
            <button type="submit" name="add">Update product</button>
            <span class="error"><?php echo $error ?></span>
        </form>
    </div>
    <?php include("footer.php")?>
</body>
<?php include("cookies.php");?>
</html>
<?php }?>