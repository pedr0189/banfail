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

function typecheck($type){ //needs to be called first to set variables, returns array as [$table,$column]
    switch ($type) {
        case 'ip':
        $table = "ip_login_attempts";
        $column = "ip_address";
        break;
        case 'user':
        $table = "user_login_attempts";
        $column = "email";
        break;
    }
    return array($table,$column);
}
function getstatus($target){ //check status, returns null (does not exist) or 0 (free) to 3 (locked)
    $query = $connection->prepare('SELECT status FROM :table WHERE :column = :target');
    $query->bindValue(':table', $table);
    $query->bindValue(':column', $column);
    $query->bindValue(':target', $target);
    $query->execute();    
    $result = $query->fetchAll();
    return $result[0];
}
function redcard($target) //used for imediate lock out (attack detected)
{
    if(checkifexists($table,$column,$target))
    {
        $query = $connection->prepare('UPDATE :table SET status = "3" WHERE :column = :target');
        $query->bindValue(':table', $table);
        $query->bindValue(':column', $column);
        $query->bindValue(':target', $target);
        $query->execute();
        $count= $query->rowCount();
        return $count;
    }else {
        $query = $connection->prepare('INSERT INTO :table VALUES (null, :target, DATETIME("now"), "3")');
        $query->bindValue(':table', $table);
        $query->bindValue(':column', $column);
        $query->bindValue(':target', $target);
        $query->execute(); 
        $count= $query->rowCount();
        return $count;
    }
}
function yellowcard($status, $target) //three yellow cards equals red card
{
    $query = $connection->prepare('UPDATE :table SET status = "3" WHERE :column = :target');
    $query->bindValue(':table', $table);
    $query->bindValue(':column', $column);
    $query->bindValue(':target', $target);
    $query->execute();
}
function checkifexists($table,$column,$target){
    $query = $connection->prepare('SELECT * FROM :table WHERE :column = :target');
    $query->bindValue(':column', $column);
    $query->bindValue(':table', $table);
    $query->bindValue(':target', $target);
    $query->execute();
    $count= $query->rowCount();
    return $count;
}
?>
