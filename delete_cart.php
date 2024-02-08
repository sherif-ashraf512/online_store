<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: sign.php");
    exit();
}else{  
    include("config.php");
    $id =$_GET["id"];

    $sqls ="DELETE FROM cart WHERE id=:id";
    $con->prepare($sqls)->execute([":id"=>$id]);
    if($_GET["name"]==2){
        header("location: profile.php");
        exit();
    }
    if($_GET["name"]==1){
        header("location: cart.php");
        exit();
    }
}
?>