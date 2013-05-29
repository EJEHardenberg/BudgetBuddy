	<?php

require_once('View.php');

class CheckBookView extends View{
	public function __construct(){

	}

	public function displayAccountTabs($accounts){
		echo '<div class ="CheckBookTabs">';
		echo '<ul class = "CheckTabs">';
		foreach ($accounts as $acct) {
			echo '<li class="CheckTabs"><a class = "AccountTab" href= "/CheckBook.php/' . $acct .':Display">' . $acct . '</a></li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	public function displaySubMenus($links,$account){
		echo '<div class ="SubMenu">';
		echo '<ul class = "SubMenu">';
		foreach ($links as $link) {
			echo '<li>';
			echo '<form name = "' . $link . '" method="post" action = "/CheckBook.php/' . $account . ':' . $link . '">';
			echo '<button class = "SubMenu"  type="submit">'. $link .'</button>';
			echo '</form>';
		}
		echo '</ul>';
		echo '</div>';
	}


}

?>



