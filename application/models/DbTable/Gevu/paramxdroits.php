<?php
/**
 * Ce fichier contient la classe Gevu_paramxdroits.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_paramxdroits'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_paramxdroits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_paramxdroits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_droit';

    
    /**
     * Vérifie si une entrée Gevu_paramxdroits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_droit'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_droit; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_paramxdroits.
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
     * Recherche une entrée Gevu_paramxdroits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_paramxdroits.id_droit = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_paramxdroits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_paramxdroits.id_droit = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_paramxdroits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_paramxdroits" => "gevu_paramxdroits") );
                    
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
     * Recherche une entrée Gevu_paramxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_droit
     */
    public function findByIdDroit($id_droit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramxdroits") )                           
                    ->where( "g.id_droit = ?", $id_droit );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_paramxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramxdroits") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
