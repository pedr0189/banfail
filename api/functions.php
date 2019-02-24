<?php
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
    $resultarray = array($table,$column);
    //var_dump($resultarray);
    return $resultarray;
}
function getstatus($target){ //check status, returns null (does not exist) or 0 (free) to 3 (locked)
    global $connection;
    global $table;
    global $column;
    $querystring = "SELECT status FROM $table WHERE $column = \"$target\"";
    //echo "The query should be \n$querystring\n";
    $query = $connection->prepare("$querystring");
    //var_dump($query);
    // $query->bindValue(':table', $table, SQLITE3_TEXT);
    // $query->bindValue(':column', $column, SQLITE3_TEXT);
    // $query->bindValue(':target', $target, SQLITE3_TEXT);
    $query->execute();    
    $result = $query->fetchAll();
    return $result[0][0];
}
function redcard($target) //used for imediate lock out (attack detected)
{
    global $connection;
    global $table;
    global $column;
    if(checkifexists($table,$column,$target))
    {
        $querystring = "UPDATE $table SET status = \"3\" WHERE $column = $target";
        $query = $connection->prepare($querystring);
        // $query->bindValue(':table', $table);
        // $query->bindValue(':column', $column);
        // $query->bindValue(':target', $target);
        $query->execute();
        $count= $query->rowCount();
        return $count;
    }else {
        $querystring = "INSERT INTO $table VALUES(null, $target, DATETIME(\"now\"), \"3\"";
        $query = $connection->prepare($querystring);
        // $query = $connection->prepare('INSERT INTO :table VALUES (null, :target, DATETIME("now"), "3")');
        // $query->bindValue(':table', $table);
        // $query->bindValue(':column', $column);
        // $query->bindValue(':target', $target);
        $query->execute(); 
        $count= $query->rowCount();
        return $count;
    }
}
function yellowcard($status, $target) //three yellow cards equals red card
{
    global $connection;
    global $table;
    global $column;
    $querystring = "UPDATE $table SET status =".($status+1)." WHERE $column = \"$target\"";
    //echo $querystring;
    $query = $connection->prepare($querystring);
    //var_dump($query);
    // $query->bindValue(':table', $table);
    // $query->bindValue(':column', $column);
    // $query->bindValue(':target', $target);
    $query->execute();
}
function checkifexists($table,$column,$target){
    global $connection;
    $querystring = "SELECT * FROM $table WHERE $column = $target";
    $query = $connection->prepare($querystring);
    // $query->bindValue(':column', $column);
    // $query->bindValue(':table', $table);
    // $query->bindValue(':target', $target);
    $query->execute();
    $count= $query->rowCount();
    return $count;
}
