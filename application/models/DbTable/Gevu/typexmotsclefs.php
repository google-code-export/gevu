<?php
/**
 * Ce fichier contient la classe Gevu_typexmotsclefs.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_typexmotsclefs'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_typexmotsclefs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_typexmotsclefs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_motsclefs';

    
    /**
     * Vérifie si une entrée Gevu_typexmotsclefs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_motsclefs'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_motsclefs; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_typexmotsclefs.
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
     * Recherche une entrée Gevu_typexmotsclefs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_typexmotsclefs.id_type_motsclefs = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_typexmotsclefs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_typexmotsclefs.id_type_motsclefs = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_typexmotsclefs avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_typexmotsclefs" => "gevu_typexmotsclefs") );
                    
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
     * Récupère les spécifications des colonnes Gevu_typexmotsclefs 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_motsclefs","champ"=>"id_type_motsclefs","visible"=>true),
    	array("titre"=>"id_parent","champ"=>"id_parent","visible"=>true),
    	array("titre"=>"titre","champ"=>"titre","visible"=>true),
    	array("titre"=>"descriptif","champ"=>"descriptif","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_typexmotsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param bigint $id_type_motsclefs
     */
    public function findById_type_motsclefs($id_type_motsclefs)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typexmotsclefs") )                           
                    ->where( "g.id_type_motsclefs = ?", $id_type_motsclefs );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_typexmotsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param bigint $id_parent
     */
    public function findById_parent($id_parent)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typexmotsclefs") )                           
                    ->where( "g.id_parent = ?", $id_parent );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_typexmotsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $titre
     */
    public function findByTitre($titre)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typexmotsclefs") )                           
                    ->where( "g.titre = ?", $titre );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_typexmotsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $descriptif
     */
    public function findByDescriptif($descriptif)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typexmotsclefs") )                           
                    ->where( "g.descriptif = ?", $descriptif );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
