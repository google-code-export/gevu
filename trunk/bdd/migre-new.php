<?php

/**********************************
* - problème de parenthèse dans les titre!
* y'a t-il un risque de trouver des doubles quotes ou pas? comment y remédier? 
* 
* - dans mes fonctions: l'id du parent doit-être celui de $dbN et pas celuis de $dbO
*/

$dbN = "gevu_solus";    
$dbO = "gevu_global";

$ldb = mysql_connect("localhost", "root", "") or die("Impossible de se connecter : " . mysql_error());    
mysql_select_db($dbN);

/* 
 * Chercher un lft=0 (chercher univers).
 * Si n'existe pas, c'est que les tableaux sont vides.
 * Créer ce noeud de base (créer univers)
 */
$sql = "SELECT * FROM gevu_lieux WHERE lft=0";
$result = mysql_query($sql);
if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
if(mysql_fetch_array($result)==NULL){
    echo "gauche=0 est introuvable. l'univers sera créé ...<p>";
    $sql="INSERT INTO $dbN.gevu_lieux
          (`id_rubrique`, `lib`, `id_parent`, `id_instant`, `lft`, `rgt`, `niv`, `maj`, `lieu_parent`)
          VALUES
          (0, 'univers', 0, 0, 0, 1 ,0 ,now() ,'-1')";
    $result = mysql_query($sql);
    if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
}

define('merge', true);

if(!merge){
    migreBase($dbN, $dbO);
}else{
    CompareBases($dbN, $dbO);
}


