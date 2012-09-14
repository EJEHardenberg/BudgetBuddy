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
	private $subMenu = array('MonthBack' => 'Prev. Month','Delete' =>  'Delete', 'Add' => 'Add', 'Edit' => 'Edit', 'MonthForward' => 'Next Month');
	private $accountToLoad = null;
	private $accounts = null;
	private $actionToTake = 'Display';
	private $curMonth = null;
	private $curYear = null;

	public function __construct($user){
		//We can populate and anything else from knowing the user to lookup
		$this->db = new Database();
		$this->db->connect();
		$this->username = $user;
		$this->userid = $this->db->matchNameToID($user);
		$this->accounts = $this->db->getAccountNamesByID($this->userid);
		if(isset($this->accounts[0])){
			$this->accountToLoad = rawurldecode($this->accounts[0]);
		}
		//Populate the current month and year from session if its set
		if( isset($_SESSION['curMonth'])){
			$this->curMonth = $_SESSION['curMonth'];	
		}else{
			$this->curMonth = date('m');
		}
		if( isset($_SESSION['curYear'])){
			$this->curYear = $_SESSION['curYear'];	
		}else{
			$this->curYear = date('Y');
		}
				

	}

	public function parseActions(){
		$url  = explode(':',end(explode('/', $_SERVER['REQUEST_URI']))) ;
		$this->accountToLoad = rawurldecode($url[0]);
		$this->actionToTake = rawurldecode($url[1]);
		//Transaction stuff
		if(isset($url[2])){
			$this->transID = $url[2];
		}
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
			case 'Prev.%20Month':
					$this->curMonth = strval($this->curMonth)-1;
					if($this->curMonth < 1){
						$this->curMonth = 12;
						$this->curYear = $this->curYear -1;
					}
					$_SESSION['curMonth'] = $this->curMonth;
					$_SESSION['curYear'] = $this->curYear;
			case 'Next%20Month':
					if($this->actionToTake == 'Next%20Month'){ 
						$this->curMonth = $this->curMonth+1;
						if($this->curMonth > 12){
							$this->curMonth = 1;
							$this->curYear = $this->curYear + 1;
						}
					}
					$_SESSION['curMonth'] = $this->curMonth;
					$_SESSION['curYear'] = $this->curYear;
			case 'Display':
				//If there are no accounts:
				if(is_null($this->accountToLoad)){
					echo '<span class = "Info">You have no account to load, use the buttons above to add some.</span>';
				}else{
					//Because we can fall in from the prev or next month if we're just straight display then we want this month
					if($this->actionToTake == 'Display'){
						$this->curMonth = date('m');
						$this->curYear = date('Y');
					}
					$month = date($this->curYear .'-'. $this->curMonth . '-t 23:59:59');
					//I wonder if this would break if the accountTOLoad was null.
					$transactions = $this->db->getTransactionsForMonth($month,$this->userid,$this->accountToLoad);
					//ADD BUTTON HERE
					echo '<h5 id="DropDownH5" onmouseout="DropDownColorChange(); " onmouseover="DropDownColorChange();"  onclick="dropClicked(); ">+ Add Transaction </h5><br />';
					echo '<form action = "/BudgetBuddy/CheckBook.php/'. $this->accountToLoad .':AddTransaction" method="post">';
					echo '<ul id="DropDownUL" >';
						echo '<li id="DropDownLI"> Name: <input = "text" class ="trans" name="name" value = ""/> </li>';
						echo '<li id="DropDownLI"> Amount: <input = "text" class ="trans" name="amt" value = ""/></li>';
						echo '<li id="DropDownLI"> Date: <input = "text" class ="trans" name="dte" value = "'. date('Y-m-d') .'"/> </li>';
						echo '<li id="DropDownLI"> <button type="submit" class = "trans"> Add Transaction </button> </li>';
					echo '</ul>';
					echo '<span class="HelpText" id="left">Really long names can be seen by hovering over them in the transaction view</span>';
					echo '</form>';

					echo '<div class ="largespacer"></div>';

					//This seems like an awfully good place for a table, or at least something like it
					echo '<table class ="Transaction">';
					echo '<tr><th class = "Transaction">ID</th><th class = "Transaction">Date</th><th class = "Transaction">Name</th><th class = "Transaction">Amount</th><th></th><th></th><th class = "Transaction">Account Balance</th>';
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
								case 'name':
									echo '<td class = "Transaction"><span title="' .  $transaction[$key] . '">'. substr($transaction[$key],0,6) . '</span></td>';
									break;
								default:
									echo '<td class = "Transaction">' .  $transaction[$key] . '</td>';
									break;
							}
						}
						//Put out the Edit and Delete button
						echo '<td class = "Transaction"><a class = "Transaction" href="/BudgetBuddy/CheckBook.php/Transaction:EditTransaction:' . $transaction['id'] .  '">Edit</a></td>';
						echo '<td class = "Transaction"><a class = "Transaction" href="/BudgetBuddy/CheckBook.php/Transaction:DeleteTransaction:' . $transaction['id'] . '">Delete</a></td>';
						echo '<td class = "Transaction"><a class = "Transaction" href="/BudgetBuddy/CheckBook.php/Transaction:Tag:' . $transaction['id'] . '">Tag</a></td>';
						//Empty for balance
						echo '<td></td>';
						//We could add a move transaction here to move one transaction from one account to anothet
						echo '</tr>';
					}
					echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td>';
						echo '<td class = "Transaction">';
						$act = $this->db->getAccountByName($this->accountToLoad,$this->userid);
						echo $act['name'] . ': $' . $act['amount'];
						echo '</td>';
					echo '</table>';
					//And now include the total amount in the Account:
					

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
					echo '<form action = "/BudgetBuddy/CheckBook.php/' . ($this->accountToLoad) . ':DeleteYes" method="post"> ';
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
				echo '<form id="Login" name="login" method="post" action="/BudgetBuddy/CheckBook.php/' .$this->accountToLoad.':EditAccount">';
				
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
				if($_POST['confirm'] == "yes"){

					if(!isset($this->accountToLoad) || !isset($_POST['name'])){
						echo 'Something has gone terribly wrong! Byebye.';
						echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 	
						break;				
					}

					//Post the new values to the database
					if($this->db->setAccountInfo($_POST['name'],$_POST['amount'],$this->userid,$this->accountToLoad)){
						echo 'Account Successfully Edited. Redirecting...';
						echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php/' . $this->accountToLoad .':Display" />'; 
					}else{
						echo 'An error occured, redirecting...';
						echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/CheckBook.php" />'; 					
					}

				}else{
					echo '<meta http-equiv="REFRESH" content="0; url=/BudgetBuddy/CheckBook.php/'.$accountToLoad.':Display" />'; 										
				}
				break;
			case 'AddTransaction':
				//Add transaction protocols
				if(($_POST["dte"] != "") && $_POST["amt"] != "" && $_POST["name"] != ""){
					//Validate the date somehow.
					$valid = preg_match('/^\d{4}\-\d{2}\-\d{2}/', $_POST['dte']);
					if($valid){
						//Check the number field for amount should have xxxx.xx or something along those lines
						$valid = preg_match('/\A[+-]?\d+(?:\.\d{0,2})?\z/', $_POST["amt"]);
						if($valid){
							//Now that all the numbers are valid (yay) actually add the transaction
							$sub = preg_match('/\-/', $_POST["amt"]);
							$success = $this->db->AddTransaction($this->accountToLoad,$this->userid,$_POST['amt'],$sub,$_POST["name"],$_POST['dte']);
							if($success){
								echo 'Transaction Successfully added.<br />Returning to Home Page';
							}else{
								echo 'There was a problem adding your transaction.<br /> Returning to Home Page';
							}
						}else{
							echo 'There was a problem with the amount entered, please follow the format of numbers, decimal point, and only 2 digits of change';
						}
					}else{
						echo 'There was a problem with your date field, please enter with the following form: yyyy-mm-dd with leading zeros.';
					}
				}else{
					echo 'There was a problem with adding your transaction.';	 					
				}
				//Redirect
				echo '<meta http-equiv="REFRESH" content="1; url=/BudgetBuddy/CheckBook.php/' . $this->accountToLoad .':Display" />';
				break;
			case 'DeleteTransaction':
				if(!is_null($this->transID)){
					$acct = $this->db->getTransactionInfo($this->transID);
					$success = $this->db->deleteTransaction($this->transID,$this->accountToLoad,$this->userid);
					if($success){
						echo 'Transaction Successfully Deleted<br />Redirecting...';
					}
					else{
						echo 'There was an issue deleting the transaction<br />Redirecting...';
					}
					$this->transID = null;
					echo '<meta http-equiv="REFRESH" content="1; url=/BudgetBuddy/CheckBook.php/' . $acct['accountname'] .':Display" />';
				}//If not then what are we deleting seriously?
				break;
			case 'EditTransaction':
				//Pretty form to edit the transaction
				if(!is_null($this->transID)){ 

					$info = $this->db->getTransactionInfo($this->transID);
					echo '<div class ="largespacer"></div>';
					echo '<form id="Login" name="login" method="post" action="/BudgetBuddy/CheckBook.php/' .$this->accountToLoad.':DOEditTransaction:'. $this->transID .'">';
					
						echo '<label class="Login">Transaction Description<br />';
						echo '</label>';
						echo '<input type="text" name="name" id="name"  class="rounded" value = "'. $info["name"] .'"/>';
						echo '<br />';
						echo  '<div class="largespacer"></div>';

						echo '<label class="Login">New Amount<br />';
						echo '</label>';
						echo '<input type="text" name="amount" id="amount" class="rounded" value = "'. $info["amount"].'"/>';

						echo '<label class="Login">Date<br />';
						echo '</label>';
						echo '<input type="text" name="date" id="date" class="rounded" value = "'. (View::convertReverseDate(View::convertSlashToHyph(View::getJustDate(($info["date"]))))).'"/>';

						echo '<div class="largespacer"></div>';
							echo '<button type="submit" class ="trans" name="confirm" value = "yes" >Submit</button>';
							echo '<button type="submit" class ="trans" name="confirm" value = "no" >Back</button>';
						echo '<div class="largespacer"></div>'; 	
					
					echo '</form>';	
				}
				break;
			case 'DOEditTransaction':
				if($_POST['confirm'] == "yes"){ 
					if(!is_null($this->transID)){
						$acct = $this->db->getTransactionInfo($this->transID);
						$success = $this->db->EditTransaction($this->transID,$acct['accountname'],$this->userid,$_POST);
						echo $success ? ' Transaction Edited Successfully' : 'Error Editing Transaction';
						echo '<br />Returning to Acccount...<meta http-equiv="REFRESH" content="1; url=/BudgetBuddy/CheckBook.php/' . $acct['accountname'].':Display" />';

					}//IF we have no id then none of this matters so break
				}
				break;
			case 'Tag':
				//ID for the item to be tagged
				if(!is_null($this->transID)){ 
					$info = $this->db->getTransactionInfo($this->transID);
					echo '<div class ="largespacer"></div>';
					echo '<form id="Login" name="login" method="post" action="/BudgetBuddy/CheckBook.php/' .$this->accountToLoad.':TagAdd:'. $this->transID .'">';
						echo '<div class="TagInfo">';
						echo '<span class="HelpText" id="left">Add a tag to your transaction to categorize it. Then try out the reports feature to check out your spending habits!</span>';
						echo '</div>';
						//List out info for transactions, and get the tags for that transactions.
						echo '<table>';
							echo '<tr><th>ID</th><th>Date</th><th>Account</th><th>Name</th><th></th><th></th><th>Tags</th></tr><tr>';
							foreach ($info as $key => $value) {
								//We do want to format things correctly:
								switch(strtolower($key)){
									case 'date':
										echo '<td class = "Transaction">' . View::getJustDate($info[$key]) . '</td>';
										break;
									case 'id':
										echo '<td class = "Transaction">' .  str_pad($info[$key],5,"0",STR_PAD_LEFT) . '</td>'; 
										break;
									case 'name':
										echo '<td class = "Transaction"><span title="' .  $info[$key] . '">'. substr($info[$key],0,6) . '</span></td>';
										break;
									default:
										echo '<td class = "Transaction">' .  $info[$key] . '</td>';
										break;
								}
							}
							//Get the list of tags with the transaction and put them in a drop down
							echo '<td>';
								echo '<div id="taglist"><select class="nice"><option>Tags...</option>';
									//Get the list!
									$tagList = $this->db->getTagsFor($this->transID);
									foreach ($tagList as $tag) {
										echo '<option>' . $tag . '</option>';
									}
								echo '</select></div>';
							echo '</td>';
							echo '</tr>';
						echo '</table>';


						//Instructional text
						echo '<div class="TagInfo">';
						echo '<span class="HelpText" id="left">To add a tag either write it in the space below or check the box of the existing tag you want to add.<br />To add multiple tags: check off multiple boxes, or enter the tags seperated by commas in the text field below.</span>';
						echo '</div>';

						//Text field to enter new field
						echo '<textarea name="entered_tags", rows = "2" cols="40" class="TagEnter"></textarea>';

						//Now show possible tags to add: with check boxes
						$allTags = $this->db->getAllTags();				
						echo '<div class = "" id ="Scrollable">';
							foreach ($allTags as $tag) {
								echo '<input type="checkbox" class = "TagCheck" name="checkTags[]" value="' . $tag . '"/>'.$tag;
							}
						echo '</div>';

						///Buttons for submit and go back
						echo '<div class="largespacer"></div>';
							echo '<button type="submit" class ="trans" name="confirm" value = "yes" >Submit</button>';
							echo '<button type="submit" class ="trans" name="confirm" value = "no" >Back</button>';
						echo '<div class="largespacer"></div>'; 	
					
					echo '</form>';	
				}
				break;
			case 'TagAdd':
				//ID for the item to be tagged
				if($_POST['confirm']=='yes'){ 
					if(!is_null($this->transID)){ 
						//Get the tags from the entered_tags textarea
						$tags = explode(',', $_POST['entered_tags']);
					 	//Get the tags from the checkboxes
						if(!empty($_POST['checkTags'])){
							foreach ($_POST['checkTags'] as $tag) {
								$tags[]=trim($tag);	
							}

							//Pass all those tags to the database to add to the transaction
							$this->db->addTagsToTransaction($tags,$this->transID);
						}
					}
				}
				//REDIRECT 
				echo '<br />Returning to Transaction...<meta http-equiv="REFRESH" content="1; url=/BudgetBuddy/CheckBook.php/Transactions:Tag:' . $this->transID .'" />';
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