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

		$theme = 'metal';
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
    	$statement->bindValue(4, 'metal',PDO::PARAM_STR);
    	return $statement->execute(); //returns true or false
	}

	public function attemptLogin($name, $pass){
		//we should insure that both the name and password aren't injected evil code.
		
		//We can grab some of the automated stuff:
		$ip = $_SERVER['REMOTE_ADDR'];
		//We need the hash and salt to verify the password
		$selector = $this->link->prepare('select salt, hash from accounts where username = "' . $name . '";');
		$selector->execute();
		$results = $selector->fetchall();
		//There should be only one returned array because of unique ness
		$result = $results[0];
		//prepend the salt to the password and hash it and see if it matches
		$salted = $result['salt'] . $pass;
		//I have to base 64 encode or mysql complains
		$checkHash = base64_encode(hash('sha256', $salted));
		
		var_dump(CRYPT_BLOWFISH);
	}

}

?>