<?php
// Start session to track user data between page loads
session_start();

// System Configuration
define('CREDIT_CARD_FEE', 0.025); // 2.5%
define('PAYPAL_FEE', 0.035); // 3.5%
define('CRYPTO_FEE', 0.01); // 1%
define('FRAUD_THRESHOLD', 100000); // R100,000

// Class for payment method management
class PaymentMethodRegistry {
    private static $paymentMethods = [
        'credit_card' => [
            'name' => 'Credit Card',
            'fee_percentage' => CREDIT_CARD_FEE,
            'fields' => ['cardNumber', 'expiryDate', 'cvv']
        ],
        'paypal' => [
            'name' => 'PayPal', 
            'fee_percentage' => PAYPAL_FEE,
            'fields' => ['email']
        ],
        'crypto' => [
            'name' => 'Cryptocurrency',
            'fee_percentage' => CRYPTO_FEE,
            'fields' => ['walletAddress']
        ]
    ];
    
    public static function getAvailableMethods() {
        return self::$paymentMethods;
    }
    
    public static function isValidMethod($methodKey) {
        return isset(self::$paymentMethods[$methodKey]);
    }
    
    public static function getFeePercentage($methodKey) {
        return self::isValidMethod($methodKey) ? self::$paymentMethods[$methodKey]['fee_percentage'] : 0;
    }
    
    public static function getMethodName($methodKey) {
        return self::isValidMethod($methodKey) ? self::$paymentMethods[$methodKey]['name'] : 'Unknown';
    }
    
    public static function getRequiredFields($methodKey) {
        return self::isValidMethod($methodKey) ? self::$paymentMethods[$methodKey]['fields'] : [];
    }
    
    public static function registerNewMethod($key, $name, $feePercentage, $fields = []) {
        self::$paymentMethods[$key] = [
            'name' => $name,
            'fee_percentage' => $feePercentage,
            'fields' => $fields
        ];
    }
}

// Initialize or reset the system data for testing purposes
function initializeSystem() {
    // Clear any existing sessions
    if (isset($_GET['reset']) && $_GET['reset'] == 1) {
        session_unset();
    }
    
    // Initialize users if not already set
    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [
            'alice' => [
                'id' => 'U001',
                'name' => 'Alice',
                'balance' => 100000,
                'paymentMethods' => [
                    'cc1' => ['type' => 'credit_card', 'cardNumber' => '1234-5678-9012-3456', 'expiryDate' => '12/26', 'cvv' => '123'],
                ],
                'transactions' => []
            ],
            'bob' => [
                'id' => 'U002',
                'name' => 'Bob',
                'balance' => 200000,
                'paymentMethods' => [
                    'pp1' => ['type' => 'paypal', 'email' => 'bob@example.com'],
                ],
                'transactions' => []
            ],
            'charlie' => [
                'id' => 'U003',
                'name' => 'Charlie',
                'balance' => 50000,
                'paymentMethods' => [
                    'crypto1' => ['type' => 'crypto', 'walletAddress' => '0x742d35Cc6634C0532925a3b844Bc454e4438f44e'],
                ],
                'transactions' => []
            ]
        ];
        
        $_SESSION['transactions'] = [];
        $_SESSION['current_user'] = 'alice';
    }
}

