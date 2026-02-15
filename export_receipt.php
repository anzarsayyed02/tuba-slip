<?php
require_once 'config.php';

if (!isset($_GET['transaction_id'])) {
    header("Location: index.php");
    exit();
}

$transaction_id = $_GET['transaction_id'];

// Get transaction details
$sql = "SELECT t.*, c.name as customer_name, c.mobile as customer_mobile, 
        a.account_number, a.account_holder, a.bank 
        FROM transactions t 
        JOIN customers c ON t.customer_id = c.id 
        JOIN accounts a ON t.account_id = a.id 
        WHERE t.transaction_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $transaction_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$transaction = $result->fetch_assoc();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="receipt_' . $transaction_id . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Create Excel content
echo "
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
</head>
<body>
<table border='1'>
    <tr>
        <th colspan='2'>TUBA ENTERPRISES</th>
    </tr>
    <tr>
        <th colspan='2'>Tours & Travels - Online Service</th>
    </tr>
    <tr>
        <th colspan='2'>Money Transfer Receipt</th>
    </tr>
    <tr>
        <td>Customer Name</td>
        <td>" . htmlspecialchars($transaction['customer_name']) . "</td>
    </tr>
    <tr>
        <td>Mobile Number</td>
        <td>" . htmlspecialchars($transaction['customer_mobile']) . "</td>
    </tr>
    <tr>
        <td>Account Holder</td>
        <td>" . htmlspecialchars($transaction['account_holder']) . "</td>
    </tr>
    <tr>
        <td>Account Number</td>
        <td>" . htmlspecialchars(str_repeat("X", strlen($transaction['account_number']) - 4) . substr($transaction['account_number'], -4)) . "</td>
    </tr>
    <tr>
        <td>Bank</td>
        <td>" . htmlspecialchars($transaction['bank']) . "</td>
    </tr>
    <tr>
        <td>Amount</td>
        <td>₹" . htmlspecialchars($transaction['amount']) . "</td>
    </tr>
    <tr>
        <td>Transaction ID</td>
        <td>" . htmlspecialchars($transaction['transaction_id']) . "</td>
    </tr>
    <tr>
        <td>Date & Time</td>
        <td>" . htmlspecialchars($transaction['date_time']) . "</td>
    </tr>
</table>
</body>
</html>";
exit();
?>

