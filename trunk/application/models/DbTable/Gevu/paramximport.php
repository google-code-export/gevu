<?php
/**
 * Ce fichier contient la classe Gevu_paramximport.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_paramximport'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_paramximport extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_paramximport';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_paramximport';

    
    /**
     * Vérifie si une entrée Gevu_paramximport existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_paramximport'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $k);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_paramximport; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_paramximport.
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
     * Recherche une entrée Gevu_paramximport avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_paramximport.id_paramximport = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_paramximport avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_paramximport.id_paramximport = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_paramximport avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_paramximport" => "gevu_paramximport") );
                    
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
     * Récupère les spécifications des colonnes Gevu_paramximport 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_paramximport","champ"=>"id_paramximport","visible"=>true),
    	array("titre"=>"colSource","champ"=>"colSource","visible"=>true),
    	array("titre"=>"objDest","champ"=>"objDest","visible"=>true),
    	array("titre"=>"ordre","champ"=>"ordre","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_paramximport avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_paramximport
     */
    public function findById_paramximport($id_paramximport)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramximport") )                           
                    ->where( "g.id_paramximport = " . $id_paramximport );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_paramximport avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $colSource
     */
    public function findByColSource($colSource)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramximport") )                           
                    ->where( "g.colSource = " . $colSource );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_paramximport avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $objDest
     */
    public function findByObjDest($objDest)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramximport") )                           
                    ->where( "g.objDest = ?", $objDest)
                    ->order(array('ordre'));

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_paramximport avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $ordre
     */
    public function findByOrdre($ordre)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramximport") )                           
                    ->where( "g.ordre = " . $ordre );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_paramximport avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $ordre
     */
    public function findByType_import($type_import)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_paramximport") )                           
                    ->where( "g.type_import = ?", $type_import);

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
