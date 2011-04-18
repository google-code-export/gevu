<!-- truncating all tables of a specific database -->
<!-- to debug or not, just modify the following line -->
<?php define("DEBUG_MODE", false); ?>

<?php
    if (DEBUG_MODE){
        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = '';
        $dbname = 'gevu_truc';
    }
    else{
        $dbhost = isset($_POST['dbhost']) ? $_POST['dbhost'] : '';
        $dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
        $dbuser = isset($_POST['dbuser']) ? $_POST['dbuser'] : '';
        $dbpass = isset($_POST['dbpass']) ? $_POST['dbpass'] : '';
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
            <tr><td colspan ="2" align="center"><input type="submit"   name="valider" value="OK"/></td></tr>
          </table>
        </form>
        <?php    
            if ( $dbhost && $dbname && $dbuser ){
                echo "\n<hr>\n";
                truncateTables($dbhost,$dbname,$dbuser,$dbpass);
            }
            else{
                echo "<p>please set host name, database and user name";
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

        echo "<p>start truncating \"".$dbname."\" @ ".date("d/m/Y - H:i:s",time())."\n<ol>\n";
        while($row = mysql_fetch_array($show_res)) {
            $sql = "TRUNCATE TABLE  `".$row[0]."`";
            //$sql = "DELETE * FROM '".$row[0]."'";
            if(!mysql_query($sql)){
                echo "Can't truncate \"".$row[0]."\".<br>\n";
                die(mysql_error());
            }
            else {
                echo "\t<li> \"".$row[0]."\" was truncated.</li>\n";
            }
        }
        echo "</ol>\nwork was done!<br>";
    }
?>
