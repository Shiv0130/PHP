<!-- <!DOCTYPE html>
<html>
<head>
    <title>Odd, Even, and Prime Numbers</title>
</head>
<body>
    <table border="1">
        <tr>
            <th>Number</th>
            <th>Odd/Even</th>
            <th>Prime</th>
        </tr> -->
<?php
// echo "Today is " . date("l")."<br/>";
// echo "The time is " . date("h:i:sa");

// $num=10;
// $sum=$num+1+1+1;
// echo("The sum is: $sum");

//$num1=10;
//$num2=30;

// if($num1==$num2){
//     echo("The two values are the same")."<br>";
// }else if($num1<$num2){
//     echo("The first value is smaller than the second")."<br>";
// }else {
//     echo("The second value is bigger than than the first")."<br>";

// }if ($num1!=$num2){
//     echo("The values aren't equal to each other")."<br>";
// }

// for ($i = 0; $i <= 10; $i++){
//     if($i%2==0){
//         echo "Number is even";
//         echo "Number is prime";
//     }else{
//         echo "Number is odd";
//     }

// 	echo $i. "<br/>";

// }
        // function isPrime($num) {
        //     if ($num <= 1) {
        //         return false;
        //     }
        //     for ($i = 2; $i <= sqrt($num); $i++) {
        //         if ($num % $i == 0) {
        //             return false;
        //         }
        //     }
        //     return true;
        // }

        // for ($i = 1; $i <= 10; $i++) {
        //     echo "<tr>";
        //     echo "<td>$i</td>";
        //     if ($i % 2 == 0) {
        //         echo "<td>Even</td>";
        //     } else {
        //         echo "<td>Odd</td>";
        //     }
        //     echo "<td>";
        //     if (isPrime($i)) {
        //         echo "Prime";
        //     } else {
        //         echo "-";
        //     }
        //     echo "</td>";
        //     echo "</tr>";
        // }

        // $month = 3; // Example: March
        // $day = 8; // Example: 8th
        // $year = 2024; // Example: 2024
        
        // printf("%02d/%02d/%04d", $month, $day, $year);
        
       // $myString = "Hello";
        //$desiredLength = 10;
        
        //$paddedString = str_pad($myString, $desiredLength);
        
        //echo "Original String: $myString<br>";
        //echo "Padded String: $paddedString";
        ?>
		
		
    <!-- </table>
	
	
</body>
</html> -->

<?php
// Sample data for authors and books
/*$authors = [
    1 => "J.K. Rowling",
    2 => "George R.R. Martin",
    3 => "J.R.R. Tolkien"
];

$books = [
    ["title" => "Harry Potter", "authorId" => 1],
    ["title" => "A Song of Ice and Fire", "authorId" => 2],
    ["title" => "The Hobbit", "authorId" => 3]
];

// Add authorName to each book
foreach ($books as &$book) {
    if (isset($authors[$book["authorId"]])) {
        $book["authorName"] = $authors[$book["authorId"]];
    }
}

// Display the resulting books array
echo "<pre>";
print_r($books);
echo "</pre>";*/

/*// Initialize the grid
$grid = array_fill(0, 20, array_fill(0, 20, '.'));

// Place 10 mines randomly
$mines = 10;
while ($mines > 0) {
    $x = rand(0, 19);
    $y = rand(0, 19);

    // Place a mine if the cell is empty
    if ($grid[$x][$y] === '.') {
        $grid[$x][$y] = '*';
        $mines--;
    }
}

// Display the grid
echo "<pre>";
for ($i = 0; $i < 20; $i++) {
    for ($j = 0; $j < 20; $j++) {
        echo $grid[$i][$j] . " ";
    }
    echo "\n";
}
echo "</pre>";*/

/*function generateDefinitionList($array) {
    $output = '<dl>';
    
    foreach ($array as $term => $definition) {
        $output .= "<dt>$term</dt><dd>$definition</dd>";
    }
    
    $output .= '</dl>';
    
    return $output;
}

// Example usage:
$array = array(
    'PHP' => 'Hypertext Preprocessor',
    'HTML' => 'Hypertext Markup Language',
    'CSS' => 'Cascading Style Sheets'
);

echo generateDefinitionList($array);*/

/*function factorial($n) {
    if ($n == 0) {
        return 1; // Base case: factorial of 0 is 1
    } else {
        return $n * factorial($n - 1); // Recursive case
    }
}

for ($i = 0; $i <= 10; $i++) {
    echo "Factorial of $i is " . factorial($i) . "<br/>";
}*/

/*function resetCounter($c) {
    $c = 0;
}

$counter = 0;
$counter++;
$counter++;
$counter++;
$counter++;
echo "$counter<br />";
resetCounter($counter);
echo "$counter<br />";*/

$myVar = 123;
$myRef = &$myVar;
$myRef++;
echo $myRef . "<br />";
echo $myVar . "<br />";


?>





