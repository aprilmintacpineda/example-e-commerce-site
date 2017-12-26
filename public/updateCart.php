<?php

if (empty($_POST)) {
	header('Location: /');
}
session_start();
$_SESSION['cart'] = json_decode($_POST['cart']);
session_commit();
echo json_encode($_SESSION['cart']);