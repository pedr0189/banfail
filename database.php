<?php
try {
    $connection = new PDO('sqlite:userpool.db');
} catch(PDOException $e) {
    echo $e->getMessage();
}
?>
