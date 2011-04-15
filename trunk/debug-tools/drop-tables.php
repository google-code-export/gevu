
<?php
	/* vider toute les tables d'une bdd */


    /* try to connect to the server */
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
    
    /* try to connect to the database */
    $dbname = 'gevu_trouville_voirie1';
    mysql_select_db($dbname);

    $num = 0;
    $show = "SHOW TABLES";
    $show_res = mysql_query($show,$conn) or die(mysql_error()); 
    
    while($row = mysql_fetch_array($show_res)) {
        $sql = "TRUNCATE TABLE '".$row[$num]."'";
        mysql_query($sql) or die(mysql_error());
        $num++;
    }
?>