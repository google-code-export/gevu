<?php
/**
 * Ce fichier contient la classe Gevu_metiers.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_metiers'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_metiers extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_metiers';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_metier';

    /*
     * dépendance avec les autres tables.
     */
    protected $_dependentTables = array(
		'Model_DbTable_Gevu_metiersxsolutions'
		);
    
    /**
     * Vérifie si une entrée Gevu_solutions existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array("id_metier"));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]["id_metier"]; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_solutions.
     *
     * @param array $data
     * @param boolean $existe
     * 
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
    
    
    /**
     * Recherche une entrée Gevu_metiers avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_metiers.id_metier = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_metiers avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_metiers.id_metier = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_metiers avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_metiers") );
                    
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
     * Récupère les spécifications des colonnes Gevu_metiers 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
			array("titre"=>"id_metier","champ"=>"id_metier","visible"=>true),
			array("titre"=>"lib","champ"=>"lib","visible"=>true)
		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_metiers avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_metier
     */
    public function findById_metier($id_metier)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_metiers") )                           
                    ->where( "g.id_metier = " . $id_metier );

        return $this->fetchRow($query); 
    }
    /*
     * Recherche une entrée Gevu_metiers avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_metiers") )                           
                    ->where( "g.lib = " . $lib );

        return $this->fetchRow($query); 
    }
    
}
