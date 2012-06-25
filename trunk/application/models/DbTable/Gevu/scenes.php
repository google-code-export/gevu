<?php
/**
 * Ce fichier contient la classe Gevu_scenes.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_scenes'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_scenes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_scenes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_scene';

    
    /**
     * Vérifie si une entrée Gevu_scenes existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_scene'));
		foreach($data as $k=>$v){
			if($k!=maj)
				$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_scene; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_scenes.
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
    		$data['maj'] = new Zend_Db_Expr('NOW()');
    		$id = $this->insert($data);
    	}
    	return $id;
    } 
    /**
     * Copie une entrée Gevu_scenes.
     *
     * @param array $data
     *  
     * @return integer
     */
    public function copier($data)
    {
		$str = "INSERT INTO gevu_scenes (id_scenario, lib, params, maj, type, xml)
			SELECT ".$data["id_scenario"]
			.",lib, params, now(), type, xml
			FROM gevu_scenes
			WHERE id_scene = ".$data["id_scene"];			
        $db = Zend_Db_Table::getDefaultAdapter();
    	$stmt = $db->query($str);
    	$id = $db->lastInsertId();
    	return $id;
    } 
    
    /**
     * Recherche une entrée Gevu_scenes avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_scenes.id_scene = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_scenes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_scenes.id_scene = ' . $id);
        return -1;
    }

    /**
    * Recherche une entrée Gevu_scenes avec la clef secondaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id_scenario
     *
     * @return integer
     */
    public function removeScenario($id_scenario)
    {
        $this->delete('gevu_scenes.id_scenario = ' . $id_scenario);
        return -1;
    }
    
    /**
    * Recherche une entrée Gevu_scenes avec les paramètres spécifiés
     * et supprime cette entrée.
     *
     * @param integer $id_scenario
     * @param string $type
     *
     * @return integer
     */
    public function removeByScenarioType($id_scenario, $type)
    {
        $this->delete('gevu_scenes.id_scenario = '.$id_scenario.' AND gevu_scenes.type = "'.$type.'"');
        return -1;
    }
    
    /**
     * Récupère toutes les entrées Gevu_scenes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_scenes" => "gevu_scenes") );
                    
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
     * Récupère les spécifications des colonnes Gevu_scenes 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_scene","champ"=>"id_scene","visible"=>true),
    	array("titre"=>"id_scenario","champ"=>"id_scenario","visible"=>true),
    	array("titre"=>"lib","champ"=>"lib","visible"=>true),
    	array("titre"=>"params","champ"=>"params","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_scene
     */
    public function findByIdScene($id_scene)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.id_scene = ?", $id_scene );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_scenario
     */
    public function findById_scenario($id_scenario)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.id_scenario = ?", $id_scenario );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_scenario
     * @param string $type
     */
    public function findByIdScenarioType($id_scenario, $type)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.type = ?", $type)
                    ->where( "g.id_scenario = ?", $id_scenario );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param longtext $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
