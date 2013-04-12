<?php
 $username = 'username';
 $password = 'password';
 $database = 'gevu_new';
 $server = '127.0.0.1';
 
 // Ouvre connexion MySQL
 $connection = mysql_connect ($server, $username, $password);
 if (!$connection)
 {
 	die('Not connected : ' . mysql_error());
 }
 
 // Définit la BDD active
 
 $db_selected = mysql_select_db($database, $connection);
 if (!$db_selected)
 {
 	die ('Can\'t use db : ' . mysql_error());
 }
  
?>