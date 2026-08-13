<?php
/**
 * Beyoncé Concert Booking System
 * Concert Date: December 25, 2025
 * Venue Capacity: 60,000 attendees
 */

class ConcertBookingSystem {
    // Changed from private to public for HTML access
    public $venueCapacity = 60000;
    public $ticketPrices = [
        'VVIP' => 3000,
        'VIP' => 2000,
        'General Admission' => 500
    ];
    private $ticketsSold = [
        'VVIP' => 0,
        'VIP' => 0,
        'General Admission' => 0
    ];
    private $totalSales = 0;
    private $buyerDemographics = [
        'Female' => [
            '16-21' => 0,
            '22-35' => 0,
            '36-50' => 0,
            '51+' => 0
        ],
        'Male' => [
            '16-21' => 0,
            '22-35' => 0,
            '36-50' => 0,
            '51+' => 0
        ],
        'Other' => [
            '16-21' => 0,
            '22-35' => 0,
            '36-50' => 0,
            '51+' => 0
        ]
    ];
    private $seats = [];
    private $soldSeats = [];

    public function __construct() {
        // Initialize seats for each ticket type
        $this->seats = [
            'VVIP' => $this->generateSeats(5000),  // Allocating first 5000 seats to VVIP
            'VIP' => $this->generateSeats(15000),  // Next 15000 seats for VIP
            'General Admission' => $this->generateSeats(40000)  // Remaining 40000 seats for General Admission
        ];
        
        // Initialize sold seats tracking
        $this->soldSeats = [
            'VVIP' => [],
            'VIP' => [],
            'General Admission' => []
        ];
    }

    private function generateSeats($count) {
        $seats = [];
        for ($i = 1; $i <= $count; $i++) {
            $seats[] = $i;
        }
        return $seats;
    }

    private function determineAgeGroup($age) {
        if ($age >= 16 && $age <= 21) {
            return '16-21';
        } elseif ($age >= 22 && $age <= 35) {
            return '22-35';
        } elseif ($age >= 36 && $age <= 50) {
            return '36-50';
        } else {
            return '51+';
        }
    }

    public function purchaseTicket($ticketType, $age, $gender) {
        // Check if the person is at least 16 years old
        if ($age < 16) {
            return [
                'status' => 'Error',
                'message' => 'Tickets cannot be sold to individuals under 16 years old.'
            ];
        }
        
        // Check if the ticket type is valid
        if (!isset($this->ticketPrices[$ticketType])) {
            return [
                'status' => 'Error',
                'message' => 'Invalid ticket type. Available types: ' . implode(', ', array_keys($this->ticketPrices))
            ];
        }
        
        // Check if there are tickets available for this type
        if (count($this->soldSeats[$ticketType]) >= count($this->seats[$ticketType])) {
            return [
                'status' => 'Error',
                'message' => "Sorry, all $ticketType tickets are sold out."
            ];
        }
        
        // Check if the venue has reached capacity
        $totalTicketsSold = array_sum(array_map('count', $this->soldSeats));
        if ($totalTicketsSold >= $this->venueCapacity) {
            return [
                'status' => 'Error',
                'message' => 'Sorry, the concert is sold out.'
            ];
        }
        
        // Find an available seat
        $availableSeats = array_diff($this->seats[$ticketType], $this->soldSeats[$ticketType]);
        if (empty($availableSeats)) {
            return [
                'status' => 'Error',
                'message' => "No more $ticketType seats available."
            ];
        }
        
        $seatNumber = min($availableSeats);  // Get the lowest available seat number
        $this->soldSeats[$ticketType][] = $seatNumber;
        
        // Update sales information
        $this->ticketsSold[$ticketType]++;
        $this->totalSales += $this->ticketPrices[$ticketType];
        
        // Update demographics
        $ageGroup = $this->determineAgeGroup($age);
        if (!isset($this->buyerDemographics[$gender])) {
            $gender = 'Other';
        }
        $this->buyerDemographics[$gender][$ageGroup]++;
        
        return [
            'status' => 'Success',
            'message' => 'Ticket purchased successfully!',
            'details' => [
                'ticket_type' => $ticketType,
                'seat_number' => $seatNumber,
                'price' => $this->ticketPrices[$ticketType]
            ]
        ];
    }
    
    public function getSalesSummary() {
        return [
            'tickets_sold_by_type' => $this->ticketsSold,
            'total_tickets_sold' => array_sum($this->ticketsSold),
            'total_sales_amount' => $this->totalSales,
            'demographics' => $this->buyerDemographics
        ];
    }
    
    public function getAvailableTickets() {
        $available = [];
        foreach ($this->ticketPrices as $type => $price) {
            $available[$type] = count($this->seats[$type]) - count($this->soldSeats[$type]);
        }
        return $available;
    }

    public function validateTestData() {
        $testData = [
            'Female' => [
                '16-21' => 5,
                '22-35' => 5
            ],
            'Male' => [
                '16-21' => 6,
                '22-35' => 4
            ]
        ];
        
        foreach ($testData as $gender => $ageGroups) {
            foreach ($ageGroups as $ageGroup => $expected) {
                $actual = $this->buyerDemographics[$gender][$ageGroup];
                if ($actual != $expected) {
                    return false;
                }
            }
        }
        return true;
    }
    
