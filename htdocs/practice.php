
<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="get">
<label>Username:</label><br>
<input type= "text" name="username"><br>

<label>Password:</label><br>
<input type= "password" name="password"><br>

<input type="submit" value ="Log in">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<label>quantity:</label><br>
<input type= "text" name="quantity"><br>

<input type="submit" value ="Total">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<label>Grade:</label><br>
<input type= "text" name="grade"><br>

<input type="submit" value ="check">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<label>Enter a number to count to:</label><br>
<input type= "text" name="counter"><br>

<input type="submit" value ="start">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<label>Enter a number to count down from:</label><br>
<input type= "text" name="counter"><br>

<input type="submit" value ="start">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<input type="submit" value ="stop">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head>

</head>
<body>

<title>Document</title>
<form action="practice.php" method="post">
<label>Enter a country:</label>
<input type= "text" name = "country"> 
<input type="submit" value ="submit">
</form>

</body>
</html>-->

<!--<!Doctype html>
<html>
<head> 

</head>
<body>
<form action="practice.php" method="post">
<label>Enter a number:</label>
<input type ="text"  name="number">
</form>
</body>
</html>-->

<!--<!Doctype html>
<html>
<head> 

</head>
<body>
<form action="practice.php" method="post">
<label>Enter side1:</label>
<input type ="text"  name="side1">

<label>Enter side2:</label>
<input type ="text"  name="side2">

<input type="submit" value="calculate">
</form>
</body>
</html>-->

<?php
//$_GET, $_POST = special variables used to collect data from an HTML form 
//data is sent to the file in the action attribute of <form> <form action="some_file.php" method="get"> 

//$_GET = Data is appended to the it;
//Not secure
//char limit
//Bookmark is possible w/ values
//Get requests can be cached
//Better for a search page

//echo $_GET["username"]."<br>";
//echo $_GET["password"]."<br>";

//$_POST = Data is packaged inside the body of the HTTP requests
//More Secure
//No data limit
//Cannot bookmark
//GET requests are not cached
//Better for submitting credentials

//echo $_POST["username"]."<br>";
//echo $_POST["password"]."<br>";

//Using knowldege from above create an order page for a restaurant

/*$item = "pizza";
$price =5.99;
$quantity = $_POST["quantity"];
$total = null;

$total = $quantity * $price;

echo "You have ordered {$quantity} x {$item}/s <br>";
echo "Your total is: \${$total} <br>";*/


//
//$grade="A";
/*$grade = $_POST["grade"];
switch($grade){
	case "A": echo "You did great";
	break;
	case "B": echo "You did good";
	break;	
	case "C": echo "You did okay";
	break;	
	case "D": echo "You did poorly";
	break;
	case "F": echo "You failed";
	break;
	default:
	echo "{$grade} is not applicable/valid";
}*/

// For loop = repeat number of code a certain amount of times
//for($i=0;$i<5;$i++){
//echo "Hello <br>";
//echo $i . "<br>";
//}

//for($i=2;$i<=100;$i+=2){
//echo $i."<br>";
//}

//for($i=2;$i<100;$i+=3){
//echo $i . "<br>";
//}

//for($i=10; $i>0; $i--){
	//echo $i. "<br>";
//}

//$counter=$_POST["counter"];
//for($i=0;$i<=$counter;$i++){
	//echo $i ."<br>";
//}

//$counter= $_POST["counter"];
//for($i = $counter; $i> 0; $i--){
	//echo $i. "<br>";
//}

//While loop
//$counter = 0; 
//while($counter <= 10){
	//$counter++;
	//echo $counter . "<br>";
//}

//$seconds= 0;
//$running = true;

//while($running){
	//if(isset($_POST["stop"])){
		//$running = false;
	//}else{
		//wait 1 second
		//$seconds++;
		//echo $seconds. "<br>";
	//}
//}

// array = "variable " which can hold more than one value at a time

//$foods = array("apple","orange","banana","coconut");

//$foods[0]="pineapple";
//array_push($foods, "pineapple","Kiwi");//adds value to end of array;
//array_pop($foods);//pop removes the last element of the array;
//array_shift($foods);// removes first element and moves rest of the elements by 1;
//$reversed_foods=array_reverse($foods);

//echo $foods;
//foreach($foods as $food){
	//foreach($reversed_foods as $food){
	//echo $food. "<br>";
//}
//echo count($foods);

//Associative array = An array made of key=> value pairs
// countries => capitals 
//id => username
//item => price
//echo "Country  =  Capitals <br> ";
//$capitals = array("USA"=>"Washington D.C","Japan"=>"Kyoto","South Korea"=>"Seoul","India"=>"New Delhi");

//echo $capitals["USA"];
//echo $capitals["Japan"];

//$capitals["USA"] = "Las Vegas";// to change value;

//$capitals["China"] = "Bejing"; // add new key value pair;
 //array_pop($capitals);// removes last key and value pair;
 //array_shift($capitals); // removes the first key and value pair;
 
 
 
//foreach($capitals as $key=> $value){
	//echo "{$key} = {$value} <br>";
//}

//$keys = array_keys($capitals);
//foreach($keys as $key){
	//echo "{$key} <br>";
//}

//$values = array_values($capitals);
//foreach($values as $value){
	//echo"{$value} <br>";
