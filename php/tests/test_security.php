<?php

// what is all this? PHPUnit was unworkable for long enough that, on a hobby project, I dropped it
// so here are some do-it-yourself unit tests
// run with "php <test file name>.php"

include("../security.php");

function testPasswordsAreSalted() : void
{
	//Arrange
	$password_raw = "redyellowblue";
	//Act
	$hashed_a = EraFarmTool\Security\create_password_hash($password_raw);
	$hashed_b = EraFarmTool\Security\create_password_hash($password_raw);
	//Assert
	if($hashed_a != $hashed_b) return;
	echo "Failed: ".__FUNCTION__."\n";
}

function testHashedPasswordPassesValidation() : void
{
	//Arrange
	$password_raw = "redyellowblue";
	$password_hashed = EraFarmTool\Security\create_password_hash($password_raw);
	//Act
	$verify_result = EraFarmTool\Security\verify_login($password_raw, $password_hashed);
	//Assert
	if($verify_result) return;
	echo "Failed: ".__FUNCTION__."\n";
}

echo "running unit tests...\n";
testPasswordsAreSalted();
testHashedPasswordPassesValidation();
echo "done\n";

/*
use PHPUnit\Framework\TestCase;
use EraFarmTool\Security;

class TestSecurity extends TestCase
{
	public function testPasswordsAreSalted() : void
	{
		//Arrange
		$password_raw = "redyellowblue";
		//Act
		$hashed_a = Security\create_password_hash($password_raw);
		$hashed_b = Security\create_password_hash($password_raw);
		//Assert
		self::assertNotSame($hashed_a, $hashed_b);
	}

	public function testHashedPasswordPassesValidation() : void
	{
		//Arrange
		$password_raw = "redyellowblue";
		$password_hashed = Security\create_password_hash($password_raw);
		//Act
		$verify_result = Security\verify_login($password_raw, $password_hashed);
		//Assert
		self::assertTrue($verify_result);
	}
}
*/

?>