<?php
    require_once __DIR__ . "/config.php";

    $conn = mysqli_connect($host, $user, $password, $db);

    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");
?>