    // Add a new method to get ticket types for the form
    public function getTicketTypes() {
        $types = [];
        foreach ($this->ticketPrices as $type => $price) {
            $types[] = [
                'name' => $type,
                'price' => $price
            ];
        }
        return $types;
    }
}

// Create a simple web interface for the booking system
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyoncé Concert Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .ticket-form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .summary {
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Beyoncé Concert Booking</h1>
        <h2>December 25, 2025</h2>
        
        <?php
        // Initialize booking system
        $bookingSystem = new ConcertBookingSystem();
        $message = null;
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
            $ticketType = $_POST['ticket_type'] ?? '';
            $age = intval($_POST['age'] ?? 0);
            $gender = $_POST['gender'] ?? '';
            
            $result = $bookingSystem->purchaseTicket($ticketType, $age, $gender);
            
            if ($result['status'] === 'Success') {
                $message = [
                    'type' => 'success',
                    'text' => $result['message'] . ' Seat #' . $result['details']['seat_number'] . ' - R' . $result['details']['price']
                ];
            } else {
                $message = [
                    'type' => 'error',
                    'text' => $result['message']
                ];
            }
        }
        
        // For demo purposes, load test data
        if (isset($_GET['loadTestData'])) {
            // Female, 16-21 (5 tickets)
            for ($i = 0; $i < 5; $i++) {
                $age = rand(16, 21);
                $ticketTypes = array_keys($bookingSystem->ticketPrices);
                $ticketType = $ticketTypes[array_rand($ticketTypes)];
                $bookingSystem->purchaseTicket($ticketType, $age, 'Female');
            }
            
            // Female, 22-35 (5 tickets)
            for ($i = 0; $i < 5; $i++) {
                $age = rand(22, 35);
                $ticketTypes = array_keys($bookingSystem->ticketPrices);
                $ticketType = $ticketTypes[array_rand($ticketTypes)];
                $bookingSystem->purchaseTicket($ticketType, $age, 'Female');
            }
            
            // Male, 16-21 (6 tickets)
            for ($i = 0; $i < 6; $i++) {
                $age = rand(16, 21);
                $ticketTypes = array_keys($bookingSystem->ticketPrices);
                $ticketType = $ticketTypes[array_rand($ticketTypes)];
                $bookingSystem->purchaseTicket($ticketType, $age, 'Male');
            }
            
            // Male, 22-35 (4 tickets)
            for ($i = 0; $i < 4; $i++) {
                $age = rand(22, 35);
                $ticketTypes = array_keys($bookingSystem->ticketPrices);
                $ticketType = $ticketTypes[array_rand($ticketTypes)];
                $bookingSystem->purchaseTicket($ticketType, $age, 'Male');
            }
            
            $message = [
                'type' => 'success',
                'text' => 'Test data loaded successfully!'
            ];
        }
        
        // Display message if any
        if ($message) {
            echo '<div class="message ' . $message['type'] . '">' . $message['text'] . '</div>';
        }
        ?>
        
        <div class="ticket-form">
            <h3>Purchase Ticket</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="ticket_type">Ticket Type:</label>
                    <select id="ticket_type" name="ticket_type" required>
                        <?php foreach ($bookingSystem->ticketPrices as $type => $price): ?>
                            <option value="<?php echo $type; ?>"><?php echo $type; ?> - R<?php echo $price; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" min="1" max="120" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <button type="submit" name="purchase">Purchase Ticket</button>
            </form>
        </div>
        
        <div class="summary">
            <h3>Sales Summary</h3>
            <?php
            $summary = $bookingSystem->getSalesSummary();
            $available = $bookingSystem->getAvailableTickets();
            ?>
            
            <h4>Tickets Sold by Type</h4>
            <table>
                <tr>
                    <th>Ticket Type</th>
                    <th>Price</th>
                    <th>Sold</th>
                    <th>Available</th>
                </tr>
                <?php foreach ($bookingSystem->ticketPrices as $type => $price): ?>
                <tr>
                    <td><?php echo $type; ?></td>
                    <td>R<?php echo $price; ?></td>
                    <td><?php echo $summary['tickets_sold_by_type'][$type]; ?></td>
                    <td><?php echo $available[$type]; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <h4>Demographics</h4>
            <table>
                <tr>
                    <th>Gender</th>
                    <th>Age Group</th>
                    <th>Count</th>
                </tr>
                <?php foreach ($summary['demographics'] as $gender => $ageGroups): ?>
                    <?php foreach ($ageGroups as $ageGroup => $count): ?>
                        <?php if ($count > 0): ?>
                        <tr>
                            <td><?php echo $gender; ?></td>
                            <td><?php echo $ageGroup; ?></td>
                            <td><?php echo $count; ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </table>
            
            <p>Total Tickets Sold: <?php echo $summary['total_tickets_sold']; ?></p>
            <p>Total Sales Amount: R<?php echo $summary['total_sales_amount']; ?></p>
            
            <?php if ($bookingSystem->validateTestData()): ?>
            <div class="message success">Test data validation: Passed</div>
            <?php else: ?>
            <div class="message error">Test data validation: Failed</div>
            <?php endif; ?>
            
            <p><a href="?loadTestData=1">Load Test Data</a></p>
        </div>
    </div>
</body>
</html>