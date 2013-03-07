<?php
/**
 * Ce fichier contient la classe Gevu_locaux.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_locaux'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_locaux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_locaux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_local';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_locaux existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_local'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_local; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_locaux.
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
	    	if($lib=="")$lib="Local - ".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, false, $lib, true, false);
	    	$data["id_lieu"] = $idLieu;
	    	$data["id_instant"] = $idInst;
	    	$data["ref"] = $ref;
	    	$this->ajouter($data);
	    	$arr = $this->findByRef($ref);
	    }
    	return $arr[0];
    } 
    
    /**
     * Recherche une entrée Gevu_locaux avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_locaux.id_local = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_locaux avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_locaux.id_local = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_locaux avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_locaux avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_locaux" => "gevu_locaux") );
                    
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
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_local
     *
     * @return array
     */
    public function findById_local($id_local)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.id_local = ?", $id_local );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     *
     * @return array
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $activite
     *
     * @return array
     */
    public function findByActivite($activite)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.activite = ?", $activite );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_locaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     *
     * @return array
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_locaux") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
