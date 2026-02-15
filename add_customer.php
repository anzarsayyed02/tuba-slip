<?php
require_once 'config.php';

$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    
    // Check if mobile already exists
    $sql = "SELECT id FROM customers WHERE mobile = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "A customer with this mobile number already exists.";
    } else {
        // Insert new customer
        $sql = "INSERT INTO customers (name, mobile) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $mobile);
        
        if ($stmt->execute()) {
            $customer_id = $conn->insert_id;
            $success = "Customer added successfully!";
            header("Location: index.php?step=2&customer_id=" . $customer_id . "&customer_name=" . urlencode($name) . "&customer_mobile=" . urlencode($mobile));
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Customer - Tuba Enterprises</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="min-h-screen">
        <div class="container">
            <div class="logo-container">
                <img src="logo.jpg" alt="Tuba Enterprises Logo" class="logo">
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Add New Customer</h2>
                </div>
                
                <div class="card-content">
                    <?php if($error): ?>
                    <div style="background-color: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                    <div style="background-color: #d1fae5; color: #047857; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                        <?php echo $success; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="name">Customer Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile Number</label>
                            <input type="text" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-full">Add Customer</button>
                    </form>
                </div>
                
                <div class="card-footer">
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

