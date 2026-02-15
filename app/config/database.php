<?php

$host = "localhost";
$db   = "ultraware_gaming";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:unix_socket=/xampp/mysql/mysql.sock;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
