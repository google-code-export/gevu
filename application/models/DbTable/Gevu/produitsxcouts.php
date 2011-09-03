<?php
/**
 * Ce fichier contient la classe Gevu_produitsxcouts.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_produitsxcouts'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_produitsxcouts extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_produitsxcouts';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_produit';

    
    /**
     * Vérifie si une entrée Gevu_produitsxcouts existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_produit'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_produit; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_produitsxcouts.
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
     * Recherche une entrée Gevu_produitsxcouts avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_produitsxcouts.id_produit = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_produitsxcouts avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_produitsxcouts.id_produit = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_produitsxcouts avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_produitsxcouts" => "gevu_produitsxcouts") );
                    
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
     * Récupère les spécifications des colonnes Gevu_produitsxcouts 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_produit","champ"=>"id_produit","visible"=>true),
    	array("titre"=>"id_cout","champ"=>"id_cout","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_produitsxcouts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_produit
     */
    public function findById_produit($id_produit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produitsxcouts") )                           
                    ->where( "g.id_produit = ?", $id_produit );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_produitsxcouts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_cout
     */
    public function findById_cout($id_cout)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produitsxcouts") )                           
                    ->where( "g.id_cout = ?", $id_cout );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
