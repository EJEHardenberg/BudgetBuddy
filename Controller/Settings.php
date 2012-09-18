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

	public function render(){
			//Will render the page using the view functions and such
			require_once('../BudgetBuddy/View/' . $this->viewName . '.php');
			$view = new $this->viewName;
			echo '<h1 class ="Home">Settings</h1><hr>';
			//Render Settings links
			echo '<div>';
			//Render the tabs for changing things
			echo '<h3 id="changeHead">Change your...</h3>';
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
			$view->displayMenus($this->links);
			$view->logout();
	}

}