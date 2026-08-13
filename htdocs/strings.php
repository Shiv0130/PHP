<?php

/* Introduction to string manipulation
string -> a sequence of characters. Anything can be a string

'string' or "string"
single quotes -> php will pop the string out exactly as is.
double quotes -> php will give you the option to manipulate the
				//string using the built-in options.
				
 $demoString = 'World';

 echo "Its ,$demoString"."<br/>"; this will write out the variable

 echo 'Its, $demoString'."<br/>"; this does not write out the variable

echo "hi\t$demoString"."<br/>"; adds a tab space because of \t

 echo 'hi\t$demoString'; doesn't work
 * end Intro/

/* String methods

strlen() - > takes a string as a parameter and then gives out the length
str_word_count() -> counts the number of words in a string
str_contains() -> checks if a specific character is contained within a string
substr() -> returns part of a string 
strpos() -> returns the position of a string
strrev -> reverse the order
str_replace -> replace a string with another string
*/

//strpos
$myString = "Hello World";
$mySubstring = "World";
$pos = strpos($myString, $mySubstring);

echo "$pos"."<br/>";

echo strpos($myString, "World")."<br/>";

//str_replace
echo str_replace($mySubstring,"Shivaar",$myString);
//this will replace the word "World with Shivaar"

echo "<br/>";

echo strrev($myString). "<br/>";
//reverse words instead of individual strings.
//get the substring, if empty space = word.

 if (str_contains($myString, $mySubstring)){
	 echo "String contains $mySubstring". "<br/>";
 }
 else {
	 echo "String does not contain $mySubstring". "<br/>";
 }


//there's another method called strstr 
// checks the first occurrence of a string and then returns the rest of the string

$email = "aphelele@gmail.com";

$domain = strstr($email, '@');
echo $domain. "<br>";
$user = strstr ($email,'@',true); //true tells it to return whatever before the first occurence 

echo $user. "<br/>";


//usage for strtr (replacing characters / substrings)
$object = array("hello" => "hi", "hi" => "bonjour");
echo strtr("hi people, issa  hello",$object)."<br/>";


//strlen
echo strlen("there  there") . "<br/>";

//str_word_count
echo str_word_count("Hello world, I am tired"). "<br/>";

$forSlee = "Hello World, we are extracting a string";

//substr(string,start, length)
echo substr($forSlee,20,10)."<br/>";

//strtolower | strtoupper ( will send characters to lowercase 
//or uppercase)

//there's a case sensitive version of str_replace
// this is called str_ireplace

$text = str_ireplace("Liverpool","Arsenal","liverpool UTD Lost");
echo $text."<br/>";

/*
 str_replace - case sensitive
 str_ireplace - not case sensitive
*/

//str_pad -> will add another string to a string 
echo str_pad("Shivek",20,"*",STR_PAD_BOTH)."<br/>";

//str_repeat => repeats characters / strings

echo str_repeat("End",15)."<br/>";

//str_split => convert a string to an array
//str_split(string,length)
$quade_split = str_split("This is an example of string split",5);

print_r($quade_split);

echo "<br/>";

//str_shuffle -> will randomly shuffle a string

$shuffled = str_shuffle("Deck of cards");
echo $shuffled;



?>