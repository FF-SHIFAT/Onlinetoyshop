<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toyshop_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$GLOBALS['site_config'] = [
    'app_name' => ' ToyShop',
    'currency' => 'Tk. '
];

?>