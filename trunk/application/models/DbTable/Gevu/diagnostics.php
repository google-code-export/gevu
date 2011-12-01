<?php
/**
 * Ce fichier contient la classe Gevu_diagnostics.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_diagnostics'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_diagnostics extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_diagnostics';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_diag';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_diagnostics existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_diag'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_diag; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_diagnostics.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_diagnostics avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_diagnostics" => "gevu_diagnostics") );
                    
        if($order != null)
        {
            $query->order($order);
        }

        if($limit != 0)
        {
            $query->limit($limit, $from);
        }

        return $this->fetchAll($query)->toArray();
    }

    /*
     * Recherche la description totale d'un diagnostic à partir d'un lieu
     * et retourne ces entrées.
     *
     * @param int $idLieu
     * @return array
     */
    public function getAllDesc($idLieu)
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('diag' => 'gevu_diagnostics'))
            ->joinInner(array('crit' => 'gevu_criteres'),
            	'diag.id_critere = crit.id_critere',array('ref','handicateur_moteur','handicateur_auditif','handicateur_visuel','handicateur_cognitif','criteres'))
        	->joinInner(array('tc' => 'gevu_typesxcontroles'),
            	'tc.id_type_controle = crit.id_type_controle',array('controle'=>'lib','icone'))
        	->joinInner(array('mc' => 'gevu_motsclefs'),
            	'diag.id_reponse = mc.id_motclef',array('reponse'=>'titre'))
        	/*
        	->joinInner(array('exi' => 'gevu_exis'),
            	'inst.id_exi = exi.id_exi',array('exis'=>'nom'))
        	->joinInner(array('inst' => 'gevu_instants'),
            	'diag.id_instant = inst.id_instant',array('instant'=>"DATE_FORMAT(maintenant,'%W %d %M %Y')",'ici','nom'))
        	->joinLeft(array('prob' => 'gevu_problemes'),
            	'diag.id_lieu = prob.id_lieu AND diag.id_instant = prob.id_instant AND diag.id_critere = prob.id_critere',array('id_probleme','num_marker','mesure','observations','fichier','doc'))
        	->joinLeft(array('obs' => 'gevu_observations'),
            	'diag.id_lieu = obs.id_lieu AND diag.id_instant = obs.id_instant AND diag.id_critere = obs.id_critere',array('id_observations','id_reponse','num_marker','lib'))
        	*/
        	->where( "diag.id_lieu = ?", $idLieu)
			->group(array('id_instant','id_critere'))
        	->order(array('id_instant','id_critere'));        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }

    /*
     * Recherche les différentes campagen de diagnostis à partir d'un lieu
     * et retourne ces entrées.
     *
     * @param int $idLieu
     * @return array
     */
    public function getCampagnes($idLieu)
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('diag' => 'gevu_diagnostics'),array('id_instant','id_lieu','nbReponse'=>'COUNT(diag.id_reponse)'))
            ->joinInner(array('crit' => 'gevu_criteres'),
            	'diag.id_critere = crit.id_critere',array('id_type_controle'))
        	->joinInner(array('inst' => 'gevu_instants'),
            	'diag.id_instant = inst.id_instant',array('instant'=>"DATE_FORMAT(maintenant,'%W %d %M %Y')",'ici','nom','commentaires'))
        	->joinInner(array('tc' => 'gevu_typesxcontroles'),
            	'tc.id_type_controle = crit.id_type_controle',array('controle'=>'lib','icone'))
        	->joinInner(array('exi' => 'gevu_exis'),
            	'inst.id_exi = exi.id_exi',array('exis'=>'nom'))
        	->joinLeft(array('prob' => 'gevu_problemes'),
            	'diag.id_lieu = prob.id_lieu AND diag.id_instant = prob.id_instant AND diag.id_critere = prob.id_critere',array('nbProbleme'=>'COUNT(id_probleme)'))
        	->joinLeft(array('obs' => 'gevu_observations'),
            	'diag.id_lieu = obs.id_lieu AND diag.id_instant = obs.id_instant AND diag.id_critere = obs.id_critere',array('nbObservation'=>'COUNT(id_observations)'))
        	->where( "diag.id_lieu = ?", $idLieu)
			->group(array('id_instant','id_type_controle'))
        	->order(array('id_instant','id_type_controle'));        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }
    
    /*
     * récupère la liste des réponses
     * et retourne un tableau.
     *
    * @return array
     */
    public function getReponses()
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('diag' => 'gevu_diagnostics'))
        	->joinInner(array('crit' => 'gevu_criteres'),
            	'diag.id_critere = crit.id_critere',array('ref','handicateur_moteur','handicateur_auditif','handicateur_visuel','handicateur_cognitif','criteres'))
        	->joinInner(array('mc' => 'gevu_motsclefs'),
            	'diag.id_reponse = mc.id_motclef',array('reponse'=>'titre'))
        	->joinInner(array('l' => 'gevu_lieux'),
            	'l.id_lieu = diag.id_lieu',array('lib','id_lieu'))
        	->order(array('diag.id_instant','l.id_lieu'));        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }    
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diag
     */
    public function findById_diag($id_diag)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_diag = ?", $id_diag );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse
     */
    public function findById_reponse($id_reponse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_reponse = ?", $id_reponse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche des entrées Gevu_diagnostics pour un utilisateur
     * et retourne ces entrées.
     *
     * @param int $idExi
     */
    public function findByExi($idExi)
    {
        $query = $this->select()
                ->from( array("g" => "gevu_diagnostics") )                           
        		->joinInner(array('i' => 'gevu_instant'),'i.id_instant = g.id_instant')
                ->where( "i.id_exi = ?", $idExi);
                    
        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Recherche les instants lié aux diagnostics
     * et retourne cette entrée.
     *
     */
    public function findInstants()
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('d' => 'gevu_diagnostics'),array('nbDiag'=>'COUNT(d.id_diag)'))                      
        	->joinInner(array('i' => 'gevu_instants'),
            	'd.id_instant = i.id_instant',array('instant'=>"DATE_FORMAT(maintenant,'%d %M %Y')",'id_instant','ici','nom','commentaires'))
        	->joinInner(array('e' => 'gevu_exis'),
            	'i.id_exi = e.id_exi',array('exis'=>'nom'))
       		->group("d.id_instant");
    	
        return $this->fetchAll($query)->toArray(); 
    }

    /*
     * Recherche le dernier diagnostics pour un lieu
     * et retourne cette entrée.
     * 
     * @param int $idLieu
     *
     */
    public function findLastDiagForLieu($idLieu)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('d' => 'gevu_diagnostics'),array('id_lieu'))                      
        	->joinInner(array('i' => 'gevu_instants'),
            	'd.id_instant = i.id_instant',array('lastinstant'=>"MAX(i.id_instant)"))
            ->joinInner(array('le' => 'gevu_lieux'),
                'le.id_lieu = d.id_lieu',array('lib'))
            ->joinInner(array('l' => 'gevu_lieux'),
                'le.lft BETWEEN l.lft AND l.rgt',array('lib'))
            ->where('l.id_lieu = ? ',$idLieu)
       		->group("d.id_lieu");
        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Marque les derniers diagnostics pour un lieu
     * 
     * @param int $idLieu
     *
     */
    public function setLastDiagForLieu($idLieu)
    {
    	//récupère les identifiants de lieu
    	$sql = "SELECT `l`.`id_lieu`, GROUP_CONCAT(DISTINCT `d`.`id_lieu`) ids
			FROM `gevu_diagnostics` AS `d`
				INNER JOIN `gevu_lieux` AS `le` ON le.id_lieu = d.id_lieu
				INNER JOIN `gevu_lieux` AS `l` ON le.lft BETWEEN l.lft AND l.rgt 
			WHERE (l.id_lieu = ".$idLieu.")
			GROUP BY `l`.`id_lieu`";
		$db = $this->getAdapter()->query($sql);
        $arr = $db->fetchAll();
        //vérifie la fin de la chaine
        $ids = $arr[0]['ids'];
        if(substr($ids,-1)==",")$ids.=-1;
        
       	//initialise le tag des lieux    	
    	$where = $this->getAdapter()->quoteInto('id_lieu IN ('.$ids.')');
    	$query = $this->update(array('last'=>0), $where);
    	
    	//récupère les identifiants des derniers diagnostics
    	$sql = "SELECT GROUP_CONCAT(sd.id_diag) ids 
			FROM gevu_diagnostics sd,
			(
			SELECT `d`.`id_lieu`, MAX(i.id_instant) AS `lastinstant`
			FROM `gevu_diagnostics` AS `d`
				INNER JOIN `gevu_instants` AS `i` ON d.id_instant = i.id_instant
				INNER JOIN `gevu_lieux` AS `le` ON le.id_lieu = d.id_lieu
				INNER JOIN `gevu_lieux` AS `l` ON le.lft BETWEEN l.lft AND l.rgt WHERE (l.id_lieu = ".$idLieu.") GROUP BY `d`.`id_lieu`) ssd
			WHERE sd.id_instant = ssd.lastinstant AND sd.id_lieu = ssd.id_lieu";
		$db = $this->getAdapter()->query($sql);
        $arr = $db->fetchAll();
        //vérifie la fin de la chaine
        $ids = $arr[0]['ids'];
        if(substr($ids,-1)==",")$ids.=-1;
        
       	//met à jour le tag qui indique les derniers    	
    	$where = $this->getAdapter()->quoteInto('id_lieu IN ('.$ids.')');
    	$query = $this->update(array('last'=>1), $where);
        
    }
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne les reponses aux diagnostics correspondant à la valeur demandée.
     *
     * @param integer $idLieu
     * @param integer $idReponse
     * @return array
     */
    public function getDiagReponse($idLieu, $idInstant, $idReponse="")
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('d' => 'gevu_diagnostics'),array('nbDiag'=>'COUNT(d.id_diag)'))
            ->joinInner(array('le' => 'gevu_lieux'),
                'le.id_lieu = d.id_lieu',array('nbControle'=>'COUNT(DISTINCT le.id_lieu)'))
            ->joinInner(array('l' => 'gevu_lieux'),
                'le.lft BETWEEN l.lft AND l.rgt',array('lib', 'id_lieu'))
            
            ->joinLeft(array('c1_0' => 'gevu_criteres'),
                'c1_0.id_critere = d.id_critere  AND c1_0.handicateur_auditif = 0',array('auditif_0'=>'COUNT(c1_0.id_critere)'))
            ->joinLeft(array('c2_0' => 'gevu_criteres'),
                'c2_0.id_critere = d.id_critere  AND c2_0.handicateur_cognitif = 0',array('cognitif_0'=>'COUNT(c2_0.id_critere)'))
            ->joinLeft(array('c3_0' => 'gevu_criteres'),
                'c3_0.id_critere = d.id_critere  AND c3_0.handicateur_moteur = 0',array('moteur_0'=>'COUNT(c3_0.id_critere)'))
            ->joinLeft(array('c4_0' => 'gevu_criteres'),
                'c4_0.id_critere = d.id_critere  AND c4_0.handicateur_visuel = 0',array('visuel_0'=>'COUNT(c4_0.id_critere)'))

            ->joinLeft(array('c1_1' => 'gevu_criteres'),
                'c1_1.id_critere = d.id_critere  AND c1_1.handicateur_auditif = 1',array('auditif_1'=>'COUNT(c1_1.id_critere)'))
            ->joinLeft(array('c2_1' => 'gevu_criteres'),
                'c2_1.id_critere = d.id_critere  AND c2_1.handicateur_cognitif = 1',array('cognitif_1'=>'COUNT(c2_1.id_critere)'))
            ->joinLeft(array('c3_1' => 'gevu_criteres'),
                'c3_1.id_critere = d.id_critere  AND c3_1.handicateur_moteur = 1',array('moteur_1'=>'COUNT(c3_1.id_critere)'))
            ->joinLeft(array('c4_1' => 'gevu_criteres'),
                'c4_1.id_critere = d.id_critere  AND c4_1.handicateur_visuel = 1',array('visuel_1'=>'COUNT(c4_1.id_critere)'))

            ->joinLeft(array('c1_2' => 'gevu_criteres'),
                'c1_2.id_critere = d.id_critere  AND c1_2.handicateur_auditif = 2',array('auditif_2'=>'COUNT(c1_2.id_critere)'))
            ->joinLeft(array('c2_2' => 'gevu_criteres'),
                'c2_2.id_critere = d.id_critere  AND c2_2.handicateur_cognitif = 2',array('cognitif_2'=>'COUNT(c2_2.id_critere)'))
            ->joinLeft(array('c3_2' => 'gevu_criteres'),
                'c3_2.id_critere = d.id_critere  AND c3_2.handicateur_moteur = 2',array('moteur_2'=>'COUNT(c3_2.id_critere)'))
            ->joinLeft(array('c4_2' => 'gevu_criteres'),
                'c4_2.id_critere = d.id_critere  AND c4_2.handicateur_visuel = 2',array('visuel_2'=>'COUNT(c4_2.id_critere)'))
            
            ->joinLeft(array('c1_3' => 'gevu_criteres'),
                'c1_3.id_critere = d.id_critere  AND c1_3.handicateur_auditif = 3',array('auditif_3'=>'COUNT(c1_3.id_critere)'))
            ->joinLeft(array('c2_3' => 'gevu_criteres'),
                'c2_3.id_critere = d.id_critere  AND c2_3.handicateur_cognitif = 3',array('cognitif_3'=>'COUNT(c2_3.id_critere)'))
            ->joinLeft(array('c3_3' => 'gevu_criteres'),
                'c3_3.id_critere = d.id_critere  AND c3_3.handicateur_moteur = 3',array('moteur_3'=>'COUNT(c3_3.id_critere)'))
            ->joinLeft(array('c4_3' => 'gevu_criteres'),
                'c4_3.id_critere = d.id_critere  AND c4_3.handicateur_visuel = 3',array('visuel_3'=>'COUNT(c4_3.id_critere)'))
            
            ->where( "l.id_lieu = ?", $idLieu);

        if ($idInstant!=-1){
            $query->where( "d.id_instant = ?", $idInstant);
        }else{
            $query->where( "d.last = ?", 1);
        }        
            
        if ($idReponse!=""){
        	$query->where("d.id_reponse = ?", $idReponse);
        }        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }    
}
