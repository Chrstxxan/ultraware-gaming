<?php
require_once "../app/config/path.php";
require_once ROOT . "/app/config/database.php";
require_once ROOT . "/app/helpers/session.php";
require_once ROOT . "/app/models/Product.php";

$category = $_GET['categoria'] ?? null;
$products = Product::all($pdo, $category);

require_once ROOT . "/views/products/list.php";
