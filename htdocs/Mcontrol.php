<?php
/*
Program for Grades
Create code in PHP using if-elseif-else & switch to grade a student's marks based on
the following conditions:
80 - 100=A
70 - 79=B
60 - 69=C
50 - 59=D
0 - 49=F
*/

$grade=100;
/*
if($grade>=80 && $grade<=100){
	echo "A";
	
}
elseif($grade>=70 && $grade<=79){
	echo "B";
}
elseif($grade>=60 && $grade<=69){
	echo "C";
}
elseif($grade>=50 && $grade<=59){
	echo "D";
}
else {
	echo "F";
}
*/

/*switch($grade){
	case 'A': $grade>=80 && $grade<=100 
	echo "A";
	break;
}*/

//correction

switch(true) {
    case ($grade >= 80 && $grade <= 100):
        echo "A";
        break;
    case ($grade >= 70 && $grade <= 79):
        echo "B";
        break;
    case ($grade >= 60 && $grade <= 69):
        echo "C";
        break;
    case ($grade >= 50 && $grade <= 59):
        echo "D";
        break;
    default:
        echo "F";
        break;
}

?>