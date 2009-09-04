<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * ??? 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
	function Forms_structure2table($row,$clean=false){
		$id_form=$row[id_form];
		// netoyer la structure precedente en table
		if ($clean){
			spip_query("DELETE FROM spip_forms_champs WHERE id_form="._q($id_form));
			spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form="._q($id_form));
		}
		
		$structure = unserialize($row['structure']);
		$rang = 1;
		foreach($structure as $cle=>$val){
			$champ = $val['code'];
			$titre = $val['nom'];
			$type = $val['type'];
			$obligatoire = $val['obligatoire'];
			$type_ext = $val['type_ext'];
			$extra_info = isset($type_ext['id_groupe']) ? $type_ext['id_groupe']:'';
			$extra_info = isset($type_ext['taille']) ? $type_ext['taille']:$extra_info;
			$obligatoire = $val['obligatoire'];
			spip_query("INSERT INTO spip_forms_champs (id_form,rang,champ,titre,type,obligatoire,extra_info) 
				VALUES("._q($id_form).","._q($rang++).","._q($champ).","._q($titre).","._q($type).","._q($obligatoire).","._q($extra_info).")");
			if ($type=='select' OR $type=='multiple'){
				$rangchoix = 1;
				foreach($type_ext as $choix=>$titre){
					spip_query("INSERT INTO spip_forms_champs_choix (id_form,champ,choix,titre,rang) 
						VALUES("._q($id_form).","._q($champ).","._q($choix).","._q($titre).","._q($rangchoix++).")");
				}
			}
		}
	}
	function Forms_allstructure2table($clean=false){
		$res = spip_query("SELECT * FROM spip_forms");
		while ($row=spip_fetch_array($res))
			Forms_structure2table($row,$clean);
	}

	function Forms_upgrade(){
		$version_base = 0.25;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['forms_base_version']) )
				&& (($current_version = $GLOBALS['meta']['forms_base_version'])==$version_base))
			return;

		include_spip('base/forms');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// attention on vient peut etre d'une table spip-forms 1.8
			$desc = spip_abstract_showtable('spip_forms','',true);
			if (isset($desc['field'])) 
				$current_version=0.1;
			else {
				creer_base();
				ecrire_meta('forms_base_version',$current_version=$version_base);
			}
		}
		if ($current_version<0.11){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			$query = "ALTER TABLE spip_forms CHANGE `email` `email` TEXT NOT NULL ";
			$res = spip_query($query);
			$query = "SELECT * FROM spip_forms";
			$res = spip_query($query);
			while ($row = spip_fetch_array($res)){
				$email = $row['email'];
				$id_form = $row['id_form'];
				if (unserialize($email)==FALSE){
					$email=addslashes(serialize(array('defaut'=>$email)));
					$query = "UPDATE spip_forms SET email='$email' WHERE id_form=$id_form";
					spip_query($query);
				}
			}
			ecrire_meta('forms_base_version',$current_version=0.11);
		}
		if ($current_version<0.12){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			spip_query("ALTER TABLE spip_forms CHANGE `descriptif` `descriptif` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `schema` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `email` `email` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `texte` `texte` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.12);
		}
		if ($current_version<0.13){
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `structure` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.13);
		}
		if ($current_version<0.14){
			spip_query("ALTER TABLE spip_reponses ADD `id_article_export` BIGINT( 21 ) NOT NULL AFTER `id_auteur` ");
			ecrire_meta('forms_base_version',$current_version=0.14);
		}
		if ($current_version<0.15){
			spip_query("ALTER TABLE spip_reponses ADD `url` VARCHAR(255) NOT NULL AFTER `id_article_export` ");
			ecrire_meta('forms_base_version',$current_version=0.15);
		}
		// maj en version 0.16 annulee et remplacee par 0.17
		if ($current_version<0.17){
			// virer les tables temporaires crees manuellement sur les serveurs ou ca foirait
			spip_query("DROP TABLE spip_forms_champs");
			spip_query("DROP TABLE spip_forms_champs_choix");
			
			// renommer les tables qui changent de nom, sinon elles vont etre crees
			spip_query("ALTER TABLE spip_reponses RENAME spip_forms_donnees");
			spip_query("ALTER TABLE spip_reponses_champs RENAME spip_forms_donnees_champs");
			// creer toutes les nouvelles tables
			include_spip('base/forms');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			Forms_allstructure2table();

  		spip_query("ALTER TABLE spip_forms DROP structure");
  		spip_query("ALTER TABLE spip_forms CHANGE sondage type_form VARCHAR(255) NOT NULL");
			spip_query("ALTER TABLE spip_forms ADD moderation VARCHAR(10) DEFAULT 'posteriori' NOT NULL AFTER texte");
			spip_query("ALTER TABLE spip_forms ADD public ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER moderation");
			spip_query("UPDATE spip_forms SET public='non'"); // par securite
			spip_query("UPDATE spip_forms SET type_form='sondage', public='oui' WHERE type_form='public'");
			spip_query("UPDATE spip_forms SET type_form='sondage', public='non' WHERE type_form='prot'");
			spip_query("UPDATE spip_forms SET type_form='', public='non' WHERE type_form='non'");
			
			spip_query("ALTER TABLE spip_forms_donnees CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL AUTO_INCREMENT");
			spip_query("ALTER TABLE spip_forms_donnees_champs CHANGE id_reponse id_donnee BIGINT( 21 ) NOT NULL");
			spip_query("ALTER TABLE spip_forms_donnees_champs DROP INDEX id_reponse ,ADD INDEX id_donnee (id_donnee) ");

			spip_query("ALTER TABLE spip_forms_champs ADD specifiant ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER extra_info");
			spip_query("ALTER TABLE spip_forms_champs ADD public ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER specifiant");
			spip_query("ALTER TABLE spip_forms_champs ADD aide text AFTER public");
			spip_query("ALTER TABLE spip_forms_champs ADD html_wrap text AFTER aide");
			spip_query("UPDATE spip_forms_champs SET specifiant='non', public='non'"); // par securite

			spip_query("ALTER TABLE spip_forms_donnees CHANGE statut confirmation VARCHAR(10) NOT NULL");
			spip_query("ALTER TABLE spip_forms_donnees ADD statut VARCHAR(10) NOT NULL AFTER confirmation");
			spip_query("UPDATE spip_forms_donnees SET statut='publie'"); // par securite
			ecrire_meta('forms_base_version',$current_version=0.17);
		}
		if ($current_version<0.18){
			spip_query("ALTER TABLE spip_forms ADD linkable ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER public");
			
			// init la valeur par defaut de extra_info sur les champs select (aurait du etre fait en 0.17
			$res = spip_query("SELECT * FROM spip_forms_champs WHERE type='select'");
			while ($row = spip_fetch_array($res)){
				if (!in_array($row['extra_info'],array('liste','radio'))){
					$extra_info = 'liste';
					$row2 = spip_fetch_array(spip_query("SELECT COUNT(choix) as n FROM spip_forms_champs_choix WHERE id_form="._q($row['id_form'])." AND champ="._q($row['champ'])));
					if ($row2 && $row2['n']<6) $extra_info='radio';
					spip_query("UPDATE spip_forms_champs SET extra_info='$extra_info' WHERE id_form="._q($row['id_form'])." AND champ="._q($row['champ']));
				}
			}
			ecrire_meta('forms_base_version',$current_version=0.18);
		}
		if ($current_version<0.19){
			spip_query("ALTER TABLE spip_forms ADD html_wrap text AFTER linkable");
			ecrire_meta('forms_base_version',$current_version=0.19);
		}
		if ($current_version<0.20){
			spip_query("ALTER TABLE spip_forms_champs CHANGE champ champ varchar(100) NOT NULL");
			spip_query("ALTER TABLE spip_forms_champs_choix CHANGE champ champ varchar(100) NOT NULL");
			// on rappelle creer base car la creation de forms_champs et forms_champs_choix a pu echouer sur mysql 3.23
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('forms_base_version',$current_version=0.20);
		}
		if ($current_version<0.21){
			spip_query("ALTER TABLE spip_forms ADD forms_obligatoires VARCHAR(255) DEFAULT '' AFTER type_form");
			spip_query("ALTER TABLE spip_forms ADD modifiable ENUM('non', 'oui') DEFAULT 'non' AFTER type_form");
			spip_query("ALTER TABLE spip_forms ADD multiple ENUM('non', 'oui') DEFAULT 'non' AFTER type_form");
			ecrire_meta('forms_base_version',$current_version=0.21);
		}
		if ($current_version<0.22){
			// creer toutes la nouvelle table spip_documents_donnees
			include_spip('base/forms');
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('documents_donnee','oui');
			ecrire_meta('forms_base_version',$current_version=0.22);
		}
		if ($current_version<0.23){
			spip_query("ALTER TABLE spip_forms ADD documents ENUM('non', 'oui') DEFAULT 'non' NOT NULL AFTER linkable");
			ecrire_meta('forms_base_version',$current_version=0.23);
		}
		if ($current_version<0.24){
			spip_query("ALTER TABLE spip_forms_donnees ADD rang bigint(21) NOT NULL AFTER cookie");
			$res = spip_query("SELECT id_form FROM spip_forms");
			while ($row = spip_fetch_array($res)){
				$res2 = spip_query("SELECT id_donnee FROM spip_forms_donnees WHERE id_form=".$row['id_form']." ORDER BY id_donnee");
				$rang=1;
				while ($row2 = spip_fetch_array($res2)){
					spip_query("UPDATE spip_forms_donnees SET rang=$rang WHERE id_donnee=".$row2['id_donnee']);
					$rang++;
				}
			}
			ecrire_meta('forms_base_version',$current_version=0.25);
		}
		if ($current_version<0.25){
			spip_query("ALTER TABLE `spip_forms_donnees_champs` 
				ADD `valint` INT NOT NULL ,
				ADD `valdec` DECIMAL( 10, 6 ) NOT NULL ");
			ecrire_meta('forms_base_version',$current_version=0.25);
		}
		ecrire_metas();
	}
	
?>