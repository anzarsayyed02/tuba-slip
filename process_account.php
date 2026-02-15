<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $customer_mobile = $_POST['customer_mobile'];
    $account_id = $_POST['account_id'];
    
    // Get customer name
    $sql = "SELECT name FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    
    header("Location: index.php?step=3&customer_id=" . $customer_id . "&customer_name=" . urlencode($customer['name']) . "&customer_mobile=" . urlencode($customer_mobile) . "&account_id=" . $account_id);
    exit();
}
?>

