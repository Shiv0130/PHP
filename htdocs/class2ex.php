<?php
/*
	
	Assume that the cost of painting a house is R30 per square meter.
    However, if the surface area to be painted exceeds 50 square meters, 
    the cost per square meter reduces to R25. 
	
	Write a PHP program that takes as input the dimensions of a rectangular portion of a house, 
	computes the total cost of painting it based on the above pricing scheme, 
	and outputs the total cost rounded to 2 decimal places.
	
	*/
	
	$costPerSquareMeter = 30;
	$discount = 25;
	
	$length = 100;
	$width = 5.5; 
	
	$totalArea = $length * $width;
	
	
	$totalCost = $totalArea * ($totalArea <=50? $costPerSquareMeter : $discount);
	
	echo "The total cost of painting the sides {$length} x {$width} is R".number_format($totalCost,2);
?>