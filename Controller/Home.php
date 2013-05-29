<?php
/******************************************************
/*Home Controller class
/*
/*Will handle queries to the Home page and direct as neccesary
/*****************************************************/

//We need the database 
require_once('Database.php');

class Home {
	private $viewName = 'HomeView';
	private $username = null;
	private $lastLogin = null ;
	private $accounts = null;
	private $links = array( 'CheckBook' => 'Check Book', 'Reports' => 'Reports' ,'Settings' => 'Settings');

	public function __construct($user){
		//We can populate theme and anything else from knowing the user to lookup
		$db = new Database();
		$db->connect();
		$userData = $db->getHomeData($user);
		$this->username = $user;

		//There will always be a last login, even on the first login
		$lastLogin = $userData[0];
		//Check for the accounts existing:
		$accounts = $userData[1];
		if(isset($accounts[0])){
			$accountsExist = true;
		}else{
			$accountsExist = false;
		}

		$this->userData = $userData;
		$this->preRender($lastLogin, $accountsExist, $accounts);

	}



	public function preRender($lastLogin, $exists, $accounts){
		//Prepares the data for the view by determining to give accounts or a button.
		$this->lastLogin = $lastLogin;
		if($exists){
			$this->accounts = $accounts;
		}else{
			$this->accounts = null;
		}


	}

	public function render(){
		require_once('View/' . $this->viewName . '.php');
		$view = new $this->viewName;
		echo '<h1 class ="Home">Budget Buddy Home</h1><hr>';
		//Render welcome and last login time
		$view->getWelcome($this->username,$this->lastLogin);
		//Render accounts and button to add a new one
		if(!is_null($this->accounts)){
			$view->getAccounts($this->accounts);
		}else{
			//Render The OMG you don't have anything yet message
			$view->noAccounts();
		}
		//Render the Menu Items
		$view->displayMenus($this->links);
		$view->logout();
	}


}



?>