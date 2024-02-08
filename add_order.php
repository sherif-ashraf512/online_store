<?php
    session_start();
    include("config.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $token_lifetime = 1800;
    if (empty($_SESSION['token_time']) || time() - $_SESSION['token_time'] > $token_lifetime) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['token_time'] = time();
    }
    
    if(!isset($_SESSION["user"])){
        header("Location: sign.php");
        exit();
    }else{


        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        function send_email($recipient, $subject, $message){
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'fakework12345@gmail.com';
            $mail->Password   = 'dkuceaajxparecdh';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;
            $mail->setFrom('fakework12345@gmail.com', 'Online Store');
            $mail->addAddress($recipient);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
        }

        date_default_timezone_set('Africa/Cairo');
        if(isset($_POST["type"])){
            switch($_POST["type"]){
                case "all":
                    $stmt = $con->prepare("SELECT * FROM cart WHERE user_id=:id");
                    $stmt->execute([":id"=>$_SESSION["id"]]);
                    $data = $stmt->fetchALL();
                    
                    foreach($data as $arrs){
                            $arr1[] = $arrs["prod_id"];
                            $arr2[] = $arrs["quantity"];
                            $arr3[] = $arrs["price"];
                    }
                    foreach($arr1 as $id){
                        $stmt1=$con->prepare("SELECT * FROM prod WHERE id=:id");
                        $stmt1->execute([":id"=>$id]);

                        $prods = $stmt1->fetchALL();
                        foreach($prods as $prod){
                            $arr_prod[]=$prod;
                        }
                    }
                    
                    for($i=0; $i<count($data); $i++){
                        $users_id[]=$arr_prod[$i]['user_id'];
                        $prods_id[]=$arr_prod[$i]['id'];
                        $prods_name[]=$arr_prod[$i]['name'];
                        $prods_coin[]=$arr_prod[$i]['coin'];
                        $prods_image[]=$arr_prod[$i]['image'];
                    }
                    
                    for($i=0; $i<count($data); $i++){
                        $da = date("Y-m-d H:i:s");
                        $stmt2 = $con->prepare("INSERT INTO orders (buyer_id, seller_id, prod_id, name, quantity, price, coin, image, seller_seen, buyer_seen, date) VALUES (:buyer_id, :seller_id, :prod_id, :name, :quantity, :price, :coin, :image, 0, 0, :date)");
                        $stmt2->execute([
                            ":buyer_id" => $_SESSION["id"],
                            ":seller_id" => $users_id[$i],
                            ":prod_id" => $prods_id[$i],
                            ":name" => $prods_name[$i],
                            ":quantity" => $arr2[$i],
                            ":price" => $arr3[$i],
                            ":coin" => $prods_coin[$i],
                            ":image" => $prods_image[$i],
                            ":date" => $da
                        ]);
                    }
                    $stmt3 = $con->prepare("DELETE FROM cart WHERE user_id = :user_id");
                    $stmt3->execute([":user_id" => $_SESSION["id"]]);
                    
                    for($i=0;$i<count($data);$i++){
                        $seller_emailStmt = $con->prepare("SELECT email FROM users WHERE id = :user_id");
                        $seller_emailStmt->execute([":user_id" => $users_id[$i]]);
                        $seller_emails[] = $seller_emailStmt->fetch()["email"];
                    }
                    for($i=0;$i<count($data);$i++){
                        send_email($seller_emails[$i],"New Order","You have a new order At : ". date("Y-m-d H:i:s")."<p>Check your orders page</p>");
                    }
                    
                    $buyer_emailStmt = $con->prepare("SELECT email FROM users WHERE id = :buyer_id");
                    $buyer_emailStmt->execute([":buyer_id" => $_SESSION["id"]]);
                    $buyer_email = $buyer_emailStmt->fetch()["email"];

                    send_email($buyer_email,"New Order","You have been orderd a new order At : ". date("Y-m-d H:i:s")."<p>Check your orders page</p>");
                break;
                case "one":
                    if(isset($_POST["id"])){
                        $cart_id = $_POST["id"];

                        $stmt = $con->prepare("SELECT * FROM cart WHERE id = :cart_id");
                        $stmt->execute([":cart_id" => $cart_id]);
                        $row = $stmt->fetch();

                        $stmt1 = $con->prepare("SELECT * FROM prod WHERE id = :prod_id");
                        $stmt1->execute([":prod_id" => $row["prod_id"]]);
                        $data = $stmt1->fetch();
                        $da=date("Y-m-d H:i:s");

                        $stmt2 = $con->prepare("INSERT INTO orders (buyer_id, seller_id, prod_id, name, quantity, price, coin, image, seller_seen, buyer_seen, date) VALUES (:buyer_id, :seller_id, :prod_id, :name, :quantity, :price, :coin, :image, 0, 0, :date)");
                        $stmt2->execute([
                            ":buyer_id" => $_SESSION["id"],
                            ":seller_id" => $data["user_id"],
                            ":prod_id" => $data["id"],
                            ":name" => $data["name"],
                            ":quantity" => $row["quantity"],
                            ":price" => $row["price"],
                            ":coin" => $data["coin"],
                            ":image" => $data["image"],
                            ":date" => $da
                        ]);

                        $stmt3 = $con->prepare("DELETE FROM cart WHERE id = :cart_id");
                        $stmt3->execute([":cart_id" => $cart_id]);

                        $buyer_emailStmt = $con->prepare("SELECT email FROM users WHERE id = :buyer_id");
                        $buyer_emailStmt->execute([":buyer_id" => $_SESSION["id"]]);
                        $buyer_email = $buyer_emailStmt->fetch()["email"];

                        $seller_emailStmt = $con->prepare("SELECT email FROM users WHERE id = :seller_id");
                        $seller_emailStmt->execute([":seller_id" => $data["user_id"]]);
                        $seller_email = $seller_emailStmt->fetch()["email"];

                        send_email($buyer_email,"New Order","You have been orderd a new order At : ". date("Y-m-d H:i:s")."<p>Check your orders page</p>");

                        send_email($seller_email,"New Order","You have a new order At : ". date("Y-m-d H:i:s")."<p>Check your orders page</p>");
                    }
                break;
                default:
                echo "Unknown Value";
            }
        }

    }

?>