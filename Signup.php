<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
	//Set up neccesary connections to a Model for database work
	require_once('Database.php');
	$db = new Database();

	//If we've posted from this page then we'll probably be signing up
	if(isset($_POST['username'])){
		$db->connect();
		$available = $db->checkAvailableName($_POST['username']);
		if($available){
			//Add the user and password to database with default theme:
			$signedUp = $db->signUserUp($_POST['username'],$_POST['password']);
		}
	}
?>
<html>

	<head>
		<title>Budget Buddy Sign Up</title>
		<link rel="stylesheet" href="CSS/Login.css" type="text/css" />
	</head>

	<body>

		<div class="Form">
			<!-- Sign in form -->
			<form id="Login" name="login" method="post" action="Signup.php">
				<h1 class="Login">Budget Buddy Sign Up</h1>
				<?php 
					//This is essentially the debug area for a user
					if(!$db->connect()){
						echo '<span id="Info"> Could not connect to Database </span>';
					}
					if(isset($available)){
						if(!$available){
							echo '<span id="Info">  Username already taken or Invalid! </span>';
						}
					}
					if(isset($signedUp)){
						if($signedUp){
							echo '<span id="Info"> You\'ve Signed Up! </span>';
							//redirect to login
							echo '<meta http-equiv="REFRESH" content="2; url=Login.php" />'; 
						}
						//redirect
					}
				?>
				<p class = "Login">Enter your Desired information</p>
				<!--Login Name -->
				<label class="Login">Log In Name<br />
					<span class ="HelpText">Your desired username</span>
				</label>
				<input type="text" name="username" id="username"  class="rounded"/>
				<br />
				<div class="largespacer"></div>

				<!--Password for login-->
				<label class="Login">Password<br />
					<span class ="HelpText">Your password</span>
				</label>
				<input type="password" name="password" id="password" class="rounded"/>
				<div class="largespacer"></div>
				<br />

				<div id="Signup">
					<div class="largespacer"></div>
					<button type="submit" class="Login">Sign Up!</button>
					<div class="largespacer"></div>
					<div class="spacer"></div>
				</div>
			</form>
		</div>



	</body>

</html>