//}
//$capitals = array_flip($capitals);//flips the keys and value pairs;

//$capitals = array_reverse($capitals);

//foreach($capitals as $key=> $value){
	//echo "{$key} = {$value} <br>";
//}

//$country=$_POST["country"];
//$capital = $capitals[$country];

//echo "The capital of {$country} is {$capital}"

//$contacts = array(
//array("name"=> "Stephen",
//"email"=> "stephan@gmail.com,"
//),

//array("name"=> "Sheldon",
//"email"=> "sheldon@gmail.com,"

//),

//array("name"=> "Aphiwe",
//"email"=> "aphiwe@gmail.com,"
//)

//);

//foreach($contacts as $contacts_1){
	//foreach($contacts_1 as $x=> $y){
		//echo $x.":".$y."<br>";
	//}
//}

//$colors = array("Red", "Green","Blue");
//echo "Before sort:<br>";
//print_r($colors);
//echo "<br><br>";
//sort($colors);
//echo "After sort:<br";
//print_r($colors);

//$myBook = array("title"=> "Bleak House","author"=> "Dickens","year"=> 1853);
//sort($myBook);
//print_r($myBook);

//asort($myBook);//sorts elements by value
//print_r($myBook);

//arsort($myBook);
//print_r($myBook);

//ksort($myBook);
//print_r($myBook);

//krsort($myBook);
//print_r($myBook);



//function = write some code once, reuse when you need it
//type() after function name to invoke
// ex. add() subtract() multiply() divide()

//function happy_birthday(){
	//for ($i=0;$i<=3;$i++){
		//echo "Happy birthday to you! <br>";
	//}
	//echo "Happy birthday happy birthday happy birthday to you <br>";
	//echo "You are x years old <br><br>";
//}

//happy_birthday();
//happy_birthday();
//happy_birthday();
 
/*function happy_birthday($first_name,$age){
	for ($i=0;$i<=2;$i++){
		echo "Happy birthday to you! <br>";
	}
	echo "Happy birthday happy birthday happy birthday to you <br>";
	echo "{$first_name} is {$age} years old now <br><br>";
} 

happy_birthday("Spongebob",30);
happy_birthday("Patrick",35);
happy_birthday("Squidward",45);*/

/*function isEven($number){
	$number = $_POST["number"];
	//$result = $number % 2;
	//return $number % 2; //$result;
if($number%2==0){
	return "$number is even"; 
}
else{
	return "$number is odd";
}	
}

echo isEven($number);*/

//$side1=$_POST["side1"];
//$side2=$_POST["side2"];
 //function hypotenuse($a,$b){
	 //$c= sqrt($a ** 2 + $b **2);
	// return $c;
 //}
 
 //echo hypotenuse($side1,$side2);

function factorial($n) {
    if ($n == 0) {
        return 1;
    } else {
        return $n * factorial($n - 1);
    }
}

// Example usage
$number = 5;
echo "Factorial of $number is " . factorial($number);

function isPrime($num) {
    if ($num <= 1) {
        return false;
    }
    for ($i = 2; $i <= sqrt($num); $i++) {
        if ($num % $i == 0) {
            return false;
        }
    }
    return true;
}

// Example usage
$number = 29;
if (isPrime($number)) {
    echo "$number is a prime number.";
} else {
    echo "$number is not a prime number.";
	
	function fibonacci($n) {
    $fib = [0, 1];
    for ($i = 2; $i < $n; $i++) {
        $fib[$i] = $fib[$i - 1] + $fib[$i - 2];
    }
    return $fib;
}

// Example usage
$n = 10;
$fibonacciSeries = fibonacci($n);
echo "First $n numbers in the Fibonacci series: " . implode(", ", $fibonacciSeries);

function printAsteriskTriangle($n) {
    for ($i = 1; $i <= $n; $i++) {
        echo str_repeat('*', $i) . "\n";
    }
}

// Example usage
$n = 5;
printAsteriskTriangle($n);


//string functions
 //$username = "Bro The Code";
// $username = array("Bro","The","Code");
 //$phone = "123-456-7890";
 
 //$username = strtolower($username);
 //$username = strtoupper($username);
 //$username = trim($username); // removes whitespace before or after your string;
 //$username = str_pad($username, 20, "/");// pad a string up to a certain amount of characters;
 //$phone= str_replace("-", "",$phone);// what to replace, what we replacing with, from where;
 //$phone= str_replace("-", "/",$phone);// what to replace, what we replacing with, from where;
 //$username = strrev($username); //reverses a string;
 //$username = str_shuffle($username); //shuffles a string;
 //$equals = strcmp($username, "Bro Code");
 //$count = strlen($phone);
 //$index=strpos($username," ");// original string and what we looking for.Gives first instance of it.
 //$index=strpos($phone,"-");
 //$firstname=substr($username,0,3);
 //$lastname = substr($username ,4);
 //$fullname = explode(" ", $username);
 //echo $fullname;// meant to be wrong because
 //foreach($fullname as $name){
	// echo $name ."<br>";
 //}
 //$username = implode("-",$username);
 //echo $username;
 //echo $phone;
 //echo $equals;
 //echo $count;
 //echo $index;
 //echo $firstname;
 //echo $lastname;
 //phpinfo();
 
// include "arrays.php";
 

?>