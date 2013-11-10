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
     * spécification du select pour récupérer les points
     */
	var $selectFields = array("id_geo", "id_instant", "id_lieu", "lat", "lng"
		, "latlng"=>"CONCAT('(',X(latlng),',',Y(latlng),')')", "sw"=>"CONCAT('(',X(sw),',',Y(sw),')')", "ne"=>"CONCAT('(',X(ne),',',Y(ne),')')"
		, "zoom_min", "zoom_max", "adresse", "codepostal", "ville", "pays", "kml", "type_carte", "maj", "data"
		,"lat_sv", "lng_sv", "zoom_sv", "heading", "pitch");

    /**
     * Vérifie la valeur des données géographiques et de date
     *
     * @param array $data
     *
     * @return array
     */
    public function verifData($data)
    {
    	/*
    	if(!isset($data["latlng"]))$data["latlng"]=new Zend_Db_Expr("Point(".$data["lat"].",".$data["lng"].")");
    	elseif($data["latlng"])$data["latlng"]=new Zend_Db_Expr("Point".$data["latlng"]);
    	else unset($data["latlng"]);
    	
    	if(isset($data["sw"])){
    		$data["sw"]=new Zend_Db_Expr("Point".$data["sw"]);
    	}else unset($data["sw"]); 
    	 
    	
    	if(isset($data["ne"])){
    		$data["ne"]=new Zend_Db_Expr("Point".$data["ne"]); 
    	}else unset($data["ne"]);
    	*/ 
    	unset($data["latlng"]);
    	unset($data["sw"]); 
    	unset($data["ne"]);
    	
    	if(!isset($data['maj']))$data['maj']=new Zend_Db_Expr('NOW()');
    	
    	return $data;
    }
		
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
		/**
		 * il n'y a qu'une géolocalisation par lieu
		 */
		$select->where('id_lieu = ?', $data['id_lieu']);
		/*
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
		*/
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
    		$data = $this->verifData($data);
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
    
    /**
     * Recherche les entrées de Gevu_batiments avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
        $this->delete('id_lieu = ' . $idLieu);
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
    	$data = $this->verifData($data);    	
    	$this->update($data, 'gevu_geos.id_geo = ' . $id);
    }

    /**
     * Recherche une entrée Gevu_geos avec la clef l'identifiant de lieu
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function editByLieu($id, $data)
    {
    	$data = $this->verifData($data);
    	$this->update($data, 'gevu_geos.id_lieu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_geos avec la clef l'identifiant de lieu
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param string $ids
     * @param array $data
     *
     * @return void
     */
    public function editByIdsLieux($ids, $data)
    {
    	$data = $this->verifData($data);
    	$this->update($data, 'gevu_geos.id_lieu IN('.$ids.')');
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields);                           
    	                    
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
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $adresse
     * @param string $codepostal
     * @param string $ville
     * @param string $pays
     *
     * @return array
     */
    public function findIdsLieuxByAdresse($adresse, $codepostal, $ville, $pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_geos"), array("id_lieu") )                           
                    ->where( 'g.adresse LIKE "%'.$adresse.'"')
                    ->where( "g.codepostal = ?", $codepostal)
					->where( "g.ville = ?", $ville)
                    ->where( "g.pays = ?", $pays);
                                        
        return $this->fetchAll($query)->toArray(); 
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
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
        	->setIntegrityCheck(false)
			->from(array("g" => "gevu_geos"),$this->selectFields)                           
    		->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
