<?php
//Start the Session if ones not already going on
session_start();
//Get config information
include_once( "../config.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Budget Buddy</title>
	<!-- If we are not logged in and there is no userID then we need to direct to the Login -->
	<?php
		//Echo out the proper theme
		if(!isset($_SESSION['userID'])){
			echo '<link rel="stylesheet" href="/BudgetBuddy/CSS/'  . DEFAULT_THEME . 'Theme.css" type="text/css" />';
			header('Location:../BudgetBuddy/Login.php'); //redirect
		}else{
			//We need the database for personalized themes
			require_once('Database.php');
			$db = new Database();
			$db->connect();
			$theme = $db->getUserTheme($_SESSION['userID']);
			//We shouldn't have to worry about theme being null or anything because we're logged in and there must be 
			//a theme associated with a user, but whether or not that theme exists is iffy
			if(file_exists('/BudgetBuddy/CSS/' . $theme . 'Theme.css')){
				echo '<link rel="stylesheet" href="/BudgetBuddy/CSS/'  . $theme . 'Theme.css" type="text/css" />';
			}else{
				echo '<link rel="stylesheet" href="/BudgetBuddy/CSS/'  . DEFAULT_THEME . 'Theme.css" type="text/css" />';
			}
		}
	?>
</head>
<body>

<?php

require_once('/Controller/Home.php');
//This is where I'd include the users home page
$homeControl = new Home($_SESSION['userID']);

?>

<div class = "Background">

<?php

$homeControl->render();

?>

</div>
</body>

</html>

