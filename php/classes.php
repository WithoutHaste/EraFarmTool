<?php

include_once("constants.php");

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
		$user->id = intval($matches[1]); //todo move the safe_get method to a shared location, use it here
		$user->created_date = DateTime::createFromFormat('YYYYddmm', $matches[2]); //todo is there a just-Date class?
		$user->is_admin = ($matches[3] == '1');
		$user->username = $matches[4];
		$user->password_hashed = $matches[5];
		$user->email = $matches[6];
		$user->phone_number = $matches[7];
		$user->last_login_date = DateTime::createFromFormat('YYYYddmm', $matches[8]); //todo is there a just-Date class?
		$user->is_deactivated = ($matches[9] == '1');
		
		return $user;
	}
}

?>