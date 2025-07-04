<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "goldfish-strategy";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}
?>

