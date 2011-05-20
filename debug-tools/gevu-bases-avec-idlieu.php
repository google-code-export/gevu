<?php
	$dbN = "gevu_solus";    
	$ldb = mysql_connect("localhost", "root", "") or die("Impossible de se connecter : " . mysql_error());    
	mysql_select_db($dbN);

	$sql = "SHOW TABLES";
	$res = mysql_query($sql);
	if (!$res) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
	
	echo "tables are:\n<ul>\n";
	
	while($row1=mysql_fetch_array($res)) {
		if ($row1[0]=="gevu_lieux"){
			continue;
		}
		$sql = "SHOW COLUMNS FROM ".$row1[0];
		$res2 = mysql_query($sql);
		if (!$res2) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
		while($row2=mysql_fetch_array($res2)) {
			if($row2[0]=="id_lieu"){
				echo "\t<li>".$row1[0]."</li>\n";
				$tab[]=$row1[0];
				break;
			}
		}
	}
	echo "</ul>\n<br />\n";
	
	$max = count($tab);
	if($max>0){
		// first request
		$req1 = "SELECT b.id_lieu";
		for ($i=0; $i<$max; ++$i){
			$req1.= ", b$i.id_lieu";
		}
		$req1.="\n  FROM gevu_lieux b\n";
		for ($i=0; $i<$max; ++$i){
			$req1.= "  LEFT JOIN $tab[$i] b$i    ON b.id_lieu=b$i.id_lieu\n";
		}
		$req1.="WHERE b.id_lieu=\$id";
		
		//second request
		$req2="SET @id=\$id\n";
		for($i=0; $i<($max-1); ++$i){
			$req2.="(SELECT * FROM gevu_lieux g1 INNER JOIN $tab[$i] WHERE g1.id_lieu=@id)\n  UNION\n";
		}
		$req2.="(SELECT * FROM gevu_lieux g1 INNER JOIN ".$tab[($max-1)]." WHERE g1.id_lieu=@id)\n  UNION\n";
	}
	echo "<p>\nfirst request:\n<br />\n".$req1."\n</p>\n";
	echo "<p>\nsecond request:\n<br />\n".$req2."\n</p>\n";

mysql_close($ldb);
?>