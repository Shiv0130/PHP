<!DOCTYPE html>
<html>
<head>
    <title>Division Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Division Table</h1>
    <table>
        <?php
        // Create a 2D array to store the division table
        $divTable = array();

        // Populate the 2D array
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                $divTable[$j][$i] = $j / $i;
            }
        }

        // Display the division table
        for ($i = 1; $i <= 10; $i++) {
            echo "<tr>";
            for ($j = 1; $j <= 10; $j++) {
                echo "<td>" . number_format($divTable[$i][$j], 3) . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>