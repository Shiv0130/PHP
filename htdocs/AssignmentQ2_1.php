<?php
// Function to calculate average of test scores
function calc_average($scores) {
    $total = array_sum($scores); // Calculate the sum of all test scores
    $average = $total / count($scores); // Calculate the average by dividing the total by the number of scores
    return $average; // Return the calculated average
}

// Function to determine letter grade based on score
function determine_grade($score) {
    if ($score >= 90) { // If the score is 90 or above, assign grade 'A'
        return 'A';
    } elseif ($score >= 80) { // If the score is between 80 and 89, assign grade 'B'
        return 'B';
    } elseif ($score >= 70) { // If the score is between 70 and 79, assign grade 'C'
        return 'C';
    } elseif ($score >= 60) { // If the score is between 60 and 69, assign grade 'D'
        return 'D';
    } else { // If the score is below 60, assign grade 'F'
        return 'F';
    }
}

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the form has been submitted via POST method
    // Retrieve test scores from form and store them in an array
    $scores = array(
        $_POST['score1'],
        $_POST['score2'],
        $_POST['score3'],
        $_POST['score4'],
        $_POST['score5']
    );

    // Calculate average score using the calc_average function
    $average_score = calc_average($scores);

    // Display grades and average score
    echo "<h2>Test Scores and Grades</h2>"; // Display heading for the test scores and grades section
    echo "<p>Average Score: " . $average_score . "</p>"; // Display the calculated average score
    echo "<ul>"; // Start an unordered list to display individual scores and grades
    foreach ($scores as $score) { // Loop through each score in the scores array
        // Determine grade for the current score using the determine_grade function
        $grade = determine_grade($score);
        // Display the score and its corresponding grade within list items
        echo "<li>Score: " . $score . " Grade: " . $grade . "</li>";
    }
    echo "</ul>"; // Close the unordered list
}
?>
