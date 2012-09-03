<?php

/**************************
/*Parent for all views, provides common functions like
/*logout,formatDate,displayMenus, 
/**************************/

class View { 

	function formatDate($date){
		//"2012-08-30 18:47:37" -> 6:47pm on 08/30/12
		$midway = explode(' ',$date);
		$dString = explode('-',$midway[0]);
		$tString = explode(':',$midway[1]);
		$hour = intval($tString[0]);
		$ampm = ($hour > 11 ? 'pm' : 'am');
		$hour = strval($hour % 12);
		return $hour . ':'  . $tString[1] . $ampm . ' on ' . $dString[1] . '/' . $dString[2] . '/' . substr($dString[0],-2);

	}

	function getJustDate($date){
		//"2012-08-30 18:47:37" -> 08/30/12
		$midway = explode(' ',$date);
		$dString = explode('-',$midway[0]);
		return $dString[1] . '/' . $dString[2] . '/' . substr($dString[0],-2);
	}

	function displayMenus($links){
		//Home page links to settings, checkbook and reports
		echo '<div class = "Menus">';
		echo '<div class = "largespacer"></div>';

		//use a horizontal list
		echo '<ul class = "Menu">';
	
		foreach ($links as $link) {

			echo '<li class="Menu">';
			echo '<form name = "' . $link . '" method="post" action = "/BudgetBuddy/' . str_replace('Home', 'index', str_replace(' ', '', $link)) . '.php">';
			echo '<button class = "Menu"  type="submit">'. $link .'</button>';
			echo '</form>';
			echo '</li><span class = "MenuSpacer"><br /></span>';
		}
		

		echo '</ul>';
		echo '</div>';
	}

	function logout(){
		//Send used back to the Login page and destroys the session
		echo '<div class = Logout">';
		echo '<form name = "logout" method="post" action = "../../BudgetBuddy/Logout.php">';
		echo '<button class = "Logout"  type="submit">Logout</button>';
		echo '</form>';
		echo '</div>';

	}



}






?>