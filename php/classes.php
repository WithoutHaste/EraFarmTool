<?php

include_once("constants.php");
include_once("global_utils.php");

class Eft_User {
	public $id;
	public $created_date;
	public $is_admin = false;
	public $username;
	public $password_hashed;
	public $email;
	public $phone_number;
	public $last_login_date;
	public $is_deactivated;

	// returns string from serializing this user record
	// end-line character is not included
	public function serialize(?string $format) : string {
		switch($format) {
			case "1.0": return $this->serialize_1_0();
			default: throw new Exception(MESSAGE_UNKNOWN_DATA_FORMAT);
		}
	}
	
	// returns string from serializing this data format version 1.0 user record
	// end-line character is not included
	private function serialize_1_0() : string {
		return "{$this->id}|{$this->serialize_date($this->created_date)}|{$this->serialize_bool($this->is_admin)}|{$this->username}|{$this->password_hashed}|{$this->email}|{$this->phone_number}|{$this->serialize_date($this->last_login_date)}|{$this->serialize_bool($this->is_deactivated)}";
	}
	
	private function serialize_date($date) : string {
		try {
			if($date == null) return ""; //shouldn't be necessary, but for some reason the try/catch is not working as expected
			return $date->format('Ymd');
		} 
		catch (Exception $e) {
			return "";
		}		
	}
	
	private function serialize_bool($bool) : string {
		return ($bool) ? "1" : "0";
	}

	// returns object from deserializing a user record
	public static function deserialize(?string $line, ?string $format) : ?Eft_User {
		switch($format) {
			case "1.0": return Eft_User::deserialize_1_0($line);
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
		$user->created_date = Eft_User::deserialize_date(eft_get_element($columns, 1));
		$user->is_admin = Eft_User::deserialize_bool(eft_get_element($columns, 2));
		$user->username = eft_get_element($columns, 3);
		$user->password_hashed = eft_get_element($columns, 4);
		$user->email = eft_get_element($columns, 5);
		$user->phone_number = eft_get_element($columns, 6);
		$user->last_login_date = Eft_User::deserialize_date(eft_get_element($columns, 7));
		$user->is_deactivated = Eft_User::deserialize_bool(eft_get_element($columns, 8));
		return $user;
	}
	
	private static function deserialize_bool(?string $text) : bool {
		return ($text == "1");
	}
	
	private static function deserialize_date(?string $text) : ?DateTime {
		$result = DateTime::createFromFormat('Ymd', $text);
		if($result == false) {
			return null;
		}
		return $result;
	}
}

?>