<?php

include("../data_access.php");

use PHPUnit\Framework\TestCase;

class TestDataAccess extends TestCase
{
	/*
	public function testGetDataVersion_tryingToMock() : void
	{
		//asking stack overflow for help
		//https://stackoverflow.com/questions/77622724/phpunit-cannot-stub-or-mock-class-or-interface-resource-which-does-not-exist
		
		$file_pointer = fopen("../data/users.txt", "r"); //verifies PHPUnit can use "resource" type
		self::assertTrue(is_resource($file_pointer)); //verifies PHPUnit can use "resource" type
		
		// all of these die on error: Cannot stub or mock class or interface "resource" which does not exist
		//$resourceMock = $this->createStub(gettype($file_pointer));
		$resourceMock = $this->createStub(resource::class);
		//$resourceMock = $this->createMock(resource::class);
		//$resourceMock = $this->getMock('resource', array('getLine')); //error: call to undefined method getMock

		$data_format = "1.0";
        $resourceMock
            ->method('getLine')
            ->will($this->onConsecutiveCalls("#version:".$data_format, false));
		$result = get_data_format($resourceMock);
		self::assertSame($data_format, $result);
		
		fclose($file_pointer);
	}
	*/
	
	//until I figure out the mocking, going forward with real files

	public function testGetDataFormatVersion_FindsIt() : void
	{
		//Arrange
		$file_pointer = fopen("./temp/users_with_version.txt", "r");
		//Act
		$result = eft_get_data_format_version($file_pointer);
		//Assert
		self::assertSame("1.0", $result);
		//Cleanup
		fclose($file_pointer);
	}
	
	public function testGetDataFormatVersion_ReturnsToStartToFindIt() : void
	{
		//Arrange
		$file_pointer = fopen("./temp/users_with_version.txt", "r");
		fgets($file_pointer); //move file cursor forward
		//Act
		$result = eft_get_data_format_version($file_pointer);
		//Assert
		self::assertSame("1.0", $result);
		//Cleanup
		fclose($file_pointer);
	}

	public function testGetDataFormatVersion_DoesNotFindIt() : void
	{
		//Arrange
		$file_pointer = fopen("./temp/users_without_version.txt", "r");
		//Act
		$result = eft_get_data_format_version($file_pointer);
		//Assert
		self::assertNull($result);
		//Cleanup
		fclose($file_pointer);
	}
	
	/////////////////////////////////////////////////

	public function testGetDataLines_AllTestCases() : void
	{
		//file contains extra comment lines mixed in
		
		//Arrange
		$file_pointer = fopen("./temp/users_data_lines.txt", "r");
		fgets($file_pointer); //move file cursor forward past at least one data line
		fgets($file_pointer);
		fgets($file_pointer);
		fgets($file_pointer);
		//Act
		$result = eft_get_data_lines($file_pointer);
		//Assert
		self::assertSame(4, count($result));
		self::assertSame("a|b", $result[0]);
		self::assertSame("c|d", $result[1]);
		self::assertSame("e|f", $result[2]);
		self::assertSame("g|h", $result[3]);
		//Cleanup
		fclose($file_pointer);
	}
	
	
}
/*
class TestDataAccess extends TestCase
{
	public function testGetDataFormatVersion_dummy() : void
	{
		
		$file_pointer = fopen("../data/users.txt", "r");
		self::assertTrue(is_resource($file_pointer)); //verifies PHPUnit can use the resource type
		
		//Arrange
		$resourceMock = $this->createStub(gettype($file_pointer)); //$this->createStub(resource::class); //$this->createMock(resource::class);
		// dies on error: Cannot stub or mock class or interface "resource" which does not exist
        $resourceMock
            ->method('getLine')
            ->will($this->onConsecutiveCalls('abc', false));
		//Act
		$result = eft_get_data_format_version($resourceMock);
		//Assert
		self::assertSame("abc", $result);

		fclose($file_pointer);
	}
}
*/
?>