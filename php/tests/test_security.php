<?php

include("../security.php");

use PHPUnit\Framework\TestCase;

class TestSecurity extends TestCase
{
	public function testPasswordsAreSalted() : void
	{
		//Arrange
		$password_raw = "redyellowblue";
		//Act
		$hashed_a = eft_create_password_hash($password_raw);
		$hashed_b = eft_create_password_hash($password_raw);
		//Assert
		self::assertNotSame($hashed_a, $hashed_b);
	}

	public function testHashedPasswordPassesValidation() : void
	{
		//Arrange
		$password_raw = "redyellowblue";
		$password_hashed = eft_create_password_hash($password_raw);
		//Act
		$verify_result = eft_verify_login($password_raw, $password_hashed);
		//Assert
		self::assertTrue($verify_result);
	}
}

?>