// Transaction class to manage all transaction operations
class TransactionManager {
    public static function processPayment($userId, $paymentMethodKey, $amount) {
        $user = &$_SESSION['users'][$userId];
        $message = '';
        
        // Check if user exists
        if (!isset($user)) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Check if payment method exists
        if (!isset($user['paymentMethods'][$paymentMethodKey])) {
            return [
                'success' => false,
                'message' => 'Payment method not found'
            ];
        }
        
        $paymentMethod = $user['paymentMethods'][$paymentMethodKey];
        
        // Validate payment method
        if (!PaymentMethodRegistry::isValidMethod($paymentMethod['type'])) {
            return [
                'success' => false,
                'message' => 'Error: Invalid payment method'
            ];
        }
        
        // Fraud detection - reject payments above threshold
        if ($amount > FRAUD_THRESHOLD) {
            return [
                'success' => false,
                'message' => 'Payment rejected due to fraud detection: Amount exceeds R' . number_format(FRAUD_THRESHOLD, 2)
            ];
        }
        
        // Calculate fee
        $feePercentage = PaymentMethodRegistry::getFeePercentage($paymentMethod['type']);
        $fee = $amount * $feePercentage;
        $totalAmount = $amount + $fee;
        
        // Check if user has sufficient balance
        if ($user['balance'] < $totalAmount) {
            return [
                'success' => false,
                'message' => 'Payment failed: Insufficient balance'
            ];
        }
        
        // Create transaction
        $transactionId = uniqid('TX');
        $transaction = [
            'id' => $transactionId,
            'userId' => $user['id'],
            'userName' => $user['name'],
            'amount' => $amount,
            'fee' => $fee,
            'paymentMethod' => $paymentMethod['type'],
            'paymentMethodName' => PaymentMethodRegistry::getMethodName($paymentMethod['type']),
            'timestamp' => date('Y-m-d H:i:s'),
            'refunded' => false
        ];
        
        // Update user balance
        $user['balance'] -= $totalAmount;
        
        // Add transaction to history
        $user['transactions'][] = $transactionId;
        $_SESSION['transactions'][$transactionId] = $transaction;
        
        return [
            'success' => true,
            'transaction' => $transaction,
            'message' => 'Payment successful: R' . number_format($amount, 2) . 
                         ' + R' . number_format($fee, 2) . ' fee = R' . number_format($totalAmount, 2) . 
                         '. Updated balance: R' . number_format($user['balance'], 2)
        ];
    }
    
    public static function processRefund($transactionId) {
        // Find transaction by ID
        if (!isset($_SESSION['transactions'][$transactionId])) {
            return [
                'success' => false,
                'message' => 'Refund failed: Transaction not found'
            ];
        }
        
        $transaction = &$_SESSION['transactions'][$transactionId];
        
        if ($transaction['refunded']) {
            return [
                'success' => false,
                'message' => 'Refund failed: Transaction already refunded'
            ];
        }
        
        // Find user
        $userId = null;
        foreach ($_SESSION['users'] as $key => $user) {
            if ($user['id'] === $transaction['userId']) {
                $userId = $key;
                break;
            }
        }
        
        if ($userId === null) {
            return [
                'success' => false,
                'message' => 'Refund failed: User not found'
            ];
        }
        
        // Process refund
        $refundAmount = $transaction['amount'] + $transaction['fee'];
        $_SESSION['users'][$userId]['balance'] += $refundAmount;
        $transaction['refunded'] = true;
        
        return [
            'success' => true,
            'message' => 'Refund issued: R' . number_format($refundAmount, 2) . 
                         ' added back to ' . $transaction['userName'] . '\'s balance'
        ];
    }
    
    public static function getAllTransactions() {
        return $_SESSION['transactions'];
    }
    
    public static function getUserTransactions($userId) {
        $userTransactions = [];
        $user = $_SESSION['users'][$userId] ?? null;
        
        if ($user) {
            foreach ($user['transactions'] as $txId) {
                if (isset($_SESSION['transactions'][$txId])) {
                    $userTransactions[] = $_SESSION['transactions'][$txId];
                }
            }
        }
        
        // Sort by timestamp (newest first)
        usort($userTransactions, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return $userTransactions;
    }
    
    public static function findTransactionsByAmount($amount) {
        $matchingTransactions = [];
        
        foreach ($_SESSION['transactions'] as $transaction) {
            if ($transaction['amount'] == $amount) {
                $matchingTransactions[] = $transaction;
            }
        }
        
        return $matchingTransactions;
    }
}

// User management class
class UserManager {
    public static function getUser($userId) {
        return $_SESSION['users'][$userId] ?? null;
    }
    
    public static function getAllUsers() {
        return $_SESSION['users'];
    }
    
    public static function addPaymentMethod($userId, $type, $data) {
        if (!isset($_SESSION['users'][$userId])) {
            return false;
        }
        
        if (!PaymentMethodRegistry::isValidMethod($type)) {
            return false;
        }
        
        $requiredFields = PaymentMethodRegistry::getRequiredFields($type);
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        
        $methodKey = $type . '_' . uniqid();
        $methodData = ['type' => $type];
        foreach ($data as $key => $value) {
            $methodData[$key] = $value;
        }
        
        $_SESSION['users'][$userId]['paymentMethods'][$methodKey] = $methodData;
        return $methodKey;
    }
}

// Initialize the system
initializeSystem();

// Process form submissions
$message = '';
$messageType = '';
$testResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'payment':
                $result = TransactionManager::processPayment(
                    $_SESSION['current_user'],
                    $_POST['paymentMethod'],
                    floatval($_POST['amount'])
                );
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;

