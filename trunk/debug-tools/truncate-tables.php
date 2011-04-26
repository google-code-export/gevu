<!-- truncating all tables of a specific database -->
<!-- to debug or not, just modify the following line -->
<?php define("DEBUG_MODE", false); ?>

<?php
    if (DEBUG_MODE){
        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = '';
        $dbname = 'gevu_truc';
        $addUnivers = false;
    }
    else{
        $dbhost = isset($_POST['dbhost']) ? $_POST['dbhost'] : '';
        $dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
        $dbuser = isset($_POST['dbuser']) ? $_POST['dbuser'] : '';
        $dbpass = isset($_POST['dbpass']) ? $_POST['dbpass'] : '';
        $addUnivers = isset($_POST['mon_champ'][0]) ? true : false;
        if ($_POST['mon_champ'][0]<>"addUniversToGevuLieux") $addUnivers=false; 
    }
 ?>
<html>
    <head>
        <title>Truncate all tables of a database</title>
    </head>
    <body>
        <form method="POST">
          <table>
            <tr><td>host name:</td><td><input type="text" name="dbhost"
                                        <?php if($dbhost) echo "value=\"".$dbhost."\""; ?>
                                       /></td></tr>
            <tr><td>database name:</td><td><input type="text" name="dbname"
                                           <?php if($dbname) echo "value=\"".$dbname."\""; ?>
                                           /></td></tr>
            <tr><td>username:</td><td><input type="text" name="dbuser"
                                      <?php if($dbuser) echo "value=\"".$dbuser."\""; ?>
                                      /></td></tr>
            <tr><td>password:</td><td><input type="password" name="dbpass"
                                      /></td></tr>
            <tr><td colspan ="2"><input type="checkbox" name="mon_champ[]"
                                        value="addUniversToGevuLieux" <?php if($addUnivers)echo "checked"; ?>/>add 'univers' to 'gevu_lieux'</td></tr>
            <tr><td colspan ="2"><hr /></td></tr>
            <tr><td colspan ="2" align="center"><input type="submit" name="valider" value="OK"/></td></tr>
          </table>
        </form>
        <?php    
            if ( $dbhost && $dbname && $dbuser ){
                echo "\n<hr />\n";
                truncateTables($dbhost,$dbname,$dbuser,$dbpass);
                if($addUnivers){
	                // Créer le noeud de base (créer univers)
                    $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
                    mysql_select_db($dbname);
                    $sql="INSERT INTO gevu_lieux
                          (`id_rubrique`, `lib`, `id_parent`, `id_instant`, `lft`, `rgt`, `niv`, `maj`, `lieu_parent`)
                          VALUES
                          (0, 'univers', 0, 0, 0, 1 ,0 ,now() ,'-1')";
                    $result = mysql_query($sql);
                    if (!$result) "<p>'gevu_lieux' wasn't found in the selected database ...</p>";
                    else echo "<p>univers was added!</p>";                    
                    mysql_close($conn);
                }
            }
            else{
                echo "<p>please set host name, database and user name</p>";
            }
        ?>
    </body>
</html>

<?php
    function truncateTables($dbhost,$dbname,$dbuser,$dbpass){
        /* try to connect to the server */
        $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

        /* try to connect to the database */
        mysql_select_db($dbname);

        $show = "SHOW TABLES";
        $show_res = mysql_query($show,$conn) or die(mysql_error());

        echo "<p>\nstart truncating \"".$dbname."\" @ ".date("d/m/Y - H:i:s",time())."\n<ol>\n";
        while($row = mysql_fetch_array($show_res)) {
            $sql = "TRUNCATE TABLE  `".$row[0]."`";
            //$sql = "DELETE * FROM '".$row[0]."'";
            if(!mysql_query($sql)){
                echo "Can't truncate \"".$row[0]."\".<br />\n";
                die(mysql_error());
            }
            else {
                echo "\t<li> \"".$row[0]."\" was truncated.</li>\n";
            }
        }
        echo "</ol>\ntruncation was done!<br />\n</p>";
        mysql_close($conn);
    }
?>
