<?php
//Start the Session
session_start();
//Get config information
include "../config.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Budget Buddy Home</title>
	<!-- If we are not logged in and there is no userID then we need to direct to the Login -->
	<?php
		//Echo out the default theme
		echo '<link rel="stylesheet" href="/BudgetBuddy/CSS/'  . DEFAULT_THEME . 'Theme.css" type="text/css" />';
		if(!isset($_SESSION['userID'])){
			echo '<meta http-equiv="REFRESH" content="0; url=/BudgetBuddy/Login.php" />';
		}	
	?>
</head>
<body>

<?php

//This is where I'd include the users home page

?>

<?php

//And this is where I'd place menus and such

?>


</body>

</html>

