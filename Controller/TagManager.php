<?php

//Tag Manager Class Controller
//Plan is to get the pieces of the database that deal with teh database to go through

class TagManager{
	private $db = null;

	public function __construct(){
		$this->db = new Database();
		$this->db->connect();
	}


}


?>