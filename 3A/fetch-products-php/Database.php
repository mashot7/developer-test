<?php

class Database {

  private PDO $pdo;

  public function __construct($host = HOST, $dbname = DBNAME, $user = USER, $password = PASSWORD) {
    try {
      $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->initTables();
    } catch (PDOException $e) {
      die('Failed to connect to the database: ' . $e->getMessage());
    }
  }

  public function getPdo() {
    return $this->pdo;
  }

  private function initTables() {
    $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `remote_id` varchar(255) DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `name_fi` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

    $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `products` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `remote_id` varchar(255) DEFAULT NULL,
      `name` varchar(255) NOT NULL,
      `name_fi` varchar(255) DEFAULT NULL,
      `description` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

    $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `product_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `category_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

    $this->pdo->prepare("CREATE TABLE IF NOT EXISTS `variations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_id` int(11) NOT NULL,
      `data` json DEFAULT NULL,
      `price` varchar(255) NOT NULL,
      `price_usd` varchar(255) DEFAULT NULL,
      `price_eur` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8")->execute();

  }

}
