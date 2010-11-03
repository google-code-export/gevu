<?php
/**
 * Ce fichier contient la classe Gevu_criteresxtypesxdeficience.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_criteresxtypesxdeficience'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_criteresxtypesxdeficience extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_criteresxtypesxdeficience';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_deficience';

    
    /**
     * Vérifie si une entrée Gevu_criteresxtypesxdeficience existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_deficience'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_deficience; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_criteresxtypesxdeficience.
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
     * Recherche une entrée Gevu_criteresxtypesxdeficience avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_criteresxtypesxdeficience.id_type_deficience = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_criteresxtypesxdeficience avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_criteresxtypesxdeficience.id_type_deficience = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_criteresxtypesxdeficience avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_criteresxtypesxdeficience" => "gevu_criteresxtypesxdeficience") );
                    
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
     * Récupère les spécifications des colonnes Gevu_criteresxtypesxdeficience 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_deficience","champ"=>"id_type_deficience","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_criteresxtypesxdeficience avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_deficience
     */
    public function findById_type_deficience($id_type_deficience)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteresxtypesxdeficience") )                           
                    ->where( "g.id_type_deficience = " . $id_type_deficience );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteresxtypesxdeficience avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteresxtypesxdeficience") )                           
                    ->where( "g.id_critere = " . $id_critere );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
