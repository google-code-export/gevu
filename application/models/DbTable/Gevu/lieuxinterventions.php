<?php
/**
 * Ce fichier contient la classe Gevu_lieuxinterventions.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieuxinterventions'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_lieuxinterventions extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_lieuxinterventions';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_lieuinterv';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_lieuxinterventions existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_lieuinterv'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_lieuinterv; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_lieuxinterventions.
     *
     * @param array $data
     * @param int $idExi
     *  
     * @return integer
     */
    public function ajouter($data, $idExi)
    {    	
		if(!isset($data['fait']))$data['fait']= new Zend_Db_Expr('NOW()');
		if(!isset($data['id_instant'])){
			$dbInst = new Models_DbTable_Gevu_instants();
			$c = str_replace("::", "_", __METHOD__); 
			$idInstant = $dbInst->ajouter(array("id_exi"=>$idExi,"nom"=>$c));
			$data['id_instant']= $idInstant;	
		}
		$id = $this->insert($data);

   	 	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_lieuxinterventions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_lieuxinterventions.id_lieuinterv = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_lieuxinterventions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_lieuxinterventions.id_lieuinterv = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_lieuxinterventions avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_lieuxinterventions avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_lieuxinterventions" => "gevu_lieuxinterventions") );
                    
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
     * Recherche une entrée Gevu_lieuxinterventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieuinterv
     *
     * @return array
     */
    public function findById_lieuinterv($id_lieuinterv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxinterventions") )                           
                    ->where( "g.id_lieuinterv = ?", $id_lieuinterv );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxinterventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findByIdLieu($id_lieu)
    {
        $query = $this->select()
			->from(array("li" => "gevu_lieuxinterventions"),array("id_lieuinterv","quantite","cout","fait"=>"DATE_FORMAT(fait, '%d %m %Y')","afaire"=>"DATE_FORMAT(afaire, '%d %m %Y')"))
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
			->joinInner( array("i" => "gevu_interventions"),
				'i.id_interv = li.id_interv',array("id_interv",'lib'=>"CONCAT(description,' : ',mc.titre,' ',i.cout,' € par ',mc1.titre, ' tout les ',frequence, ' an(s)') AS lib") )                           
            ->joinInner(array('mc' => 'gevu_motsclefs'),
                'mc.id_motclef = i.interv',array("libInterv"=>'titre'))
            ->joinInner(array('mc1' => 'gevu_motsclefs'),
                'mc1.id_motclef = i.unite',array("libUnite"=>'titre'))
            ->joinInner(array('p' => 'gevu_produits'),
                'p.id_produit = i.id_produit',array("description"))
            ->order(array("description", "mc.titre"))
			->where( "li.id_lieu = ?", $id_lieu );
        return $this->fetchAll($query)->toArray();
    }
    	/**
     * Recherche une entrée Gevu_lieuxinterventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_interv
     *
     * @return array
     */
    public function findByIdInterv($id_interv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxinterventions") )                           
                    ->where( "g.id_interv = ?", $id_interv );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxinterventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxinterventions") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxinterventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $quantite
     *
     * @return array
     */
    public function findByQuantite($quantite)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxinterventions") )                           
                    ->where( "g.quantite = ?", $quantite );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Recherche les type d'intervention autorisés pour ce lieu
     * et retourne ces entrées.
     *
     * @param int $idScenar
     * @param int $idLieu
     * 
     */
    public function getTypeInterv($idScenar, $idLieu)
    {
    	/*
    	$diag = new GEVU_Diagnostique();
    	$arrCtl = $diag->getLieuCtl($idLieu, $idScenar, false, "/node");
        return $arrCtl; 
    	*/
    	$dbInt = new Models_DbTable_Gevu_interventions();
        
        return $dbInt->findLienByIds();     	
    }
    
}
