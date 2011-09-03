<?php
/**
 * Ce fichier contient la classe Gevu_exisxdroits.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_exisxdroits'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_exisxdroits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_exisxdroits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_exi';

    
    /**
     * Vérifie si une entrée Gevu_exisxdroits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_exi'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_exi; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_exisxdroits.
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
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_exisxdroits.id_exi = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_exisxdroits.id_exi = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_exisxdroits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_exisxdroits" => "gevu_exisxdroits") );
                    
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
     * Récupère les spécifications des colonnes Gevu_exisxdroits 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_exi","champ"=>"id_exi","visible"=>true),
    	array("titre"=>"id_droit","champ"=>"id_droit","visible"=>true),
    	array("titre"=>"params","champ"=>"params","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findByIdExi($id_exi)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.id_exi = ?", $id_exi );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_droit
     */
    public function findById_droit($id_droit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.id_droit = ?", $id_droit );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
