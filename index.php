<?php
//Start the Session
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Budget Buddy Home</title>
	<link rel="stylesheet" href="/BudgetBuddy/CSS/MetalTheme.css" type="text/css" />
	<!-- If we are not logged in and there is no userID then we need to direct to the Login -->
	<?php
		if(!isset($_SESSION['userID'])){
			echo '<meta http-equiv="REFRESH" content="0; url=/BudgetBuddy/Login.php" />';
		}	
	?>
</head>
<body>


stuff


</body>

</html>

