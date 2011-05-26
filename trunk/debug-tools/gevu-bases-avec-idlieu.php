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
		$req1 = "SELECT b0.id_lieu B0 ";
		for ($i=1; $i<$max; ++$i){
			$req1.= ", b$i.id_lieu B$i";
		}
		$req1.="\n  FROM gevu_lieux b\n";
		for ($i=0; $i<$max; ++$i){
			$req1.= "  LEFT JOIN $tab[$i] b$i    ON b.id_lieu=b$i.id_lieu\n";
		}
		$req1.="WHERE b.id_lieu=\$id";
		
		//second request
		$req2="SET @id=\$id;\n";
		for($i=0; $i<($max-1); ++$i){
			$req2.="(SELECT $i Bi FROM $tab[$i] g WHERE g.id_lieu=@id)  UNION\n";
		}
		$req2.="(SELECT ".($max-1)." NMB FROM gevu_lieux g1 INNER JOIN ".$tab[($max-1)]." g2 ON g1.id_lieu=g2.id_lieu WHERE g1.id_lieu=@id);\n";
		
		// 1st php instructions
		$inst1="\t\t\$table = new Model_DbTable_Gevu_lieux();\n\n";
		for($i=0; $i<$max; ++$i){
			$inst1.="\t\t\$s = \$table->select()\n";
			$inst1.="\t\t->from( array(\"g\" => \"$tab[$i]\"),array(\"Bi\" => \"($i)\") )";
			$inst1.="\t\t->where( \"g.id_lieu = ?\", \$idLieu )";
			$inst1.="\t\t->group(\"Bi\");\n";
			$inst1.="\t\t\$rows = \$table->fetchAll(\$s)->toArray();\n";
			$inst1.="\t\tif(count(\$rows)>0) \$result[]=\$rows[0];\n\n";
		}
		
	}
	echo "<p>\nfirst request:\n<br />\n".$req1."\n</p>\n";
	echo "<p>\nsecond request:\n<br />\n".$req2."\n</p>\n";
	echo "<p>\nfirst instrunctions:\n<br />\n".$inst1."\n</p>\n";

mysql_close($ldb);
?>