<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'restaurant') {
    header("Location: /views/auth/login.php");
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

header("Location: /views/restaurant/orders.php");
