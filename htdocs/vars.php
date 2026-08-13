<?php

	//type identifier = value; //default var declaration
	
	//variable declaration in php
	//$variableName = value;
	
	$name = "Shivaar";
	
	//echo $name;
	
	// $num = 5;
	// $num2 = 10;
	
	// echo $num + $num2;
	
	$age = 21; 
	
	//concatenation in PHP is denoted by a . (period)
	echo $name." is ".$age. " years<br/>";
	
	//$name is $age years.
	
	//Class exercise
	
	/*
	
	Assume that the cost of painting a house is R30 per square meter.
    However, if the surface area to be painted exceeds 50 square meters, 
    the cost per square meter reduces to R25. 
	
	Write a PHP program that takes as input the dimensions of a rectangular portion of a house, 
	computes the total cost of painting it based on the above pricing scheme, 
	and outputs the total cost rounded to 2 decimal places.
	
	*/
	
	$costPerSquareMeter = 30; // cost of painting per square meter
    $discount = 25; // discounted cost of painting per square meter for areas > 50 sqm
    $length = 5.5; // length of rectangular portion in meters
    $width = 5.5; // width of rectangular portion in meters
    $totalArea = $length * $width; // calculate the total area to be painted
    $totalCost = $totalArea * ($totalArea <= 50 ? $costPerSquareMeter : $discount); // calculate the total cost of painting
    
	echo "The cost of painting a rectangular portion of the house with dimensions {$length}m x {$width}m is R" . number_format($totalCost, 2) . ".";
	
?>