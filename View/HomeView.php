<?php
/******************************************
/*Home View page,
/*
/*****************************************/

require_once('View.php');

class HomeView extends View{

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
		echo '<form name="accounts" method="post" action="/BudgetBuddy/CheckBook.php/:Add">';
		echo '<button type="submit">Add an Account</button>';
		echo '</form>';
	}
}




?>


