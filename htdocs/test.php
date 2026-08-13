<?php
/*$num1=readline("Enter the first number:");
$num2=readline("Enter the second number:");

while($num1 &&$num2!=0) {
    $num1=readline("Enter the first number:");
    $num2=readline("Enter the second number:");

    $sum=$num1+$num2;


echo("The sum is:".$sum)
}*/

// $num=0;
// while($num<10){
//     $num++;

//     if($num!=4){
//         echo($num."<br>");

//     }
// }

// $string="I love Internet Programming 621";
// $replace = str_replace("I","students","I Love Internet Programming 621");

// echo($replace);

$sum=0;

 $num=readline("Enter number");
 $string =readline("Want to enter number again");

 while($num!=0){
     $num1=readline("Enter first number");
     $num2=readline("Enter Second number");
     $sum=$num1+$num2;

     echo("The sum is".$sum);

     if($string=="Yes"){
     $num1=readline("Enter first number");
     $num2=readline("Enter Second number");
     $sum=$num1+$num2;

        
     }else{
        break;

     }


 }

// $pocketNum=readline("Enter a pocket number:");

// if( $pocketNum==0 ){
//     echo("Pockets are green");
// } 

// if($pocketNum>1 && $pocketNum<10&& $pocketNum%2==0){
//     echo("Odd numbered Pockets are red.Even numbered Pockets are black");

// } if($pocketNum>11 && $pocketNum<18 && $pocketNum%2==0){
//     echo("Odd pockets are black and even are red");

// }
// if($pocketNum>19 && $pocketNum<28 && $pocketNum%2==0) {

//     echo("odd- numbered pockets are red and the even number pockets ar black");
// }
// if($pocketNum>29 && $pocketNum<36 && $pocketNum%2==0){
//     echo("Odd pockets are black and even are red");
// }

// if($pocketNum>36){
//     echo("Enter a number within range");
// }

?>




