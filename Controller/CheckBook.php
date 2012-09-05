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
		echo '<div class = "TransactionArea" id ="Scrollable">';
		switch($this->actionToTake){
			//Displays all transactions for an account
			case 'Display':
				//If there are no accounts:
				if(is_null($this->accountToLoad)){
					echo '<span class = "Info">You have no account to load, use the buttons above to add some.</span>';
				}else{
					$month = date('Y-m-t 23:59:59');
					//I wonder if this would break if the accountTOLoad was null.
					$transactions = $this->db->getTransactionsForMonth($month,$this->userid,$this->accountToLoad);
					//ADD BUTTON HERE
					echo '<h5 id="DropDownH5" onmouseout="DropDownColorChange(); " onmouseover="DropDownColorChange();"  onclick="dropClicked(); ">+ Add Transaction </h5><br />';
					echo '<form action = "/BudgetBuddy/CheckBook.php/'. $this->accountToLoad .':AddTransaction" method="post">';
					echo '<ul id="DropDownUL" >';
						echo '<li id="DropDownLI"> Name: <input = "text" class ="trans" name="name" /> </li>';
						echo '<li id="DropDownLI"> Amount: <input = "text" class ="trans" name="amt" /></li>';
						echo '<li id="DropDownLI"> Date: <input = "text" class ="trans" name="dte" value = "'. date('Y-m-d') .'"/> </li>';
						echo '<li id="DropDownLI"> <button type="submit" class = "trans"> Add Transaction </button> </li>';
					echo '</ul>';
					
					echo '</form>';

					echo '<div class ="largespacer"></div>';

					//This seems like an awfully good place for a table, or at least something like it
					echo '<table class ="Transaction">';
					echo '<tr><th class = "Transaction">ID</th><th class = "Transaction">Date</th><th class = "Transaction">Name</th><th class = "Transaction">Amount</th>';
					echo '<th></th><th></th></tr>';
					foreach ($transactions as $transaction) {
						echo '<tr>';
						foreach ($transaction as $key => $value) {
							//We do want to format things correctly:
							switch(strtolower($key)){
								case 'date':
									echo '<td class = "Transaction">' . View::getJustDate($transaction[$key]) . '</td>';
									break;
								case 'id':
									echo '<td class = "Transaction">' .  str_pad($transaction[$key],5,"0",STR_PAD_LEFT) . '</td>'; 
									break;
								default:
									echo '<td class = "Transaction">' .  $transaction[$key] . '</td>';
									break;
							}
						}
						//Put out the Edit and Delete button
						echo '<td class = "Transaction"><a class = "Transaction" href="/BudgetBuddy/CheckBook.php/Transaction:Edit:' . $transaction['id'] .  '">Edit</a></td>';
						echo '<td class = "Transaction"><a class = "Transaction" href="/BudgetBuddy/CheckBook.php/Transaction:Delete:' . $transaction['id'] . '">Delete</a></td>';
						//We could add a move transaction here to move one transaction from one account to anothet
						echo '</tr>';
					}
					echo '</table>';

				}
				break;
			//Add is for adding an account only
			case 'Add':
				//Maybe I'll ask josh about validating input before submitting for the number stuff
				echo '<div class ="largespacer"></div>';
				echo '<form id="Login" name="login" method="post" action="/BudgetBuddy/CheckBook.php/:AddAccount">';
				
					echo '<label class="Login">Account Name<br />';
					echo '</label>';
					echo '<input type="text" name="name" id="name"  class="rounded"/>';
					echo '<br />';
					echo  '<div class="largespacer"></div>';

					echo '<label class="Login">Initial Amount<br />';
					echo '</label>';
					echo '<input type="text" name="amount" id="amount" class="rounded"/>';

					echo '<div class="largespacer"></div>';
						echo '<button type="submit" class ="trans" name="confirm" value = "yes" >Submit</button>';
						echo '<button type="submit" class ="trans" name="confirm" value = "no" >Back</button>';
					echo '<div class="largespacer"></div>'; 	
				
				echo '</form>';

				break;
			case 'AddAccount':
					if(!is_null($_POST['confirm'])){
						$success = $this->db->AddAccount($_POST['name'],$_POST['amount'],$this->userid);
						if($success){
							echo 'Account Successfully Created!<br />';
							echo 'Redirecting you to your new account...';
							echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php/' . $_POST['name'] .':Display" />'; 
						}else{
							echo 'Sorry we couldn\'t create your account right now!<br />';
							echo 'Redirecting you to the main page';
							echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 
						}
					}else{
						echo 'Sorry, we couldn\'t create the account for some reason';
					}
					break;
			//For Deleting an account:
			case 'Delete':
				if(is_null($this->accountToLoad)){
					echo '<span class = "Info">You can\'t delete an account that doesn\'t exist!</span>';
				}else{
					//Oh hey an account exists? Better make sure of that
					//Put out a comfirmation screen:
					echo '<div class ="largespacer"></div>';
					echo '<form action = "/BudgetBuddy/CheckBook.php/' . $this->accountToLoad . ':DeleteYes" method="post"> ';
						echo 'Deleting the account ' . $this->accountToLoad . ' is permanent and you will lose all transactions on this account, are you sure?';
						echo '<div class ="largespacer"></div>';
						echo '<button type = "submit" name="confirm" value = "yes">Delete This Account</button>';
						echo '<div class ="largespacer"></div>';
						echo '<button type = "submit" name="confirm" value = "no">Don\'t Delete This Account</button>';
					echo '</form>';
					echo '<div class ="largespacer"></div>';
				}
				break;
			case 'DeleteYes':
				if(isset($_POST['confirm'])){
					if($_POST['confirm'] == 'yes'){
						$success = $this->db->deleteAccount($this->accountToLoad,$this->userid);
						if($success){
							echo 'Account Successfully Deleted.';
							//Redirect:
							echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 
						}else{
							echo 'There was a problem deleting the account. Please try again';
							echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 
						}
					}else{
						echo '<meta http-equiv="REFRESH" content="0; url=/BudgetBuddy/CheckBook.php/'.$this->accountToLoad .':Display" />'; 
					}
				}
				break;
			case 'Edit':
				if(!isset($this->accountToLoad)){
					echo 'No account to Edit!<br /> Redirecting...';
					echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 
				}
				//If theres an account to load than we should grab its data, populate the fields and pretty much do the same thing as add edit but with a different function
				$info = $this->db->getAccountByName($this->accountToLoad,$this->userid);
				echo '<div class ="largespacer"></div>';
				echo '<form id="Login" name="login" method="post" action="/BudgetBuddy/CheckBook.php/:EditAccount">';
				
					echo '<label class="Login">Account Name<br />';
					echo '</label>';
					echo '<input type="text" name="name" id="name"  class="rounded" value = "'. $info["name"] .'"/>';
					echo '<br />';
					echo  '<div class="largespacer"></div>';

					echo '<label class="Login">New Amount<br />';
					echo '</label>';
					echo '<input type="text" name="amount" id="amount" class="rounded" value = "'. $info["amount"].'"/>';

					echo '<div class="largespacer"></div>';
						echo '<button type="submit" class ="trans" name="confirm" value = "yes" >Submit</button>';
						echo '<button type="submit" class ="trans" name="confirm" value = "no" >Back</button>';
					echo '<div class="largespacer"></div>'; 	
				
				echo '</form>';				
				break;
			case 'EditAccount':
				//Actually edit and then redirect


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
		echo '<div class = "largespacer"></div>';
		echo '<div class = "largespacer"></div>';
		echo '<div class = "largespacer"></div>';
		echo '<div class = "largespacer"></div>';
		$view->displayMenus($this->links);
		$view->logout();
	}

}



?>