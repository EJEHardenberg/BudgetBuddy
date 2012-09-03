<?php
/****************************************
/* Check Book Controller
/*
/* Will handle the queries and such for the checkbook.
/****************************************/
require_once('Database.php');

class CheckBook{
	private $viewName = 'CheckBookView';
	private $username = null;
	private $userid = null;
	private $db = null;
	private $links = array( 'Home' => 'Home', 'Reports' => 'Reports' ,'Settings' => 'Settings');
	private $subMenu = array('MonthBack' => '<','Delete' =>  'Delete', 'Add' => 'Add', 'Edit' => 'Edit', 'MonthForward' => '>');
	private $accountToLoad = null;
	private $accounts = null;
	private $actionToTake = 'Display';

	public function __construct($user){
		//We can populate and anything else from knowing the user to lookup
		$this->db = new Database();
		$this->db->connect();
		$this->username = $user;
		$this->userid = $this->db->matchNameToID($user);
		$this->accounts = $this->db->getAccountNamesByID($this->userid);
		if(isset($this->accounts[0])){
			$this->accountToLoad = $this->accounts[0];
		}

	}

	public function parseActions(){
		$url  = explode(':',end(explode('/', $_SERVER['REQUEST_URI']))) ;
		$this->accountToLoad = $url[0];
		$this->actionToTake = $url[1];
	}

	public function valid($user){
		if(is_null($this->db)){return false;}
		//If the name exists then the function returns false so we ! it to make true
		return !$this->db->checkAvailableName($user);
	}

	private function transactionArea(){
		echo '<div class = "TransactionArea" id ="Scrollage">';
		switch($this->actionToTake){
			case 'Display':
				//If there are no accounts:
				if(is_null($this->accountToLoad)){
					echo '<span class = "Info">You have no account to load, use the buttons above to add some.</span>';
				}else{
					$month = date('Y-m-t 23:59:59');
					var_dump($this->accountToLoad);
					$this->db->getTransactionsForMonth($month,$this->userid,$this->accountToLoad);

				}
				break;
		}
		echo '</div>';
	}

	public function render(){
		//Will render the page using the view functions and such
		require_once('../BudgetBuddy/View/' . $this->viewName . '.php');
		$view = new $this->viewName;
		echo '<h1 class ="Home">'.$this->username."'s".' Check Book</h1><hr>';
		//Render account tabs and their items:
		echo '<div class = "CheckBook">';
		//Render the tabs for changing accounts
		$view->displayAccountTabs($this->accounts );
		$view->displaySubMenus($this->subMenu,$this->accountToLoad);	
		//This is where the action takes affect, display should show transactions
		//Delete should give prompt to remove account, add gives options to add
		//edit should bring up the account details and allow them to be cchanged
		//Render the Transactions:
		$this->transactionArea();

		echo '</div>';
		//Render the Menu Items
		$view->displayMenus($this->links);
		$view->logout();
	}

}



?>