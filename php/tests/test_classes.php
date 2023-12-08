<?php

include("../classes.php");

use PHPUnit\Framework\TestCase;

class TestClasses extends TestCase
{
	///////////////////////////////////
	
	public function testUser_Deserialize_NullLine_ThrowsException() : void
	{
		//Arrange
		$line = null;
		$format = "1.0";
		$this->expectExceptionMessage(MESSAGE_UNKNOWN_DATA_FORMAT);
		//Act Assert
		$result = Eft_User::deserialize($line, $format);
	}
	
	public function testUser_Deserialize_UnknownVersion_ThrowsException() : void
	{
		//Arrange
		$line = "a|b|c";
		$format = "any";
		$this->expectExceptionMessage(MESSAGE_UNKNOWN_DATA_FORMAT);
		//Act Assert
		$result = Eft_User::deserialize($line, $format);
	}
	
	public function testUser_Deserialize_1_0_AllFieldsFound() : void
	{
		//Arrange
		//Id|CreatedDate|IsAdmin|Username|PasswordHashed|Email|PhoneNumber|LastLoginDate|IsDeactivated
		$line = "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0";
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		
		//todo
	}
	
	//todo deserialize success
	
	//todo deserialize_1_0 tests, succeess cases, and error cases for each field
	
	//todo move "1.0" magic string to constants
}

?>