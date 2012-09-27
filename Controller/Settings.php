<?php
/****************************************
/* Check Book Controller
/*
/* Will handle the queries and such for the checkbook.
/****************************************/
require_once('Database.php');

class Settings{
	private $db = null;
	private $viewName = 'SettingsView';
	private $links = array( 'Home' => 'Home', 'Reports' => 'Reports' ,'CheckBook' => 'CheckBook');
	private $module = null;
	

	public function __construct($user){
		//We can populate and anything else from knowing the user to lookup
		$this->db = new Database();
		$this->db->connect();

	}

	public function valid($user){
		if(is_null($this->db)){return false;}
		//If the name exists then the function returns false so we ! it to make true
		return !$this->db->checkAvailableName($user);
	}

	public function parseActions(){
		$url  = explode(':',end(explode('/', $_SERVER['REQUEST_URI']))) ;
		$this->module = rawurldecode($url[0]);
	}

	public function doActionUser(){
		//Grab things out of the post and then arcall the database (remember to update logins to reflect new username)
				if(!$this->db->checkAvailableName($_POST['old'])){
					//If they exist in the database
					if($this->db->checkAvailableName($_POST['new'])){
						//If they can actually change their name to that
						if($_SESSION['userID']==$_POST['old']){ 
							//If they match the session id of whose logged in (dont want nobody changing other people)
							//Change em!
							if(  $this->db->updateUserName($_POST['old'],$_POST['new'])){
								$_SESSION['userID'] = $_POST['new'];
								return true;
							}
						}
					}
				}
				return false;
	}

	public function doActionTheme(){
		//Get the theme selected
				$newTheme = $_POST['theme'];
				//Make sure this theme exists in the database
				if($this->db->themeExists($newTheme)){
					//if it exists we can change it
					if($this->db->changeUserTheme($_SESSION['userID'],$newTheme)){
						//Success
						return true;
					}
				}
				return false;
	}

	public function doAction(){
		//Place to put the ugly switch for everything
		switch ($this->module) {
			case 'User':
				return $this->doActionUser();
			case 'Pass':
				//Generate new salt for them too!
				return $this->db->changePassword($_POST['old'],$_POST['new'],$_SESSION['userID']);
			case 'Theme':
				return $this->doActionTheme();
			case 'Tag':
				//Dunno If I'm going to just make a tag manager page or not yet

				break;
			default:
				//Return
				return false;
				break;
		}
	}

	public function render(){
			//Will render the page using the view functions and such
			require_once('../BudgetBuddy/View/' . $this->viewName . '.php');
			$view = new $this->viewName;
			echo '<h1 class ="Home">Settings</h1><hr>';
			//Render Settings links
			echo '<div>';
			//Render the tabs for changing things
			echo '<h3 id="changeHead">Change your...</h3>';
			if($this->module == "" || $this->module == "Settings.php"){ 
				$view->changeUser();
				$view->changePass();
				$view->changeTheme($this->db->getThemes());
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
				$view->TagManagerLink();
				echo '</div>';
				//Render the Menu Items
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
				echo '<div class = "largespacer"></div>';
			}else{
				//This is where we would call up the controller to deal with things, oh wait I am the controller!
				if($this->doAction()){
					echo 'Operation Completed Successfully';
				}else{
					echo 'Update failed, please check your input';
				}
				echo '<br />This is a redirect!';
			}
			$view->displayMenus($this->links);
			$view->logout();
	}

}