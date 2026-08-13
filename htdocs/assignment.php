<?php 
// Function to convert seconds to minutes and seconds
function secondsToMinutesSeconds($seconds) {
    $minutes = floor($seconds / 60); // Calculate minutes
    $remainingSeconds = $seconds % 60; // Calculate remaining seconds
    echo "$minutes minute(s) and $remainingSeconds second(s)";
}

// Function to convert seconds to hours, minutes, and seconds
function secondsToHoursMinutesSeconds($seconds) {
    $hours = floor($seconds / 3600); // Calculate hours
    $remainingSeconds = $seconds % 3600; // Calculate remaining seconds
    secondsToMinutesSeconds($remainingSeconds); // Call function to convert remaining seconds to minutes and seconds
    echo " ($hours hour(s))";
}

// Function to convert seconds to days, hours, minutes, and seconds
function secondsToDaysHoursMinutesSeconds($seconds) {
    $days = floor($seconds / 86400); // Calculate days
    $remainingSeconds = $seconds % 86400; // Calculate remaining seconds
    secondsToHoursMinutesSeconds($remainingSeconds); // Call function to convert remaining seconds to hours, minutes, and seconds
    echo " ($days day(s))";
}

// Enter the number of seconds
$seconds =$_POST["number1"]?? null;
if($seconds<0){
    echo "Please enter a positive number of seconds";
} else{
if ($seconds >= 86400) {
    secondsToDaysHoursMinutesSeconds($seconds); // Call function to convert seconds to days, hours, minutes, and seconds
} elseif ($seconds >= 3600) {
    secondsToHoursMinutesSeconds($seconds); // Call function to convert seconds to hours, minutes, and seconds
} elseif ($seconds >= 60) {
    secondsToMinutesSeconds($seconds); // Call function to convert seconds to minutes and seconds
} else {
    echo "The entered value is less than 60 seconds.<br>";
}
}


//1.2.//
for ($i = 0; $i < 6; $i++) {         // Outer loop for the rows
    echo "#";                          // Print a '#' at the beginning of each row
    for ($j = 0; $j < $i; $j++) {      // Inner loop for printing spaces
      echo "&nbsp;&nbsp;";             // Print two spaces for each iteration
    }
    echo "#<br>";                      // Print a '#' at the end of each row followed by a line break
  }
  
?> 
