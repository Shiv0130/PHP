<?php
// Function to calculate day of the week
function getDayOfWeek($m, $d) {
    // Array of days indexed by day number
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // Get timestamp for the input date
    $timestamp = strtotime("$m/$d/2024");

    // Use date function to get day of the week
    $dayOfWeek = date('w', $timestamp);

    // Return the corresponding day of the week
    return $days[$dayOfWeek];
}

// Function to decompress the string
function decompressString($compString) {
    // Initialize an empty string to store decompressed result
    $decompString = '';

    // Split the compressed string into characters
    $chars = str_split($compString);

    // Initialize variables to keep track of compressed character and count
    $count = 0;
    $char = '';

    // Loop through each character
    foreach ($chars as $ch) {
        if ($ch === '#') { // Check for compressed character indicator
            $count = 0; 
        } elseif (ctype_digit($ch)) { // Check if character is a digit
            $count = $count * 10 + intval($ch); 
        } else { 
            if ($count > 0) {
                $decompString .= str_repeat($char, $count); // Repeat character based on count
            }
            $char = $ch; // Update current character
            $count = 1; 
        }
    }

    // Append last character if count is greater than 0
    if ($count > 0) {
        $decompString .= str_repeat($char, $count);
    }

    return $decompString;
}

// Example input for date
$month = 1;
$day = 5;

// Output the day of the week for the given date
echo "Day of the week for $month/$day/2024: " . getDayOfWeek($month, $day) . "<br>";

// Example input for decompressed string
$compString = '88888888 + 1 = 100000000';

// Output the decompressed string
echo "Decompressed String: " . decompressString($compString) . "<br>";



?>