<?php
require_once 'config.php';
require_once 'banks.php';

if (!isset($_GET['customer_id'])) {
    header("Location: index.php");
    exit();
}

$customer_id = $_GET['customer_id'];
$error = '';
$success = '';

// Get customer details
$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_number = $_POST['account_number'];
    $account_holder = $_POST['account_holder'];
    $bank = $_POST['bank'];
    
    // Check if account already exists for this customer
    $sql = "SELECT id FROM accounts WHERE customer_id = ? AND account_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $customer_id, $account_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "This account number already exists for this customer.";
    } else {
        // Insert new account
        $sql = "INSERT INTO accounts (customer_id, account_number, account_holder, bank) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $customer_id, $account_number, $account_holder, $bank);
        
        if ($stmt->execute()) {
            $success = "Account added successfully!";
            header("Location: index.php?step=2&customer_id=" . $customer_id . "&customer_name=" . urlencode($customer['name']) . "&customer_mobile=" . urlencode($customer['mobile']));
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
    <title>Add New Account - Tuba Enterprises</title>
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
                    <h2 class="card-title">Add New Account</h2>
                    <p>Customer: <?php echo htmlspecialchars($customer['name']); ?></p>
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
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?customer_id=" . $customer_id); ?>">
                        <div class="form-group">
                            <label for="account_number">Account Number</label>
                            <input type="text" id="account_number" name="account_number" required>
                        </div>
                        <div class="form-group">
                            <label for="account_holder">Account Holder</label>
                            <input type="text" id="account_holder" name="account_holder" required>
                        </div>
                        <div class="form-group">
                            <label for="bank">Bank</label>
                            <div style="position: relative;">
                                <input type="text" id="bankSearch" placeholder="Search for a bank..." onkeyup="filterBanks()">
                                <div id="bankDropdown" style="display: none; position: absolute; width: 100%; max-height: 200px; overflow-y: auto; z-index: 100; background: white; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                                    <?php foreach($banks as $bank): ?>
                                    <div class="bank-option" onclick="selectBank('<?php echo htmlspecialchars($bank); ?>')" style="padding: 0.5rem; cursor: pointer; border-bottom: 1px solid var(--border-color);">
                                        <?php echo htmlspecialchars($bank); ?>
                                    </div>
                                    <?php endforeach; ?>
                                    <div style="padding: 0.5rem; text-align: center; border-top: 1px solid var(--border-color);">
                                        <button type="button" class="btn btn-outline" style="width: 100%;" onclick="addNewBank()">
                                            <i class="fas fa-plus-circle"></i> Add New Bank
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="text" id="bank" name="bank" required readonly>
                            <button type="button" class="btn btn-outline" style="margin-top: 0.5rem;" onclick="document.getElementById('bankDropdown').style.display = 'block';">
                                <i class="fas fa-list"></i> Show Bank List
                            </button>
                        </div>
                        <button type="submit" class="btn btn-full">Add Account</button>
                    </form>
                </div>
                
                <div class="card-footer">
                    <a href="index.php?step=2&customer_id=<?php echo $customer_id; ?>&customer_name=<?php echo urlencode($customer['name']); ?>&customer_mobile=<?php echo urlencode($customer['mobile']); ?>" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        function filterBanks() {
            const input = document.getElementById('bankSearch');
            const filter = input.value.toUpperCase();
            const dropdown = document.getElementById('bankDropdown');
            const options = dropdown.getElementsByClassName('bank-option');
            
            dropdown.style.display = 'block';
            
            for (let i = 0; i < options.length; i++) {
                const txtValue = options[i].textContent || options[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }
        }
        
        function selectBank(bank) {
            document.getElementById('bank').value = bank;
            document.getElementById('bankDropdown').style.display = 'none';
        }
        
        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#bankSearch') && !event.target.matches('.bank-option')) {
                document.getElementById('bankDropdown').style.display = 'none';
            }
        }

        function addNewBank() {
            const newBank = prompt("Enter new bank name:");
            if (newBank && newBank.trim() !== '') {
                selectBank(newBank.trim());
            }
        }
    </script>
</body>
</html>

