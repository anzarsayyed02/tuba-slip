<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['customerMobile'];
    
    // Prepare a statement to prevent SQL injection
    $sql = "SELECT * FROM customers WHERE mobile = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Customer found
        $customer = $result->fetch_assoc();
        header("Location: index.php?step=2&customer_id=" . $customer['id'] . "&customer_name=" . urlencode($customer['name']) . "&customer_mobile=" . urlencode($customer['mobile']));
        exit();
    } else {
        // Customer not found
        header("Location: add_customer.php?mobile=" . urlencode($mobile));
        exit();
    }
}
?>

