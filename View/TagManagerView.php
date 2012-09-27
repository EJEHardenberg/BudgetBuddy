<?php require_once('View.php'); 

class TagManagerView extends View{ 


	public function __construct(){}

	public function getHead(){
		echo '<h1 class ="Home">Tag Manager</h1><hr>';
	}

	
	public function getTagTable($tags){
		echo '<div>';
			echo '<table class = "TransactionArea" id ="Scrollable">';
				$counter = 0;
				echo '<tr>';
				foreach ($tags as $tag) {
					//Echo the name, delete button, and 
					echo '<td class = "Transaction">' . $tag ;
					echo '<a href="" class = "Transaction" >X</a>';
					echo '</td>';
					if($counter == 6){
						echo '</tr><tr>';
					}
				}
				if($counter < 6){
					//end the row just in case we don't have an even amount
					echo '</tr>';
				}
			echo '<table>';
		echo '</div>';
	}
	
}
?>