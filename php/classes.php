<?php

include_once("constants.php");
include_once("global_utils.php");

define("ENCODED_END_LINE", "%0A");

class Eft_Record {
	static function serialize_date($date) : string {
		try {
			if($date == null) return ""; //shouldn't be necessary, but for some reason the try/catch is not working as expected
			return $date->format('Ymd');
		} 
		catch (Exception $e) {
			return "";
		}		
	}

	static function serialize_bool($bool) : string {
		return ($bool) ? "1" : "0";
	}
		
	static function deserialize_date(?string $text) : ?DateTime {
		$result = DateTime::createFromFormat('Ymd', $text);
		if($result == false) {
			return null;
		}
		return $result;
	}

	static function deserialize_bool(?string $text) : bool {
		return ($text == "1");
	}
}

class Eft_User extends Eft_Record {
	public $id;
	public $created_date;
	public $is_admin = false;
	public $username;
	public $password_hashed;
	public $email;
	public $phone_number;
	public $last_login_date;
	public $session_key;
	public $is_deactivated;

	// returns string from serializing this user headers
	// end-line character is not included
	public static function serialize_headers(?string $format) : string {
		switch($format) {
			case FORMAT_1_0: return Eft_User::serialize_headers_1_0();
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns string from serializing this data format version 1.0 user headers
	// end-line character is not included
	private function serialize_headers_1_0() : string {
		return "Id|CreatedDate|IsAdmin|Username|PasswordHashed|Email|PhoneNumber|LastLoginDate|SessionKey|IsDeactivated";
	}

	// returns string from serializing this user record
	// end-line character is not included
	public function serialize(?string $format) : string {
		switch($format) {
			case FORMAT_1_0: return $this->serialize_1_0();
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns string from serializing this data format version 1.0 user record
	// end-line character is not included
	private function serialize_1_0() : string {
		if(str_contains($this->username, '|')
			|| str_contains($this->password_hashed, '|')
			|| str_contains($this->email, '|')
			|| str_contains($this->phone_number, '|')) {
			throw new Exception(MESSAGE_CANNOT_CONTAIN_PIPES);
		}
		
		return "{$this->id}|{$this->serialize_date($this->created_date)}|{$this->serialize_bool($this->is_admin)}|{$this->username}|{$this->password_hashed}|{$this->email}|{$this->phone_number}|{$this->serialize_date($this->last_login_date)}|{$this->session_key}|{$this->serialize_bool($this->is_deactivated)}";
	}
	
	// returns object from deserializing a user record
	public static function deserialize(?string $line, ?string $format) : ?Eft_User {
		switch($format) {
			case FORMAT_1_0: return Eft_User::deserialize_1_0($line);
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns object from deserializing a data format version 1.0 user record
	private static function deserialize_1_0(?string $line) : ?Eft_User {
		if(is_null($line)) {
			return null;
		}
		$columns = explode('|', $line);
		$user = new Eft_User();
		$user->id = intval(eft_get_element($columns, 0));
		$user->created_date = Eft_Record::deserialize_date(eft_get_element($columns, 1));
		$user->is_admin = Eft_Record::deserialize_bool(eft_get_element($columns, 2));
		$user->username = eft_get_element($columns, 3);
		$user->password_hashed = eft_get_element($columns, 4);
		$user->email = eft_get_element($columns, 5);
		$user->phone_number = eft_get_element($columns, 6);
		$user->last_login_date = Eft_Record::deserialize_date(eft_get_element($columns, 7));
		$user->session_key = eft_get_element($columns, 8);
		$user->is_deactivated = Eft_Record::deserialize_bool(eft_get_element($columns, 9));
		return $user;
	}
}

class Eft_Task extends Eft_Record {
	public $id;
	public $created_by_user_id;
	public $created_date;
	public $due_date;
	public $text;
	public $is_closed = false;
	public $closed_by_user_id;
	public $closed_date;
	public $closing_text;

	// returns string from serializing this headers
	// end-line character is not included
	public static function serialize_headers(?string $format) : string {
		switch($format) {
			case FORMAT_1_0: return Eft_Task::serialize_headers_1_0();
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns string from serializing this data format version 1.0 headers
	// end-line character is not included
	private function serialize_headers_1_0() : string {
		return "Id|CreatedByUserId|CreatedDate|DueDate|Text|IsClosed|ClosedByUserId|ClosedDate|ClosingText";
	}

	// returns string from serializing this record
	// end-line character is not included
	public function serialize(?string $format) : string {
		switch($format) {
			case FORMAT_1_0: return $this->serialize_1_0();
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns string from serializing this data format version 1.0 record
	// end-line character is not included
	// text fields have end-line characters stored as html encoding "%0A"
	private function serialize_1_0() : string {
		if($this->text != null) {
			$this->text = str_replace('|', '-', $this->text);
			$this->text = str_replace("\n", ENCODED_END_LINE, $this->text);
		}
		if($this->closing_text != null) {
			$this->closing_text = str_replace('|', '-', $this->closing_text);
			$this->closing_text = str_replace("\n", ENCODED_END_LINE, $this->closing_text);
		}
		
		return "{$this->id}|{$this->created_by_user_id}|{$this->serialize_date($this->created_date)}|{$this->serialize_date($this->due_date)}|{$this->text}|{$this->serialize_bool($this->is_closed)}|{$this->closed_by_user_id}|{$this->serialize_date($this->closed_date)}|{$this->closing_text}";
	}
	
	// returns object from deserializing a record
	public static function deserialize(?string $line, ?string $format) : ?Eft_Task {
		switch($format) {
			case FORMAT_1_0: return Eft_Task::deserialize_1_0($line);
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns object from deserializing a data format version 1.0 record
	private static function deserialize_1_0(?string $line) : ?Eft_Task {
		if(is_null($line)) {
			return null;
		}
		$columns = explode('|', $line);
		$task = new Eft_Task();
		$task->id = intval(eft_get_element($columns, 0));
		$task->created_by_user_id = intval(eft_get_element($columns, 1));
		$task->created_date = Eft_Record::deserialize_date(eft_get_element($columns, 2));
		$task->due_date = Eft_Record::deserialize_date(eft_get_element($columns, 3));
		$task->text = str_replace(ENCODED_END_LINE, "\n", eft_get_element($columns, 4));
		$task->is_closed = Eft_Record::deserialize_bool(eft_get_element($columns, 5));
		$task->closed_by_user_id = intval(eft_get_element($columns, 6));
		$task->closed_date = Eft_Record::deserialize_date(eft_get_element($columns, 7));
		$task->closing_text = str_replace(ENCODED_END_LINE, "\n", eft_get_element($columns, 8));
		return $task;
	}
}

?>