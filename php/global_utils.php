<?php

// Safe-get element from array, for arrays and associative arrays
// Returns null if the $index does not exist
// Expects $array is an array
function eft_get_element($array, $index) {
	if(array_key_exists($index, $array)) {
		return $array[$index];
	}
	return null;
}

// Implementing a PHP 8 built-in util
function str_contains($string, $substring) : bool {
	return (strpos($string, $substring, 0) != False);
}
?>