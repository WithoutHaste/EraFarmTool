<?php

//include("../constants.php");
include("../command_line_utils.php");

use PHPUnit\Framework\TestCase;

class TestCommandLineUtils extends TestCase
{
	////////////////////////////////////
	
	public function testHandleAddUser_MissingAllRequiredArguments_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_handle_add_user([]);
	}	
	
	public function testHandleAddUser_MissingUsernameRequiredArgument_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_handle_add_user(["-p", "password"]);
	}	
	
	public function testHandleAddUser_MissingPasswordRequiredArgument_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_handle_add_user(["-u", "username"]);
	}	
	
	public function testHandleAddUser_HasAllRequiredArguments() : void
	{
		eft_handle_add_user(["-u", "username", "-p", "password"]);
		// no exception is thrown
		self::assertTrue(true);
		
		//todo expand this as functionality is filled in
	}	
	
	////////////////////////////////////
	
	public function testGetArgumentPairs_EmptyArguments() : void
	{
		//Arrange
		$arguments = [];
		//Act
		$result = eft_get_argument_pairs($arguments);
		//Assert
		self::assertSame(0, count($result));
	}
	
	public function testGetArgumentPairs_DashWithoutPlain() : void
	{
		//Arrange
		$arguments = ["-a", "-b"];
		//Act
		$result = eft_get_argument_pairs($arguments);
		//Assert
		self::assertSame(0, count($result));
	}
	
	public function testGetArgumentPairs_PlainWithoutDash() : void
	{
		//Arrange
		$arguments = ["a", "b"];
		//Act
		$result = eft_get_argument_pairs($arguments);
		//Assert
		self::assertSame(0, count($result));
	}
	
	public function testGetArgumentPairs_HasPairs() : void
	{
		//Arrange
		$arguments = ["-a", "b", "-c", "d"];
		//Act
		$result = eft_get_argument_pairs($arguments);
		//Assert
		self::assertSame(2, count($result));
		self::assertSame("b", $result["-a"]);
		self::assertSame("d", $result["-c"]);
	}
	
	public function testGetArgumentPairs_AllMixedUp() : void
	{
		//Arrange
		$arguments = ["x", "-a", "b", "B", "-c", "-d", "e", "-f", "g", "y"];
		//Act
		$result = eft_get_argument_pairs($arguments);
		//Assert
		self::assertSame(3, count($result));
		self::assertSame("b", $result["-a"]);
		self::assertSame("e", $result["-d"]);
		self::assertSame("g", $result["-f"]);
	}

	////////////////////////////////////
	
	public function testIsDashArgument_ReturnsFalse() : void
	{
		self::assertFalse(eft_is_dash_argument(""));
		self::assertFalse(eft_is_dash_argument("-"));
		self::assertFalse(eft_is_dash_argument("a"));
		self::assertFalse(eft_is_dash_argument("ab"));
	}

	public function testIsDashArgument_ReturnsTrue() : void
	{
		self::assertTrue(eft_is_dash_argument("-a"));
		self::assertTrue(eft_is_dash_argument("-ab"));
		self::assertTrue(eft_is_dash_argument("--"));
		self::assertTrue(eft_is_dash_argument("--a"));
		self::assertTrue(eft_is_dash_argument("--ab"));
	}
	
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
}

?>