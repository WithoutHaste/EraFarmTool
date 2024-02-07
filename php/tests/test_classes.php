<?php

include_once("../constants.php");
include_once("../classes.php");
include_once("builders.php");

use PHPUnit\Framework\TestCase;

class TestClasses extends TestCase
{
	public function testUser_SerializeHeaders_UnknownVersion_ThrowsException() : void
	{
		//Arrange
		$format = "any";
		$this->expectExceptionMessage(MESSAGE_UNKNOWN_DATA_FORMAT);
		//Act Assert
		$result = Eft_User::serialize_headers($format);
	}	
	
	public function testUser_SerializeHeaders_1_0() : void
	{
		//Arrange
		//Act
		$result = Eft_User::serialize_headers(FORMAT_1_0);
		$fields = explode('|', $result);
		//Assert
		self::assertSame(10, count($fields));
	}	
	
	///////////////////////////////////
	
	public function testUser_Serialize_1_0_DefaultUser() : void
	{
		//Arrange
		$user = new Eft_User();
		//Act
		$result = $user->serialize(FORMAT_1_0);
		//Assert
		self::assertSame('||0|||||||0', $result);
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
		$user->session_key = "apples";
		$user->is_deactivated = False;
		//Act
		$result = $user->serialize(FORMAT_1_0);
		//Assert
		self::assertSame("1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315|apples|0", $result);
	}
	
