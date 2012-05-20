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

		$this->idExi = $idExi;
		
		//création des gestionnaires de bases    			
		$this->arrDbSrc['diagnostic'] = new Models_DbTable_Gevu_diagnostics($this->dbSrc);		
		$this->arrDbSrc['lieu'] = new Models_DbTable_Gevu_lieux($this->dbSrc);		    	
		$this->arrDbSrc['batiment'] = new Models_DbTable_Gevu_batiments($this->dbSrc);
		$this->arrDbSrc['geo'] = new Models_DbTable_Gevu_geos($this->dbSrc);
		$this->arrDbSrc['antenne'] = new Models_DbTable_Gevu_diagnostics($this->dbSrc);
		$this->arrDbSrc['doc'] = new Models_DbTable_Gevu_docsxlieux($this->dbSrc);
		$this->arrDbSrc['espace'] = new Models_DbTable_Gevu_espaces($this->dbSrc);
		$this->arrDbSrc['espace_ext'] = new Models_DbTable_Gevu_espacesxexterieurs($this->dbSrc);
		$this->arrDbSrc['espace_int'] = new Models_DbTable_Gevu_espacesxinterieurs($this->dbSrc);
		$this->arrDbSrc['etablissement'] = new Models_DbTable_Gevu_etablissements($this->dbSrc);
		$this->arrDbSrc['niveau'] = new Models_DbTable_Gevu_niveaux($this->dbSrc);
		$this->arrDbSrc['objet_ext'] = new Models_DbTable_Gevu_objetsxexterieurs($this->dbSrc);
		$this->arrDbSrc['objet_int'] = new Models_DbTable_Gevu_objetsxinterieurs($this->dbSrc);
		$this->arrDbSrc['objet_voirie'] = new Models_DbTable_Gevu_objetsxvoiries($this->dbSrc);
		$this->arrDbSrc['parcelle'] = new Models_DbTable_Gevu_parcelles($this->dbSrc);
		$this->arrDbSrc['probleme'] = new Models_DbTable_Gevu_problemes($this->dbSrc);
		$this->arrDbSrc['antenne'] = new Models_DbTable_Gevu_antennes($this->dbSrc);
		$this->arrDbSrc['groupe'] = new Models_DbTable_Gevu_groupes($this->dbSrc);
		$this->arrDbSrc['logement'] = new Models_DbTable_Gevu_logements($this->dbSrc);
		$this->arrDbSrc['local'] = new Models_DbTable_Gevu_locaux($this->dbSrc);
		$this->arrDbSrc['part_commu'] = new Models_DbTable_Gevu_partiescommunes($this->dbSrc);
		
		$this->diag = new GEVU_Diagnostique($this->dbDst);
		$this->arrDbDst['diagnostic'] = new Models_DbTable_Gevu_diagnostics($this->dbDst);		
		$this->arrDbDst['lieu'] = new Models_DbTable_Gevu_lieux($this->dbDst);		
		$this->arrDbDst['batiment'] = new Models_DbTable_Gevu_batiments($this->dbDst);
		$this->arrDbDst['geo'] = new Models_DbTable_Gevu_geos($this->dbDst);
		$this->arrDbDst['antenne'] = new Models_DbTable_Gevu_diagnostics($this->dbDst);
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
		
		
		
		if(!$this->dbI)$this->dbI = new Models_DbTable_Gevu_instants($this->dbDst);
		
		//création d'un nouvel instant
		$c = str_replace("::", "_", __METHOD__).$this->idBaseSrc."_".$idLieuSrc."_".$this->idBaseDst."_".$idLieuDst; 
		$this->idInst = $this->dbI->ajouter(array("id_exi"=>$this->idExi,"nom"=>$c));
		

		//migre les enfants du lieu
		$this->migreLieuEnfant($idLieuSrc, $idLieuDst, 0);
			
	}
	
	function migreLieuEnfant($idLieuSrc, $idLieuDst, $i){

		//récupère les type de données liées pour la source
		$rdSrc = $this->arrDbSrc['lieu']->getTypeRelatedData($idLieuSrc);
		
		//boucle sur les donées liées
		foreach ($rdSrc as $k => $v) {
			if($v>0){
				if($k != 'lib' && $k != 'id_lieu' && $k != 'lieu_parent' && $k != 'geo' && $k != 'diag' && $k != 'doc' && $k != 'probleme'){
					$newIdLieuDst = $this->getLieuDst($i, $this->arrDbSrc[$k], $rdSrc['id_lieu'], $this->arrDbDst[$k], $idLieuDst, $rdSrc['lib']);
				}
				if($k == 'diag'){
					//récupération du lieu de destinataion
		    		$newIdLieuDst = $this->diag->ajoutLieu($idLieuDst, $this->idExi, $this->idBaseDst, $rdSrc['lib'], true, false);				
					//récupère les infos de la source
					$rs = $this->arrDbSrc['diagnostic']->findById_lieu($rdSrc['id_lieu']);
					foreach ($rs as $r) {
						$r['id_lieu']=$newIdLieuDst;
						$r['id_instant']=$this->idInst;
						unset($r["id_diag"]);
						//ajoute le diag
						$this->arrDbDst['diagnostic']->ajouter($r,false);
					}														
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

