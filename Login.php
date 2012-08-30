<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
	//Set up neccesary connections to a Model for database work
	require_once('Database.php');
	$db = new Database();

	$loginOk = null;
	//If we've posted from this page then we'll probably be logging in
	if(isset($_POST['username'])){
		$db->connect();
		if($db->attemptLogin($_POST['username'],$_POST['password'])){
			$_SESSION['userID'] = $_POST['username'];
			unset($_POST['password']);
			$loginOk = true;
		}else{
			$loginOk = false;
		}

	}


?>
<html>

	<head>
		<title>Budget Buddy Login</title>
		<link rel="stylesheet" href="/BudgetBuddy/CSS/Login.css" type="text/css" />
	</head>

	<body>

		<div class="Form">
			<!-- Sign in form -->
			<form id="Login" name="login" method="post" action="Login.php">
				<h1 class="Login">Budget Buddy Log In</h1>
				<?php
					//Status messages
					if(!is_null($loginOk)){
						if($loginOk){
							echo '<span id="Info">  Login Succesful <br /> Redirecting to Home </span>';
							echo '<meta http-equiv="REFRESH" content="2; url=/BudgetBuddy/index.php" />'; 
							var_dump($_SESSION);
						}else{
							echo '<span id="Info">  Invalid Login name or password</span>';
						}
					}
				?>
				<p class = "Login">Please log in below</p>
				<!--Login Name -->
				<label class="Login">Log In Name<br />
					<span class ="HelpText">Your username</span>
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
				<button type="submit" class="Login">Log In</button>
				<div class="largespacer"></div>
				<div class="spacer"></div>
			</form>

			<!-- Sign Up Button -->
			
			<form id="Signup" name="signup" method="get" action="Signup.php">
				<label class="Login">Sign Up
					<span class ="HelpText">New users start here!</span>
				</label>
				<div class="spacer"></div>
				<div class="spacer"></div>
				<div class="spacer"></div>
				<button type="submit">Sign Up</button><br />
				<br />
			</form>
		</div>



	</body>

</html>
