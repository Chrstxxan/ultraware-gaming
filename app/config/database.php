<?php

require_once __DIR__."/../helpers/session.php";

$host = "localhost";
$db   = "ultraware_gaming";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // auto login persistente
    require_once __DIR__."/../helpers/auto_login.php";

} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
