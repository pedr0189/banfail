<?php
require_once('../database/database.php');
require_once('./functions.php'); //contains logic for typecheck (ip or user), getstatus, yellow and red cards
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
        $resultarray = typecheck('user');
        $table = $resultarray[0];
        $column = $resultarray[1];
        $status = getstatus($email);
        //echo "Status is $status";
        if ($status != '3') {
            // $querystring = "SELECT * FROM users WHERE email = $email AND password $password";
            // $query = $connection->prepare($querystring);
            $query = $connection->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
            $query->bindValue(':email', $email);
            $query->bindValue(':password', $password);
            $query->execute();
            $results = $query->fetchAll();
            if (count($results)) {
                $querystring = "UPDATE user_login_attempts SET status = 0 WHERE email = \"$email\"";
                //echo $querystring;
                $query = $connection->prepare($querystring);
                // $query->bindValue(':email', $email);
                // $query->bindValue(':password', $password);
                $query->execute();
                echo '{"status":"1","message":"Successfuly logged in"}';
            }else{
                typecheck('user');
                yellowcard($status, $email);
                echo '{"status":"0","message":"failed '.($status+1).' times... And yellow for you"}';
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