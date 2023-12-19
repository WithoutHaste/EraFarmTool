<?php

include("../command_line_utils.php");

use PHPUnit\Framework\TestCase;

class TestCommandLineUtils extends TestCase
{
	////////////////////////////////////
	
	public function testParseUserFromArguments_MissingAllRequiredArguments_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_parse_user_from_arguments([]);
	}	
	
	public function testParseUserFromArguments_MissingUsernameRequiredArgument_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_PASSWORD, "password"]);
	}	
	
	public function testParseUserFromArguments_MissingPasswordRequiredArgument_ThrowException() : void
	{
		$this->expectExceptionMessage(MESSAGE_ADD_USER_REQUIRED_ARGUMENTS);
		eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_USERNAME, "username"]);
	}	
	
	public function testParseUserFromArguments_HasAllRequiredArguments() : void
	{
		//Arrange
		$username = "jack";
		$password = "pumpkin";
		//Act
		$user_a = eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_USERNAME, $username, EFT_CLI_TAG_SHORT_PASSWORD, $password]);
		$user_b = eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_PASSWORD, $password, EFT_CLI_TAG_SHORT_USERNAME, $username]); //different order
		//Assert
		self::assertSame($username, $user_a->username);
		self::assertTrue(eft_verify_login($password, $user_a->password_hashed));
		self::assertNull($user_a->email);
		self::assertNull($user_a->phone_number);

		self::assertSame($username, $user_b->username);
		self::assertTrue(eft_verify_login($password, $user_b->password_hashed));
		self::assertNull($user_b->email);
		self::assertNull($user_b->phone_number);
	}	
	
	public function testParseUserFromArguments_HasAllArguments() : void
	{
		//Arrange
		$username = "jack";
		$password = "pumpkin";
		$email = "j@gmail.com";
		$phone = "1234567890";
		//Act
		$user = eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_USERNAME, $username, EFT_CLI_TAG_SHORT_PASSWORD, $password, EFT_CLI_TAG_SHORT_EMAIL, $email, EFT_CLI_TAG_SHORT_PHONENUMBER, $phone]);
		//Assert
		self::assertSame($username, $user->username);
		self::assertTrue(eft_verify_login($password, $user->password_hashed));
		self::assertSame($email, $user->email);
		self::assertSame($phone, $user->phone_number);
	}	
	
	public function testParseUserFromArguments_IsAdminFalse() : void
	{
		//Arrange
		$username = "jack";
		$password = "pumpkin";
		//Act
		$user_a = eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_USERNAME, $username, EFT_CLI_TAG_SHORT_PASSWORD, $password]); //flag not set
		$user_b = eft_parse_user_from_arguments([EFT_CLI_TAG_SHORT_USERNAME, $username, EFT_CLI_TAG_LONG_ISADMIN, EFT_CLI_TAG_SHORT_PASSWORD, $password]); //flag set in wrong position
		//Assert
		self::assertFalse($user_a->is_admin);
		self::assertFalse($user_b->is_admin);
	}	
	
	public function testParseUserFromArguments_IsAdminTrue() : void
	{
		//Arrange
		$username = "jack";
		$password = "pumpkin";
		//Act
		$user = eft_parse_user_from_arguments([EFT_CLI_TAG_LONG_ISADMIN, EFT_CLI_TAG_SHORT_USERNAME, $username, EFT_CLI_TAG_SHORT_PASSWORD, $password]);
		//Assert
		self::assertTrue($user->is_admin);
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
}

?>