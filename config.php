<?php
    $dsn = "mysql:host=localhost;dbname=online_Store";
    $user = "root";
    $pass = "";

    try {
        $con = new PDO($dsn, $user, $pass);
        $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
        echo "faild " . $e->getmessage();
    }
?>