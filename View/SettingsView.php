<?php

require_once('View.php');

class SettingsView extends View{
	public function __construct(){

	}

	public function changeUser(){
		//We want to display a simple form with two text boxes and a drop down
		//The trick is that the form drops out from a link that says Username.
		echo '<div>';
			echo '<form name ="ChangeUser" method="post" action= "/BudgetBuddy/Settings.php/User">';
				echo '<h3 class = "changeLink" onClick="dropClicked('."'userDrop'".')"> Username </h3>';
				echo '<div id="userDrop">';
					echo 'Old Username: ';
					echo '<input class = "change" type="text" name="old" method="post" /><br />';
					echo 'New Username: ';
					echo '<input class = "change" type="text" name="new" method="post" /><br />';
					echo '<input type="submit" value="Submit"/>';
				echo '</div>';
			echo '</form>';
		echo '</div>';
	}

	public function changePass(){
		echo '<div>';
			echo '<form name ="ChangePass" method="post" action= "/BudgetBuddy/Settings.php/Pass">';
				echo '<h3 class = "changeLink" onClick="dropClicked('."'passDrop'".')"> Password </h3>';
				echo '<div id="passDrop">';
					echo 'Old Password: ';
					echo '<input class = "change" type="password" name="old" method="post" /><br />';
					echo 'New Password: ';
					echo '<input class = "change" type="password" name="new" method="post" /><br />';
					echo '<input type="submit" value="Submit"/>';
				echo '</div>';
			echo '</form>';
		echo '</div>';	
	}

	public function changeTheme($themes){
		//Drop down populated by possible themes
		echo '<div>';
			echo '<h3 class = "changeLink" onClick="dropClicked('."'themeDrop'".')">Theme</h3>';
			echo '<div id="themeDrop">';
				echo '<form name ="ChangePass" method="post" action= "/BudgetBuddy/Settings.php/Theme">';
				echo 'Select a theme from the dropdown and click submit<br />';
				echo '<select class="nice" id="themeselect"><option>Themes...</option>';
					foreach ($themes as $theme) {
						echo '<option value = "'.$theme.'">' . $theme . '</option>';
					}
				echo '</select>';
				echo '<input type="submit" value="Submit" />';
				echo '</form>';
			echo '</div>';
		echo '</div>';

	}

	public function TagManagerLink(){
		//Displays the link to the tag manager
		echo '<div>';
			echo '<form name = "TagMan" method="post" action = "/BudgetBuddy/Settings.php/Tag">';
				echo '<input type = "submit" value = "Go to Tag Manager">';
			echo '</form>';

		echo '</div>';
	}

	public function displaySubMenus($links,$account){
		echo '<div class ="SubMenu">';
		echo '<ul class = "SubMenu">';
		foreach ($links as $link) {
			echo '<li>';
			echo '<form name = "' . $link . '" method="post" action = "/BudgetBuddy/CheckBook.php/' . $account . ':' . $link . '">';
			echo '<button class = "SubMenu"  type="submit">'. $link .'</button>';
			echo '</form>';
			echo '<li>';
		}
		echo '</ul>';
		echo '</div>';
	}


}

?>



