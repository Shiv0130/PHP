 <?php
// $food="pizza";
// $price=10.00;
// $quantity=2;
// $subtotal=$quantity*$price;
// define($tax_rate,0.1);
// $tax_rate=$subtotal*0.1;
// define($shipping_cost,5.00);
// $total=$subtotal+$quantity+$shipping_cost;
// echo "You ordered".$quantity."units of food at $".$price."each. <br/>";
// echo "Your subtotal is $".$subtotal."<br/>";
// echo "Tax at a rate of 10% is $".$tax_rate."<br/>";
// echo "Your total is $".$total."<br/>";

$itemName = "Food";
$itemPrice = 10.00;
$quantity = 2;
$subtotal = number_format($quantity * $itemPrice, 2);
$taxRate = 0.10;
$taxAmount = number_format($subtotal * $taxRate, 2);
$shippingCost = number_format(5.00, 2);
$total = number_format($subtotal + $taxAmount + $shippingCost, 2);

echo "You ordered ".$quantity." units of ".$itemName." at $".$itemPrice." each.<br/>";
echo "Your subtotal is $".$subtotal."<br/>";
echo "Tax at a rate of 10% is $".$taxAmount."<br/>";
echo "Shipping is $".$shippingCost."<br/>";
echo "Your total is $".$total."<br/>";

?> 


<?php
// Input variables
$mass = readline("Enter the mass of the object:"); // Mass of the object (in kilograms)
$velocity = readline("Enter velocity of the object"); // Velocity of the object (in meters per second)

// Calculate kinetic energy
$kineticEnergy = 0.5 * $mass * pow($velocity, 2);

// Output the result
echo "Mass of the object: ".$mass." kg<br/>";
echo "Velocity of the object: ".$velocity." m/s<br/>";
echo "Kinetic Energy: ".$kineticEnergy." joules<br/>";
?>

