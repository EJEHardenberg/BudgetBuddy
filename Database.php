<?php

/*********************************************
/*Database Class for logging in and verifying 
/*and inserting sign ups.
/*********************************************/
//Get database information
include "../config.php";

class Database{
	private $link = '';

	public function __construct(){}

	public function connect(){
		$this->link =  new PDO('mysql:dbname='. DATABASE_NAME .';host='. DATABASE_HOST,DATABASE_USER,DATABASE_PASS);
		$this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if(isset($this->link)){
			return true;
		}else{
			return false;
		}
	}

	public function checkAvailableName($name){
		//Try to select the name from the database, if we fail then
		//we can use the name as an available name
		if($this->link == ''){ return false;}
		
		//We should verify the name to not have anything bad in it
		if(preg_match("/\"/", $name)){
			//we'll have none of that
			return false;
		}
		
		//Run the query and check for empty set
		$result = $this->link->prepare('select * from accounts where username = "' . $name . '";');
		$result->execute();
		if(count($result->fetchall())  > 0){
			return false;
		}

		return true;
	}

	public function signUserUp($name, $pass){
		//get our defaults and crpyo on
		if(!isset($this->link)){ return false;}

		$theme = DEFAULT_THEME;
		$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
    	$salt =  base64_encode(mcrypt_create_iv($size, MCRYPT_DEV_RANDOM));
    	$salted = $salt . $pass;
    	
    	//I have to base 64 encode or mysql complains
    	$useHash = base64_encode(hash('sha256',$salted));
    	$query = 'INSERT INTO accounts (username,salt,hash,theme) VALUES (?,?,?,?);';
    	$statement = $this->link->prepare($query);
    	$statement->bindValue(1, $name, PDO::PARAM_STR);
    	$statement->bindValue(2, $salt, PDO::PARAM_STR);
    	$statement->bindValue(3, $useHash, PDO::PARAM_STR);
    	$statement->bindValue(4, DEFAULT_THEME,PDO::PARAM_STR);
    	return $statement->execute(); //returns true or false
	}

	private function compareHashes($hash1, $hash2){
		$hashgood = true;
		for($i = 0; $i < strlen($hash1); $i++){
			if($hash1[$i] != $hash2[$i]){
				$hashgood = false;
			}
		}
		return $hashgood;
	}

	public function getUserTheme($name){
		if(!isset($this->link)){ return DEFAULT_THEME;}		
	}

	public function attemptLogin($name, $pass){
		//we should insure that both the name and password aren't injected evil code.
		
		//We can grab some of the automated stuff:
		$ip = $_SERVER['REMOTE_ADDR'];
		//We need the hash and salt to verify the password
		$selector = $this->link->prepare('select salt, hash from accounts where username = ?;');
		$selector->bindValue(1,$name,PDO::PARAM_STR);
		$selector->execute();
		$results = $selector->fetchall();
		//There should be only one returned array because of unique ness
		$result = $results[0];
		//prepend the salt to the password and hash it and see if it matches
		$salted = $result['salt'] . $pass;
		//I have to base 64 encode or mysql complains
		$checkHash = base64_encode(hash('sha256', $salted));
		//Now check the hash... sloooowly
		
		if($this->compareHashes($checkHash, $result['hash'])){
			//Valid!''
			$log = $this->link->prepare('INSERT INTO logins (username, success, ip_address, logged_time) VALUES ( ?,?,?,?);');
			$log->bindValue(1,$name,PDO::PARAM_STR);
			$log->bindValue(2,true,PDO::PARAM_INT);
			$log->bindValue(3,$ip,PDO::PARAM_STR);
			$log->bindValue(4,date('Y-m-d H:i:s'),PDO::PARAM_STR);
			$log->execute();
			return true;
		}else{
			//Send back the error!
			$log = $this->link->prepare('INSERT INTO logins (username, success, ip_address, logged_time) VALUES ( ?,?,?,?);');
			$log->bindValue(1,$name,PDO::PARAM_STR);
			$log->bindValue(2,false,PDO::PARAM_INT);
			$log->bindValue(3,$ip,PDO::PARAM_STR);
			$log->bindValue(4,date('Y-m-d H:i:s'),PDO::PARAM_STR);
			$log->execute();
			
			return false;
		}


	}

}

?>