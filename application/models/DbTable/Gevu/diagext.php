<?php
/**
 * Ce fichier contient la classe Gevu_diagext.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Models_DbTable_Gevu_diagext extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_diagext';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_diagext';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	

    /**
     * retourne une connexion à une base de donnée suivant son nom
     * @param string $idBase
     * @return Zend_Db_Adapter_Abstract
     */
    public function setDb($idBase){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
    		$arr = $db->getConfig();
    		$arr['dbname']=$idBase;
    		$db = Zend_Db::factory('PDO_MYSQL', $arr);
    	}
    	$this->_db = self::_setupAdapter($db);
    }
    
    
    /**
     * Vérifie si une entrée Gevu_diagext existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_diagext'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_diagext; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_diagext.
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
     * Recherche une entrée Gevu_diagext avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_diagext.id_diagext = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagext avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_diagext.id_diagext = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_diagext avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_diagext avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_diagext" => "gevu_diagext") );
                    
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
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diagext
     *
     * @return array
     */
    public function findById_diagext($id_diagext)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.id_diagext = ?", $id_diagext );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_entreprise
     *
     * @return array
     */
    public function findById_entreprise($id_entreprise)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.id_entreprise = ?", $id_entreprise );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     *
     * @return array
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $source
     *
     * @return array
     */
    public function findBySource($source)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.source = ?", $source );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $auditif
     *
     * @return array
     */
    public function findByAuditif($auditif)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.auditif = ?", $auditif );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $cognitif
     *
     * @return array
     */
    public function findByCognitif($cognitif)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.cognitif = ?", $cognitif );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $moteur
     *
     * @return array
     */
    public function findByMoteur($moteur)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.moteur = ?", $moteur );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagext avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $visuel
     *
     * @return array
     */
    public function findByVisuel($visuel)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagext") )                           
                    ->where( "g.visuel = ?", $visuel );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
