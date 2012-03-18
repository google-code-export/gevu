<?php
/**
 * Ce fichier contient la classe Gevu_antenne.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_antenne'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_antennes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_antennes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_antenne';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_antenne existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_antenne'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_antenne; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_antenne.
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
     * Recherche une entrée Gevu_antenne avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {            	
    	$this->update($data, 'gevu_antennes.id_antenne = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_antenne avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$dbCA = new Models_DbTable_Gevu_contactsxantennes();
    	$dbCA->removeAntenne($id);    	
    	$this->delete('gevu_antennes.id_antenne = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_batiments avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
    	$arr = $this->findByIdLieu($idLieu);
    	foreach ($arr as $a) {
    		$this->remove($a['id_antenne']);
    	}
    }
    
    /**
     * Récupère toutes les entrées Gevu_antenne avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {    	
    	$query = $this->select()
                    ->from( array("gevu_antennes" => "gevu_antennes") );
                    
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
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_antenne
     *
     * @return array
     */
    public function findById_antenne($id_antenne)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_antenne = ?", $id_antenne );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idLieu
     *
     * @return array
     */
    public function findByIdLieu($idLieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_lieu = ?", $idLieu);

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     *
     * @return array
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
