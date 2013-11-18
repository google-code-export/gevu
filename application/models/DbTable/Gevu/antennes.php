<?php
/**
 * Ce fichier contient la classe Gevu_antenne.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_antenne'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_antennes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_antennes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_antenne';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_antenne existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_antenne'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_antenne; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_antenne.
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
     * Récupère ou Ajoute une entrée Gevu_antenne avec le lieu associé.
     *
     * @param string $ref
     * @param int $idInst
     * @param int $idLieuParent
     * @param string $lib
     *  
     * @return integer
     */
    public function getByRef($ref, $idInst, $idLieuParent, $lib="",$idBase)
    {    	
		//vérification de l'existence de l'antenne
	    $arr = $this->findByRef($ref);
	    if(count($arr)==0){
	    	if($lib=="")$lib="Antenne - ".$ref;
			$diag = new GEVU_Diagnostique($idBase);
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, $idBase, $lib, true, false, array("id_type_controle"=>60));
		    $this->ajouter(array("id_lieu"=>$idLieu, "id_instant"=>$idInst, "ref"=> $ref));
		    $arr = $this->findByRef($ref);
	    }
    	return $arr[0];
    } 

    /**
     * Renvoie les stats pour le type de logement.
     * 
     * @param string $type
     *  
     * @return array
     */
    public function getStatType($type="")
    {
		$sql = "
			select a.ref, l.lib
			  , count(DISTINCT b.id_batiment) 'nb batiment'
			  , count(DISTINCT lgt.id_logement) 'nb logement'
			  , count(DISTINCT lc.id_local) 'nb loc. act.'
			  , count(DISTINCT lc1.id_local) 'nb loc. velo'
			  , count(DISTINCT lc2.id_local) 'nb commerce'
			  , count(DISTINCT lc3.id_local) 'nb bat. admi.'
			  , count(DISTINCT lc4.id_local) 'nb foyer'
			  , count(DISTINCT lc5.id_local) 'nb residence'
			FROM gevu_antennes a
			inner join gevu_lieux l on l.id_lieu = a.id_lieu
			inner join gevu_lieux le on le.lft BETWEEN l.lft AND l.rgt
			left join gevu_batiments b on b.id_lieu = le.id_lieu
			left join gevu_logements lgt on lgt.id_lieu = le.id_lieu 
			left join gevu_locaux lc on lc.id_lieu = le.id_lieu and lc.activite = 68
			left join gevu_locaux lc1 on lc1.id_lieu = le.id_lieu and lc1.activite = 74
			left join gevu_locaux lc2 on lc2.id_lieu = le.id_lieu and lc2.activite = 86
			left join gevu_locaux lc3 on lc3.id_lieu = le.id_lieu and lc3.activite = 88
			left join gevu_locaux lc4 on lc4.id_lieu = le.id_lieu and lc4.activite = 90
			left join gevu_locaux lc5 on lc5.id_lieu = le.id_lieu and lc5.activite = 92
			group by a.ref
			";    	

		$sql = "
			SELECT a.ref, l.lib
				, s.type, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l on l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le on le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_stats s on s.id_lieu = le.id_lieu
			GROUP BY a.ref, s.type
			";    	

		if($type=="financement") $sql = "
			SELECT a.ref, l.lib
				, s.Type_Financement, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l on l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le on le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_stats s on s.id_lieu = le.id_lieu
			GROUP BY a.ref, Type_Financement
					";    	
		if($type=="age") $sql = "
			SELECT a.ref, l.lib
				, s.Annee_Construction, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l on l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le on le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_stats s on s.id_lieu = le.id_lieu
			GROUP BY a.ref, Annee_Construction
					";    	
		if($type=="copro") $sql = "
			SELECT a.ref, l.lib
				, s.Copropriete, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l on l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le on le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_stats s on s.id_lieu = le.id_lieu
			GROUP BY a.ref, Copropriete
					";    	
				
		if($type=="col_log") $sql = "
			SELECT a.ref, l.lib
				, s.Logement_Individuel, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_logements lgt ON lgt.id_lieu = le.id_lieu 
				INNER JOIN gevu_stats s ON s.id_lieu = lgt.id_lieu 
			GROUP BY a.ref, s.Logement_Individuel
			";    	
		if($type=="zus_log") $sql = "
			SELECT a.ref, l.lib
				, s.Indicateur_Zus, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_logements lgt ON lgt.id_lieu = le.id_lieu 
				INNER JOIN gevu_stats s ON s.id_lieu = lgt.id_lieu 
			GROUP BY a.ref, s.Indicateur_Zus
			";    	
		if($type=="vac_log") $sql = "
			SELECT a.ref, l.lib
				, s.OCCUPATION, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_logements lgt ON lgt.id_lieu = le.id_lieu 
				INNER JOIN gevu_stats s ON s.id_lieu = lgt.id_lieu 
			GROUP BY a.ref, s.OCCUPATION
			";    	
		if($type=="vac_com") $sql = "
			SELECT a.ref, l.lib
				, s.OCCUPATION, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_locaux lc on lc.id_lieu = le.id_lieu and lc.activite = 86
				INNER JOIN gevu_stats s ON s.id_lieu = lc.id_lieu 
			GROUP BY a.ref, s.OCCUPATION
			";    	
		if($type=="vac_gar") $sql = "
			SELECT a.ref, l.lib
				, s.OCCUPATION, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_espacesxinterieurs ei on ei.id_lieu = le.id_lieu and ei.id_type_specifique_int = 91
				INNER JOIN gevu_stats s ON s.id_lieu = ei.id_lieu 
			GROUP BY a.ref, s.OCCUPATION
			";    	
		if($type=="garage") $sql = "
			SELECT a.ref, l.lib
				, s.type_logement, COUNT(DISTINCT s.id_stat) 'nb'
			FROM gevu_antennes a
				INNER JOIN gevu_lieux l ON l.id_lieu = a.id_lieu
				INNER JOIN gevu_lieux le ON le.lft BETWEEN l.lft AND l.rgt
				INNER JOIN gevu_stats s ON s.id_lieu = le.id_lieu AND s.type_logement IN('GA','GP','TO') 
			GROUP BY a.ref, s.type_logement
			";    	
		
		
		$query = $this->_db->query($sql);        
        return $query->fetchAll();
    }     
        
    /**
     * Recherche une entrée Gevu_antenne avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {            	
    	$this->update($data, 'gevu_antennes.id_antenne = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_antenne avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$dbCA = new Models_DbTable_Gevu_contactsxantennes();
    	$dbCA->removeAntenne($id);    	
    	$this->delete('gevu_antennes.id_antenne = ' . $id);
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
    	$arr = $this->findByIdLieu($idLieu);
    	foreach ($arr as $a) {
    		$this->remove($a['id_antenne']);
    	}
    }
    
    /**
     * Récupère toutes les entrées Gevu_antenne avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {    	
    	$query = $this->select()
                    ->from( array("gevu_antennes" => "gevu_antennes") );
                    
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
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_antenne
     *
     * @return array
     */
    public function findById_antenne($id_antenne)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_antenne = ?", $id_antenne );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idLieu
     *
     * @return array
     */
    public function findByIdLieu($idLieu)
    {
        $query = $this->select()
            ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
			->from( array("a" => "gevu_antennes") )                           
            ->joinInner(array('l' => 'gevu_lieux'),
                'l.id_lieu = a.id_lieu',array('lib', 'id_lieu', 'lieu_parent', 'niv', 'id_type_controle', 'lock_diag'))        
            ->joinLeft(array('g' => 'gevu_geos'),
                'g.id_lieu = l.id_lieu',array('lat', 'lng', 'latlng', 'sw', 'ne', 'zoom_min', 'zoom_max', 'adresse'
            				, 'codepostal', 'ville', 'pays', 'kml', 'type_carte', 'maj', 'data', 'heading', 'pitch'
            				, 'zoom_sv', 'lat_sv', 'lng_sv'))        
            ->where( "a.id_lieu = ?", $idLieu);

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     *
     * @return array
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    
    	/**
     * Recherche une entrée Gevu_antenne avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     *
     * @return array
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_antennes") )                           
                    ->where( "g.ref = ?", $ref);

        return $this->fetchAll($query)->toArray(); 
    }
    
}
