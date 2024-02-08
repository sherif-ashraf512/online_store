<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: sign.php");
    exit();
}else{  
    include("config.php");
    $status = $_SESSION["status"];
    $image=$con->prepare("SELECT image FROM users WHERE id=:id");
    $image->execute([":id"=>$_SESSION["id"]]);
    $current_image=$image->fetch()["image"];
    if($current_image !="images/img/avatar.jpg"){
        unlink("$current_image");
    }

    $sqls = "UPDATE users SET image=:image WHERE id=:id";
    $con->prepare($sqls)->execute([":image"=>'images/img/avatar.jpg',":id"=>$_SESSION["id"]]);

    if($status=="user"){
        header("location: settings.php");
        exit();
    }elseif($status== "trader"){
        header("location: bus_settings.php");
        exit();
    }
}
?>