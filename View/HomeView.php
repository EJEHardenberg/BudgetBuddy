<?php
/******************************************
/*Home View page,
/*
/*Not making a class because its unneccesary.
/*****************************************/

class HomeView{

	function formatDate($date){
		//"2012-08-30 18:47:37" -> 6:47pm on 08/30/12
		$midway = explode(' ',$date);
		$dString = explode('-',$midway[0]);
		$tString = explode(':',$midway[1]);
		$hour = intval($tString[0]);
		$ampm = ($hour > 11 ? 'pm' : 'am');
		$hour = strval($hour % 12);
		return $hour . ':'  . $tString[1] . $ampm . ' on ' . $dString[1] . '/' . $dString[2] . '/' . substr($dString[0],-2);

	}


	function getWelcome($username,$lastLogin){
		echo '<h1 class = "Welcome">Welcome back ' . $username .'</h1>';
		echo '<p class = "Date">Last login at '. $this->formatDate($lastLogin) . '</p><hr>';
	}

	function getAccounts($accounts){
		//accounts should be stored in the session and this function will return false if that doesn't exist
		echo '<div class = "Accounts">';
		echo '<h2 class = "Accounts">Your Accounts:</h2>';
		echo '<ul class = "Accounts">';

		//Echo out each account
		foreach ($accounts as $account) {
			var_dump($account);
		}

		echo '</ul>';
		echo '</div>';
		//Returns true
		return true;
	}

	function createAccountButton(){
		//Displayed if there are no accounts
	}


}




?>


