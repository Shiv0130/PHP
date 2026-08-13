<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
</head>
<body>
    <form action ="" method="POST">
    <label>Num1:</label>
    <input type= "text" name="number1" id="number1">
    <label>Num2:</label>
    <input type="text" name ="number2" id="number2">
    <input type="submit" name="submit" value="Calculate">
	

    

    </form>
    <?php
    //2.1 //
    
    define("PI", 3.141); 
    
    $rad = 5; 
    $area = PI * $rad * $rad; 
    
    echo "The area of the circle with radius $rad is: $area"."<br/>";

    //2.2//
    $num1=$_POST["number1"]?? null;
    $num2=$_POST["number2"] ?? null;
	
	$biggestnumber=max($num1,$num2);
	$smallestnumber=min($num1,$num2);

    $sum=$num1+$num2;
	
	if($num1+$num2> 30){
    echo "The sum > 30, decrease:".$biggestnumber;
	} elseif ($num1 + $num2 < 30){
		echo "The sum < 30, increase: ". $smallestnumber;
		}else if($num1 +$num2 == 30){
			echo"The sum is 30. All is well" ;
			}
	
	

    /*if($sum>30 & $num1>$num2 ){
        echo "Try again both numbers have to equal to 30<br/>";
    

    } if($sum<30 & $num1<$num2){
        echo "The sum has to be 30";
    }

    if($sum==30){
        echo "The sum of num1 and num2 is:".$sum."<br/>";
    }*/

    

    //2.3.//

$tScore = 75; 
$assignmentScore = 80; 
$eScore = 90;


$finalMark = ($tScore + $assignmentScore + $eScore) / 3;


if ($finalMark >= 75) {
    $grade = 'A';
} elseif ($finalMark >= 50) {
    $grade = 'C';
} elseif ($finalMark >= 45) {
    $grade = 'B';
} else {
    $grade = 'F';
}

echo "Final Mark: $finalMark" . "<br/>";
switch ($grade) {
    case 'A':
        echo "Grade: Pass with distinction" . "<br/>" . "<br/>";
        break;
    case 'B':
        echo "Grade: Pass" . "<br/>" . "<br/>";
        break;
    case 'C':
        echo "Grade: Supplementary" . "<br/>" . "<br/>";
        break;
    case 'F':
        echo "Grade: Fail" . "<br/>" . "<br/>";
        break;
    default:
        echo "Enter a grade that our system recognises" . "<br/>" . "<br/>";
}
?>





    





    
    
</body>
</html>