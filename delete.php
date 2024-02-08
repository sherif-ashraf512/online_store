<?php
session_start();
if(!isset($_SESSION["user"])){
    header("Location: sign.php");
    exit();
}else{  
    include("config.php");

    $id = $_GET["id"];

    $stmt = $con->prepare("SELECT image FROM prod WHERE id=:id");

    $stmt->execute([":id"=>$id]);

    $image = $stmt->fetch();

    $delete = $con->prepare("DELETE FROM prod WHERE id=:id")->execute([":id"=>$id]);

    unlink("$image[image]");

    header("Location: products.php");
    exit();
}
?>