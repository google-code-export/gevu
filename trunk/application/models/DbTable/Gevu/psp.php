<?php
/**
 * Ce fichier contient la classe Gevu_psp.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Models_DbTable_Gevu_psp extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_psp';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_psp';
    
    /**
     * Vérifie si une entrée Gevu_psp existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_psp'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_psp; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_psp.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=false)
    {
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_psp avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_psp.id_psp = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_psp avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_psp.id_psp = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_psp avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_psp avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_psp" => "gevu_psp") );
                    
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
     * Recherche une entrée Gevu_psp avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_psp
     *
     * @return array
     */
    public function findById_psp($id_psp)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_psp") )                           
                    ->where( "g.id_psp = ?", $id_psp );

        return $this->fetchAll($query)->toArray(); 
    }

    	/**
     * Recherche une entrée Gevu_psp avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $where
     *
     * @return array
     */
    public function findByWhere($where)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_psp") )                           
                    ->where($where);

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
