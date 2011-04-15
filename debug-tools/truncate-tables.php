
<?php
	/* vider toute les tables d'une bdd */


    /* try to connect to the server */
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
    
    /* try to connect to the database */
    $dbname = 'gevu_solus';
    mysql_select_db($dbname);

    $show = "SHOW TABLES";
    $show_res = mysql_query($show,$conn) or die(mysql_error()); 
    
    echo "<p>start truncating \"".$dbname."\"\n<ol>\n";
    while($row = mysql_fetch_array($show_res)) {
        $sql = "TRUNCATE TABLE  `".$row[0]."`";
        // $sql = "DELETE * FROM '".$row[$num]."'";
        if(!mysql_query($sql)){
        	echo "Can't truncate \"".$row[0]."\".<br>\n";
        	die(mysql_error());
        }
        else {
        	echo "\t<li> \"".$row[0]."\" was truncated.</li>\n";
        }
    }
    echo "</ol>\nwork was done!<br>";
    
?>