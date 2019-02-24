<?php
try {
    $connection = new PDO('sqlite:userpool.db');
} catch(PDOException $e) {
    //echo $e->getMessage();
    echo '{"status":"0","message":"cannot connect to database"}';
}
//these functions are called when needed by other php scripts
//Possible types:
// ip
// user
//Possible Status:
// 0: No issues
// 1: One failed attempt
// 2: Two failed attempts
// 3: Three failed attemps- Locked for 5 minutes
