<?php

include_once("../data_access.php");
include_once("builders.php");

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
	
	public function testDeserializeUsersFormat_1_0_Success() : void 
	{
		//Arrange
		$user_a = new Eft_User();
		$user_a->id = 1;
		$user_a->created_date = DateTime::createFromFormat('Ymd', '20230131');
		$user_a->is_admin = False;
		$user_a->username = "jack";
		$user_a->password_hashed = "abcde";
		$user_a->email = "j@gmail.com";
		$user_a->phone_number = "1234567890";
		$user_a->last_login_date = DateTime::createFromFormat('Ymd', '20230315');
		$user_a->is_deactivated = False;
		$user_b = new Eft_User();
		$user_b->id = 3;
		$user_b->created_date = DateTime::createFromFormat('Ymd', '20230215');
		$user_b->is_admin = True;
		$user_b->username = "jill";
		$user_b->password_hashed = "fghij";
		$user_b->email = "j@yahoo.com";
		$user_b->phone_number = "0987654321";
		$user_b->last_login_date = DateTime::createFromFormat('Ymd', '20230216');
		$user_b->is_deactivated = False;
		$file_pointer = fopen("./temp/users_valid_1_0.txt", "r");		
		//Act
		$result = eft_deserialize_users_format_1_0($file_pointer);
		//Assert
		self::assertNotNull($result);
		self::assertSame(2, count($result));
		self::assertTrue(users_match($result[0], $user_a));
		self::assertTrue(users_match($result[1], $user_b));
		//Cleanup
		fclose($file_pointer);
	}
	
	/////////////////////////////////////////////////

	public function testGetDataLines() : void
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
	
	/////////////////////////////////////////////////
	
	public function testPersistNewUserCallback_DuplicateUsername_ThrowsException() : void
	{
		//Arrange
		$file_name = "./temp/persist_001.txt";
		$file_pointer = fopen($file_name, "w");
		fwrite($file_pointer, "#version:".FORMAT_1_0."\n");
		fwrite($file_pointer, "headers\n");
		fwrite($file_pointer, "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0");
		fclose($file_pointer);
		
		$new_user = build_user();
		$new_user->username = "jack";
		$new_user->email = null;
		$file_pointer = fopen($file_name, "r+");
		$this->expectExceptionMessage(MESSAGE_USERNAME_COLLISION);
		//Act Assert
		$result = eft_persist_new_user_callback($file_pointer, $new_user);		
		//Cleanup
		fclose($file_pointer);
	}

	public function testPersistNewUserCallback_DuplicateEmail_ThrowsException() : void
	{
		//Arrange
		$file_name = "./temp/persist_002.txt";
		$file_pointer = fopen($file_name, "w");
		fwrite($file_pointer, "#version:".FORMAT_1_0."\n");
		fwrite($file_pointer, "headers\n");
		fwrite($file_pointer, "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0");
		fclose($file_pointer);
		
		$new_user = build_user();
		$new_user->username = "jill";
		$new_user->email = "j@gmail.com";
		$file_pointer = fopen($file_name, "r+");
		$this->expectExceptionMessage(MESSAGE_EMAIL_COLLISION);
		//Act Assert
		$result = eft_persist_new_user_callback($file_pointer, $new_user);		
		//Cleanup
		fclose($file_pointer);
	}
	
	public function testPersistNewUserCallback_DuplicateNullEmail_Success() : void
	{
		//Arrange
		$file_name = "./temp/persist_003.txt";
		$file_pointer = fopen($file_name, "w");
		fwrite($file_pointer, "#version:".FORMAT_1_0."\n");
		fwrite($file_pointer, "headers\n");
		fwrite($file_pointer, "1|20230131|0|jack|abcde||1234567890|20230315|0");
		fclose($file_pointer);
		
		$new_user = build_user();
		$new_user->username = "jill";
		$new_user->email = null;
		$file_pointer = fopen($file_name, "r+");
		//Act
		$result = eft_persist_new_user_callback($file_pointer, $new_user);
		//Assert
		fclose($file_pointer);
		$file_pointer = fopen($file_name, "r+");
		$new_user->id = 2;
		$new_user->created_date = new DateTime();
		$users = eft_deserialize_users_format_1_0($file_pointer);
		self::assertSame($new_user->id, $result);
		self::assertSame(2, count($users));
		self::assertTrue(users_match($new_user, $users[1]));
		//Cleanup
		fclose($file_pointer);
	}

	public function testPersistNewUserCallback_FirstUser_Success() : void
	{
		//Arrange
		$file_name = "./temp/persist_004.txt";
		$file_pointer = fopen($file_name, "w");
		fwrite($file_pointer, "#version:".FORMAT_1_0."\n");
		fwrite($file_pointer, "headers");
		fclose($file_pointer);
		
		$new_user = build_user();
		$file_pointer = fopen($file_name, "r+");
		//Act
		$result = eft_persist_new_user_callback($file_pointer, $new_user);
		//Assert
		fclose($file_pointer);
		$file_pointer = fopen($file_name, "r+");
		$new_user->id = 1;
		$new_user->created_date = new DateTime();
		$users = eft_deserialize_users_format_1_0($file_pointer);
		self::assertSame($new_user->id, $result);
		self::assertSame(1, count($users));
		self::assertTrue(users_match($new_user, $users[0]));
		//Cleanup
		fclose($file_pointer);
	}

	public function testPersistNewUserCallback_NotFirstUser_Success() : void
	{
		//Arrange
		$file_name = "./temp/persist_005.txt";
		$file_pointer = fopen($file_name, "w");
		fwrite($file_pointer, "#version:".FORMAT_1_0."\n");
		fwrite($file_pointer, "headers\n");
		fwrite($file_pointer, "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|0");
		fclose($file_pointer);
		
		$new_user = build_user();
		$new_user->username = "jill";
		$new_user->email = "j@yahoo.com";
		$file_pointer = fopen($file_name, "r+");
		//Act
		$result = eft_persist_new_user_callback($file_pointer, $new_user);
		//Assert
		fclose($file_pointer);
		$file_pointer = fopen($file_name, "r+");
		$new_user->id = 2;
		$new_user->created_date = new DateTime();
		$users = eft_deserialize_users_format_1_0($file_pointer);
		self::assertSame($new_user->id, $result);
		self::assertSame(2, count($users));
		self::assertTrue(users_match($new_user, $users[1]));
		//Cleanup
		fclose($file_pointer);
	}
	
}

?>