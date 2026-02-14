<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "a_shop_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}
?>