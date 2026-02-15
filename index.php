<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Transfer Receipt - Tuba Enterprises</title>
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
                    <h2 class="card-title">Money Transfer Receipt</h2>
                </div>
                
                <div class="card-content">
                    <!-- Step 1: Mobile Number -->
                    <div class="step <?php echo !isset($_GET['step']) || $_GET['step'] == 1 ? 'active' : ''; ?>" id="step1">
                        <form id="mobileForm" action="search_customer.php" method="POST" class="space-y-4">
                            <div class="form-group">
                                <label for="customerMobile">Customer Mobile Number</label>
                                <div class="input-group">
                                    <input type="text" id="customerMobile" name="customerMobile" placeholder="Enter mobile number" required>
                                    <button type="submit" class="btn">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="add_customer.php" class="btn btn-outline" id="addNewCustomerBtn">
                            <i class="fas fa-plus-circle"></i> Add New Customer
                        </a>
                    </div>

                    <!-- Step 2: Account Selection -->
                    <div class="step <?php echo isset($_GET['step']) && $_GET['step'] == 2 ? 'active' : 'hidden'; ?>" id="step2">
                        <?php if(isset($_GET['customer_id']) && isset($_GET['customer_name'])): ?>
                        <div class="customer-info">
                            <label>Customer Name</label>
                            <p id="customerNameDisplay" class="text-lg"><?php echo htmlspecialchars($_GET['customer_name']); ?></p>
                            <input type="hidden" id="customerId" value="<?php echo htmlspecialchars($_GET['customer_id']); ?>">
                        </div>
                        <?php if(isset($_GET['error'])): ?>
                        <div style="background-color: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($_GET['success'])): ?>
                        <div style="background-color: #d1fae5; color: #047857; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars($_GET['success']); ?>
                        </div>
                        <?php endif; ?>
                        <form action="process_account.php" method="POST">
                            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($_GET['customer_id']); ?>">
                            <input type="hidden" name="customer_mobile" value="<?php echo htmlspecialchars($_GET['customer_mobile']); ?>">
                            <div class="form-group">
                                <label for="accountSelect">Select Account</label>
                                <select id="accountSelect" name="account_id" required onchange="showAccountDetails(this.value)">
                                    <option value="">Select an account</option>
                                    <?php
                                    $customer_id = $_GET['customer_id'];
                                    $sql = "SELECT * FROM accounts WHERE customer_id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $customer_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    while($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['id'] . '" data-account-number="' . htmlspecialchars($row['account_number']) . '" data-account-holder="' . htmlspecialchars($row['account_holder']) . '" data-bank="' . htmlspecialchars($row['bank']) . '">' . htmlspecialchars($row['account_holder']) . ' - ' . htmlspecialchars($row['bank']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div id="accountDetails" class="hidden" style="margin-top: 1rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                                <h3 style="margin-bottom: 0.5rem;">Account Details</h3>
                                <div class="form-group">
                                    <label>Account Number:</label>
                                    <p id="displayAccountNumber"></p>
                                </div>
                                <div class="form-group">
                                    <label>Account Holder:</label>
                                    <p id="displayAccountHolder"></p>
                                </div>
                                <div class="form-group">
                                    <label>Bank:</label>
                                    <p id="displayBank"></p>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                                    <button type="submit" class="btn">Continue with this Account</button>
                                    <a href="#" id="deleteAccountBtn" class="btn btn-outline" style="background-color: #fee2e2; color: #b91c1c; border-color: #f87171;" onclick="return confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete Account
                                    </a>
                                </div>
                            </div>
                        </form>
                        <a href="add_account.php?customer_id=<?php echo htmlspecialchars($_GET['customer_id']); ?>" class="btn btn-outline" id="addNewAccountBtn">
                            <i class="fas fa-plus-circle"></i> Add New Account
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Step 3: Amount and Details -->
                    <div class="step <?php echo isset($_GET['step']) && $_GET['step'] == 3 ? 'active' : 'hidden'; ?>" id="step3">
                        <?php if(isset($_GET['account_id'])): ?>
                        <form id="amountForm" action="process_transaction.php" method="POST" class="space-y-4">
                            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($_GET['customer_id']); ?>">
                            <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($_GET['account_id']); ?>">
                            <div class="form-group">
                                <label for="amount">Transfer Amount</label>
                                <input type="text" id="amount" name="amount" placeholder="Enter amount" required>
                            </div>
                            <div class="form-group">
                                <label for="transactionId">Transaction ID</label>
                                <input type="text" id="transactionId" name="transaction_id" value="<?php echo strtoupper(substr(uniqid(), 0, 9)); ?>">
                            </div>
                            <div class="form-group">
                                <label for="dateTime">Date & Time</label>
                                <input type="text" id="dateTime" name="date_time" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
                            </div>
                            <button type="submit" class="btn btn-full">Generate Receipt</button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Step 4: Receipt -->
                    <div class="step <?php echo isset($_GET['step']) && $_GET['step'] == 4 ? 'active' : 'hidden'; ?>" id="step4">
                        <?php if(isset($_GET['transaction_id'])): 
                            $transaction_id = $_GET['transaction_id'];
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
                            
                            if($row = $result->fetch_assoc()):
                        ?>
                        <div class="receipt">
                            <div class="receipt-header">
                                <h2>TUBA ENTERPRISES</h2>
                                <p>Tours & Travels - Online Service</p>
                                <p>Money Transfer Receipt</p>
                            </div>
                            <div class="receipt-grid">
                                <div class="receipt-item">
                                    <p class="label">Customer Name:</p>
                                    <p id="receipt-customerName"><?php echo htmlspecialchars($row['customer_name']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Mobile Number:</p>
                                    <p id="receipt-mobile"><?php echo htmlspecialchars($row['customer_mobile']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Account Holder:</p>
                                    <p id="receipt-accountHolder"><?php echo htmlspecialchars($row['account_holder']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Account Number:</p>
                                    <p id="receipt-accountNumber">
                                        <?php 
                                        $account_number = $row['account_number'];
                                        $masked_number = str_repeat("X", strlen($account_number) - 4) . substr($account_number, -4);
                                        echo htmlspecialchars($masked_number); 
                                        ?>
                                    </p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Bank:</p>
                                    <p id="receipt-bank"><?php echo htmlspecialchars($row['bank']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Amount:</p>
                                    <p id="receipt-amount">₹<?php echo htmlspecialchars($row['amount']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Transaction ID:</p>
                                    <p id="receipt-transactionId"><?php echo htmlspecialchars($row['transaction_id']); ?></p>
                                </div>
                                <div class="receipt-item">
                                    <p class="label">Date & Time:</p>
                                    <p id="receipt-dateTime"><?php echo htmlspecialchars($row['date_time']); ?></p>
                                </div>
                            </div>
                            <div class="receipt-actions">
                                <button class="btn btn-outline" id="printBtn" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Receipt
                                </button>
                                <a href="export_receipt.php?transaction_id=<?php echo $transaction_id; ?>" class="btn btn-outline" id="downloadBtn">
                                    <i class="fas fa-download"></i> Save to Excel
                                </a>
                            </div>
                        </div>
                        <?php endif; endif; ?>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if(isset($_GET['step']) && $_GET['step'] > 1): ?>
                    <a href="index.php?step=<?php echo $_GET['step']-1; ?><?php echo isset($_GET['customer_id']) ? '&customer_id='.$_GET['customer_id'] : ''; ?><?php echo isset($_GET['customer_name']) ? '&customer_name='.$_GET['customer_name'] : ''; ?><?php echo isset($_GET['customer_mobile']) ? '&customer_mobile='.$_GET['customer_mobile'] : ''; ?><?php echo isset($_GET['account_id']) ? '&account_id='.$_GET['account_id'] : ''; ?>" class="btn btn-outline" id="backBtn">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['step']) && $_GET['step'] == 4): ?>
                    <a href="index.php" class="btn" id="finishBtn">
                        <i class="fas fa-check-circle"></i> Finish
                    </a>
                    <?php endif; ?>
                    
                    <p class="step-indicator" id="stepIndicator">
                        <?php 
                        $step = isset($_GET['step']) ? $_GET['step'] : 1;
                        if($step < 4) {
                            echo "Step $step of 4: ";
                            if($step == 1) echo "Enter mobile number";
                            else if($step == 2) echo "Select account";
                            else if($step == 3) echo "Enter amount";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple print functionality
        document.getElementById('printBtn')?.addEventListener('click', function() {
            window.print();
        });
        
        // Show account details when an account is selected
        function showAccountDetails(accountId) {
            if (!accountId) {
                document.getElementById('accountDetails').classList.add('hidden');
                return;
            }
            
            const selectedOption = document.querySelector(`#accountSelect option[value="${accountId}"]`);
            if (selectedOption) {
                document.getElementById('displayAccountNumber').textContent = selectedOption.dataset.accountNumber;
                document.getElementById('displayAccountHolder').textContent = selectedOption.dataset.accountHolder;
                document.getElementById('displayBank').textContent = selectedOption.dataset.bank;
                document.getElementById('accountDetails').classList.remove('hidden');
                
                // Update delete button href
                const deleteBtn = document.getElementById('deleteAccountBtn');
                deleteBtn.href = `delete_account.php?account_id=${accountId}&customer_id=<?php echo isset($_GET['customer_id']) ? $_GET['customer_id'] : ''; ?>&customer_name=<?php echo isset($_GET['customer_name']) ? urlencode($_GET['customer_name']) : ''; ?>&customer_mobile=<?php echo isset($_GET['customer_mobile']) ? urlencode($_GET['customer_mobile']) : ''; ?>`;
            }
        }
        
        // Confirm account deletion
        function confirmDelete() {
            return confirm('Are you sure you want to delete this account? This action cannot be undone.');
        }
    </script>
</body>
</html>

