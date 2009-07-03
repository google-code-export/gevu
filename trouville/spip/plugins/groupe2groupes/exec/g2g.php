<?php 
	    
	if (!defined("_ECRIRE_INC_VERSION")) return;
	    
	include_spip('inc/presentation');
	include_spip('base/abstract_sql');
	
	
	function exec_g2g_dans_groupe($id_groupe) {
		global $table;
		$result = spip_query("SELECT id_groupe, titre FROM spip_groupes_mots ". ($table ? "WHERE $table='oui'" : '') . " ORDER BY titre");
	
		echo "<b>"._T('info_dans_groupe'), aide("motsgroupes")."</b><br />\n";
		debut_cadre_relief("groupe-mot-24.gif");
		if (spip_num_rows($result)>1) {
	
			echo  " &nbsp; <SELECT NAME='id_parent' class='fondl'>\n";
			echo  "<OPTION".mySel(0, $id_groupe).">Racine</OPTION>\n";
			while ($row_groupes = spip_fetch_array($result)){
				$groupe = $row_groupes['id_groupe'];
				//deb ajout Lucky Semiosis
				//$titre_groupe = texte_backend(supprimer_tags(typo($row_groupes['titre'])));
				$titre_groupe = pipeline('arbo_groupe',array('args'=>array('exec'=>'mots_edit','id_groupe'=>$groupe),'data'=>''));
				//fin ajout Lucky Semiosis
	
				echo  "<OPTION".mySel($groupe, $id_groupe).">$titre_groupe</OPTION>\n";
			}			
			echo  "</SELECT>";
		} else {
			$row_groupes = spip_fetch_array($result);
			if (!$row_groupes) {
				// il faut creer un groupe de mots (cas d'un mot cree depuis le script articles)
	
				$titre = _T('info_mot_sans_groupe');
				$row_groupes['id_groupe'] = spip_abstract_insert("spip_groupes_mots", "(titre, unseul, obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum)", "(" . spip_abstract_quote($titre) . ", 'non',  'non', '" . (($table=='articles') ? 'oui' : 'non') ."', '" . (($table=='breves') ? 'oui' : 'non') ."','" . (($table=='rubriques') ? 'oui' : 'non') ."','" . (($table=='syndic') ? 'oui' : 'non') ."', 'oui', 'non', 'non'" . ")");
			} else $titre = $row_groupes['titre'];
			echo $titre, '<br />';
			echo "<input type='hidden' name='id_parent' value='".$row_groupes['id_groupe']."' />";
		}
		fin_cadre_relief();
	}
	
	
	function exec_g2g_arbo_groupe($id_groupe) {
	
		$query_groupes = "SELECT g0.id_parent, g0.titre
			FROM spip_groupes_mots g0
			WHERE g0.id_groupe = ".$id_groupe;
		//echo $query_groupes."<br/>\n";
		
		$result = spip_query($query_groupes);
		while ($row_groupes = spip_fetch_array($result)){
			//echo $row_groupes['id_parent']." - ".$row_groupes['titre']."<br/>\n";
			if($row_groupes['id_parent']!= 0) {
				$titre_groupe = exec_g2g_arbo_groupe($row_groupes['id_parent']);
			}
			$titre_groupe .= " | ".$row_groupes['titre'];
		}
		return $titre_groupe;
	}
	
	    
	function exec_g2g_affiche_groupe_enfant($id_parent) {
 	    
		$result_groupes = spip_query("SELECT *, ".creer_objet_multi ("titre", "$spip_lang")." 
			FROM spip_groupes_mots WHERE id_parent = ".$id_parent." ORDER BY multi");
		
		$out = "";
		
		while ($row_groupes = spip_fetch_array($result_groupes)) {
			$id_groupe = $row_groupes['id_groupe'];
			$titre_groupe = typo($row_groupes['titre']);
			$descriptif = $row_groupes['descriptif'];
			$texte = $row_groupes['texte'];
			$unseul = $row_groupes['unseul'];
			$obligatoire = $row_groupes['obligatoire'];
			$articles = $row_groupes['articles'];
			$breves = $row_groupes['breves'];
			$rubriques = $row_groupes['rubriques'];
			$syndic = $row_groupes['syndic'];
			$acces_minirezo = $row_groupes['minirezo'];
			$acces_comite = $row_groupes['comite'];
			$acces_forum = $row_groupes['forum'];
		
			// Afficher le titre du groupe
			debut_cadre_enfonce("groupe-mot-24.gif", false, '', $titre_groupe);
			// Affichage des options du groupe (types d'elements, permissions...)
			echo "<font face='Verdana,Arial,Sans,sans-serif' size=1>";
			if ($articles == "oui") echo "> "._T('info_articles_2')." &nbsp;&nbsp;";
			if ($breves == "oui") echo "> "._T('info_breves_02')." &nbsp;&nbsp;";
			if ($rubriques == "oui") echo "> "._T('info_rubriques')." &nbsp;&nbsp;";
			if ($syndic == "oui") echo "> "._T('icone_sites_references')." &nbsp;&nbsp;";
		
			if ($unseul == "oui" OR $obligatoire == "oui") echo "<br>";
			if ($unseul == "oui") echo "> "._T('info_un_mot')." &nbsp;&nbsp;";
			if ($obligatoire == "oui") echo "> "._T('info_groupe_important')." &nbsp;&nbsp;";
		
			echo "<br />";
			if ($acces_minirezo == "oui") echo "> "._T('info_administrateurs')." &nbsp;&nbsp;";
			if ($acces_comite == "oui") echo "> "._T('info_redacteurs')." &nbsp;&nbsp;";
			if ($acces_forum == "oui") echo "> "._T('info_visiteurs_02')." &nbsp;&nbsp;";
		
			echo "</font>";
			if ($descriptif) {
				echo "<div style='border: 1px dashed #aaaaaa;'>";
				echo "<font size='2' face='Verdana,Arial,Sans,sans-serif'>";
				echo "<b>"._T('info_descriptif')."</b> ";
				echo propre($descriptif);
				echo "&nbsp; ";
				echo "</font>";
				echo "</div>";
			}
		
			if (strlen($texte)>0){
				echo "<FONT FACE='Verdana,Arial,Sans,sans-serif'>";
				echo propre($texte);
				echo "</FONT>";
			}
		
			//
			// Afficher les mots-cles du groupe
			//
			$supprimer_groupe = afficher_groupe_mots($id_groupe);
		
			echo "<div id='editer_mot-$id_groupe' style='position: relative;'>";
		
			// Preliminaire: confirmation de suppression d'un mot lie à qqch
			// (cf fin de afficher_groupe_mots_boucle executee a l'appel precedent)
			if ($conf_mot  AND $son_groupe==$id_groupe)
				echo confirmer_mot($conf_mot, $id_groupe);
		
			echo $supprimer_groupe;
		
			echo "</div>";
		
			if (acces_mots() AND !$conf_mot){
				echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
				echo "<tr>";
				echo "<td>";
				echo icone(_T('icone_modif_groupe_mots'), generer_url_ecrire("mots_type","id_groupe=$id_groupe"), "groupe-mot-24.gif", "edit.gif");
				echo "</td>";
				if (!$supprimer_groupe) {
					echo "<td>";
					echo icone(_T('icone_supprimer_groupe_mots'), generer_url_ecrire("mots_tous","supp_group=$id_groupe"), "groupe-mot-24.gif", "supprimer.gif");
					echo "</td>";
					echo "<td> &nbsp; </td>"; // Histoire de forcer "supprimer" un peu plus vers la gauche
				}
				echo "<td>";
				echo "<div align='$spip_lang_right'>";
				echo icone(_T('icone_creation_mots_cles'), generer_url_ecrire("mots_edit","new=oui&id_groupe=$id_groupe&redirect=" . generer_url_retour('mots_tous')), "mot-cle-24.gif", "creer.gif");
				echo "</div>";
				
				echo "<td>";
				echo "<div align='$spip_lang_right'>";
				icone(_T('icone_creation_groupe_mots'), generer_url_ecrire("mots_type","new=oui"), "groupe-mot-24.gif", "creer.gif");
				echo "</div>";
			
				echo "</td></tr></table>";
			}	
		
			//récupère les groupes enfants
			exec_g2g_affiche_groupe_enfant($id_groupe);	

			echo fin_cadre_enfonce();
		}
		//return $out;
	} 	    
?>  