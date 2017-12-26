<?php

if (empty($_POST)) {
	header('Location: /');
}
session_start();
$sum = json_decode($_POST['sum']);
$balance = $_SESSION['user_balance'] - $sum;
$transportType = json_decode($_POST['transportType']);
$_SESSION['purchases'][] = [
	'items' => json_decode($_POST['cart']),
	'id' => floor(rand() * 999999) + 1,
	'balanceAfter' => $balance,
	'balanceBefore' => $_SESSION['user_balance'],
	'transportType' => $transportType,
	'transportFee' => $transportType == 2? 5 : 0,
	'sum' => $sum
];
$_SESSION['cart'] = [];
$_SESSION['user_balance'] = $balance;
session_commit();
echo json_encode($_SESSION['purchases']);