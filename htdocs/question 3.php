<?php
// Start session at the beginning
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Grade Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        fieldset {
            margin-bottom: 1em;
            padding: 10px;
            border: 1px solid #ddd;
        }
        legend {
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php
// Helper function to calculate letter grade using a SWITCH
function get_letter_grade(float $avg): string {
    switch (true) {
        case $avg >= 80:
            return 'A';
        case $avg >= 70:
            return 'B';
        case $avg >= 60:
            return 'C';
        case $avg >= 50:
            return 'D';
        default:
            return 'F';
    }
}

// Control flow: decide which "step" to run
$step = $_REQUEST['step'] ?? 'start';

// Default values
$errors = [];
$results = [];
$grade_count = ['A'=>0, 'B'=>0, 'C'=>0, 'D'=>0, 'F'=>0];
$class_avg = 0;
$num_students = 0;

// Process based on step
if ($step === 'start') {
    // Show the "How many students?" form
    ?>
    <h1>Step 1: How many students?</h1>
    <form method="post">
        <input type="hidden" name="step" value="set_count">
        <label>
            Number of students (min 1):
            <input type="number" name="num_students" min="1" required>
        </label>
        <button type="submit">Next →</button>
    </form>
    <?php
} elseif ($step === 'set_count') {
    // Read and cast user input
    $num_students = max(1, intval($_POST['num_students']));
    ?>
    <h1>Step 2: Enter Names &amp; 5 Grades Each</h1>
    <form method="post">
        <input type="hidden" name="step" value="process">
        <input type="hidden" name="num_students" value="<?php echo $num_students ?>">
        <?php for ($i = 0; $i < $num_students; $i++): ?>
            <fieldset>
                <legend>Student #<?php echo $i + 1 ?></legend>
                <label>
                    Name:
                    <input type="text" name="students[<?php echo $i ?>][name]" required>
                </label><br>
                <?php for ($g = 0; $g < 5; $g++): ?>
                    <label>
                        Grade <?php echo $g + 1 ?>:
                        <input type="number"
                               name="students[<?php echo $i ?>][grades][<?php echo $g ?>]"
                               min="0" max="100" required>
                    </label><br>
                <?php endfor; ?>
            </fieldset>
        <?php endfor; ?>
        <button type="submit">Calculate →</button>
    </form>
    <?php
} elseif ($step === 'process') {
    $raw_students = $_POST['students'] ?? [];
    $clean_students = [];

    // Validation with if/else
    foreach ($raw_students as $idx => $info) {
        // Trim & sanitize
        $name = trim($info['name'] ?? '');
        if ($name === '') {
            $errors[] = "Student #".($idx+1)." has an empty name.";
            continue;
        }

        $grades = [];
        foreach ($info['grades'] as $gidx => $gval) {
            if (!is_numeric($gval) || $gval < 0 || $gval > 100) {
                $errors[] = "Student #".($idx+1).", grade ".($gidx+1)." is invalid.";
            } else {
                // Casting string → float
                $grades[] = floatval($gval);
            }
        }

        if (count($grades) === 5) {
            $clean_students[] = [
                'name'   => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                'grades' => $grades
            ];
        }
    }

    if ($errors) {
        // Show all errors at once
        ?>
        <h1 class="error">Please fix these errors:</h1>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo $e ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button type="submit" name="step" value="start">Start Over</button>
        </form>
        <?php
    } else {
        // Calculate results
        $sum_of_avgs = 0;
        
        foreach ($clean_students as $stu) {
            $avg = array_sum($stu['grades']) / count($stu['grades']);
            $letter = get_letter_grade($avg);
            $sum_of_avgs += $avg;
            $grade_count[$letter]++;

            $results[] = [
                'name'   => $stu['name'],
                'grades' => $stu['grades'],
                'avg'    => $avg,
                'letter' => $letter
            ];
        }

        // Sort descending by average
        usort($results, fn($a,$b) => $b['avg'] <=> $a['avg']);

        if (count($results) > 0) {
            $class_avg = $sum_of_avgs / count($results);
        }
        ?>
        <h1>Ranking &amp; Class Statistics</h1>

        <h2>Top Performers</h2>
        <table>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <?php for ($i=1; $i<=5; $i++) echo "<th>Grade $i</th>"; ?>
                <th>Average</th>
                <th>Letter</th>
            </tr>
            <?php foreach ($results as $i => $r): ?>
            <tr>
                <td><?php echo $i+1 ?></td>
                <td><?php echo strtoupper($r['name']) ?></td>
                <?php foreach ($r['grades'] as $g): ?>
                    <td><?php echo intval($g) ?></td>
                <?php endforeach; ?>
                <td><?php echo number_format($r['avg'], 2) ?></td>
                <td><?php echo $r['letter'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Class Statistics</h2>
        <p>Class Average: <strong><?php echo number_format($class_avg, 2) ?></strong></p>
        <table>
            <tr>
                <?php foreach ($grade_count as $L => $cnt) echo "<th>$L</th>"; ?>
            </tr>
            <tr>
                <?php foreach ($grade_count as $cnt) echo "<td>$cnt</td>"; ?>
            </tr>
        </table>

        <form method="post" style="margin-top:1em">
            <button type="submit" name="step" value="start">➥ Enter New Data</button>
        </form>
        <?php
    }
}
?>
</body>
</html>