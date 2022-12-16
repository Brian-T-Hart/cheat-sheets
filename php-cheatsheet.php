<?php

// PHP Cheatsheet

/***** Strings ************************************/
$str = 'This is a string';

strlen($str); //get length of a string

str_word_count($str); //get number of words in string

strrev($str); //reverse a string

strpos("Hello world!", "world"); //search for substring in string and return index

str_replace("world", "Dolly", "Hello world!"); // replace text within a string (outputs Hello Dolly!)


/***** Numbers *************************************/
$num1 = 2;
$num2 = 1.25;

is_int($num1); //check if value is an integer

is_float($num2); //check if value is float/decimal

is_double($num2); //alias of is_float

is_nan($num2); //check if a value is not a number

is_numeric('12'); //check if a value is a number or numeric string

(int)"123"; //cast string to int

rand(1,100); //generate a random number between the given values

