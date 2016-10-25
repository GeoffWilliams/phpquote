<?php


$db_options = [];
$db_options['db.options'] = array(
  "driver" => "pdo_mysql",
  "user" => getenv('MYSQL_DB_USER'),
  "password" => getenv('MYSQL_DB_PASSWORD'),
  "dbname" => "quote",
  "host" => getenv('MYSQL_DB_HOST'),
?>