function migreBase($dbN, $dbO){
        /*
         * création d'un instant et mise à jour de l'instant de l'univers
         */
        $sql = "INSERT INTO $dbN.gevu_instants (`maintenant`, `ici`, `id_exi`, `nom`)
                VALUES (now(), '-1', 1, 'Migre_GEVU $dbO')";
        $result = mysql_query($sql);
        if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
        $idInstant = mysql_insert_id();
        echo "$dbO Instant : ".mysql_affected_rows()."<br/>";   
        $sql = "UPDATE gevu_lieux SET id_instant=".$idInstant." WHERE lft = 0";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        
        //récupère les propriété de l'univers
        $sql = "SELECT id_lieu, lft, rgt FROM $dbN.gevu_lieux WHERE lft = 0";
        $result = mysql_query($sql);
        if (!$result) die('Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>');
        $r=mysql_fetch_array($result);
        echo "$dbO univers : ".$r['lft']." - ".$r['rgt']."<br/>";
        
        //LIEUX
        /*** note: $idRub doit-être à 0 ou  5479 ??? ***/
        setLieuxHierarchie($r['id_lieu'], $dbN, $dbO, $idInstant, 5479, $r['lft'], $r['rgt']);
        
        /*pour vérifier
        SELECT CONCAT( REPEAT(' ', COUNT(pl.lib) - 1), l.lib) AS name
        FROM gevu_lieux AS l,
        gevu_lieux AS pl
        WHERE l.lft BETWEEN pl.lft AND pl.rgt
        GROUP BY l.lib
        ORDER BY l.lft
        */
        
        //on met � jour le droite de l'univers
        $sql = "SELECT MAX(rgt)+1 m FROM gevu_lieux";
        $result = mysql_query($sql);
        $r=mysql_fetch_array($result);
        $sql = "UPDATE gevu_lieux SET rgt=".$r['m']." WHERE lft = 0";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        
        //DIAGNOSTICS 
        //on ne conserve que les r�ponses 'non' et 'sous r�serve'
        // pour les questions qui ne sont pas interm�fiaire
        $sql = "INSERT INTO $dbN.gevu_diagnostics
        (id_critere, id_reponse, id_instant, id_lieu, id_donnee, maj)
        SELECT c.id_critere, fdc1.valeur, $idInstant, l.id_lieu, fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'mot_1' 
                INNER JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_1' 
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fda.id_donnee = fd.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc2.valeur
        WHERE fd.id_form =59
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO DIAGNOSTICS : ".mysql_affected_rows()."<br/>";       
        
        
        //PROBLEMES
        //on ne conserve que le champ photo
        //les champs 'fichier' et 'doc' devront �tre ajouter � la table gevu_doc
        $sql = "INSERT INTO $dbN.gevu_problemes
        (id_lieu, id_critere, num_marker, mesure, observations, fichier, doc, id_instant, id_donnee, maj)
        SELECT l.id_lieu
                , c.id_critere
                        , fdc1.valeur
                        , fdc11.valeur
                        , fdc3.valeur
                        , fdc4.valeur
                        , fdc6.valeur
                        , $idInstant
                        , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee
                        AND fdc11.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc ON fdc.id_donnee = fd.id_donnee
                        AND fdc.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'texte_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'fichier_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_5'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
                        AND fdc6.champ = 'ligne_4'                                                                                               
                LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc.valeur
        WHERE fd.id_form =60
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO PROBLEMES : ".mysql_affected_rows()."<br/>"; 
        
        
        //GEOGRAPHIE
        $sql = "INSERT INTO $dbN.gevu_geos
        (id_lieu, id_instant, lat, lng, zoom_min, zoom_max, adresse, id_type_carte, kml, id_donnee, maj)
        SELECT l.id_lieu
                        , $idInstant
                        , fdc1.valeur
                        , fdc2.valeur
                        , fdc3.valeur
                        , fdc4.valeur
                        , fdc5.valeur
                        , fdc6.valeur
                        , fdc7.valeur
                        , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'ligne_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_7'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
                        AND fdc6.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
                        AND fdc7.champ = 'texte_1'
        WHERE fd.id_form = 1
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO GEOGRAPHIE : ".mysql_affected_rows()."<br/>";        
        
        //GEORSS
        $sql = "INSERT INTO $dbN.gevu_georss
        (id_lieu, id_instant, url, id_donnee, maj)
        SELECT l.id_lieu
                        , $idInstant
                        , fdc1.valeur
                        , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'url_1'
        WHERE fd.id_form = 81
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO GEORSS : ".mysql_affected_rows()."<br/>";    
        
        //NIVEAU
        $sql = "INSERT INTO $dbN.gevu_niveaux
        (id_lieu, id_instant, nom, ref, id_reponse_1, id_reponse_2, id_reponse_3, id_donnee, maj)
        SELECT l.id_lieu
                        , $idInstant
                        , fdc1.valeur
                        , fdc2.valeur
                        , fdc3.valeur
                        , fdc4.valeur
                        , fdc5.valeur
                        , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'mot_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'mot_3'
        WHERE fd.id_form = 35
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO NIVEAU : ".mysql_affected_rows()."<br/>";    
        
        //BATIMENT
        //les contacts devront �tre retrait�s
        //(53, 'ligne_10', 12, 'Coordonn�es du gardien'
        //les horaires devront �tre retrait�
        $sql = "INSERT INTO $dbN.gevu_batiments
        (id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
                , contact_proprietaire, contact_delegataire, contact_gardien
                , horaires_gardien, horaires_batiment
                , superficie_parcelle, superficie_batiment
                , date_achevement, date_depot_permis, date_reha
                , reponse_1
                , reponse_2
                , reponse_3
                , reponse_4
                , reponse_5
                , reponse_6
                , reponse_7
                , reponse_8
                , reponse_9
                , reponse_10
                , reponse_11
                , reponse_12
                , reponse_13
                , reponse_14
                , reponse_15
                , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc4.valeur
                , fdc5.valeur
                , fdc6.valeur
                , fdc7.valeur
                , fdc8.valeur
                , fdc9.valeur
                , fdc10.valeur
                , fdc11.valeur
                , fdc12.valeur
                , fdc13.valeur
                , fdc14.valeur
                , fdc15.valeur
                , fdc16.valeur
                , fdc17.valeur
                , fdc18.valeur
                , fdc19.valeur
                , fdc20.valeur
                , fdc21.valeur
                , fdc22.valeur
                , fdc23.valeur
                , fdc24.valeur
                , fdc25.valeur
                , fdc26.valeur
                , fdc27.valeur
                , fdc28.valeur
                , fdc29.valeur
                , fdc30.valeur
                , fdc31.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'ligne_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_5'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
                        AND fdc6.champ = 'code_postal_1'
        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
                        AND fdc7.champ = 'ligne_6'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
                        AND fdc8.champ = 'ligne_7'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc9 ON fdc9.id_donnee = fd.id_donnee
                        AND fdc9.champ = 'ligne_10'
                        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc10 ON fdc10.id_donnee = fd.id_donnee
                        AND fdc10.champ = 'ligne_9'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc11 ON fdc11.id_donnee = fd.id_donnee
                        AND fdc11.champ = 'ligne_11'
        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc12 ON fdc12.id_donnee = fd.id_donnee
                        AND fdc12.champ = 'ligne_13'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc13 ON fdc13.id_donnee = fd.id_donnee
                        AND fdc13.champ = 'ligne_12'
        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc14 ON fdc14.id_donnee = fd.id_donnee
                        AND fdc14.champ = 'ligne_14'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc15 ON fdc15.id_donnee = fd.id_donnee
                        AND fdc15.champ = 'ligne_15'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc16 ON fdc16.id_donnee = fd.id_donnee
                        AND fdc16.champ = 'ligne_16'
                        
                        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'mot_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'mot_5'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
                        AND fdc19.champ = 'mot_6'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
                        AND fdc20.champ = 'mot_7'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
                        AND fdc21.champ = 'mot_9'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc22 ON fdc22.id_donnee = fd.id_donnee
                        AND fdc22.champ = 'mot_10'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc23 ON fdc23.id_donnee = fd.id_donnee
                        AND fdc23.champ = 'mot_12'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc24 ON fdc24.id_donnee = fd.id_donnee
                        AND fdc24.champ = 'mot_13'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc25 ON fdc25.id_donnee = fd.id_donnee
                        AND fdc25.champ = 'mot_14'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc26 ON fdc26.id_donnee = fd.id_donnee
                        AND fdc26.champ = 'mot_15'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc27 ON fdc27.id_donnee = fd.id_donnee
                        AND fdc27.champ = 'mot_16'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc28 ON fdc28.id_donnee = fd.id_donnee
                        AND fdc28.champ = 'mot_17'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc29 ON fdc29.id_donnee = fd.id_donnee
                        AND fdc29.champ = 'mot_25'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc30 ON fdc30.id_donnee = fd.id_donnee
                        AND fdc30.champ = 'mot_27'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc31 ON fdc31.id_donnee = fd.id_donnee
                        AND fdc31.champ = 'mot_28'              
        WHERE fd.id_form = 53
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO BATIMENT : ".mysql_affected_rows()."<br/>";  
        
        //ETABLISSEMENT
        $sql = "INSERT INTO $dbN.gevu_etablissements
        (id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
                , contact_proprietaire, contact_delegataire
                , reponse_1
                , reponse_2
                , reponse_3
                , reponse_4
                , reponse_5
        , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc4.valeur
                , fdc5.valeur
                , fdc6.valeur
                , fdc7.valeur
                , fdc8.valeur
                , fdc17.valeur
                , fdc18.valeur
                , fdc19.valeur
                , fdc20.valeur
                , fdc21.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'ligne_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_5'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
                        AND fdc6.champ = 'code_postal_1'
        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
                        AND fdc7.champ = 'ligne_6'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
                        AND fdc8.champ = 'ligne_7'      
                        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'mot_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
                        AND fdc19.champ = 'mot_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
                        AND fdc20.champ = 'select_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
                        AND fdc21.champ = 'select_2'
        
                        WHERE fd.id_form = 55
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO ETABLISSEMENT : ".mysql_affected_rows()."<br/>";     
        
        //ESPACES
        $sql = "INSERT INTO $dbN.gevu_espaces
        (id_lieu, id_instant, ref
                , id_type_espace
                , reponse_1
                , reponse_2
                , id_type_specifique_int
                , id_type_specifique_ext
                , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc2.valeur
                , fdc17.valeur
                , fdc18.valeur
                , fdc19.valeur
                , fdc20.valeur
                , fdc21.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'              
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'mot_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
                        AND fdc19.champ = 'mot_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc20 ON fdc20.id_donnee = fd.id_donnee
                        AND fdc20.champ = 'mot_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc21 ON fdc21.id_donnee = fd.id_donnee
                        AND fdc21.champ = 'mot_5'
        WHERE fd.id_form = 56
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO ESPACES : ".mysql_affected_rows()."<br/>";   
        
        //ESPACES INTERIEURS
        $sql = "INSERT INTO $dbN.gevu_espacesxinterieurs
        (id_lieu, id_instant, nom, ref
                , fonction
                , id_type_specifique_int
                , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc4.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'select_2'
        WHERE fd.id_form = 57
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO ESPACES INTERIEURS : ".mysql_affected_rows()."<br/>";        
        
        //PARCELLES
        $sql = "INSERT INTO $dbN.gevu_parcelles
        (id_lieu, id_instant, nom, ref, adresse, commune, pays, code_postal
                , contact_proprietaire
                , reponse_1
                , reponse_2
        , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc4.valeur
                , fdc5.valeur
                , ''
                , fdc6.valeur
                , fdc7.valeur
                , fdc17.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'ligne_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_5'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc6 ON fdc6.id_donnee = fd.id_donnee
                        AND fdc6.champ = 'code_postal_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc7 ON fdc7.id_donnee = fd.id_donnee
                        AND fdc7.champ = 'ligne_6'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc8 ON fdc8.id_donnee = fd.id_donnee
                        AND fdc8.champ = 'ligne_7'      
                        
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'mot_1'
        
        WHERE fd.id_form = 58
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO PARCELLES : ".mysql_affected_rows()."<br/>"; 
        
        //ESPACES EXTERIEURS
        $sql = "INSERT INTO $dbN.gevu_espacesxexterieurs
        (id_lieu, id_instant, nom, ref
                , fonction
                , id_type_espace
                , id_type_specifique_ext
                , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc4.valeur
                , fdc4.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'select_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'select_2'
        WHERE fd.id_form = 61
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO ESPACES EXTERIEURS : ".mysql_affected_rows()."<br/>";        
        
        //DIAGNOSTICS VOIRIE
        $sql = "INSERT INTO $dbN.gevu_diagnosticsxvoirie
        (id_lieu, id_instant, nom, ref, id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc3.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
        WHERE fd.id_form = 62
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO DIAGNOSTICS VOIRIE : ".mysql_affected_rows()."<br/>";        
        
        //OBJETS INTERIEURS
        $sql = "INSERT INTO $dbN.gevu_objetsxinterieurs
        (id_lieu, id_instant, nom, ref
                , fonctions
                , reponse_1
                , reponse_2
                , id_type_objet
        , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc17.valeur
                , fdc18.valeur
                , fdc19.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'mot_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'mot_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc19 ON fdc19.id_donnee = fd.id_donnee
                        AND fdc19.champ = 'mot_3'
        WHERE fd.id_form = 63
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO OBJETS INTERIEURS : ".mysql_affected_rows()."<br/>"; 
        
        //OBJETS EXTERIEURS
        $sql = "INSERT INTO $dbN.gevu_objetsxexterieurs
        (id_lieu, id_instant, nom, ref
                , fonctions
                , id_type_objet
                , id_type_objet_ext
        , id_donnee, maj)
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc3.valeur
                , fdc17.valeur
                , fdc18.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc17 ON fdc17.id_donnee = fd.id_donnee
                        AND fdc17.champ = 'select_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'select_2'
        WHERE fd.id_form = 64
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO OBJETS EXTERIEURS : ".mysql_affected_rows()."<br/>"; 
        
        //ELEMENTS VOIRIE
        $sql = "INSERT INTO $dbN.gevu_objetsxvoiries
        (id_lieu, id_instant, nom, ref
                , id_type_objet_voirie, id_donnee, maj
        )
        SELECT l.id_lieu
                , $idInstant
                , fdc1.valeur
                , fdc2.valeur
                , fdc18.valeur
                , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc18 ON fdc18.id_donnee = fd.id_donnee
                        AND fdc18.champ = 'mot_1'
        WHERE fd.id_form = 69
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO ELEMENTS VOIRIE : ".mysql_affected_rows()."<br/>";   
        
        //DOCUMENTS
        $sql = "INSERT INTO $dbN.gevu_docs
        (tronc, id_instant, path_source, titre, content_type, maj)
        SELECT id_document
                , $idInstant
                , fichier
                , d.titre
                , mime_type
                , d.date 
        FROM $dbO.spip_documents d
                INNER JOIN $dbO.spip_types_documents td ON td.id_type = d.id_type
        ";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO DOCUMENTS : ".mysql_affected_rows()."<br/>"; 
        
        //DOCUMENTS LIEUX
        $sql = "INSERT INTO $dbN.gevu_docsxlieux
        (id_doc, id_lieu, id_instant)
        SELECT gd.id_doc
                , l.id_lieu
                , $idInstant
        FROM $dbN.gevu_docs gd
                INNER JOIN $dbO.spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = $idInstant
                INNER JOIN $dbO.spip_documents_rubriques dr ON dr.id_document = d.id_document
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = dr.id_rubrique  AND l.id_instant = $idInstant
        ";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO DOCUMENTS LIEUX rubrique: ".mysql_affected_rows()."<br/>";   
        
        $sql = "INSERT INTO $dbN.gevu_docsxlieux
        (id_doc, id_lieu, id_instant)
        SELECT gd.id_doc
                , l.id_lieu
                , $idInstant
        FROM $dbN.gevu_docs gd
                INNER JOIN $dbO.spip_documents d ON d.id_document = gd.tronc AND gd.id_instant = $idInstant
                INNER JOIN $dbO.spip_documents_articles da ON da.id_document = d.id_document
                INNER JOIN $dbO.spip_articles a ON a.id_article = da.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
        ";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO DOCUMENTS LIEUX article: ".mysql_affected_rows()."<br/>";    
        
        
        //OBSERVATIONS
        //on ne conserve que le champ photo
        //les champs 'fichier' et 'doc' devront �tre ajouter � la table gevu_doc
        $sql = "INSERT INTO $dbN.gevu_observations
        (id_lieu, id_instant, id_reponse, num_marker, lib, id_critere, id_donnee, maj)
        SELECT l.id_lieu
                        , $idInstant
                        , m.id_mot
                        , fdc2.valeur
                        , fdc3.valeur
                        , c.id_critere
                        , fd.id_donnee, fd.date 
        FROM $dbO.spip_forms_donnees fd
                INNER JOIN $dbO.spip_forms_donnees_articles fda ON fd.id_donnee = fda.id_donnee
                INNER JOIN $dbO.spip_articles a ON a.id_article = fda.id_article
                INNER JOIN $dbN.gevu_lieux l ON l.id_rubrique = a.id_rubrique AND l.id_instant = $idInstant
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc1 ON fdc1.id_donnee = fd.id_donnee
                        AND fdc1.champ = 'ligne_1'
                INNER JOIN $dbO.spip_mots m ON m.titre = fdc1.valeur
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc2 ON fdc2.id_donnee = fd.id_donnee
                        AND fdc2.champ = 'ligne_2'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc3 ON fdc3.id_donnee = fd.id_donnee
                        AND fdc3.champ = 'ligne_3'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc4 ON fdc4.id_donnee = fd.id_donnee
                        AND fdc4.champ = 'ligne_4'
                LEFT JOIN $dbO.spip_forms_donnees_champs fdc5 ON fdc5.id_donnee = fd.id_donnee
                        AND fdc5.champ = 'ligne_5'
                LEFT JOIN $dbN.gevu_criteres c ON c.ref = fdc5.valeur
        WHERE fd.id_form =67
        GROUP BY fd.id_donnee";
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        echo "$dbO OBSERVATIONS: ".mysql_affected_rows()."<br/>";       


        //AUTEURS
        //l'importation n'est � faire que une fois
        $sql = "INSERT INTO $dbN.gevu_exis
        (nom, url, mail, mdp, mdp_sel, role)
        SELECT a.nom
			, a.url_site 	
			, a.email
			, pass
			, alea_actuel
            , statut
        FROM $dbO.spip_auteurs a
        ";
        //$result = mysql_query($sql);
        //if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        $sql = "UPDATE gevu_exis SET role = 'manager'";
        //$result = mysql_query($sql);
        //if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        //echo "$dbO AUTEURS: ".mysql_affected_rows()."<br/>";       
        
        echo "FIN IMPORT --------------------<br/>--------------<br/>"; 
        
}
	
/*
 * Compare $dbO avec $dbN. Si un noeud de $dbO n'est pas inclus dans $dbN, le copier avec tous ces
 * enfants via la fonction setLieuxHierarchie(...).
 *
 * N.B.
 * la fonction doit-être modifiée pour prendre en compte des noeuds éxistants dans $dbN mais ayants
 * des attributs différents 
 */

function CompareBases($dbN, $dbO, $idRub=5479, $niv=0){
/*
 * comme la france n'est pas dans $dbN, il y a une coupure dans la hiérarchie (pas de fils de 
 * UNIVERS dans cette base). Soit on inclus la France, soit on ajoute le code si dessous.
 * Autre incohérence: Univers a un id=0 et est fils de 0!!! tout comme territoire et compagnie.
 */

    // pour chaque enfant de FRANCE, appliquer CompareBase(...)
    $sql = "SELECT r.id_rubrique FROM $dbO.spip_rubriques r
            WHERE r.id_parent = $idRub";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        
    ++$niv;
    while($row=mysql_fetch_array($res))
    {
        CompareBasesCore($dbN, $dbO, $row['id_rubrique'], $niv);
    }
    /*
     * code qui aurait du être éxécuté:        
    CompareBasesCore($dbN, $dbO, 5479, $niv???);
    */
}

function CompareBasesCore($dbN, $dbO, $idRub, $niv=1){
/* !!! niv est nécessaire ? à vérifier ...
 * $niv<0  =>  niveau inconnu.
 * doit chercher dans toute la base
 */

    // récupérer les paramètres de $idRub
    $sql = "SELECT titre, id_parent FROM $dbO.spip_rubriques r
            WHERE r.id_rubrique=$idRub";
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
    $row = mysql_fetch_array($res);

    // cherche le noeud $idRub dans $dbN en ce basant sur les paramètres précédents
    $sql = "SELECT id_lieu FROM $dbN.gevu_lieux r
            WHERE r.lib=\"".$row['titre']."\" AND id_parent=".$row['id_parent'];
    $res = mysql_query($sql);
    if (!$res) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
    
    // s'il n'éxiste pas
    if (mysql_fetch_array($res)==NULL)
    {
        echo "\t- noeud à copier: \"".$row['titre']."\" fils de ".$row['id_parent']."<br/>";
        // copier ce noeud et sa descendance via setLieuxHierarchie()
        
        // modifier les (gauche-droite)s des noeuds à droite des ajouts
    }
    
    // s'il éxiste
    else
    {
        // appliquer la fonction de façon récursive sur tout les enfants de $idRub
        $sql = "SELECT r.id_rubrique FROM $dbO.spip_rubriques r
                WHERE r.id_parent = $idRub";
        $res = mysql_query($sql);
        if (!$res) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        
        ++$niv;
        while($row=mysql_fetch_array($res))
        {
            CompareBasesCore($dbN, $dbO, $row['id_rubrique'], $niv);
        }   
    }
}


/*
 * Pour créer la hiérarchie des lieux selon le format "Nested Sets" 
 * http://dev.mysql.com/tech-resources/articles/hierarchical-data.html
 * 
 * recherche les enfants de $idRub
 */
function setLieuxHierarchie($idParent, $dbN, $dbO, $idInstant, $idRub, $lft, $rgt, $niv=1){

    //récupère les enfants de la rubrique
    $sql = "SELECT r.id_rubrique, r.titre, r.id_parent
            FROM $dbO.spip_rubriques r
            WHERE id_parent = $idRub";
    $res = mysql_query($sql);

    while($row=mysql_fetch_array($res)) {
   	    $lft++;
   	    $rgt++;
   	
    	//on insert un lieu pour récupérer l'identifiant
       	$sql = "INSERT INTO $dbN.gevu_lieux
    	        (id_instant, lieu_parent, id_rubrique, lib, id_parent, lft, rgt, niv, maj)
                SELECT $idInstant, $idParent, r.id_rubrique, r.titre, r.id_parent, $lft, $rgt, $niv, r.maj 
                FROM $dbO.spip_rubriques r
                WHERE id_rubrique = ".$row['id_rubrique'];
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        $idLieu = mysql_insert_id();
   	
        echo str_repeat("----", $niv)." ".$row['id_rubrique'].' -> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
        $arr= setLieuxHierarchie($idLieu, $dbN, $dbO, $idInstant, $row['id_rubrique'], $lft, $rgt, $niv+1);
        if($lft<$arr[0]){
            $rgt=$arr[1];
        }else{
            $lft=$arr[0];	//----
            $rgt=$arr[1];	//----
        }

        echo str_repeat("----", $niv)." ".$row['id_rubrique'].' <-> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
	
        //on met à jour le gauche droite
        $sql = "UPDATE $dbN.gevu_lieux SET lft = $lft, rgt = $rgt WHERE id_lieu = ".$idLieu;
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        $lft = $rgt;         //----
        $rgt= $lft+1;	     //----
    }
    return array($lft,$rgt);		
}


//fermeture de la connexion    
mysql_close($ldb);
?>


<?php
/**
 * setLieuxHierarchie(...) fonctionel: à piocher dedans en cas de pépin
 * 

function setLieuxHierarchie($idParent, $dbN, $dbO, $idInstant, $idRub, $lft, $rgt, $niv=1){

    //récupère les enfants de la rubrique
    $sql = "SELECT r.id_rubrique, r.titre, r.id_parent
            FROM $dbO.spip_rubriques r
            WHERE id_parent = $idRub";
    $res = mysql_query($sql);

    while($row=mysql_fetch_array($res)) {
        $lft++;
        $rgt++;
    
        //on insert un lieu pour récupérer l'identifiant
        $sql = "INSERT INTO $dbN.gevu_lieux
                (id_instant, lieu_parent, id_rubrique, lib, id_parent, lft, rgt, niv, maj)
                SELECT $idInstant, $idParent, r.id_rubrique, r.titre, r.id_parent, $lft, $rgt, $niv, r.maj 
                FROM $dbO.spip_rubriques r
                WHERE id_rubrique = ".$row['id_rubrique'];
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        $idLieu = mysql_insert_id();
    
        echo str_repeat("----", $niv)." ".$row['id_rubrique'].' -> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
        $arr= setLieuxHierarchie($idLieu, $dbN, $dbO, $idInstant, $row['id_rubrique'], $lft, $rgt, $niv+1);
        if($lft<$arr[0]){
            $rgt=$arr[1];
        }else{
            $lft=$arr[0];   //----
            $rgt=$arr[1];   //----
        }

        echo str_repeat("----", $niv)." ".$row['id_rubrique'].' <-> lft: '.$lft.' rgt: '.$rgt.' niv: '.$niv.'<br/>';
    
        //on met à jour le gauche droite
        $sql = "UPDATE $dbN.gevu_lieux SET lft = $lft, rgt = $rgt WHERE id_lieu = ".$idLieu;
        $result = mysql_query($sql);
        if (!$result) echo 'Requête invalide : ' . mysql_error().'<br/>'.$sql.'<br/>';
        $lft = $rgt;         //----
        $rgt= $lft+1;        //----
    }
    return array($lft,$rgt);        
}
**/
?>