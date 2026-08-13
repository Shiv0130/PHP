<?php
session_start();

// System Configuration
define('CREDIT_CARD_FEE', 0.025); // 2.5%
define('PAYPAL_FEE', 0.035); // 3.5%
define('CRYPTO_FEE', 0.01); // 1%
define('FRAUD_THRESHOLD', 100000); // R100,000

// Initialize or reset the system data
function initializeSystem() {
    if (isset($_GET['reset']) && $_GET['reset'] == 1) {
        session_unset();
    }

    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [
            'alice' => ['id' => 'U001', 'name' => 'Alice', 'balance' => 100000, 'paymentMethods' => []],
            'bob' => ['id' => 'U002', 'name' => 'Bob', 'balance' => 200000, 'paymentMethods' => []],
            'charlie' => ['id' => 'U003', 'name' => 'Charlie', 'balance' => 50000, 'paymentMethods' => []],
        ];
        $_SESSION['transactions'] = [];
        $_SESSION['current_user'] = 'alice';
    }
}

// Validate input using regex and empty checks
function validateInput($input, $type) {
    if (empty($input)) {
        return false;
    }

    switch ($type) {
        case 'amount':
            return preg_match('/^\d+(\.\d{1,2})?$/', $input);
        case 'cardNumber':
            return preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $input);
        case 'expiryDate':
            return preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $input);
        case 'cvv':
            return preg_match('/^\d{3}$/', $input);
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        case 'walletAddress':
            return preg_match('/^0x[a-fA-F0-9]{40}$/', $input);
        default:
            return false;
    }
}

// Calculate transaction fee based on payment method
function calculateFee($amount, $method) {
    switch ($method) {
        case 'credit_card':
            return $amount * CREDIT_CARD_FEE;
        case 'paypal':
            return $amount * PAYPAL_FEE;
        case 'crypto':
            return $amount * CRYPTO_FEE;
        default:
            return 0;
    }
}

// Process payment
function processPayment($user, $amount, $paymentMethod) {
    // Check for fraud
    if ($amount > FRAUD_THRESHOLD) {
        return ['status' => 'error', 'message' => 'Payment rejected due to fraud detection.'];
    }

    $fee = calculateFee($amount, $paymentMethod);
    $totalAmount = $amount + $fee;

    // Check if user has enough balance
    if ($_SESSION['users'][$user]['balance'] < $totalAmount) {
        return ['status' => 'error', 'message' => 'Insufficient balance.'];
    }

    // Deduct amount from user's balance
    $_SESSION['users'][$user]['balance'] -= $totalAmount;

    // Create transaction record
    $transactionId = 'TXN' . time() . rand(1000, 9999);
    $transaction = [
        'id' => $transactionId,
        'user' => $user,
        'amount' => $amount,
        'fee' => $fee,
        'total' => $totalAmount,
        'method' => $paymentMethod,
        'timestamp' => time(),
        'status' => 'completed'
    ];

    // Store transaction
    $_SESSION['transactions'][] = $transaction;

    return [
        'status' => 'success',
        'message' => 'Payment of R' . number_format($amount, 2) . ' processed successfully. Fee: R' . number_format($fee, 2),
        'transaction' => $transaction
    ];
}

// Process refund
function processRefund($transactionId) {
    // Find the transaction
    $transaction = null;
    foreach ($_SESSION['transactions'] as $key => $txn) {
        if ($txn['id'] == $transactionId && $txn['status'] == 'completed') {
            $transaction = $txn;
            break;
        }
    }

    if (!$transaction) {
        return ['status' => 'error', 'message' => 'Transaction not found or already refunded.'];
    }

    // Refund the amount to user's balance
    $user = $transaction['user'];
    $refundAmount = $transaction['total']; // Including fee
    $_SESSION['users'][$user]['balance'] += $refundAmount;

    // Update transaction status
    foreach ($_SESSION['transactions'] as $key => $txn) {
        if ($txn['id'] == $transactionId) {
            $_SESSION['transactions'][$key]['status'] = 'refunded';
            break;
        }
    }

    // Add refund transaction
    $refundTransactionId = 'REF' . time() . rand(1000, 9999);
    $refundTransaction = [
        'id' => $refundTransactionId,
        'user' => $user,
        'amount' => $refundAmount,
        'fee' => 0,
        'total' => $refundAmount,
        'method' => 'refund',
        'timestamp' => time(),
        'status' => 'completed',
        'refundedTransaction' => $transactionId
    ];

    $_SESSION['transactions'][] = $refundTransaction;

    return [
        'status' => 'success',
        'message' => 'Refund of R' . number_format($refundAmount, 2) . ' processed successfully.',
        'transaction' => $refundTransaction
    ];
}

