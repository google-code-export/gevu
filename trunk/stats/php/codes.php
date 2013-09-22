<?php
 $username = 'root';
 $password = '';
 $database = 'gevu_new';
 $server = 'localhost';
 
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