<?php

/*********************************************
/*Database Class for logging in and verifying 
/*and inserting sign ups.
/*********************************************/

include_once( "../config.php");

class Database{
	private $link = '';

	public function __construct(){}

	public function connect(){

		$this->link =  new PDO('mysql:host='.DATABASE_HOST.';dbname='.DATABASE_NAME,DATABASE_USER,DATABASE_PASS);
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
		$result = $this->link->prepare('select * from userinfo where username = ?;');
		$result->bindValue(1,$name,PDO::PARAM_STR);
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
    	$query = 'INSERT INTO userinfo (username,salt,hash,theme) VALUES (?,?,?,?);';
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

		$request = $this->link->prepare('SELECT theme FROM userinfo WHERE username = ? ;')	;
		$request->bindValue(1,$name,PDO::PARAM_STR);
		$request->execute();
		//There should be only one returned
		$result = $request->fetchall();
		$theme = $result[0];
		return $theme['theme'];
	}

	public function attemptLogin($name, $pass){
		if(!isset($this->link)){ return false;}
		//we should insure that both the name and password aren't injected evil code.
		
		//We can grab some of the automated stuff:
		$ip = $_SERVER['REMOTE_ADDR'];
		//We need the hash and salt to verify the password
		$selector = $this->link->prepare('select salt, hash from userinfo where username = ?;');
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

	public function matchNameToID($username){
		//Fairly simple function
		if(!isset($this->link)){ return false;}
		$matchQ = $this->link->prepare('SELECT userid FROM userinfo WHERE username = ? ;');
		$matchQ->bindValue(1,$username,PDO::PARAM_STR);
		$matchQ->execute();

		$results = $matchQ->fetchall();
		$result = $results[0];
		return $result['userid'];
	}

	public function getHomeData($username){
		//Gives back an associative array containing:
		//last login, account balances. last login will be first, balances after
		if(!isset($this->link)){ return false;}

		//Get last login
		$lastLog = $this->link->prepare('SELECT logged_time FROM logins WHERE username = ? ORDER BY logged_time DESC limit 1;');
		$lastLog->bindValue(1,$username,PDO::PARAM_STR);
		$lastLog->execute();
		$result = $lastLog->fetchall();
		$result = $result[0];
		$lastLogin = $result['logged_time'];



		//Get the account balances
		$actGet = $this->link->prepare('SELECT * FROM accounts WHERE userid = ?');
		$actGet->bindValue(1,$this->matchNameToID($username),PDO::PARAM_INT);
		$actGet->execute();

		//IF we get no accounts the home page is going to prompt to send them to make accounts page
		$accounts = $actGet->fetchall();

		return array($lastLogin,$accounts);

	}

	public function getAccountNamesByID($userId){
		if(is_null($this->link)){return false;}

		//Snag Account Names
		$qAccount = $this->link->prepare('SELECT name FROM accounts WHERE userid = ?;');
		$qAccount->bindValue(1,$userId,PDO::PARAM_INT);
		$qAccount->execute();

		$results =  $qAccount->fetchall();
		$names = array();
		foreach ($results as $result) {
			$names[] = $result['name'];
		}
		return $names;

	}

	public function getTransactionsForMonth($month,$userid,$accountname){
		//give this function (for month) the last day of the month. so for septemeber it be 2012-09-30 23:59:59
		//select * from logins where logged_time betweeen date_sub(now(), interval 1 month) and now();
		$log = $this->link->prepare('SELECT id,date,name,amount from transactions WHERE date between DATE_SUB(?, INTERVAL 1 MONTH) and ? AND userid = ? AND accountname = ? ORDER BY date DESC');
		$log->bindValue(1,$month,PDO::PARAM_STR);
		$log->bindValue(2,$month,PDO::PARAM_STR);
		$log->bindValue(3,$userid,PDO::PARAM_STR);
		$log->bindValue(4,$accountname,PDO::PARAM_STR);
		$log->execute();
		
		return $log->fetchall(PDO::FETCH_ASSOC);
	}

	public function deleteAccount($accountName,$userid){
		//This function does exactly what it says it does
		$delCom = $this->link->prepare('DELETE FROM accounts WHERE userid = ? AND name = ?');
		$delCom->bindValue(1,$userid,PDO::PARAM_STR);
		$delCom->bindValue(2,$accountName,PDO::PARAM_STR);
		return $delCom->execute();
	}

	public function addAccount($accountName, $initialAmount,$userid){
		$addCom = $this->link->prepare('INSERT INTO accounts (userid,name,amount) VALUES (?,?,?);')	;
		$addCom->bindValue(1,$userid,PDO::PARAM_INT);
		$addCom->bindValue(2,$accountName, PDO::PARAM_STR);
		$addCom->bindValue(3,$initialAmount,PDO::PARAM_STR);//such crap that pdo doesnt have decimal
		return $addCom->execute();
	}

	public function getAccountByName($accountName,$userid){
		$getAcc = $this->link->prepare('SELECT name,amount FROM accounts WHERE userid = ? AND name = ?');
		$getAcc->bindValue(1,$userid,PDO::PARAM_STR);
		$getAcc->bindValue(2,$accountName,PDO::PARAM_STR);
		$getAcc->execute();

		$result =  $getAcc->fetchall(PDO::FETCH_ASSOC);
		return $result[0];

	}

	public function setAccountInfo($accountName,$amount,$userid,$oldName){
		//This function will set the account to the info passed in.
		$setAcc = $this->link->prepare('UPDATE accounts SET name = ?, amount = ? WHERE userid = ? AND name = ?;');
		$setAcc->bindValue(1,$accountName,PDO::PARAM_STR);
		$setAcc->bindValue(2,$amount,PDO::PARAM_STR);
		$setAcc->bindValue(3,$userid,PDO::PARAM_STR);
		$setAcc->bindValue(4,$oldName,PDO::PARAM_STR);
		return $setAcc->execute();
	}

	public function addTransaction($account, $userid, $amount, $sub, $name, $date){
		//Attempt to add the transaction
		$trans = $this->link->prepare('INSERT INTO transactions (userid,accountname,name,amount,date) VALUES (?,?,?,?,?);');
		$trans->bindValue(1,$userid,PDO::PARAM_INT);
		$trans->bindValue(2,$account,PDO::PARAM_STR);
		$trans->bindValue(3,$name,PDO::PARAM_STR);
		$trans->bindValue(4,$amount,PDO::PARAM_STR);
		$trans->bindValue(5,View::convertPHPDate($date),PDO::PARAM_STR);
		$transAdd = $trans->execute();

		//If we didn't add it then that sucks:
		if(!$transAdd){return false;}

		//We won therefore we should update the account, and hopefully it exists, if it doesnt then we'll have to take out the transaction
		$old = $this->link->prepare('SELECT amount FROM accounts WHERE userid = ? AND name = ?;');
		$old->bindValue(1,$userid,PDO::PARAM_STR);
		$old->bindValue(2,$account,PDO::PARAM_STR);
		$old->execute();

		//Get the old amount
		$results = $old->fetchall(PDO::PARAM_STR);
		$result = $results[0];
		$oldAmount  = $result['amount'];

		//apply the new transaction to the old amount
		if($sub){
			$amount = strval($oldAmount) + strval($amount)*-1;	
		}else{
			$amount = strval($oldAmount) - strval($amount);	
		}
		

		//Update the account
		$acct = $this->link->prepare('UPDATE accounts SET amount = ? WHERE name = ? AND userid = ?');
		$acct->bindValue(1,$amount,PDO::PARAM_STR);
		$acct->bindValue(2,$account,PDO::PARAM_STR);
		$acct->bindValue(3,$userid,PDO::PARAM_STR);
		$success =  $acct->execute();

		if(!$success){
			//Damnit. Now we get rid of the transaction
			$remove = $this->link->prepare('DELETE FROM transactions WHERE userid = ? AND name = ? AND accountname = ? AND date = ? AND amount = ?');
			$remove->bindValue(1,$userid,PDO::PARAM_STR);
			$remove->bindValue(2,$name,PDO::PARAM_STR);
			$remove->bindValue(3,$account,PDO::PARAM_STR);
			$remove->bindValue(4,$date,PDO::PARAM_STR);
			$remove->bindValue(5,$amount,PDO::PARAM_STR);
			$remove->execute();
		}

		return $success;

	}

	//Sadly we can't use this above because of the lack of id up there.
	public function deleteTransaction($id,$account,$userid){
		//Get how much we have to modify the account by first
		var_dump($id);
		var_dump($userid);
		var_dump($account);
		$get = $this->link->prepare('SELECT * FROM  transactions WHERE id = ?');
		$get->bindValue(1,$id);
		$get->execute();

		$results = $get->fetchall(PDO::FETCH_ASSOC);
		$trans = $results[0];
		var_dump($trans);
		$tAmount = $trans['amount'];

		$old = $this->link->prepare('SELECT amount FROM accounts WHERE userid = ? AND name = ?;');
		$old->bindValue(1,$userid,PDO::PARAM_STR);
		$old->bindValue(2,$trans['accountname'],PDO::PARAM_STR);
		$old->execute();

		//Get the old amount
		$results = $old->fetchall(PDO::PARAM_STR);
		$result = $results[0];
		$oldAmount  = $result['amount'];

		//I don't think I need to check the sign. if its + then that means it was a subtraction to the original account so I have to add it in
		//and if it was a - then it added to the account but adding a - will still subtract from the account so yay
		$new = strval($oldAmount) + strval($tAmount);
		var_dump($oldAmount);
		var_dump($new);
		var_dump($tAmount);
		
		$modAccount = $this->link->prepare('UPDATE accounts SET amount = ? WHERE name = ? AND userid = ?');
		$modAccount->bindValue(1,$new,PDO::PARAM_STR);
		$modAccount->bindValue(2,$trans['accountname'],PDO::PARAM_STR);
		$modAccount->bindValue(3,$userid,PDO::PARAM_STR);
		$success =  $modAccount->execute();

		if(!$success){return false;}

		$del = $this->link->prepare('DELETE FROM transactions WHERE id = ?;');
		$del->bindValue(1,$id);
		return $del->execute();
	}


}

?>