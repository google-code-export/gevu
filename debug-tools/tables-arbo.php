<?php
    $dbN = "gevu";    
    $ldb = mysql_connect("localhost", "root", "") or die("Impossible de se connecter : " . mysql_error());    
    mysql_select_db($dbN);


    
    $sql = "TRUNCATE TABLE table_noms";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
    
    $sql = "TRUNCATE TABLE table_arborescence";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
    
    
    // tables et leurs noms
    $sql = "INSERT INTO table_noms (id_table, titre)
            VALUES  ('0',  'gevu_batiments'),
                    ('1',  'gevu_diagnostics'),
                    ('2',  'gevu_diagnosticsxvoirie'),
                    ('3',  'gevu_docsxlieux'),
                    ('4',  'gevu_espaces'),
                    ('5',  'gevu_espacesxexterieurs'),
                    ('6',  'gevu_espacesxinterieurs'),
                    ('7',  'gevu_etablissements'),
                    ('8',  'gevu_georss'),
                    ('9',  'gevu_geos'),
                    ('10', 'gevu_niveaux'),
                    ('11', 'gevu_objetsxexterieurs'),
                    ('12', 'gevu_objetsxinterieurs'),
                    ('13', 'gevu_objetsxvoiries'),
                    ('14', 'gevu_observations'),
                    ('15', 'gevu_parcelles'),
                    ('16', 'gevu_problemes'),
                    ('17', 'gevu_lieux')";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
    
    // arbo:
    $sql = "INSERT INTO table_arborescence (id_table, id_accept)
            VALUES  ('7',  '0'),
                    ('7',  '15'),
                    ('0',  '10'),
                    ('0',  '12'),
                    ('15', '5'),
                    ('10', '6'),
                    ('10', '12')";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br />'.$sql.'<br />';
    
mysql_close($ldb);
?>