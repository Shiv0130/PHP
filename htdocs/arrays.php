<?php
/*
 arrays in php
  we mainly have indexed and associative arrays
 

*/

//creating an indexed array
$array = array(1,2,3,4,5,6);

print_r($array);

echo "<br/>"."<br/>";

//unset to delete all array elements
foreach($array as $i => $value){
	unset($array[$i]);
}
print_r($array);

echo "<br/>"."<br/>";

$array = array(1,2,3,4,5,6);
$array[3]; // appending the array index
print_r($array); //so that the array is readable by the user
//use print_r()


echo "<br/>"."<br/>";
//2D array

$arr = [[5,10],
		[15,20],
		['Bob', 7]
		];
		
echo $arr[2][0];
echo "<br/>"."<br/>";

//indexed string array

$students = array("Sammy", "Blake", "Keziah", "Khetho", "Sli");

//printing array items
foreach($students as $person){
	echo $person. "<br/>";
}

echo "<br/>"."<br/>";
//associate array. | key-value pair

$cars = array("brand" => "German made",
			  "model" => "C200",
			  "colour" => "Matte black",
			  "registration" => "BH652AZN");

//printing an associative array
foreach($cars as $key => $value){
	echo $key.":".$value."<br/>";
}
echo "<br/>"."<br/>";
/*sorting arrays

sort -> sorts in ascending order
rsort -> reverse sort. Sorts in descending order

asort -> sorts an associative array in ascending order
arsort -> sorts an associative array in reverse, i.e. descending

ksort -> sorts the keys of an associative array in ascending order
krsort -> sorts the keys of an associative array in descending order


*/
$students = array("Sammy", "Blake", "Keziah", "Khetho", "Sli");
sort($students); //array is sorted in ascending order

foreach ($students as $student){
	echo $student. "<br/>";
}

echo "<br/>"."<br/>";
rsort($students);//array sorted in descending order

foreach ($students as $student){
	echo $student. "<br/>";
}

echo "<br/>"."<br/>";
//sorting through an associative array 

$prem = array("z"=>"Arsenal", "a" => "Liverpool", "b" => "Manchester City",
				"c" => "Tottenham", "d" => "Chelsea", "e" => "Manchester UTD");
asort($prem);//our associative array is sorted in ascending order
				
foreach ($prem as $key => $value){
	
	echo $key.":".$value."<br/>";
}
echo "<br/>"."<br/>";

arsort($prem);//associative array sorted in descending order

foreach($prem as $key => $value){
	echo $key.":".$value."<br/>";
}
echo "<br/>"."<br/>";
//ksort and krsort => sort the associative array using the key

ksort($prem);//associative array keys sorted in ascending order

foreach ($prem as $key => $value){
	echo $key.":".$value."<br/>";
}

echo "<br/>"."<br/>";
krsort($prem);//associative array keys sorted in descending order
foreach ($prem as $key => $value){
	echo $key.":".$value."<br/>";
}

?>