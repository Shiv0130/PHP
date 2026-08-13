<?php
// Section A: Theory
// Question 1 
// 1.1 -To terminate php statements, we use a semicolon -;
// significance- shows us wher one line of codes ends
// and other begins.
// -this informs the compiler when it has reached the end statement(that it needs to process)

// 1.2. It ignores it. The PHP parser treats whitespace like normal
// Should you desire to get new line then you can either use a \n escape character or <br/>

// 1.3. A variable is a reference to a memory location that stores data/input.  

// To declare a variable in php, we use $, followed by the variable / identifier 
// and then the value. e.g. $name="John";

// 1.4.Comments communicate what specific elements of the code do.
// They also provide as a basis for upgrading and modifying code, to ensure that the code refactoring sticks to what the original code execution achieved.

// Single ine comments 

/*Multi-line comment*/


// Section B

//defining a constant 
//2.1.

define("PI",3.141);
$radius = 5;
$area = PI * $radius*$radius;

echo "Area of a circle with the radius of 5 is:".$area;

//Question 2 
/*Write a program that accepts2 numbers and calculates a sum of 30
If the sum> 30, informs the user to lower/ decrease the bigger number.
If the sum< 30, imforms the user to increase the value of the smallest number */

$sum=30;

$num1=readline("Enter the first number:");
$num2=readline("Enter the second number:");

$biggestnumber=max($num1,$num2);
$smallestnumber=min($num1,$num2);

if($num1+$num2> 30){
    echo "The sum > 30, decrease:".$biggestnumber;
} elseif ($num1 + $num2 < 30){
    echo "The sum < 30, increase: ". $smallestnumber;
}else if($num1 +$num2 == 30){
echo"The sum is 30. All is well" ;
}

$testMark= readline("Enter test mark:");
$assignMark= readline("Enter assignment mark:");
$examMark= readline("Enter exam mark:");

$finalMark = ($testMark * 0.5 + $assignMark* 0.5) *0.5 + $examMark *0.5;

if($finalMark<45){
    echo "Failed". $finalMark;

}

else if($finalMark >44 && $finalMark<50) {
    echo "Supplementry:". $finalMark;


}
else if($finalMark>49 && $finalMark<75){
echo "Pass:".$finalMark;

}

else if($finalMark >74) {
    echo "You got a distinction".$finalMark; 
}





?>