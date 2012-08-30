<?php
/******************************************************
/*Home Controller class
/*
/*Will handle queries to the Home page and direct as neccesary
/*****************************************************/

//We need the database 
require_once('Database.php');

class Home {

	public function __construct($user){
		//We can populate theme and anything else from knowing the user to lookup
		$db = new Database();
		$db->connect();
		$userData = $db->getHomeData($user);


	}





}



?>