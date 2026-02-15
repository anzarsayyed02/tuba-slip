<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $account_id = $_POST['account_id'];
    $amount = $_POST['amount'];
    $transaction_id = $_POST['transaction_id'];
    $date_time = $_POST['date_time'];
    
    // Insert transaction
    $sql = "INSERT INTO transactions (transaction_id, customer_id, account_id, amount, date_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siisd", $transaction_id, $customer_id, $account_id, $amount, $date_time);
    
    if ($stmt->execute()) {
        header("Location: index.php?step=4&transaction_id=" . $transaction_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

