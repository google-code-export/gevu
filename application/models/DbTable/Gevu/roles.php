<?php
/**
 * Ce fichier contient la classe Gevu_roles.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_roles'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_roles extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_roles';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_role';

    
    /**
     * Vérifie si une entrée Gevu_roles existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_role'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_role; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_roles.
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
     * Recherche une entrée Gevu_roles avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_roles.id_role = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_roles avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_roles.id_role = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_roles avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_roles" => "gevu_roles") );
                    
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
     * Récupère les spécifications des colonnes Gevu_roles 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_role","champ"=>"id_role","visible"=>true),
    	array("titre"=>"lib","champ"=>"lib","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_roles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_role
     */
    public function findById_role($id_role)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_roles") )                           
                    ->where( "g.id_role = ?", $id_role );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_roles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_roles") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
