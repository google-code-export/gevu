<?php
/**
 * Ce fichier contient la classe Gevu_geos.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_geos'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_geos extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_geos';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_geo';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_geos existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_geo'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_geo; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_geos.
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
     * Recherche une entrée Gevu_geos avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_geos.id_geo = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_geos avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_geos.id_geo = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_geos avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_geos" => "gevu_geos") );
                    
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
     * Récupère les spécifications des colonnes Gevu_geos 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_geo","champ"=>"id_geo","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"lat","champ"=>"lat","visible"=>true),
    	array("titre"=>"lng","champ"=>"lng","visible"=>true),
    	array("titre"=>"zoom_min","champ"=>"zoom_min","visible"=>true),
    	array("titre"=>"zoom_max","champ"=>"zoom_max","visible"=>true),
    	array("titre"=>"adresse","champ"=>"adresse","visible"=>true),
    	array("titre"=>"kml","champ"=>"kml","visible"=>true),
    	array("titre"=>"id_type_carte","champ"=>"id_type_carte","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_geo
     */
    public function findById_geo($id_geo)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.id_geo = ?", $id_geo );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param decimal $lat
     */
    public function findByLat($lat)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.lat = ?", $lat );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param decimal $lng
     */
    public function findByLng($lng)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.lng = ?", $lng );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $zoom_min
     */
    public function findByZoom_min($zoom_min)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.zoom_min = ?", $zoom_min );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $zoom_max
     */
    public function findByZoom_max($zoom_max)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.zoom_max = ?", $zoom_max );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $adresse
     */
    public function findByAdresse($adresse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.adresse = ?", $adresse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $kml
     */
    public function findByKml($kml)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.kml = ?", $kml );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_carte
     */
    public function findById_type_carte($id_type_carte)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.id_type_carte = ?", $id_type_carte );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_geos avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
