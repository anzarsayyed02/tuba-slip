<?php
require_once 'config.php';

if (!isset($_GET['account_id']) || !isset($_GET['customer_id'])) {
    header("Location: index.php");
    exit();
}

$account_id = $_GET['account_id'];
$customer_id = $_GET['customer_id'];
$customer_name = isset($_GET['customer_name']) ? $_GET['customer_name'] : '';
$customer_mobile = isset($_GET['customer_mobile']) ? $_GET['customer_mobile'] : '';

// Check if there are any transactions using this account
$sql = "SELECT COUNT(*) as count FROM transactions WHERE account_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $account_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // Account has transactions, don't delete
    $error = "Cannot delete this account because it has transactions associated with it.";
    header("Location: index.php?step=2&customer_id=$customer_id&customer_name=" . urlencode($customer_name) . "&customer_mobile=" . urlencode($customer_mobile) . "&error=" . urlencode($error));
    exit();
}

// Delete the account
$sql = "DELETE FROM accounts WHERE id = ? AND customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $account_id, $customer_id);

if ($stmt->execute()) {
    $success = "Account deleted successfully.";
    header("Location: index.php?step=2&customer_id=$customer_id&customer_name=" . urlencode($customer_name) . "&customer_mobile=" . urlencode($customer_mobile) . "&success=" . urlencode($success));
} else {
    $error = "Error deleting account: " . $stmt->error;
    header("Location: index.php?step=2&customer_id=$customer_id&customer_name=" . urlencode($customer_name) . "&customer_mobile=" . urlencode($customer_mobile) . "&error=" . urlencode($error));
}
exit();
?>

