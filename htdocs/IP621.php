<?php
//1.1.
// Nested loop to construct the pattern
 $rows = 5; // Define the number of rows for the pattern

 // Loop for upper half of the pattern
 for ($i = 1; $i <= $rows; $i++) {
     for ($j = 1; $j <= $i; $j++) {
         echo "* ";
     }
     echo "<br>";
 }

 // Loop for lower half of the pattern
 for ($i = $rows - 1; $i >= 1; $i--) {
     for ($j = 1; $j <= $i; $j++) {
         echo "* ";
     }
     echo "<br>";
}

// function factorial($n) {
//     if ($n === 0 || $n === 1) {
//         return 1; // Factorial of 0 and 1 is 1
//     } else {
//         return $n * factorial($n - 1); // Recursive call to calculate factorial
//     }
// }

// // Test the function with a number (e.g., 4)
// $number = 4;
// echo "Factorial of $number is: " . factorial($number);

// Get the current day of the week as a number (0 for Sunday, 1 for Monday, ..., 6 for Saturday)
/*$currentDay = date("w");

// Using if-else statements
if ($currentDay == 1) {
    echo "This is Monday";
} elseif ($currentDay == 2) {
    echo "This is Tuesday";
} elseif ($currentDay == 3) {
    echo "This is Wednesday";
} elseif ($currentDay == 4) {
    echo "This is Thursday";
} elseif ($currentDay == 5) {
    echo "Have a nice weekend!";
} elseif ($currentDay == 6) {
    echo "Let’s turn up";
} elseif ($currentDay == 0) {
    echo "Have a nice Sunday!";
} else {
    echo "Have a nice day!";
}*/

// Using switch statement (alternative approach)
$currentday=0;
switch ($currentDay) {
    case 1:
        echo "This is Monday";
        break;
    case 2:
        echo "This is Tuesday";
        break;
    case 3:
        echo "This is Wednesday";
        break;
    case 4:
        echo "This is Thursday";
        break;
    case 5:
        echo "Have a nice weekend!";
        break;
    case 6:
        echo "Let’s turn up";
        break;
    case 0:
        echo "Have a nice Sunday!";
        break;
    default:
        echo "Have a nice day!";
}




// Define the function to reverse a string
function reverseString($str1) {
    // Using strrev() function to reverse the string
    $reversedString = strrev($str1);
    return $reversedString;
}

// Test the function with the provided string "1234"
$str1 = "1234";
$reversed = reverseString($str1);
echo "Reversed string: " . $reversed;


?>


