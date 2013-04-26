<?php

class GEVU_Migration extends GEVU_Site{

	var $idBaseSrc;
	var $idBaseDst;
	var $dbSrc;
	var $dbDst;
	var $arrDbSrc;
	var $arrDbDst;
	
	/**
	* constructeur de la class
	*
    * @param string $idBase
    * 
    */
	public function __construct($idBaseSrc=false, $idBaseDst=false)
    {
    	parent::__construct($idBaseSrc);
		$this->idBaseSrc=$idBaseSrc;
		$this->idBaseDst=$idBaseDst;
    	
    	$this->dbSrc = $this->getDb($idBaseSrc);
		$this->dbDst = $this->getDb($idBaseDst);
		
    }
	
    /**
     * migre les données de référence du serveur vers la tablette
     *
     * ATTENTION les références de la tablette sont supprimée
     * merci à 
     * http://forum.phpfrance.com/vos-contributions/copie-tables-serveur-distant-t7994.html pour le script
     * http://wiki.gandi.net/fr/hosting/using-linux/tutorials/ubuntu/remote-mysql pour la connexion distante
     */
    public function migreRefServeurToLocal(){

    	 
    	// paramètres de configuration    	
    	$this->bTrace = true;								// pour afficher le nombre de lignes importées et le timing    	
    	$this->echoTrace = "migreRefServeurToLocal<br/>";	//pour stocker les traces
    	$this->temps_debut = microtime(true);
    	$timeLimit          = 30;                			// en sec. Au cas où une connexion distante bloquerait...
    	$ecraser            = true;            				// true on écrase la table cible si elle existe (ATTENTION ! 	   	
    														// ne pas se tromper de nom de table cible sinon... zap!)

    	// paramètres de connexion source
    	$hostSource         = 'localhoszt';		// adresse IP du serveur Source    	
    	$portSource         = '3306';           	// port serveur MySql (3306 par défaut)
    	$userSource         = 'machin';         // utilisateur
    	$mdpSource          = 'truc' ;      	// mot de passe    	
    	$bddSource          = 'bidule';       	// base de donnée Source
    	// tables Sources = 27
    	$tablesSources = array('gevu_contacts','gevu_couts','gevu_criteres','gevu_criteresxtypesxcriteres','gevu_criteresxtypesxdeficiences','gevu_criteresxtypesxdroits'
    			,'gevu_droits','gevu_exis','gevu_exisxdroits','gevu_motsclefs','gevu_produits','gevu_produitsxcouts','gevu_roles','gevu_scenario','gevu_scenes'
    			,'gevu_solutions','gevu_solutionsxcouts','gevu_solutionsxcriteres','gevu_solutionsxmetiers','gevu_solutionsxproduits'
    			,'gevu_typesxcontroles','gevu_typesxcriteres','gevu_typesxdeficiences','gevu_typesxdroits','gevu_typesxsolutions','gevu_typexmotsclefs');
    	    	
  	
    	// paramètres de connexion cible   	
    	$hostCible          = 'localhost';      // adr. IP du serveur Cible (ici localhost pour l'exemple)
    	$portCible          = '3306';           // port serveur MySql (3306 par défaut)
    	$userCible          = 'root';           // utilisateur
    	$mdpCible           = '';    			// mot de passe
    	$bddCible           = 'gevu_vide';      // base de donnée Cible
    	   	
    	// texte d'erreur de connexion
    	$errConnexion = "Impossible d'établir une connexion au port <b>%1\$s</b> du host <u>%2\$s</u> <b>%3\$s</b> dans la limite du temps imparti (%4\$s secondes). <br />Vérifiez l'adresse du host et le numéro de port du serveur %2\$s MySQL.<br />S'ils vous semblent corrects, essayez en changeant la valeur de <b>\$timeLimit</b>.";
    	
    	
    	// CONNEXION - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	// test d'ouverture du port MySQL sur les serveurs
    	if (!@fsockopen($hostSource, $portSource, $errno, $errstr, $timeLimit)){
    		printf($errConnexion, $portSource, 'source', $hostSource,  $timeLimit);
    		exit;
    	}
    	$this->trace('connexion_serveur_source');
    	 
    	if (!@fsockopen($hostCible, $portCible, $errno, $errstr, $timeLimit)){
    		printf($errConnexion, $portCible, 'cible', $hostCible,  $timeLimit);
    		exit;
    	}
    	$this->trace('connexion_serveur_cible');
    	 
    	// connexion aux deux serveurs MySql
    	$linkSource = mysql_connect($hostSource.':'.$portSource, $userSource, $mdpSource)
    	or die (mysql_error());
    	$this->trace('connexion_bdd_source');

    	$linkCible = mysql_connect($hostCible.':'.$portCible, $userCible, $mdpCible)    	
    	or die (mysql_error());
    	$this->trace('connexion_bdd_cible');
    	
    	// SERVEUR SOURCE - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	// sélection de la bdd source
    	mysql_select_db($bddSource, $linkSource)
    	or die (mysql_error($linkSource));
    	$this->trace('selection de la bdd source');
    	 
    	foreach ($tablesSources as $table) {
    		/* importation de la structure de la table source
    		 $qry_import_structure = 'SHOW CREATE TABLE '.$tableSource;
    		$result = mysql_query($qry_import_structure, $linkSource)
    		or die (mysql_error($linkSource));
    		$row = mysql_fetch_row($result);
    		$qry_create = str_replace ('CREATE TABLE `'.$tableSource.'`', 'CREATE TABLE `'.$tableCible.'`', $row[1]);
    		*/
    		
	    	// importation des lignes de la table source et construction de l'INSERT   	
	    	$qry_import_lignes = 'SELECT * FROM '.$table;
	    	$result = mysql_query($qry_import_lignes, $linkSource)
	    	or die (mysql_error($linkSource));    	    	
	    	while ($row = mysql_fetch_row($result)){
	    		$values = '(\''.implode("','", array_map('addslashes', $row)).'\')';
	    		$qry_insert[] = 'INSERT INTO '.$table.' VALUES '.$values;
	    	}
	    	$this->trace('requête : '.$table);
		}
	    	
    	// SERVEUR CIBLE - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    	// sélection de la bdd cible
    	mysql_select_db($bddCible, $linkCible)
    	or die (mysql_error($linkCible));
    	$this->trace('selection de la bdd cible');
    	 
    	foreach ($tablesSources as $table) {
	    	// suppression de la table cible si elle existe déjà
	    	if ($ecraser){
	    		$qry_drop = 'TRUNCATE TABLE '.$table;
	    		mysql_query($qry_drop, $linkCible)
	    		or die (mysql_error($linkCible));
	    		$this->trace('supression données table : '.$table);
	    	}
	    	
	    	/* création de la structure de la table cible
	    	mysql_query($qry_create, $linkCible)
	    	or die (mysql_error($linkCible));
	    	*/	    	
    	}
    	// insertion des lignes dans la table-cible
    	$i=0;
	    $this->trace('données à insérer : '.count($qry_insert));
    	foreach ($qry_insert as $v){
    		mysql_query($v, $linkCible)
    		or die (mysql_error($linkCible));
	    	$this->trace('insertion données : '.$i);
	    	$i++;
    	}
    	
    	// FERMETURE DES SESSIONS SERVEURS - - - - - - - - - - - - - - - - - - - - - - - - -
	    $this->trace('fermeture des connexions');
    	mysql_close($linkSource);
    	mysql_close($linkCible);    	    	

    	$this->trace('fin de la migration des références');

    	return $this->echoTrace;
    }
    
