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
	public function migre($data, $db, $idLieu)
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
			." WHERE ".$this->idBaseSrc.".".$tableName.".id_lieu=".$idLieu;

		$result = $db->getAdapter()->query($query);
		echo $this->idBaseSrc." -> ".$this->idBaseDst.$tableName." : ".$result->rowCount()."<br/>";
		
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