// Initialize the system
initializeSystem();

$message = '';
$messageType = '';

// Handle user switching
if (isset($_POST['switch_user']) && isset($_POST['user'])) {
    $newUser = $_POST['user'];
    if (isset($_SESSION['users'][$newUser])) {
        $_SESSION['current_user'] = $newUser;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'payment':
                $amount = $_POST['amount'];
                $paymentMethod = $_POST['paymentMethod'];

                // Validate amount
                if (!validateInput($amount, 'amount')) {
                    $message = 'Invalid amount.';
                    $messageType = 'error';
                    break;
                }

                // Process payment
                $result = processPayment($_SESSION['current_user'], $amount, $paymentMethod);
                $message = $result['message'];
                $messageType = $result['status'];
                break;

            case 'refund':
                $transactionId = $_POST['transactionId'];
                
                if (empty($transactionId)) {
                    $message = 'Please select a transaction to refund.';
                    $messageType = 'error';
                    break;
                }
                
                $result = processRefund($transactionId);
                $message = $result['message'];
                $messageType = $result['status'];
                break;

            case 'addPaymentMethod':
                $type = $_POST['methodType'];
                $data = [];
                
                if ($type == 'credit_card') {
                    if (!validateInput($_POST['cardNumber'], 'cardNumber') ||
                        !validateInput($_POST['expiryDate'], 'expiryDate') ||
                        !validateInput($_POST['cvv'], 'cvv')) {
                        $message = 'Invalid credit card details.';
                        $messageType = 'error';
                    } else {
                        $data = [
                            'type' => $type,
                            'cardNumber' => $_POST['cardNumber'],
                            'expiryDate' => $_POST['expiryDate'],
                            'cvv' => $_POST['cvv'],
                        ];
                        // Add to session
                        $_SESSION['users'][$_SESSION['current_user']]['paymentMethods'][] = $data;
                        $message = 'Credit card added successfully.';
                        $messageType = 'success';
                    }
                } elseif ($type == 'paypal') {
                    if (!validateInput($_POST['email'], 'email')) {
                        $message = 'Invalid PayPal email.';
                        $messageType = 'error';
                    } else {
                        $data = ['type' => $type, 'email' => $_POST['email']];
                        $_SESSION['users'][$_SESSION['current_user']]['paymentMethods'][] = $data;
                        $message = 'PayPal method added successfully.';
                        $messageType = 'success';
                    }
                } elseif ($type == 'crypto') {
                    if (!validateInput($_POST['walletAddress'], 'walletAddress')) {
                        $message = 'Invalid wallet address.';
                        $messageType = 'error';
                    } else {
                        $data = ['type' => $type, 'walletAddress' => $_POST['walletAddress']];
                        $_SESSION['users'][$_SESSION['current_user']]['paymentMethods'][] = $data;
                        $message = 'Cryptocurrency wallet added successfully.';
                        $messageType = 'success';
                    }
                }
                break;
        }
    }
}

// Get current user data
$currentUser = $_SESSION['users'][$_SESSION['current_user']];