    /**
	* migre un lieu d'une base source vers une base de destination 
	* 
	* ATTENTION le lieu source et ses enfant écrasent les information de destination
	* 
    * @param int $idLieuSrc
    * @param int $idLieuDst
    * 
    */
	public function migreLieu($idLieuSrc, $idLieuDst, $idExi){

	try {

		$this->idExi = $idExi;
		
		//création des gestionnaires de bases    			
		$this->arrDbSrc['lieu'] = new Models_DbTable_Gevu_lieux($this->dbSrc);		    	

		$this->arrDbDst['diag'] = new Models_DbTable_Gevu_diagnostics($this->dbDst);		
		$this->arrDbDst['lieu'] = new Models_DbTable_Gevu_lieux($this->dbDst);		
		$this->arrDbDst['batiment'] = new Models_DbTable_Gevu_batiments($this->dbDst);
		$this->arrDbDst['geo'] = new Models_DbTable_Gevu_geos($this->dbDst);
		$this->arrDbDst['doc'] = new Models_DbTable_Gevu_docsxlieux($this->dbDst);
		$this->arrDbDst['espace'] = new Models_DbTable_Gevu_espaces($this->dbDst);
		$this->arrDbDst['espace_ext'] = new Models_DbTable_Gevu_espacesxexterieurs($this->dbDst);
		$this->arrDbDst['espace_int'] = new Models_DbTable_Gevu_espacesxinterieurs($this->dbDst);
		$this->arrDbDst['etablissement'] = new Models_DbTable_Gevu_etablissements($this->dbDst);
		$this->arrDbDst['niveau'] = new Models_DbTable_Gevu_niveaux($this->dbDst);
		$this->arrDbDst['objet_ext'] = new Models_DbTable_Gevu_objetsxexterieurs($this->dbDst);
		$this->arrDbDst['objet_int'] = new Models_DbTable_Gevu_objetsxinterieurs($this->dbDst);
		$this->arrDbDst['objet_voirie'] = new Models_DbTable_Gevu_objetsxvoiries($this->dbDst);
		$this->arrDbDst['parcelle'] = new Models_DbTable_Gevu_parcelles($this->dbDst);
		$this->arrDbDst['probleme'] = new Models_DbTable_Gevu_problemes($this->dbDst);
		$this->arrDbDst['antenne'] = new Models_DbTable_Gevu_antennes($this->dbDst);
		$this->arrDbDst['groupe'] = new Models_DbTable_Gevu_groupes($this->dbDst);
		$this->arrDbDst['logement'] = new Models_DbTable_Gevu_logements($this->dbDst);
		$this->arrDbDst['local'] = new Models_DbTable_Gevu_locaux($this->dbDst);
		$this->arrDbDst['part_commu'] = new Models_DbTable_Gevu_partiescommunes($this->dbDst);
		$this->arrDbDst['lieux_interventions'] = new Models_DbTable_Gevu_lieuxinterventions($this->dbDst);
		$this->arrDbDst['chaines_deplacements'] = new Models_DbTable_Gevu_chainesdeplacements($this->dbDst);
		$this->arrDbDst['lieux_chaines_deplacements'] = new Models_DbTable_Gevu_lieuxchainedeplacements($this->dbDst);
		
		
		
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->dbDst);
		
		//création d'un nouvel instant
		$c = str_replace("::", "_", __METHOD__).$this->idBaseSrc."_".$idLieuSrc."_".$this->idBaseDst."_".$idLieuDst; 
		$this->idInst = $this->dbI->ajouter(array("id_exi"=>$this->idExi,"nom"=>$c));
		

		//migre les enfants du lieu
		$this->migreLieuEnfant($idLieuSrc, $idLieuDst, 0);

		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
		}		
	}
	
	
	/**
	 * migre l'ensemble des solutions d'une base source vers une base de destination
	 *
	 *
	 * @param int $idExi
	 *
	 */
	public function migreSolutions($idExi){
	
		try {
	
			$this->idExi = $idExi;
	
			//création des gestionnaires de bases	
			$DdbS = new Models_DbTable_Gevu_solutions($this->dbDst);
			$DdbSCout = new Models_DbTable_Gevu_solutionsxcouts($this->dbDst);
			$DdbSCrit = new Models_DbTable_Gevu_solutionsxcriteres($this->dbDst);
			$DdbSMet = new Models_DbTable_Gevu_solutionsxmetiers($this->dbDst);
			$DbSProd = new Models_DbTable_Gevu_solutionsxproduits($this->dbDst);
			$DdbC = new Models_DbTable_Gevu_couts($this->dbDst);
			$DdbD = new Models_DbTable_Gevu_docs($this->dbDst);
			$DdbDP = new Models_DbTable_Gevu_docsxproduits($this->dbDst);
			$DdbDS = new Models_DbTable_Gevu_docsxsolutions($this->dbDst);
			$DdbP = new Models_DbTable_Gevu_produits($this->dbDst);
			$DdbPC = new Models_DbTable_Gevu_produitsxcouts($this->dbDst);
			$DdbE = new Models_DbTable_Gevu_entreprises($this->dbDst);
			$DdbM = new Models_DbTable_Gevu_metiers($this->dbDst);
				
			$SdbS = new Models_DbTable_Gevu_solutions($this->dbSrc);
			$SdbSCout = new Models_DbTable_Gevu_solutionsxcouts($this->dbSrc);
			$SdbSCrit = new Models_DbTable_Gevu_solutionsxcriteres($this->dbSrc);
			$SdbSMet = new Models_DbTable_Gevu_solutionsxmetiers($this->dbSrc);
			$SbSProd = new Models_DbTable_Gevu_solutionsxproduits($this->dbSrc);
			$SdbC = new Models_DbTable_Gevu_couts($this->dbSrc);
			$SdbD = new Models_DbTable_Gevu_docs($this->dbSrc);
			$SdbDP = new Models_DbTable_Gevu_docsxproduits($this->dbSrc);
			$SdbDS = new Models_DbTable_Gevu_docsxsolutions($this->dbSrc);
			$SdbP = new Models_DbTable_Gevu_produits($this->dbSrc);
			$SdbPC = new Models_DbTable_Gevu_produitsxcouts($this->dbSrc);
			$SdbE = new Models_DbTable_Gevu_entreprises($this->dbSrc);
			$SdbM = new Models_DbTable_Gevu_metiers($this->dbSrc);
				
			if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->dbDst);
	
			//création d'un nouvel instant
			$c = str_replace("::", "_", __METHOD__).$this->idBaseSrc."_".$idLieuSrc."_".$this->idBaseDst;
			$this->idInst = $this->dbI->ajouter(array("id_exi"=>$this->idExi,"nom"=>$c));
	
			//migre les solutions
			$arr = $SdbS->getAll();
			$arrSIds = array();
			foreach ($arr as $r) {
				//création d'une nouvelle solution
				$idO = $r["id_solution"];
				unset($r["id_solution"]);
				unset($r["LibTypeSolution"]);
				$idN = $DdbS->ajouter($r, false, false);
				$arrSIds[$idO]=$idN;				
			}
			//migre les couts
			$arr = $SdbC->getAll();
			$arrCIds = array();
			foreach ($arr as $r) {
				//création d'un nouveau cout
				$idO = $r["id_cout"];
				unset($r["id_cout"]);
				$r["id_instant"]=$this->idInst;
				$idN = $DdbC->ajouter($r);
				$arrCIds[$idO]=$idN;
			}
			//migre les entreprises
			$arr = $SdbE->getAll();
			$arrEIds = array();
			foreach ($arr as $r) {
				//création d'une nouvelle entreprise
				$idO = $r["id_entreprise"];
				unset($r["id_entreprise"]);
				$idN = $DdbE->ajouter($r);
				$arrEIds[$idO]=$idN;
			}
			//migre les produits
			$arr = $SdbP->getAll();
			$arrPIds = array();
			foreach ($arr as $r) {
				//création d'un nouveau produit
				$idO = $r["id_produit"];
				unset($r["id_produit"]);
				unset($r["LibEntreprise"]);
				$r["id_entreprise"]=$arrEIds[$r["id_entreprise"]];
				$idN = $DdbP->ajouter($r,false,false);
				$arrPIds[$idO]=$idN;
			}
			//migre les metiers
			$arr = $SdbM->getAll();
			$arrMIds = array();
			foreach ($arr as $r) {
				//création d'un nouveau métiers
				$idO = $r["id_metier"];
				unset($r["id_metier"]);
				$idN = $DdbM->ajouter($r);
				$arrMIds[$idO]=$idN;
			}
				
			//création des données liées
			$arr = $SdbSCout->getAll();
			foreach ($arr as $r) {
				if($arrCIds[$r["id_cout"]]){
					$DdbSCout->ajouter(array("id_solution"=>$arrSIds[$r["id_solution"]],"id_cout"=>$arrCIds[$r["id_cout"]]),false);
				}
			}
			$arr = $SdbSCrit->getAll();
			foreach ($arr as $r) {
				$DdbSCrit->ajouter($arrSIds[$r["id_solution"]],$r["id_critere"],false);
			}
			$arr = $SdbSMet->getAll();
			foreach ($arr as $r) {
				$DdbSMet->ajouter($arrSIds[$r["id_solution"]],$arrMIds[$r["id_metier"]],false);
			}
			$arr = $SbSProd->getAll();
			foreach ($arr as $r) {
				if($arrPIds[$r["id_produit"]]){
					$DbSProd->ajouter($arrSIds[$r["id_solution"]],$arrPIds[$r["id_produit"]],false);
				}
			}
			$arr = $SdbPC->getAll();
			foreach ($arr as $r) {
				if($arrCIds[$r["id_cout"]] && $arrPIds[$r["id_produit"]]){
					$DdbPC->ajouter(array("id_cout"=>$arrCIds[$r["id_cout"]],"id_produit"=>$arrPIds[$r["id_produit"]]),false);
				}
			}
			
			//migre les documents des produits
			$arr = $SdbDP->getAll();
			foreach ($arr as $r) {
				if($arrPIds[$r["id_produit"]]){				
					//récupération des données du document
					$arrD = $SdbD->findByIdDoc($r['id_doc']);
					$idO = $r["id_doc"];
					unset($arrD["id_doc"]);
					$arrD["id_instant"]=$this->idInst;
					$idN = $DdbD->ajouter($arrD,false);
					$DdbDP->ajouter(array("id_doc"=>$idN,"id_produit"=>$arrPIds[$r["id_produit"]]));
				}
			}
			
			//migre les documents des solutions
			$arr = $SdbDS->getAll();
			foreach ($arr as $r) {
				if($arrSIds[$r["id_solution"]]){				
					//récupération des données du document
					$arrD = $SdbD->findByIdDoc($r['id_doc']);
					$idO = $r["id_doc"];
					unset($arrD["id_doc"]);
					$arrD["id_instant"]=$this->idInst;
					$idN = $DdbD->ajouter($arrD,false);
					$DdbDS->ajouter(array("id_doc"=>$idN,"id_solution"=>$arrSIds[$r["id_solution"]]));
				}
			}
				
	
		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
		}
	}	
	
	function migreLieuEnfant($idLieuSrc, $idLieuDst, $i){

		try {
		
		//récupère les type de données liées pour la source
		$rdSrc = $this->arrDbSrc['lieu']->getTypeRelatedData($idLieuSrc);
		
		//boucle sur les donées liées
		foreach ($rdSrc as $k => $v) {
			if($v>0){
				if($k == 'id_lieu'){
					$data = $this->arrDbSrc['lieu']->findById_lieu($v);
					$data[0]["id_instant"]= $this->idInst;
					$data[0]["lieu_parent"]= $idLieuDst;
					unset($data[0]["id_lieu"]);
					//ajoute un lieu au parent
					$newIdLieuDst = $this->arrDbDst['lieu']->ajouter($data[0], false);
				}else{
					$this->migre(array("id_lieu"=>$newIdLieuDst, "id_instant"=>$this->idInst), $this->arrDbDst[$k], $idLieuSrc);
				}
			}
		}

		//récupère les enfants du lieu source
		$enfsSrc = $this->arrDbSrc['lieu']->findByLieu_parent($idLieuSrc);
		
		//ajoute les enfants et les informations liées
		$i++;
		foreach ($enfsSrc as $enf) {
			$this->migreLieuEnfant($enf['id_lieu'], $newIdLieuDst, $i);
		}		
		
		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
		}
	}

	/**
	 * migre les données d'une base source vers une autre
	 *
	 * @param array $data
	 * @param Zend_Db_Table_Abstract $db
	 *
	 * @return integer
	 */
	public function migre($data, $db, $id, $keyName = "id_lieu")
	{
		try {
		
		$fieldSelect = ' ';
		$fieldInsert = ' (';
		$fields = $db->info(Zend_Db_Table_Abstract::COLS);
		$clef = $db->info(Zend_Db_Table_Abstract::PRIMARY);
		$clef = $clef[1];
		$tableName = $db->info(Zend_Db_Table_Abstract::NAME);
				
		$first = true;
		foreach($fields as $fieldKey => $field)
		{
			//suprime la clef unique
			if($field!=$clef){
				if(!$first){
					$fieldSelect .= ', ';
					$fieldInsert .= ', ';
				}
				$fieldInsert .= $field;
				if(isset($data[$field])){
					$fieldSelect .= $data[$field];
				}else{
					$fieldSelect .= $field;
				}
				$first = false;
			}
		}
		$fieldInsert .= ') ';
		$query  = "INSERT INTO ".$this->idBaseDst.".".$tableName.$fieldInsert
			." SELECT ".$fieldSelect." FROM ".$this->idBaseSrc.".".$tableName
			." WHERE ".$this->idBaseSrc.".".$tableName.".".$keyName."=".$id;

		$adpt = $db->getAdapter();
		$result = $adpt->query($query);
		echo $this->idBaseSrc." -> ".$this->idBaseDst.$tableName." : ".$result->rowCount()."<br/>";
		
		return $adpt->lastInsertId();
		
		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
		}
		
	}	
	function getRef($i, $db, $r, $idDst){
		//recherche la référence si le lieu est la cible de destination
		if($i==0){
			$rDst = $db->findById_lieu($idDst);
			$ref = $rDst[0]['ref'];
		}elseif($r["ref"]=="")$ref = "mig_".$this->idInst."_".$r["id_lieu"];
		else $ref = $r["ref"];
		
		return $ref;
	}

	function getLieuDst($i, $dbSrc, $idSrc, $dbDst, $idDst, $libDst){
		//récupère les infos de la source
		$rs = $dbSrc->findById_lieu($idSrc);
		//calcule la référence
		$ref = $this->getRef($i,$dbDst,$rs[0],$idDst);
		//récherche la référence dans la destination
		$rDst = $dbDst->getByRef($ref, $this->idInst, $idDst, $libDst, $rs[0]);				    													
				
		return $rDst["id_lieu"];
	}
	
}

