<?php

 include_once 'init.php';

$database = new Database();
$pdo = $database->getPdo();

$products = new Products($database, "http://fetch-products-php.lndo.site/api.php");

$products->insertAndUpdate();
echo "Hello";