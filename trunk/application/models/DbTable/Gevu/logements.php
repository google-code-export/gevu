<?php
/**
 * Ce fichier contient la classe Gevu_logement.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_logement'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_logements extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_logements';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_logement';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_logements existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_logement'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_logement; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_logements.
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
     * Récupère ou Ajoute une entrée avec le lieu associé.
     *
     * @param string $ref
     * @param int $idInst
     * @param int $idLieuParent
     * @param string $lib
     * @param array $data
     *  
     * @return integer
     */
    public function getByRef($ref, $idInst, $idLieuParent, $lib="", $data=false)
    {    	
		//vérification de l'existence de l'antenne
	    $arr = $this->findByRef($ref);
	    if(count($arr)==0){
	    	if($lib=="")$lib="Logement - ".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, false, $lib, true, false, array("id_type_controle"=>62));
	    	$data["id_lieu"] = $idLieu;
	    	$data["id_instant"] = $idInst;
	    	$data["ref"] = $ref;
	    	unset($data["id_logement"]);	
	    	$this->ajouter($data);
	    	$arr = $this->findByRef($ref);
	    }
    	return $arr[0];
    }     

    /**
     * Renvoie les stats pour le type de logement.
     *  
     * @return array
     */
    public function getStatType()
    {    	
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('l' => 'gevu_logements'))
            ->joinInner(array('s' => 'gevu_stats'),
            	's.id_lieu = l.id_lieu',array('ref','handicateur_moteur','handicateur_auditif','handicateur_visuel','handicateur_cognitif','criteres'))
        	->order(array('l.id_lieu'));
        
        $result = $this->fetchAll($query);
        return $result->toArray(); 
    }     
    
    /**
     * Recherche une entrée Gevu_logements avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_logements.id_logement = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_logement avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_logements.id_logement = ' . $id);
    }

    /**
     * Recherche les entrées  avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
        $this->delete('id_lieu = ' . $idLieu);
    }
    
    /**
     * Récupère toutes les entrées Gevu_logement avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_logements" => "gevu_logement") );
                    
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
     * Recherche une entrée Gevu_logements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_logement
     *
     * @return array
     */
    public function findById_logement($id_logement)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.id_logement = ?", $id_logement );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     *
     * @return array
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $num_porte
     *
     * @return array
     */
    public function findByNum_porte($num_porte)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.num_porte = ?", $num_porte );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type
     *
     * @return array
     */
    public function findById_type($id_type)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logements") )                           
                    ->where( "g.id_type = ?", $id_type );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_logement avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     *
     * @return array
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_logement") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