            case 'refund':
                $result = TransactionManager::processRefund($_POST['transactionId']);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
                
            case 'switchUser':
                $_SESSION['current_user'] = $_POST['user'];
                $message = 'Switched to ' . $_SESSION['users'][$_SESSION['current_user']]['name'];
                $messageType = 'success';
                break;
                
            case 'addPaymentMethod':
                $type = $_POST['methodType'];
                $data = [];
                $fields = PaymentMethodRegistry::getRequiredFields($type);
                foreach ($fields as $field) {
                    $data[$field] = $_POST[$field] ?? '';
                }
                
                $result = UserManager::addPaymentMethod($_SESSION['current_user'], $type, $data);
                if ($result) {
                    $message = 'Payment method added successfully';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to add payment method';
                    $messageType = 'error';
                }
                break;
                
            case 'runTest':
                $testResult = runTestCase($_POST['testCase']);
                $message = $testResult['message'];
                $messageType = $testResult['success'] ? 'success' : 'error';
                break;
        }
    }
}

// Function to run test cases
function runTestCase($testCase) {
    switch ($testCase) {
        case 'alice_pay':
            // Test Alice paying R20,000 with Credit Card
            foreach ($_SESSION['users']['alice']['paymentMethods'] as $methodKey => $method) {
                if ($method['type'] == 'credit_card') {
                    $result = TransactionManager::processPayment('alice', $methodKey, 20000);
                    return $result;
                }
            }
            return ['success' => false, 'message' => 'Test failed: Credit card method not found for Alice'];
            
        case 'bob_pay':
            // Test Bob trying to pay R120,000 with PayPal
            foreach ($_SESSION['users']['bob']['paymentMethods'] as $methodKey => $method) {
                if ($method['type'] == 'paypal') {
                    $result = TransactionManager::processPayment('bob', $methodKey, 120000);
                    return $result;
                }
            }
            return ['success' => false, 'message' => 'Test failed: PayPal method not found for Bob'];
            
        case 'charlie_pay':
            // Test Charlie paying R5,000 with Crypto
            foreach ($_SESSION['users']['charlie']['paymentMethods'] as $methodKey => $method) {
                if ($method['type'] == 'crypto') {
                    $result = TransactionManager::processPayment('charlie', $methodKey, 5000);
                    return $result;
                }
            }
            return ['success' => false, 'message' => 'Test failed: Crypto method not found for Charlie'];
            
        case 'alice_refund':
            // Find Alice's R20,000 transaction and refund it
            $transactions = TransactionManager::getUserTransactions('alice');
            $transactionId = null;
            
            foreach ($transactions as $tx) {
                if ($tx['amount'] == 20000 && !$tx['refunded']) {
                    $transactionId = $tx['id'];
                    break;
                }
            }
            
            if ($transactionId) {
                $result = TransactionManager::processRefund($transactionId);
                return $result;
            }
            return ['success' => false, 'message' => 'Test failed: No eligible R20,000 transaction found for Alice'];
            
        case 'unsupported_method':
            // Add an unsupported payment method temporarily for testing
            $result = ['success' => false, 'message' => 'Error: Invalid payment method'];
            return $result;
            
        case 'transaction_history':
            // Get all transactions
            $allTransactions = TransactionManager::getAllTransactions();
            $count = count($allTransactions);
            return ['success' => true, 'message' => "Found $count transactions in the system"];
            
        default:
            return ['success' => false, 'message' => 'Unknown test case'];
    }
}

