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

	static function numToMonth($m){
		$month = 'Jan';
		switch(strval($m)){
			case 1:
				break;
			case 2:
				$month = 'Feb';
				break;
			case 3:
				$month = 'Mar';
				break;
			case 4:
				$month = 'Apr';
				break;
			case 5:
				$month = 'May';
				break;
			case 6:
				$month = 'Jun';
				break;
			case 7:
				$month = 'Jul';
				break;
			case 8:
				$month = 'Aug';
				break;
			case 9:
				$month = 'Sep';
				break;
			case 10:
				$month = 'Oct';
				break;
			case 11:
				$month = 'Nov';
				break;
			case 12:
				$month = 'Dec';
				break;
			default:
				break;
		}
		return $month;
	}

	static function getJustDate($date){
		//"2012-08-30 18:47:37" -> 08/30/12
		$midway = explode(' ',$date);
		$dString = explode('-',$midway[0]);
		return $dString[1] . '/' . $dString[2] . '/' . substr($dString[0],-2);
	}

	static function convertSlashToHyph($date){
		//"09/06/12" -> 09-06-12ma

		$nDate = explode('/', $date);
		return $nDate[0] . '-' . $nDate[1] . '-' . $nDate[2];

	}

	static function convertPHPDate($date){
		//yyyy-mm-dd -> yyyy-mm-dd oo:00:00
		return $date . ' 00:00:00'; //So difficult.
	}

	static function convertReverseDate($date){
		//09-01-12 -> 2012-09-01
		$temp = explode('-', $date);
		return '20' . $temp[2] . '-' . $temp[0] . '-' . $temp[1];
	}

	function displayMenus($links){
		//Home page links to settings, checkbook and reports
		echo '<div class = "Menus">';
		echo '<div class = "largespacer"></div>';

		//use a horizontal list
		echo '<ul class = "Menu">';
	
		foreach ($links as $link) {

			echo '<li class="Menu">';
			echo '<form name = "' . $link . '" method="post" action = "' . str_replace('Home', 'index', str_replace(' ', '', $link)) . '.php">';
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
		echo '<form name = "logout" method="post" action = "/Logout.php">';
		echo '<button class = "Logout"  type="submit">Logout</button>';
		echo '</form>';
		echo '</div>';

	}



}






?>