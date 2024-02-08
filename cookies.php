<?php 
if(!isset($_SESSION["user"])){
    header("Location: sign.php");
    exit();
}else{  

    if(isset($_COOKIE["hd-status"])){
        echo "<style>
                .profile .prof-info p#status{
                    display: none;
                }
            </style>";
    }

    if(isset($_COOKIE["hd-cart"])){
        echo "<style>
                .profile .pro.res-products{
                    display: none;
                }
            </style>";
    }

    if(isset($_COOKIE["hd-product"])){
        echo "<style>
                .profile .pro.res-products{
                    display: none;
                }
            </style>";
    }

    if(isset($_COOKIE["dark-mood"])){
        echo "<style>
                :root {
                    --bg-color: #232f3e;
                }
                body{
                    background-color: black;
                    color: white;
                }
                h1::after{
                    background-color: #2a2727;
                }
                .products .card {
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .res-products table {
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .main {
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .confirm {
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .orders table{
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .payment .info{
                    box-shadow: 10px 10px 10px #2f3235;
                }
                .search-box {
                    background-color: #232f3e;
                }
                .search-box input {
                    background-color: #e1e1e1;
                }
            </style>";
    }
}
?>