	public function testUser_Serialize_1_0_Dates() : void
	{
		//Arrange
		$user = build_user();
		$user->created_date = DateTime::createFromFormat('Ymd', '20230131'); //single digit month
		$user->last_login_date = DateTime::createFromFormat('Ymd', '20231105'); //single digit day
		//Act
		$result = $user->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, "20230131") !== False);
		self::assertTrue(strpos($result, "20231105") !== False);
	}
	
	public function testUser_Serialize_1_0_Bools() : void
	{
		//Arrange
		$user = build_user();
		$user->is_admin = True;
		$user->is_deactivated = False;
		//Act
		$result = $user->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, "|1|".$user->username) !== False);
		self::assertTrue(strpos($result, "|0") == strlen($result) - 2);
	}
	
	public function testUser_Serialize_1_0_DelimiterUsedInString_ThrowsException() : void
	{
		//Arrange
		$user = build_user();
		$user->username = "a|b";
		$user->password_hashed = "c|d";
		$user->email = "e|f";
		$user->phone_number = "g|h";
		$this->expectExceptionMessage(MESSAGE_CANNOT_CONTAIN_PIPES);
		//Act Assert
		$result = $user->serialize(FORMAT_1_0);
	}
	
	public function testUser_Deserialize_NullLine_ReturnsNull() : void
	{
		//Arrange
		$line = null;
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
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
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
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
		self::assertSame(null, $result->session_key);
		self::assertSame(false, $result->is_deactivated);
	}
	
	public function testUser_Deserialize_1_0_AllFieldsFound() : void
	{
		//Arrange
		$user = build_user();
		$line = $user->serialize(FORMAT_1_0);
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNotNull($result);
		self::assertTrue(users_match($user, $result));
	}
	
	public function testUser_Deserialize_1_0_IdField() : void
	{
		$user = build_user();

		//Arrange Leading Zero
		$line = "01|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(1, $result->id);

		//Arrange Large Int
		$line = "999999999|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(999999999, $result->id);

		//Arrange No Id Field - Defaults To Zero
		$line = "|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(0, $result->id);
	}
	
	public function testUser_Deserialize_1_0_CreatedDateField() : void
	{
		$user = build_user();

		//Arrange Empty
		$line = "1||0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);

		//Arrange Invalid Characters
		$line = "1|text|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);

		//Arrange Invalid Format
		$line = "1|2023-01-31|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);

		//Arrange Success
		$line = "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame('2023', $result->created_date->format('Y'));
		self::assertSame('01', $result->created_date->format('m'));
		self::assertSame('31', $result->created_date->format('d'));
	}
	
	public function testUser_Deserialize_1_0_IsAdminField() : void
	{
		$user = build_user();

		//Arrange Empty
		$line = "1|20230131||jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_admin);

		//Arrange Invalid Format
		$line = "1|20230131|true|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_admin);

		//Arrange Explicit False
		$line = "1|20230131|0|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_admin);

		//Arrange Explicit True
		$line = "1|20230131|1|jack|abcde|j@gmail.com|1234567890|20230315||0";
		//Act
		$result = Eft_User::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertTrue($result->is_admin);
	}
	
	////////////////////////////////////////
	
	public function testTask_SerializeHeaders_UnknownVersion_ThrowsException() : void
	{
		//Arrange
		$format = "any";
		$this->expectExceptionMessage(MESSAGE_UNKNOWN_DATA_FORMAT);
		//Act Assert
		$result = Eft_Task::serialize_headers($format);
	}	
	
	public function testTask_SerializeHeaders_1_0() : void
	{
		//Arrange
		//Act
		$result = Eft_Task::serialize_headers(FORMAT_1_0);
		$fields = explode('|', $result);
		//Assert
		self::assertSame(9, count($fields));
	}	
	
	///////////////////////////////////
	
	public function testTask_Serialize_1_0_DefaultTask() : void
	{
		//Arrange
		$task = new Eft_Task();
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertSame('|||||0|||', $result);
	}
	
	public function testTask_Serialize_1_0_FullTask() : void
	{
		//Arrange
		$task = new Eft_Task();
		$task->id = 1;
		$task->created_by_user_id = 2;
		$task->created_date = DateTime::createFromFormat('Ymd', '20230131');
		$task->due_date = DateTime::createFromFormat('Ymd', '20230215');
		$task->text = 'abcdef';
		$task->is_closed = True;
		$task->closed_by_user_id = 3;
		$task->closed_date = DateTime::createFromFormat('Ymd', '20230216');
		$task->closing_text = 'hijklm';
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertSame("1|2|20230131|20230215|abcdef|1|3|20230216|hijklm", $result);
	}
	
	public function testTask_Serialize_1_0_Dates() : void
	{
		//Arrange
		$task = build_task();
		$task->created_date = DateTime::createFromFormat('Ymd', '20230131'); //single digit month
		$task->due_date = DateTime::createFromFormat('Ymd', '20231105'); //single digit day
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, "20230131") !== False);
		self::assertTrue(strpos($result, "20231105") !== False);
	}
	
	public function testTask_Serialize_1_0_Bools() : void
	{
		//Arrange True
		$task = build_task();
		$task->is_closed = True;
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, $task->text."|1|") !== False);

		//Arrange False
		$task->is_closed = False;
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, $task->text."|0|") !== False);
	}
	
	public function testTask_Serialize_1_0_DelimiterUsedInString_Replaced() : void
	{
		//Arrange
		$task = build_task();
		$task->text = "a|b";
		$task->closing_text = "c|d";
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, "a-b") !== False);
		self::assertTrue(strpos($result, "c-d") !== False);
	}
	
	public function testTask_Serialize_1_0_EndLineUsedInString_Replaced() : void
	{
		//Arrange
		$task = build_task();
		$task->text = "a\nb";
		$task->closing_text = "c\nd";
		//Act
		$result = $task->serialize(FORMAT_1_0);
		//Assert
		self::assertTrue(strpos($result, "a".ENCODED_END_LINE."b") !== False);
		self::assertTrue(strpos($result, "c".ENCODED_END_LINE."d") !== False);
	}
	
	public function testTask_Deserialize_NullLine_ReturnsNull() : void
	{
		//Arrange
		$line = null;
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result);
	}
	
	public function testTask_Deserialize_UnknownVersion_ThrowsException() : void
	{
		//Arrange
		$line = "a|b|c";
		$format = "any";
		$this->expectExceptionMessage(MESSAGE_UNKNOWN_DATA_FORMAT);
		//Act Assert
		$result = Eft_Task::deserialize($line, $format);
	}
	
	public function testTask_Deserialize_1_0_InvalidFormat_ReturnsDefaultValues() : void
	{
		//Arrange
		$line = "abc|def";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNotNull($result);
		self::assertSame(0, $result->id);
		self::assertSame(0, $result->created_by_user_id);
		self::assertSame(null, $result->created_date);
		self::assertSame(null, $result->due_date);
		self::assertSame('', $result->text);
		self::assertSame(false, $result->is_closed);
		self::assertSame(0, $result->closed_by_user_id);
		self::assertSame(null, $result->closed_date);
		self::assertSame('', $result->closing_text);
	}
	
	public function testTask_Deserialize_1_0_AllFieldsFound() : void
	{
		//Arrange
		$task = build_task();
		$line = $task->serialize(FORMAT_1_0);
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNotNull($result);
		self::assertTrue(tasks_match($task, $result));
	}
	
	public function testTask_Deserialize_1_0_IdField() : void
	{
		$task = build_task();

		//Arrange Leading Zero
		$line = "01|02|20230131|20230215|abcdef|1|03|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(1, $result->id);
		self::assertSame(2, $result->created_by_user_id);
		self::assertSame(3, $result->closed_by_user_id);

		//Arrange Large Int
		$line = "999999999|999999998|20230131|20230215|abcdef|1|999999997|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(999999999, $result->id);
		self::assertSame(999999998, $result->created_by_user_id);
		self::assertSame(999999997, $result->closed_by_user_id);

		//Arrange No Id Field - Defaults To Zero
		$line = "||20230131|20230215|abcdef|1||20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame(0, $result->id);
		self::assertSame(0, $result->created_by_user_id);
		self::assertSame(0, $result->closed_by_user_id);
	}
	
	public function testTask_Deserialize_1_0_DateFields() : void
	{
		//Arrange Empty
		$line = "1|2|||abcdef|1|3||hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);
		self::assertNull($result->due_date);
		self::assertNull($result->closed_date);

		//Arrange Invalid Characters
		$line = "1|2|text|text|abcdef|1|3|text|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);
		self::assertNull($result->due_date);
		self::assertNull($result->closed_date);

		//Arrange Invalid Format
		$line = "1|2|2023-01-31|2023-02-15|abcdef|1|3|2023-02-16|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertNull($result->created_date);
		self::assertNull($result->due_date);
		self::assertNull($result->closed_date);

		//Arrange Success
		$line = "1|2|20230131|20230215|abcdef|1|3|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame('2023', $result->created_date->format('Y'));
		self::assertSame('01', $result->created_date->format('m'));
		self::assertSame('31', $result->created_date->format('d'));
		self::assertSame('2023', $result->due_date->format('Y'));
		self::assertSame('02', $result->due_date->format('m'));
		self::assertSame('15', $result->due_date->format('d'));
		self::assertSame('2023', $result->closed_date->format('Y'));
		self::assertSame('02', $result->closed_date->format('m'));
		self::assertSame('16', $result->closed_date->format('d'));
	}
	
	public function testTask_Deserialize_1_0_BoolFields() : void
	{
		//Arrange Empty
		$line = "1|2|20230131|20230215|abcdef||3|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_closed);

		//Arrange Invalid Format
		$line = "1|2|20230131|20230215|abcdef|text|3|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_closed);

		//Arrange Explicit False
		$line = "1|2|20230131|20230215|abcdef|0|3|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertFalse($result->is_closed);

		//Arrange Explicit True
		$line = "1|2|20230131|20230215|abcdef|1|3|20230216|hijklm";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertTrue($result->is_closed);
	}
	
	public function testTask_Deserialize_1_0_TextFields() : void
	{
		//Arrange Empty
		$line = "1|2|20230131|20230215||1|3|20230216|";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame('', $result->text);
		self::assertSame('', $result->closing_text);

		//Arrange Encoded End Lines
		$line = "1|2|20230131|20230215|a".ENCODED_END_LINE."b|text|3|20230216|c".ENCODED_END_LINE."d";
		//Act
		$result = Eft_Task::deserialize($line, FORMAT_1_0);
		//Assert
		self::assertSame("a\nb", $result->text);
		self::assertSame("c\nd", $result->closing_text);
	}
	
	////////////////////////////////////////
}

?>