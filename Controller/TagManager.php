<?php

//Tag Manager Class Controller
//Plan is to get the pieces of the database that deal with teh database to go through
require_once('../BudgetBuddy/View/TagManagerView.php');

class TagManager{
	private $db = null;
	private $tags;
	private $user;

	public function __construct($user){
		$this->db = new Database();
		$this->db->connect();
		$this->tags = $this->db->getAllTags($user);
		$this->user = $user;
	}

	public function getTags(){
		return $tags;
	}

	public function render(){
		$v = new TagManagerView();
		$v->getHead();
		$v->getTagTable($this->getTags());
	}

}


?>