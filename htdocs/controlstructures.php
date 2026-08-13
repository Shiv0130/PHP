<?php

//Control structures
// 3 Structures - Selection, Iteration, Repetition

//Selection - if statements, switch statements, if .. else statements
//Iteration and Repetition -> Loops
//Iteration -> step over/into, read some list -> foreach loop is desirable
//Repetition -> normal looping, for loop, do while loop, while loop


//Selection example 
$x = 17;
if ( $x >= 18) //condition
{
	
	echo "You can vote"; //executes if condition is true
}
else {
	echo "You are still a minor". "<br/>"; //executes if condition is false
	
}


//Selection using Switch

$grade = 'B'; //expression for our switch statement

switch ($grade) {
	case 'A':
	echo "You got a distinction". "<br/>"."<br/>";
	break;
	case 'B':
	echo "You got a merit pass". "<br/>"."<br/>";
	break;
	case 'C':
	echo "You passed". "<br/>"."<br/>";
	break;
	case 'F':
	echo "Unfortunately you did not pass". "<br/>"."<br/>";
	break;
	default:
		echo "Enter a grade that our system recognises". "<br/>"."<br/>";
	//default serves as error handling
}

//Iteration example
$fruit = array("apple", "banana","orange", "pineapple");

foreach($fruit as $individualFruit){
	echo $individualFruit . "<br/>";
}

//Repetition examples -Loops

//for loop

for ($i = 0; $i <= 10; $i++){
	echo $i. "<br/>";
}
// while loop
$p = 1;
while ($p <=10) {
	echo $p." it will increment <br/>";
	$p++;
}
//Do While loop
$j = 1;
do {
	echo $j . " this is a test <br/>";
	$j++;
} while ($j <= 5);
?>