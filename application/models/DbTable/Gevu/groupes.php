<?php
/**
 * Ce fichier contient la classe Gevu_groupe.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_groupe'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_groupes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_groupes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_groupe';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_groupe existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_groupe'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_groupe; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_groupe.
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
     * Recherche une entrée Gevu_groupe avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {            	
    	$this->update($data, 'gevu_groupes.id_groupe = ' . $id);
    }
    
    /**
     * Recherche une entrée gevu_groupes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_groupes.id_groupe = ' . $id);
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
        $this->delete('id_lieu = ' . $idLieu);
    }
    
    /**
     * Récupère toutes les entrées gevu_groupes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
    	$query = $this->select()
                    ->from( array("gevu_groupes" => "gevu_groupes") );
                    
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
     * Recherche une entrée gevu_groupes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_groupe
     *
     * @return array
     */
    public function findById_groupe($id_groupe)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_groupes") )                           
                    ->where( "g.id_groupe = ?", $id_groupe );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée gevu_groupes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_groupes") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée gevu_groupes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_groupes") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée gevu_groupes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_groupes") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée gevu_groupes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $reponse1
     *
     * @return array
     */
    public function findByReponse1($reponse1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_groupes") )                           
                    ->where( "g.reponse_1 = ?", $reponse1);

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
