<?php
/**
 * Ce fichier contient la classe Gevu_scenario.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_scenario'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_scenario extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_scenario';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_scenario';

    
    /**
     * Vérifie si une entrée Gevu_scenario existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_scenario'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_scenario; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_scenario.
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
     * Recherche une entrée Gevu_scenario avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {
    	$data['maj'] = new Zend_Db_Expr('NOW()');        
        $this->update($data, 'gevu_scenario.id_scenario = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_scenario avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return integer
     */
    public function remove($id)
    {
    	$dbScene = new Models_DbTable_Gevu_scenes;
    	$dbScene->removeScenario($id);
        $this->delete('gevu_scenario.id_scenario = ' . $id);
        return -1;
        
    }
    
    /**
     * Récupère toutes les entrées Gevu_scenario avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_scenario" => "gevu_scenario") );
                    
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
     * Récupère les spécifications des colonnes Gevu_scenario 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_scenario","champ"=>"id_scenario","visible"=>true),
    	array("titre"=>"lib","champ"=>"lib","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
    	array("titre"=>"params","champ"=>"params","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_scenario
     */
    public function findById_scenario($id_scenario)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.id_scenario = ?", $id_scenario );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param longtext $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
