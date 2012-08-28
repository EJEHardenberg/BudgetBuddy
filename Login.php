<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

	<head>
		<title>Budget Buddy Login</title>
		<link rel="stylesheet" href="/BudgetBuddy/CSS/Login.css" type="text/css" />
	</head>

	<body>

		<div class="Form">
			<!-- Sign in form -->
			<form id="Login" name="login" method="post" action="index.php">
				<h1 class="Login">Budget Buddy Log In</h1>
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
				<div class="spacer"</div>
			</form>

			<!-- Sign Up Button -->
			
			<form id="Signup" name="signup" method="get" action="Signup.php">
				<label class="Login">Sign Up
					<span class ="HelpText">New users start here!</span>
				</label>
				<button type="submit">Sign Up</button><br />
			</form>
		</div>



	</body>

</html>
