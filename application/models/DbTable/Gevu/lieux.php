<?php
/**
 * Ce fichier contient la classe Gevu_lieux.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieux'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_lieux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_lieux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_lieu';

	protected $_dependentTables = array(
       "Models_DbTable_Gevu_batiments"
       ,"Models_DbTable_Gevu_diagnostics"
       ,"Models_DbTable_Gevu_diagnosticsxvoirie"
       ,"Models_DbTable_Gevu_docsxlieux"
       ,"Models_DbTable_Gevu_espaces"
       ,"Models_DbTable_Gevu_espacesxexterieurs"
       ,"Models_DbTable_Gevu_espacesxinterieurs"
       ,"Models_DbTable_Gevu_etablissements"
       ,"Models_DbTable_Gevu_georss"
       ,"Models_DbTable_Gevu_geos"
       ,"Models_DbTable_Gevu_niveaux"
       ,"Models_DbTable_Gevu_objetsxexterieurs"
       ,"Models_DbTable_Gevu_objetsxinterieurs"
       ,"Models_DbTable_Gevu_objetsxvoiries"
       ,"Models_DbTable_Gevu_observations"
       ,"Models_DbTable_Gevu_parcelles"
       ,"Models_DbTable_Gevu_problemes"
       );
    
    
    /**
     * Vérifie si une entrée Gevu_lieux existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_lieu'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_lieu; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_lieux.
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
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_lieux avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_lieux" => "gevu_lieux") );
                    
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

    
    /**
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
	 *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Compte le nombre d'enfant d'une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne ce nombre.
     *
     * @param int $id_lieu
     * 
     * @return integer
     */
    public function getNbEnfant($id_lieu)
    {
        $select = $this->select()
        	->from($this, array('count(*) as amount'))
            ->where( "lieu_parent = ?", $id_lieu );
        
        $rows = $this->fetchAll($select);
       
        return($rows[0]->amount);       
    }    
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_rubrique
     */
    public function findById_rubrique($id_rubrique)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_rubrique = ?", $id_rubrique );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lib LIKE '%".$lib."%'");

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_parent
     */
    public function findById_parent($id_parent)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_parent = ?", $id_parent );

        return $this->fetchAll($query)->toArray(); 
    }
        
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lieu_parent
     */
    public function findByLieu_parent($lieu_parent)
    {
        $query = $this->select()
			->from( array("g" => "gevu_lieux") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinLeft(array('diag' => 'gevu_diagnostics'),
                'diag.id_lieu = g.id_lieu',array('nbDiag'=>'COUNT(diag.id_diag)'))
			->group("id_lieu")
            ->where( "g.lieu_parent = ?", $lieu_parent );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lft
     */
    public function findByLft($lft)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lft = ?", $lft );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $rgt
     */
    public function findByRgt($rgt)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.rgt = ?", $rgt );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $niv
     */
    public function findByNiv($niv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.niv = ?", $niv );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }

     /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param integer $idLieu
     * @param string $order
     * @return array
     */
    public function getFullPath($idLieu, $order="lft")
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'))
            ->joinInner(array('parent' => 'gevu_lieux'),
                'node.lft BETWEEN parent.lft AND parent.rgt',array('lib', 'id_lieu'))
            ->where( "node.id_lieu = ?", $idLieu)
                        ->order("parent.".$order);        
                $result = $this->fetchAll($query);
        return $result->toArray(); 
    }

     /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne les reponses aux diagnostics correpsondant à la valeur demandée.
     *
     * @param integer $idLieu
     * @param integer $idReponse
     * @return array
     */
    public function getDiagReponse($idLieu, $idReponse="")
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
        if ($idReponse!=""){
        	$query->where("d.id_reponse = ?", $idReponse);
        }        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }
        
}