// Get current user data
$currentUser = $_SESSION['users'][$_SESSION['current_user']];
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
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="switch-user">
            <form method="post">
                <input type="hidden" name="action" value="switchUser">
                <select name="user">
                    <?php foreach ($_SESSION['users'] as $userId => $user): ?>
                        <option value="<?php echo $userId; ?>" <?php echo $userId === $_SESSION['current_user'] ? 'selected' : ''; ?>>
                            <?php echo $user['name']; ?> (<?php echo $user['id']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Switch User</button>
            </form>
        </div>
        
        <main>
            <section>
                <div class="card">
                    <h2>Make a Payment</h2>
                    <form method="post">
                        <input type="hidden" name="action" value="payment">
                        
                        <div class="form-group">
                            <label for="amount">Amount (R)</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" name="paymentMethod" required>
                                <?php foreach ($currentUser['paymentMethods'] as $key => $method): ?>
                                    <option value="<?php echo $key; ?>">
                                        <?php echo PaymentMethodRegistry::getMethodName($method['type']); ?> 
                                        <?php 
                                            if ($method['type'] == 'credit_card') {
                                                echo '(xxxx-xxxx-xxxx-' . substr($method['cardNumber'], -4) . ')';
                                            } elseif ($method['type'] == 'paypal') {
                                                echo '(' . $method['email'] . ')';
                                            } elseif ($method['type'] == 'crypto') {
                                                echo '(' . substr($method['walletAddress'], 0, 10) . '...)';
                                            }
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit">Process Payment</button>
                    </form>
                </div>
                
                <div class="card">
                    <h2>Process Refund</h2>
                    <form method="post">
                        <input type="hidden" name="action" value="refund">
                        
                        <div class="form-group">
                            <label for="transactionId">Transaction ID</label>
                            <input type="text" id="transactionId" name="transactionId" required>
                        </div>
                        
                        <button type="submit" class="danger">Process Refund</button>
                    </form>
                </div>
                
                <div class="card">
                    <h2>Add Payment Method</h2>
                    <form method="post" id="addMethodForm">
                        <input type="hidden" name="action" value="addPaymentMethod">
                        
                        <div class="form-group">
                            <label for="methodType">Payment Method Type</label>
                            <select id="methodType" name="methodType" required onchange="showRelevantFields()">
                                <?php foreach (PaymentMethodRegistry::getAvailableMethods() as $key => $method): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $method['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Credit Card Fields -->
                        <div id="creditCardFields" class="method-fields">
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
                        <div id="paypalFields" class="method-fields" style="display:none;">
                            <div class="form-group">
                                <label for="email">PayPal Email</label>
                                <input type="email" id="email" name="email" placeholder="example@email.com">
                            </div>
                        </div>
                        
                        <!-- Crypto Fields -->
                        <div id="cryptoFields" class="method-fields" style="display:none;">
                            <div class="form-group">
                                <label for="walletAddress">Wallet Address</label>
                                <input type="text" id="walletAddress" name="walletAddress" placeholder="0x...">
                            </div>
                        </div>
                        
                        <button type="submit">Add Payment Method</button>
                    </form>
                </div>
            </section>
            
            <section>
                <div class="card">
                    <h2>Transaction History</h2>
                    <?php 
                    // Get user transactions
                    $userTransactions = TransactionManager::getUserTransactions($_SESSION['current_user']);
                    ?>
                    
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
                                <?php foreach ($userTransactions as $tx): ?>
                                    <tr>
                                        <td><?php echo $tx['id']; ?></td>
                                        <td><?php echo $tx['timestamp']; ?></td>
                                        <td><?php echo $tx['paymentMethodName']; ?></td>
                                        <td>R<?php echo number_format($tx['amount'], 2); ?></td>
                                        <td>R<?php echo number_format($tx['fee'], 2); ?></td>
                                        <td>R<?php echo number_format($tx['amount'] + $tx['fee'], 2); ?></td>
                                        <td><?php echo $tx['refunded'] ? '<span class="refunded">Refunded</span>' : 'Completed'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <div class="card test-panel">
                    <h2>Test Cases</h2>
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
                                <td><span class="success-icon">✓</span> Payment successful, transaction fee deducted, updated balance displayed</td>
                            </tr>
                            <tr>
                                <td>Bob tries to pay R120,000 using PayPal</td>
                                <td><span class="warning-icon">▲</span> Payment rejected due to fraud detection</td>
                            </tr>
                            <tr>
                                <td>Charlie pays R5,000 using Crypto</td>
                                <td><span class="success-icon">✓</span> Payment successful, fee applied, updated balance displayed</td>
                            </tr>
                            <tr>
                                <td>Alice requests a refund for a past transaction of R20,000</td>