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
			//var_dump($account);
			echo '<li><span class = "Spacer"><br /></span><span class = "AccountName">' . $account['name'] . ': </span><span class ="AccountAmount">$' . $account['amount'] . '</span></li>';
			echo '<hr class ="Accounts">';
		}

		echo '</ul><div class = "largespacer"></div>';
		$this->createAccountButton();
		echo '</div>';
		//Returns true
		return true;
	}

	function noAccounts(){
		echo '<div class = "Accounts">';
		echo '<h2 class = "Accounts">You have no accounts!</h2>';
		echo '</ul><div class = "largespacer"></div>';
		echo "<div class = 'Info'>Looks like you haven't made any accounts yet! Click the button below to get started!</div>";
		echo '</ul><div class = "largespacer"></div>';
		echo '</ul><div class = "largespacer"></div>';

		$this->createAccountButton();
		echo '</div>';

	}

	function createAccountButton(){
		//Displayed if there are no accounts
		echo '<form name="accounts" method="post" action="Login.php">';
		echo '<button type="submit">Add an Account</button>';
		echo '</form>'
;	}

	function displayMenus($links){
		//Home page links to settings, checkbook and reports
		echo '<div class = "Menus">';
		echo '<div class = "largespacer"></div>';

		//use a horizontal list
		echo '<ul class = "Menu">';
	
		foreach ($links as $link) {
			echo '<li class="Menu">';
			echo '<form name = "' . $link . '" method="post" action = "../BudgetBuddy/Controller/' . str_replace(' ', '', $link) . '.php">';
			echo '<button class = "Menu"  type="submit">'. $link .'</button>';
			echo '</form>';
			echo '</li><span class = "MenuSpacer"><br /></span>';
		}
		

		echo '</ul>';
		echo '</div>';
	}


}




?>


