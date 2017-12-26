<?php

require 'config.php';

$pdo = new PDO($config['db']['connection_string'], $config['db']['user'], $config['db']['pass']);
$items = $pdo->query('select * from products');
$items = $items->fetchAll(PDO::FETCH_ASSOC);

return $items;