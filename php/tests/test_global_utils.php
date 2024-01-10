<?php

include("../global_utils.php");

use PHPUnit\Framework\TestCase;

class TestGlobalUtils extends TestCase
{
	////////////////////////////////////

	public function testGetElement_IntegerIndexOutOfRange_ReturnsNull() : void
	{
		$array = [4,8,9];
		self::assertNull(eft_get_element($array, -1));
		self::assertNull(eft_get_element($array, count($array)));
	}

	public function testGetElement_IntegerIndexInRange_ReturnsElement() : void
	{
		//Arrange
		$array = [4,8,9];
		$index = 1;
		//Act
		$result = eft_get_element($array, $index);
		//Assert
		self::assertSame($result, $array[$index]);
	}

	public function testGetElement_StringIndexNotInArray_ReturnsNull() : void
	{
		//Arrange
		$array = array("a"=>"AAA","b"=>"BBB");
		$index = "c";
		//Act
		$result = eft_get_element($array, $index);
		//Assert
		self::assertNull($result);
	}

	public function testGetElement_StringIndexInArray_ReturnsElement() : void
	{
		//Arrange
		$array = array("a"=>"AAA","b"=>"BBB");
		$index = "b";
		//Act
		$result = eft_get_element($array, $index);
		//Assert
		self::assertSame($result, $array[$index]);
	}

	////////////////////////////////////
	
	public function testStrContains() : void
	{
		//Arrange Null String
		$str = null;
		$substr = "abc";
		//Act
		$result = str_contains($str, $substr);
		//Assert
		self::assertFalse($result);

		//Arrange Substring Not Found
		$str = "abbbbb";
		$substr = "abc";
		//Act
		$result = str_contains($str, $substr);
		//Assert
		self::assertFalse($result);

		//Arrange Substring Found
		$str = "xxxabcxxx";
		$substr = "abc";
		//Act
		$result = str_contains($str, $substr);
		//Assert
		self::assertTrue($result);
	}
	
	//////////////////////////////////////
	
	public function testNewGuid() : void
	{
		//Arrange Has Correct Format
		//Act
		$result = new_guid();
		//Assert
		self::assertSame(1, preg_match('/^\w{8}\-\w{4}\-\w{4}\-\w{4}\-\w{12}$/', $result));

		//Arrange Randomized
		//Act
		$result_a = new_guid();
		$result_b = new_guid();
		$result_c = new_guid();
		//Assert
		self::assertNotSame($result_a, $result_b);
		self::assertNotSame($result_a, $result_c);
		self::assertNotSame($result_b, $result_c);
	}

}

?>