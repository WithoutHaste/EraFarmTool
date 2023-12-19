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
		$matches = "";
		//Id|CreatedDate|IsAdmin|Username|PasswordHashed|Email|PhoneNumber|LastLoginDate|IsDeactivated
		$found_match = preg_match('/(\d*)\|(\d*)\|([01])\|(\w*)\|(.*)\|(.*)\|(\d*)\|(\d*)\|([01])/i', $line, $matches);
		if(!$found_match) {
			return null;
		}
		$user = new Eft_User();
		$user->id = intval(eft_get_element($matches, 1));
		$user->created_date = DateTime::createFromFormat('Ymd', eft_get_element($matches, 2));
		$user->is_admin = (eft_get_element($matches, 3) == '1');
		$user->username = eft_get_element($matches, 4);
		$user->password_hashed = eft_get_element($matches, 5);
		$user->email = eft_get_element($matches, 6);
		$user->phone_number = eft_get_element($matches, 7);
		$user->last_login_date = DateTime::createFromFormat('Ymd', eft_get_element($matches, 8));
		$user->is_deactivated = (eft_get_element($matches, 9) == '1');
		
		return $user;
	}
}

?>