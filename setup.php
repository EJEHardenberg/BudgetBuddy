<?php


include('../config.php');

$link = mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
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

$userTable = "CREATE TABLE IF NOT EXISTS userinfo (userid INT(10) AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50),salt VARCHAR(64),hash VARCHAR(64),theme VARCHAR(20));";
$loginTable = "CREATE TABLE IF NOT EXISTS logins (username VARCHAR(50) REFERENCES userinfo(username), success INT(1), ip_address VARCHAR(15), logged_time DATETIME,id INT(10) AUTO_INCREMENT PRIMARY KEY); ";
$accountsTable = "CREATE TABLE IF NOT EXISTS accounts (userid INT(10) REFERENCES userinfo(userid),name VARCHAR(30),amount VARCHAR(10),PRIMARY KEY(userid,name));";
$transactionsTable = "CREATE TABLE IF NOT EXISTS transactions (userid INT(10) REFERENCES userinfo(userid),accountname VARCHAR(30) REFERENCES accounts(name),name VARCHAR(100),amount VARCHAR(15),date DATETIME, id INT(10) PRIMARY KEY AUTO_INCREMENT)";
$tagsTable = "CREATE TABLE IF NOT EXISTS tags (id INT(10) AUTO_INCREMENT PRIMARY KEY,uid INT(10) REFERENCES userinfo(userid),name VARCHAR(50));";
$transTagsTable = "CREATE TABLE IF NOT EXISTS transaction_tags (trans_id INT(10) REFERENCES transactions(id),tag_id INT(10) REFERENCES tags(id));";

if(mysql_query($userTable,$link)){
	echo "userinfo table created successfully\n";
}else{
	echo "Failed to create userinfo table\n";
}

if(mysql_query($loginTable,$link)){
	echo "logins table created successfully\n";
}else{
	echo "Failed to create logins table\n";
}

if(mysql_query($accountsTable,$link)){
	echo "accounts table created successfully\n";
}else{
	echo "Failed to create accounts table\n";
}

if(mysql_query($transactionsTable,$link)){
	echo "transactions table created successfully\n";
}else{
	echo "Failed to create transactions table\n";
}

if(mysql_query($tagsTable,$link)){
	echo "tags table created successfully\n";
}else{
	echo "Failed to create tags table\n";
}

if(mysql_query($transTagsTable,$link)){
	echo "Transaction tags table created successfully\n";
}else{
	echo "Failed to create transaction tags table\n";
}

//Close connection to database
mysql_close($link);

?>