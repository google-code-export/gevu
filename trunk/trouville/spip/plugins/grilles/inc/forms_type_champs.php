<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	$GLOBALS['forms_types_champs_etendus']=array();
	Forms_importe_types_etendus();

	function Forms_importe_types_etendus(){
		if ($f = find_in_path('etc/forms_types_champs.xml')){
			$date = filemtime($f);
			if (isset($GLOBALS['meta']['forms_types_champs']))
				$t = unserialize($GLOBALS['meta']['forms_types_champs']);
			if (isset($t['date'])&& ($t['date']==$date) && isset($t['types']) && is_array($t['types']))
				$GLOBALS['forms_types_champs_etendus'] = $t['types'];
			else {
				include_spip('inc/plugin');
				$contenu = "";
				lire_fichier ($f, $contenu);
				$GLOBALS['forms_types_champs_etendus']=array();
				$data = parse_plugin_xml($contenu);
				if (isset($data['types']))
					foreach($data['types'] as $types)
						if (isset($types['type'])) 
							foreach($types['type'] as $type){
								if (isset($type['field'])){
									$champ = end($type['field']);
									$libelle = isset($type['label'])?trim(applatit_arbre($type['label'])):$champ;
									$match = isset($type['match'])?trim(end($type['match'])):"";
									if (!in_array($champ,Forms_liste_types_champs()))
										$GLOBALS['forms_types_champs_etendus'][$champ]=array('label'=>$libelle,'match'=>$match);
								}
							}
				ecrire_meta('forms_types_champs',serialize(array("date"=>$date,"types"=>$GLOBALS['forms_types_champs_etendus'])));
				ecrire_metas();
			}
		}
	}


	function Forms_liste_types_champs(){
		$types_etendus = array_keys($GLOBALS['forms_types_champs_etendus']);
		return array_merge(array('ligne', 'texte', 'date', 'email', 'url', 'select', 'multiple', 'fichier', 'mot','separateur','textestatique'),$types_etendus);
	}
	function Forms_type_champ_autorise($type) {
		static $t;
		if (!$t) {
			$t = array_flip(Forms_liste_types_champs());
		}
		return isset($t[$type]);
	}
	function Forms_nom_type_champ($type) {
		static $noms;
		if (!$noms) {
			$noms = array(
				'ligne' => _T("forms:champ_type_ligne"),
				'texte' => _T("forms:champ_type_texte"),
				'date' => _T("forms:champ_type_date"),
				'url' => _T("forms:champ_type_url"),
				'email' => _T("forms:champ_type_email"),
				'select' => _T("forms:champ_type_select"),
				'multiple' => _T("forms:champ_type_multiple"),
				'fichier' => _T("forms:champ_type_fichier"),
				'mot' => _T("forms:champ_type_mot"),
				'separateur' => _T("forms:champ_type_separateur"),
				'textestatique' => _T("forms:champ_type_textestatique")
			);
			foreach($GLOBALS['forms_types_champs_etendus'] as $t=>$champ)
				$noms[$t] = $champ['label'];
		}
		return ($s = $noms[$type]) ? $s : $type;
	}

	function Forms_valide_champs_reponse_post($id_form, $c = NULL, $structure = NULL){
		$erreur = array();
		if (!$structure){
			include_spip("inc/forms");
			$structure = Forms_structure($id_form);
		}
		foreach($structure as $champ=>$infos){
			$type = $infos['type'];
			if ($GLOBALS['spip_version_code']<1.92)
				$val = _request($champ);
			else
				$val = _request($champ, $c);

			// verifier la presence des champs obligatoires
			if (!$val || ($type == 'fichier' && !$_FILES[$champ]['tmp_name'])) {
				if ($infos['obligatoire'] == 'oui')
					$erreur[$champ] = _T("forms:champ_necessaire");
			}
		}

		$erreur = array_merge($erreur,
			Forms_valide_conformite_champs_reponse_post($id_form, $c, $structure));

		return $erreur;
	}


	function Forms_valide_conformite_champs_reponse_post($id_form, $c = NULL, $structure = NULL){
		$erreur = array();
		if (!$structure){
			include_spip("inc/forms");
			$structure = Forms_structure($id_form);
		}
		foreach($structure as $champ=>$infos){
			$type = $infos['type'];
			if ($GLOBALS['spip_version_code']<1.92)
				$val = _request($champ);
			else
				$val = _request($champ, $c);
			if ( $val!=NULL && strlen($val) ) {

				// Verifier la conformite des donnees entrees
				if ($type == 'date') {
					if (!preg_match("#^\s*([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})\s*$#",$val,$regs)) {
					$erreur[$champ] = _T("forms:date_invalide");
					}
					else 
						if (!mktime(0,0,0,$reg[1],$regs[0],$regs[2]))
							$erreur[$champ] = _T("forms:date_invalide");
				}
				// Verifier la conformite des donnees entrees
				if ($type == 'email') {
					include_spip('inc/filtres');
					if (!strpos($val, '@') || !email_valide($val)) {
						$erreur[$champ] = _T("forms:adresse_invalide");
					}
				}
				if ($type == 'url') {
					if ($row['extra_info'] == 'oui') {
						include_spip("inc/sites");
						if (!recuperer_page($val)) {
							$erreur[$champ] = _T("forms:site_introuvable");
						}
					}
				}
				if ($type == 'fichier') {
					if (!$taille = $_FILES[$champ]['size']) {
						$erreur[$champ] = _T("forms:echec_upload");
					}
					else if ($row['extra_info'] && $taille > ($row['extra_info'] * 1024)) {
					$erreur[$champ] = _T("forms:fichier_trop_gros");
					}
					else if (!Forms_type_fichier_autorise($_FILES[$champ]['name'])) {
						$erreur[$champ] = _T("fichier_type_interdit");
					}
					if ($erreur[$champ]) {
						supprimer_fichier($_FILES[$champ]['tmp_name']);
					}
				}
				if ($type=='multiple' or $type=='select' or $type=='mot'){
					if (!is_array($val)) $val = array($val);
					foreach($val as $v)
						if (!isset($infos['choix'][$v]))
							$erreur[$champ] = _T("forms:donnee_inattendue");
				}
				if (isset($GLOBALS['forms_types_champs_etendus'][$type])){
					$match = $GLOBALS['forms_types_champs_etendus'][$type]['match'];
					if (strlen($match) && !preg_match($match,$val))
						$erreur[$champ] = _T("forms:champs_perso_invalide");
				}
			}
		}
		return $erreur;
	}


?>