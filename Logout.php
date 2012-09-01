<?php
session_start();
unset($_SESSION['userID']);
unset($_SESSION);
session_destroy();
var_dump($_SESSION);
header('Location:../BudgetBuddy/index.php');

?>