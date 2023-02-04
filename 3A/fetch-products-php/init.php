<?php

const HOST = 'database';
const DBNAME = 'lamp';
const USER = 'lamp';
const PASSWORD = 'lamp';

function dpm($message): void {
  echo '<pre>';
  var_dump($message);
  echo '</pre>';
}

include_once 'Database.php';
include_once 'Products.php';
