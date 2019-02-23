<?php 
print "0";

if( isset($_POST["email"]) 
&& isset($_POST["password"]) 
&& strlen($_POST["password"]) >= 8 
&& filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    print "1";
    
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    try{
        require_once './database.php';
        print "2";
        
        $query = $connection->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
        
        $query->bindValue(':email', $email);
        $query->bindValue(':password', $password);
        $query->execute();
        print "3";
        $users = $query->fetchAll();
        
        $log = $connection->prepare('INSERT INTO login_attempts(user_email, timestamp, success) VALUES(:email, DATETIME("now"), :success)');
        
        $log->bindValue(':email', $email);
        echo "Count of users is: ".count($users);
        $log->bindValue(':success', count($users));
        $log->execute();
        
        if (count($users) == 0) {
            $attempts = $connection->prepare("SELECT * FROM login_attempts WHERE user_email=:email AND timestamp > date('now', '-1 hours') ORDER BY timestamp DESC LIMIT 2");
            $attempts->bindValue(':email', $email);
            $attempts->execute();
            var_dump($attempts->fetchAll());
            if user has failed twice in a row and this time, then log -1 as quarantine 
            exit;
        }
        
        
        //     SELECT * FROM login_attempts WHERE user_email='cosmin@gmail.com' AND timestamp > date('now', '-1 hours') ORDER BY timestamp DESC LIMIT 3;
        //     switch ($variable) {
        //         case 'value':
        //             # code...
        //             break;
                
        //         default:
        //             # code...
        //             break;
        //     }
        //     CASE trynumber === 1 
        //         return trynumber + 1
        //     case tynumber === 2
        //         return trynumber + 1
        //     case trynumber >= 3
        //         return 0
        // ;

        } catch( PDOException $e) {
              echo '{"status":"err","message":"cannot connect to database"}';
              exit();  
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="login.php" >
        <input type="text" placeholder="Email" name="email">
        <input type="password" placeholder="Password" name="password">
        <input type="submit" value="Submit">
    </form>
</body>
</html>
