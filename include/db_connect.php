<?php
$servername = "31.11.39.35:3306";
$username = "Sql1661498";
$password = "!Erminiamamma@62";
$dbname = "Sql1661498_4";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>