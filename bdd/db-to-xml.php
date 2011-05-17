<?php

$dbN = "gevu_solus";
$ldb = mysql_connect("localhost", "root", "") or die("Impossible de se connecter : " . mysql_error());    
mysql_select_db($dbN);

echo "start creating xml file...<br />\n";
GenererHierarchieXML();
echo "xml was created!<br />\n";

mysql_close($ldb);

function GenererHierarchieXML()
{
    $fp = fopen("hierarchie.xml", "w");
    fputs($fp, "<?xml version=\"1.0\"?>\n");
    fputs($fp, "<hierarchie>\n");
    ParcoursNoeuds($fp, -1);
    fputs($fp, "</hierarchie>");
}


function ParcoursNoeuds(&$fp, $lieuParent){
    $sql = "SELECT r.id_lieu, r.lib, r.niv FROM gevu_lieux r
            WHERE r.lieu_parent = $lieuParent";
    $res = mysql_query($sql);
    if (!$res) echo 'Requ�te invalide : ' . mysql_error().'<br />'.$sql.'<br />';
    
    while($row=mysql_fetch_array($res)){
    
        // regarder le nombre d'enfants de $row['id_lieu']
        $sql = "SELECT COUNT(*) FROM gevu_lieux r
                WHERE r.lieu_parent = \"".$row['id_lieu']."\"";
        $ress = mysql_query($sql);
        if (!$ress) echo 'Requ�te invalide : ' . mysql_error().'<br />'.$sql.'<br />';
        $n = mysql_fetch_array($ress);
    
        if($n[0]>0)
        {
            $str = str_repeat("\t", $row['niv'])."<node idLieu=\"".$row['id_lieu']."\" lieuParent=\"$lieuParent\" lib=\"".$row['lib']."\" niv=\"".$row['niv']."\">\n";
            fputs($fp, $str);
            ParcoursNoeuds($fp, $row['id_lieu']);
            $str = str_repeat("\t", $row['niv'])."</node>\n";
            fputs($fp, $str);
        }
        else
        {
            $str = str_repeat("\t", $row['niv'])."<node idLieu=\"".$row['id_lieu']."\" lieuParent=\"$lieuParent\" lib=\"".$row['lib']."\" niv=\"".$row['niv']."\"/>\n";
            fputs($fp, $str);
        }
    }
}

?>