// Get user's transactions
$userTransactions = [];
if (isset($_SESSION['transactions'])) {
    foreach ($_SESSION['transactions'] as $transaction) {
        if ($transaction['user'] == $_SESSION['current_user']) {
            $userTransactions[] = $transaction;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing System</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .balance {
            background-color: var(--dark-color);
            padding: 0.5rem 1rem;
            border-radius: 3px;
            margin-left: 15px;
        }
        
        main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            main {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        h1, h2, h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .message {
            padding: 10px 15px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        form {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        input, select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        button.danger {
            background-color: var(--danger-color);
        }
        
        button.danger:hover {
            background-color: #c0392b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .refunded {
            color: var(--danger-color);
            font-weight: 600;
        }
        
        .switch-user {
            margin-bottom: 20px;
        }
        
        .switch-user form {
            display: flex;
            align-items: center;
        }
        
        .switch-user select {
            margin-right: 10px;
            width: auto;
        }
        
        .test-panel {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .test-case-table {
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .test-case-table th, .test-case-table td {
            padding: 8px 12px;
        }
        
        .test-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .test-buttons button {
            font-size: 14px;
        }
        
        .reset-link {
            display: inline-block;
            margin-top: 10px;
            color: var(--danger-color);
            text-decoration: none;
        }
        
        .reset-link:hover {
            text-decoration: underline;
        }
        
        .success-icon {
            color: var(--success-color);
            font-weight: bold;
        }
        
        .error-icon {
            color: var(--danger-color);
            font-weight: bold;
        }
        
        .warning-icon {
            color: var(--warning-color);
            font-weight: bold;
        }
    </style>
    <script>
        // Show only relevant fields based on payment method
        function togglePaymentFields() {
            var methodType = document.getElementById('methodType').value;
            
            // Hide all fields first
            document.getElementById('creditCardFields').style.display = 'none';
            document.getElementById('paypalFields').style.display = 'none';
            document.getElementById('cryptoFields').style.display = 'none';
            
            // Show relevant fields
            if (methodType === 'credit_card') {
                document.getElementById('creditCardFields').style.display = 'block';
            } else if (methodType === 'paypal') {
                document.getElementById('paypalFields').style.display = 'block';
            } else if (methodType === 'crypto') {
                document.getElementById('cryptoFields').style.display = 'block';
            }
        }
        
        // Initialize when page loads
        window.onload = function() {
            togglePaymentFields();
        }
    </script>
</head>
<body>
    <div class="container">
        <header>
            <div class="user-info">
                <h1>Payment System</h1>
                <div class="balance">Balance: R<?php echo number_format($currentUser['balance'], 2); ?></div>
            </div>
            <div>
                <h2><?php echo $currentUser['name']; ?> (<?php echo $currentUser['id']; ?>)</h2>
            </div>
        </header>
        
        <!-- User Switch -->
        <div class="switch-user">
            <form method="post">
                <select name="user">
                    <?php foreach ($_SESSION['users'] as $username => $userData): ?>
                        <option value="<?php echo $username; ?>" <?php echo ($_SESSION['current_user'] == $username) ? 'selected' : ''; ?>>
                            <?php echo $userData['name']; ?> (<?php echo $userData['id']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="switch_user">Switch User</button>
            </form>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <main>
            <!-- Left Column -->
            <div>
                <!-- Make Payment Section -->
                <div class="card">
                    <h3>Make a Payment</h3>
                    <form method="post">
                        <input type="hidden" name="action" value="payment">
                        <div class="form-group">
                            <label for="amount">Amount (R)</label>
                            <input type="number" id="amount" name="amount" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" name="paymentMethod" required>
                                <?php if (empty($currentUser['paymentMethods'])): ?>
                                    <option value="">No payment methods available</option>
                                <?php else: ?>
                                    <?php foreach ($currentUser['paymentMethods'] as $index => $method): ?>
                                        <option value="<?php echo $method['type']; ?>">
                                            <?php 
                                            switch($method['type']) {
                                                case 'credit_card':
                                                    echo 'Credit Card ending in ' . substr($method['cardNumber'], -4);
                                                    break;
                                                case 'paypal':
                                                    echo 'PayPal (' . $method['email'] . ')';
                                                    break;
                                                case 'crypto':
                                                    echo 'Crypto Wallet (' . substr($method['walletAddress'], 0, 6) . '...' . substr($method['walletAddress'], -4) . ')';
                                                    break;
                                            }
                                            ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" <?php echo empty($currentUser['paymentMethods']) ? 'disabled' : ''; ?>>Process Payment</button>
                    </form>
                </div>
                
                <!-- Add Payment Method Section -->
                <div class="card">
                    <h3>Add Payment Method</h3>
                    <form method="post">
                        <input type="hidden" name="action" value="addPaymentMethod">
                        <div class="form-group">
                            <label for="methodType">Payment Method Type</label>
                            <select id="methodType" name="methodType" required onchange="togglePaymentFields()">
                                <option value="credit_card">Credit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="crypto">Cryptocurrency</option>
                            </select>
                        </div>
                        
                        <!-- Credit Card Fields -->
                        <div id="creditCardFields">
                            <div class="form-group">
                                <label for="cardNumber">Card Number</label>
                                <input type="text" id="cardNumber" name="cardNumber" placeholder="XXXX-XXXX-XXXX-XXXX">
                            </div>
                            <div class="form-group">
                                <label for="expiryDate">Expiry Date</label>
                                <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="XXX">
                            </div>
                        </div>
                        
                        <!-- PayPal Fields -->
                        <div id="paypalFields" style="display: none;">
                            <div class="form-group">
                                <label for="email">PayPal Email</label>
                                <input type="email" id="email" name="email" placeholder="example@email.com">
                            </div>
                        </div>
                        
                        <!-- Crypto Fields -->
                        <div id="cryptoFields" style="display: none;">
                            <div class="form-group">
                                <label for="walletAddress">Wallet Address</label>
                                <input type="text" id="walletAddress" name="walletAddress" placeholder="0x...">
                            </div>
                        </div>
                        
                        <button type="submit">Add Payment Method</button>
                    </form>
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <!-- Process Refund Section -->
                <div class="card">
                    <h3>Process Refund</h3>
                    <form method="post">
                        <input type="hidden" name="action" value="refund">
                        <div class="form-group">
                            <label for="transactionId">Transaction ID</label>
                            <select id="transactionId" name="transactionId" required>
                                <option value="">Select transaction</option>
                                <?php 
                                $refundableTransactions = [];
                                foreach ($userTransactions as $transaction) {
                                    if ($transaction['status'] == 'completed' && $transaction['method'] != 'refund') {
                                        $refundableTransactions[] = $transaction;
                                    }
                                }
                                if (empty($refundableTransactions)): 
                                ?>
                                    <option value="" disabled>No refundable transactions available</option>
                                <?php else: ?>
                                    <?php foreach ($refundableTransactions as $transaction): ?>
                                        <option value="<?php echo $transaction['id']; ?>">
                                            <?php echo $transaction['id']; ?> - R<?php echo number_format($transaction['amount'], 2); ?> 
                                            (<?php echo ucfirst($transaction['method']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" <?php echo empty($refundableTransactions) ? 'disabled' : ''; ?>>Process Refund</button>
                    </form>
                </div>
                
                <!-- Transaction History Section -->
                <div class="card">
                    <h3>Transaction History</h3>
                    <?php if (empty($userTransactions)): ?>
                        <p>No transactions found.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Fee</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userTransactions as $transaction): ?>
                                    <tr class="<?php echo $transaction['status'] == 'refunded' ? 'refunded' : ''; ?>">
                                        <td><?php echo $transaction['id']; ?></td>
                                        <td><?php echo date('Y-m-d H:i', $transaction['timestamp']); ?></td>
                                        <td><?php echo ucfirst($transaction['method']); ?></td>
                                        <td>R<?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td>R<?php echo number_format($transaction['fee'], 2); ?></td>
                                        <td>R<?php echo number_format($transaction['total'], 2); ?></td>
                                        <td><?php echo ucfirst($transaction['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Test Cases Section -->
                <div class="card test-panel">
                    <h3>Test Cases</h3>
                    <table class="test-case-table">
                        <thead>
                            <tr>
                                <th>Test Case</th>
                                <th>Expected Output</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Alice pays R20,000 using Credit Card</td>
                                <td>✓ Payment successful, transaction fee deducted, updated balance displayed</td>
                            </tr>
                            <tr>
                                <td>Bob tries to pay R120,000 using PayPal</td>
                                <td>✗ Payment rejected due to fraud detection</td>
                            </tr>
                            <tr>
                                <td>Charlie pays R5,000 using Crypto</td>
                                <td>✓ Payment successful, fee applied, updated balance displayed</td>
                            </tr>
                            <tr>
                                <td>Alice requests a refund for a past transaction of R20,000</td>
                                <td>✓ Refund issued, amount added back to balance</td>
                            </tr>
                            <tr>
                                <td>User attempts to pay using an unsupported method (e.g., Bitcoin)</td>
                                <td>✗ Error: Invalid payment method</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        
        <div>
            <a href="?reset=1" class="reset-link">Reset System Data</a>
        </div>
    </div>
</body>
</html>