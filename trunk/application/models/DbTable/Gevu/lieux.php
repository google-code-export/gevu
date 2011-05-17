<?php
/**
 * Ce fichier contient la classe Gevu_lieux.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieux'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_lieux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_lieux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_lieu';

    
    /**
     * Vérifie si une entrée Gevu_lieux existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_lieu'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_lieu; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_lieux.
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
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_lieux avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_lieux" => "gevu_lieux") );
                    
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
     * Récupère les spécifications des colonnes Gevu_lieux 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_rubrique","champ"=>"id_rubrique","visible"=>true),
    	array("titre"=>"lib","champ"=>"lib","visible"=>true),
    	array("titre"=>"id_parent","champ"=>"id_parent","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"lft","champ"=>"lft","visible"=>true),
    	array("titre"=>"rgt","champ"=>"rgt","visible"=>true),
    	array("titre"=>"niv","champ"=>"niv","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_rubrique
     */
    public function findById_rubrique($id_rubrique)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_rubrique = ?", $id_rubrique );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_parent
     */
    public function findById_parent($id_parent)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_parent = ?", $id_parent );

        return $this->fetchAll($query)->toArray(); 
    }
    
	/*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lieu_parent
     */
    public function findByLieu_parent($lieu_parent)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lieu_parent = ?", $lieu_parent );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lft
     */
    public function findByLft($lft)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lft = ?", $lft );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $rgt
     */
    public function findByRgt($rgt)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.rgt = ?", $rgt );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $niv
     */
    public function findByNiv($niv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.niv = ?", $niv );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
