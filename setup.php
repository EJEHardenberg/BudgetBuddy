<?php


include('../config.php');

mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

//Attempt to select database
$db_selected = mysql_select_db(DATABASE_NAME, $link);


if (!$db_selected) {
  // If we couldn't, then it either doesn't exist, or we can't see it.
  $sql = 'CREATE DATABASE ' . DATABASE_NAME;

  if (mysql_query($sql, $link)) {
      echo "Database my_db created successfully\n";
  } else {
      die( 'Error creating database: ' . mysql_error() . "\n");
  }
}

//Database is selected, create tables

$userTable = "CREATE TABLE IF NOT EXISTS userinfo (userId INT(10) AUTO_INCREMENT,username VARCHAR(50),salt VARCHAR(64),hash VARCHAR(64),theme VARCHAR(20), PRIMARY KEY(userId));"
$loginTable = "logins (username, success, ip_address, logged_time) "

mysql_close($link);

?>