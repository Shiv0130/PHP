<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyoncé Concert - December 25, 2025</title>
    <style>
        :root {
            --primary: #f2a900;
            --secondary: #000;
            --light: #f9f9f9;
            --danger: #dc3545;
            --success: #28a745;
            --dark: #333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #111;
            color: var(--light);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background-color: var(--secondary);
            color: var(--primary);
            padding: 20px 0;
            text-align: center;
            border-bottom: 4px solid var(--primary);
        }
        
        .event-details {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: center;
        }
        
        .ticket-info {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        
        .ticket-category {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            margin: 10px;
            border-radius: 10px;
            width: 30%;
            min-width: 250px;
            text-align: center;
            border: 1px solid var(--primary);
        }
        
        .booking-form {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            margin: 20px 0;
            border-radius: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: var(--light);
            font-size: 16px;
        }
        
        button {
            background-color: var(--primary);
            color: var(--secondary);
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .alert-success {
            background-color: var(--success);
            color: white;
        }
        
        .stats {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            margin: 20px 0;
            border-radius: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid var(--primary);
        }
        
        .receipt {
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--dark);
            padding: 30px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .seat-numbers {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .seat-number {
            background-color: var(--primary);
            color: var(--secondary);
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        footer {
            background-color: var(--secondary);
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            border-top: 4px solid var(--primary);
        }
        
        .sold-out-message {
            background-color: var(--danger);
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php
// Initialize variables
$errors = [];
$success = false;

// Define ticket categories and prices
$ticketCategories = [
    'VVIP' => 3000,
    'VIP' => 2000,
    'General' => 500
];

// Define age groups for statistics
$ageGroups = [
    '16-25',
    '26-35',
    '36-45',
    '46-55',
    '56+'
];

// Total venue capacity
$venueCapacity = 60000;

// Initialize or retrieve ticket sales data
if (!isset($_SESSION['ticketSales'])) {
    $_SESSION['ticketSales'] = [
        'totalSold' => 0,
        'vvip' => 0,
        'vip' => 0,
        'general' => 0,
        'demographics' => [
            'male' => [
                '16-25' => 0,
                '26-35' => 0,
                '36-45' => 0,
                '46-55' => 0,
                '56+' => 0
            ],
            'female' => [
                '16-25' => 0,
                '26-35' => 0,
                '36-45' => 0,
                '46-55' => 0,
                '56+' => 0
            ],
            'other' => [
                '16-25' => 0,
                '26-35' => 0,
                '36-45' => 0,
                '46-55' => 0,
                '56+' => 0
            ]
        ],
        'soldSeats' => []
    ];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate full name
    if (empty($_POST['fullName'])) {
        $errors[] = "Full name is required";
    }
    
    // Validate email
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate age
    if (empty($_POST['age'])) {
        $errors[] = "Age is required";
    } elseif (!is_numeric($_POST['age'])) {
        $errors[] = "Age must be a number";
    } elseif ((int)$_POST['age'] < 16) {
        $errors[] = "You must be at least 16 years old to purchase tickets";
    }
    
    // Validate gender
    if (empty($_POST['gender'])) {
        $errors[] = "Gender is required";
    }
    
    // Validate ticket category
    if (empty($_POST['ticketCategory'])) {
        $errors[] = "Ticket category is required";
    } elseif (!array_key_exists($_POST['ticketCategory'], $ticketCategories)) {
        $errors[] = "Invalid ticket category";
    }
    
    // Validate number of tickets
    if (empty($_POST['numTickets'])) {
        $errors[] = "Number of tickets is required";
    } elseif (!is_numeric($_POST['numTickets']) || (int)$_POST['numTickets'] <= 0) {
        $errors[] = "Number of tickets must be a positive number";
    } elseif ((int)$_POST['numTickets'] > 6) {
        $errors[] = "Maximum 6 tickets per purchase";
    }
    
    // Check if venue capacity would be exceeded
    $requestedTickets = (int)$_POST['numTickets'];
    if ($_SESSION['ticketSales']['totalSold'] + $requestedTickets > $venueCapacity) {
        $remainingTickets = $venueCapacity - $_SESSION['ticketSales']['totalSold'];
        if ($remainingTickets > 0) {
            $errors[] = "Only $remainingTickets tickets left. Please reduce your quantity.";
        } else {
            $errors[] = "Sorry, the concert is sold out.";
        }
    }
    
    // Process valid submission
    if (empty($errors)) {
        $fullName = htmlspecialchars($_POST['fullName']);
        $email = htmlspecialchars($_POST['email']);
        $age = (int)$_POST['age'];
        $gender = htmlspecialchars($_POST['gender']);
        $ticketCategory = $_POST['ticketCategory'];
        $numTickets = (int)$_POST['numTickets'];
        $ticketPrice = $ticketCategories[$ticketCategory];
        $totalPrice = $numTickets * $ticketPrice;
        
        // Determine age group
        $ageGroup = '';
        if ($age >= 16 && $age <= 25) $ageGroup = '16-25';
        elseif ($age <= 35) $ageGroup = '26-35';
        elseif ($age <= 45) $ageGroup = '36-45';
        elseif ($age <= 55) $ageGroup = '46-55';
        else $ageGroup = '56+';
        
        // Generate seat numbers
        $seatNumbers = [];
        for ($i = 0; $i < $numTickets; $i++) {
            do {
                // Generate random seat number between 1 and venue capacity
                $seatNumber = mt_rand(1, $venueCapacity);
            } while (in_array($seatNumber, $_SESSION['ticketSales']['soldSeats']));
            
            $seatNumbers[] = $seatNumber;
            $_SESSION['ticketSales']['soldSeats'][] = $seatNumber;
        }
        
        // Update sales statistics
        $_SESSION['ticketSales']['totalSold'] += $numTickets;
        $_SESSION['ticketSales'][strtolower($ticketCategory)] += $numTickets;
        $_SESSION['ticketSales']['demographics'][$gender][$ageGroup] += $numTickets;
        
        // Save purchase info for receipt
        $_SESSION['lastPurchase'] = [
            'name' => $fullName,
            'email' => $email,
            'category' => $ticketCategory,
            'quantity' => $numTickets,
            'price' => $ticketPrice,
            'total' => $totalPrice,
            'seats' => $seatNumbers,
            'date' => date('Y-m-d H:i:s')
        ];
        
        $success = true;
    }
}

// Calculate remaining tickets
$remainingTickets = $venueCapacity - $_SESSION['ticketSales']['totalSold'];
$soldOutStatus = $remainingTickets <= 0;

// Function to pre-populate the test data if needed
function populateTestData() {
    // Add female 16-21 (5 tickets)
    $_SESSION['ticketSales']['totalSold'] += 5;
    $_SESSION['ticketSales']['general'] += 5;
    $_SESSION['ticketSales']['demographics']['female']['16-25'] += 5;
    
    // Add female 22-35 (5 tickets)
    $_SESSION['ticketSales']['totalSold'] += 5;
    $_SESSION['ticketSales']['vip'] += 5;
    $_SESSION['ticketSales']['demographics']['female']['26-35'] += 5;
    
    // Add male 16-21 (6 tickets)
    $_SESSION['ticketSales']['totalSold'] += 6;
    $_SESSION['ticketSales']['vvip'] += 6;
    $_SESSION['ticketSales']['demographics']['male']['16-25'] += 6;
    
    // Add male 22-35 (4 tickets)
    $_SESSION['ticketSales']['totalSold'] += 4;
    $_SESSION['ticketSales']['general'] += 4;
    $_SESSION['ticketSales']['demographics']['male']['26-35'] += 4;
}

// Uncomment this line to automatically populate test data
// populateTestData();
?>

    <header>
        <div class="container">
            <h1>Beyoncé Concert</h1>
            <p>December 25, 2025 | The Ultimate Experience</p>
        </div>
    </header>
    
    <div class="container">
        <div class="event-details">
            <h2>Event Details</h2>
            <p>Join us for an unforgettable night with Beyoncé, live in concert on Christmas Day 2025!</p>
            <p>Venue capacity: 60,000 attendees</p>
            <p><strong>Remaining tickets: <?php echo $remainingTickets; ?></strong></p>
            
            <div class="ticket-info">
                <div class="ticket-category">
                    <h3>VVIP</h3>
                    <div class="price">R3000</div>
                    <p>Premium seating, exclusive merchandise, and meet & greet opportunity</p>
                </div>
                
                <div class="ticket-category">
                    <h3>VIP</h3>
                    <div class="price">R2000</div>
                    <p>Priority seating and exclusive merchandise</p>
                </div>
                
                <div class="ticket-category">
                    <h3>General Admission</h3>
                    <div class="price">R500</div>
                    <p>Standard seating with good views</p>
                </div>
            </div>
        </div>
        
        <?php if ($soldOutStatus): ?>
        <div class="sold-out-message">
            Sorry, all tickets for this concert have been sold out!
        </div>
        <?php else: ?>
        
        <?php if ($success): ?>
        <div class="receipt">
            <h2>Ticket Purchase Confirmation</h2>
            <div class="receipt-details">
                <div class="receipt-row">
                    <span class="receipt-label">Name:</span>
                    <span><?php echo $_SESSION['lastPurchase']['name']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Email:</span>
                    <span><?php echo $_SESSION['lastPurchase']['email']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Ticket Category:</span>
                    <span><?php echo $_SESSION['lastPurchase']['category']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Quantity:</span>
                    <span><?php echo $_SESSION['lastPurchase']['quantity']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Price Per Ticket:</span>
                    <span>R<?php echo $_SESSION['lastPurchase']['price']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Purchase Date:</span>
                    <span><?php echo $_SESSION['lastPurchase']['date']; ?></span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Seat Numbers:</span>
                    <div class="seat-numbers">
                        <?php foreach ($_SESSION['lastPurchase']['seats'] as $seat): ?>
                        <span class="seat-number">Seat #<?php echo $seat; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="receipt-total">
                Total Amount: R<?php echo $_SESSION['lastPurchase']['total']; ?>
            </div>
            <p style="text-align: center; margin-top: 20px;">Thank you for your purchase!</p>
        </div>
        <?php endif; ?>
        
        <div class="booking-form">
            <h2>Book Your Tickets</h2>
            
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="list-style-type: none;">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" min="16" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="ticketCategory">Ticket Category</label>
                    <select id="ticketCategory" name="ticketCategory" required>
                        <option value="">Select Ticket Category</option>
                        <option value="VVIP">VVIP - R3000</option>
                        <option value="VIP">VIP - R2000</option>
                        <option value="General">General Admission - R500</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="numTickets">Number of Tickets (Max 6)</label>
                    <input type="number" id="numTickets" name="numTickets" min="1" max="6" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Book Now</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="stats">
            <h2>Ticket Sales Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>Total Sold</h3>
                    <p><?php echo $_SESSION['ticketSales']['totalSold']; ?> / <?php echo $venueCapacity; ?></p>
                </div>
                <div class="stat-item">
                    <h3>VVIP Tickets</h3>
                    <p><?php echo $_SESSION['ticketSales']['vvip']; ?></p>
                </div>
                <div class="stat-item">
                    <h3>VIP Tickets</h3>
                    <p><?php echo $_SESSION['ticketSales']['vip']; ?></p>
                </div>
                <div class="stat-item">
                    <h3>General Admission</h3>
                    <p><?php echo $_SESSION['ticketSales']['general']; ?></p>
                </div>
            </div>
            
            <!-- Demographic breakdown -->
            <h3 style="margin-top: 20px; text-align: center;">Demographics</h3>
            <div class="stats-grid">
                <?php foreach (array('female', 'male', 'other') as $gender): ?>
                    <?php foreach ($ageGroups as $ageGroup): ?>
                        <div class="stat-item">
                            <h3><?php echo ucfirst($gender); ?> (<?php echo $ageGroup; ?>)</h3>
                            <p><?php echo $_SESSION['ticketSales']['demographics'][$gender][$ageGroup]; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Beyoncé Concert. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>