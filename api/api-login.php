<?php
require_once('../database/database.php'); //contains logic for typecheck (ip or user), getstatus, yellow and red cards
$clientipaddress = $_SERVER['REMOTE_ADDR'];
//
// insert logic to stop blocked ip address here
//

if( isset($_POST["email"]) //check if post is sent correctly containing form data, otherwise it's an attack and needs redcard
&& isset($_POST["password"]) 
&& strlen($_POST["password"]) >= 8 
&& filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    try {
        typecheck('user');
        $status = getstatus($email);
        if ($status != '3') {
            $query = $connection->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
            $query->bindValue(':email', $email);
            $query->bindValue(':password', $password);
            $query->execute();
            $results = $query->fetchAll();
            if (count($results)) {
                echo '{"status":"1","message":"Successfuly logged in"}';
            }else{
                typecheck('user');
                yellowcard($email);
                echo '{"status":"0","message":"failed... And yellow for you"}';

            }
        }else{
            echo '{"status":"blocked","message":"You are blocked... Try again later"}';
        }
        

        
    } catch( PDOException $e) {
        echo '{"status":"err","message":"cannot connect to database"}';
        exit();
    }
}else{
    echo '{"status":"err","message":"frontend validation bypass detected"}';
    typecheck('ip');
    redcard($clientipaddress);
}


?>