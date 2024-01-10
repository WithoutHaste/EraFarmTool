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

// Returns a random GUID string
// source: https://stackoverflow.com/questions/18206851/com-create-guid-function-got-error-on-server-side-but-works-fine-in-local-usin
function new_guid() : string {
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }

	//linux does not support php "com" functions
	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
		mt_rand(0, 65535), 
		mt_rand(0, 65535), 
		mt_rand(0, 65535), 
		mt_rand(16384, 20479), 
		mt_rand(32768, 49151), 
		mt_rand(0, 65535), 
		mt_rand(0, 65535), 
		mt_rand(0, 65535));
}
?>