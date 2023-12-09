<?php

include("../data_access.php");

use PHPUnit\Framework\TestCase;

/* demonstration of how to use a wrapper class to enable mock testing
* not going forward with this because it can't test all the edge cases

class Eft_File_Pointer {
	private $file_pointer; //what you get when you fopen a file
	
	public function __construct($file_pointer) {
		$this->file_pointer = $file_pointer;
	}
	
	public function go_to_start() {
		rewind($this->file_pointer);
	}
	
	public function get_line() {
		return fgets($this->file_pointer);
	}
}

function eft_get_data_format_version(Eft_File_Pointer $file_pointer) : ?string {
	$file_pointer->go_to_start();
	$line = $file_pointer->get_line();
	if(!$line) {
		return null;
	}

	$line = trim($line, "\n\r ");
	$matches = "";
	preg_match_all('/\#version\:(.*)/i', $line, $matches);
	if(count($matches) < 2 || count($matches[1]) < 1 || !$matches[1][0]) {
		return null;
	}

	return $matches[1][0];
}

class TestDataAccess extends TestCase
{
	public function testGetDataFormatVersion_FindsIt() : void
	{
		//Arrange
		$data_format = "1.0";
		$resource_mock = $this->createMock(Eft_File_Pointer::class);
        $resource_mock
            ->method('go_to_start')
            ->willReturn(null);
        $resource_mock
            ->method('get_line')
            ->will($this->onConsecutiveCalls("#version:".$data_format, false));
		//Act
		$result = eft_get_data_format_version($resource_mock);
		//Assert
		self::assertSame($data_format, $result);
	}
}
*/

class TestDataAccess extends TestCase
{
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

?>