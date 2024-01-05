<?php

include("../classes.php");

use PHPUnit\Framework\TestCase;

class TestClasses extends TestCase
{
	///////////////////////////////////
	
	public function testUser_Serialize_1_0_DefaultUser() : void
	{
		//Arrange
		$user = new Eft_User();
		$format = "1.0";
		//Act
		$result = $user->serialize($format);
		//Assert
		self::assertSame('||0||||||0', $result);
	}
	
	public function testUser_Serialize_1_0_FullUser() : void
	{
		//Arrange
		$user = new Eft_User();
		$user->id = 1;
		$user->created_date = DateTime::createFromFormat('Ymd', '20230131');
		$user->is_admin = False;
		$user->username = "jack";
		$user->password_hashed = "abcde";
		$user->email = "j@gmail.com";
		$user->phone_number = "1234567890";
		$user->last_login_date = DateTime::createFromFormat('Ymd', '20230315');
		$user->is_deactivated = False;
		$format = "1.0";
		//Act
		$result = $user->serialize($format);
		//Assert
		self::assertSame("1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0", $result);
	}
	
	public function testUser_Serialize_1_0_Dates() : void
	{
		//Arrange
		$user = $this->build_user();
		$user->created_date = DateTime::createFromFormat('Ymd', '20230131'); //single digit month
		$user->last_login_date = DateTime::createFromFormat('Ymd', '20231105'); //single digit day
		$format = "1.0";
		//Act
		$result = $user->serialize($format);
		//Assert
		self::assertTrue(strpos($result, "20230131") !== False);
		self::assertTrue(strpos($result, "20231105") !== False);
	}
	
	public function testUser_Serialize_1_0_Bools() : void
	{
		//Arrange
		$user = $this->build_user();
		$user->is_admin = True;
		$user->is_deactivated = False;
		$format = "1.0";
		//Act
		$result = $user->serialize($format);
		//Assert
		self::assertTrue(strpos($result, "|1|".$user->username) !== False);
		self::assertTrue(strpos($result, "|0") == strlen($result) - 2);
	}
	
	public function testUser_Deserialize_NullLine_ThrowsException() : void
	{
		//Arrange
		$line = null;
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertNull($result);
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
	
	public function testUser_Deserialize_1_0_InvalidFormat_ReturnsDefaultValues() : void
	{
		//Arrange
		$line = "abc|def";
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertNotNull($result);
		self::assertSame(0, $result->id);
		self::assertSame(null, $result->created_date);
		self::assertSame(false, $result->is_admin);
		self::assertSame(null, $result->username);
		self::assertSame(null, $result->password_hashed);
		self::assertSame(null, $result->email);
		self::assertSame(null, $result->phone_number);
		self::assertSame(null, $result->last_login_date);
		self::assertSame(false, $result->is_deactivated);
	}
	
	public function testUser_Deserialize_1_0_AllFieldsFound() : void
	{
		//Arrange
		$user = $this->build_user();
		$format = "1.0";
		$line = $user->serialize($format);
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertNotNull($result);
		self::assertSame($user->id, $result->id);
		self::assertSame($user->created_date->format('Y-m-d'), $result->created_date->format('Y-m-d'));
		self::assertSame($user->is_admin, $result->is_admin);
		self::assertSame($user->username, $result->username);
		self::assertSame($user->password_hashed, $result->password_hashed);
		self::assertSame($user->email, $result->email);
		self::assertSame($user->phone_number, $result->phone_number);
		self::assertSame($user->last_login_date->format('Y-m-d'), $result->last_login_date->format('Y-m-d'));
		self::assertSame($user->is_deactivated, $result->is_deactivated);
	}
	
	public function testUser_Deserialize_1_0_IdField() : void
	{
		$user = $this->build_user();

		//Arrange Leading Zero
		$line = "01|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0";
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertSame(1, $result->id);

		//Arrange Large Int
		$line = "999999999|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0";
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertSame(999999999, $result->id);

		//Arrange No Id Field - Defaults To Zero
		$line = "|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0";
		$format = "1.0";
		//Act
		$result = Eft_User::deserialize($line, $format);
		//Assert
		self::assertSame(0, $result->id);
	}
	
	//todo deserialize tests, succeess cases, and error cases for each field
	
	//todo move "1.0" magic string to constants
	
	//todo test if username or password hash contains pipe
	
	////////////////////////////////////////
	
	private function build_user() : Eft_User {
		$user = new Eft_User();
		$user->id = 1;
		$user->created_date = DateTime::createFromFormat('Ymd', '20230131');
		$user->is_admin = False;
		$user->username = "jack";
		$user->password_hashed = "abcde";
		$user->email = "j@gmail.com";
		$user->phone_number = "1234567890";
		$user->last_login_date = DateTime::createFromFormat('Ymd', '20230315');
		$user->is_deactivated = False;
		return $user;
	}
}

?>