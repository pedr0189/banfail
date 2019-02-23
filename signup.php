<?php

if( isset($_POST["email"]) 
&& isset($_POST["password"]) 
&& strlen($_POST["password"]) >= 8 
&& filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)
&& preg_match('/^[a-zA-Z0-9]+$/', $_POST["email"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $options = [
        'cost' => 12 
    ];
    $hash = password_hash($password, PASSWORD_DEFAULT, $options);

    try{
        require_once './database.php';

        // would fail if the email is not unique so no need to check first
        $query = $connection->prepare('INSERT INTO users("email", "password") VALUES(:email, :password)');

        $query->bindValue(':email', $email);
        $query->bindValue(':password', $password);
        $query->execute();



        $success = $query->fetchAll();

    } catch( PDOException $e) {
          echo '{"status":"err","message":"cannot connect to database"}';
          exit();  
    }
}

