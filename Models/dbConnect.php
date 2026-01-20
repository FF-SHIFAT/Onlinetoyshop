<?php

$host = "localhost";
$user = "root";
$pass = "";
$db_name = "toyshop_db";

function dbConnect()
{
    global $host;
    global $user;
    global $pass;
    global $db_name;

    $conn = mysqli_connect($host, $user, $pass, $db_name);

    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }
    
    return $conn;
}

$conn = dbConnect();

?>