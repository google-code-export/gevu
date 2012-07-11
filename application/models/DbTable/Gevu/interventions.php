<?php
/**
 * Ce fichier contient la classe Gevu_interventions.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_interventions'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_interventions extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_interventions';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_interv';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_interventions existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_interv'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_interv; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_interventions.
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
     * Vérifie si une intervention est lié à un lieu
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function verifLienLieu($id)
    {        
   		$db = new Models_DbTable_Gevu_lieuxinterventions();
   		return $db->findByIdInterv($id);
    }

    /**
     * Recherche une entrée Gevu_interventions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_interventions.id_interv = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_interventions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_interventions.id_interv = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_interventions avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_interventions avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_interventions" => "gevu_interventions") );
                    
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
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_interv
     *
     * @return array
     */
    public function findById_interv($id_interv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_interventions") )                           
                    ->where( "g.id_interv = ?", $id_interv );

        return $this->fetchAll($query)->toArray(); 
    }

    /**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_produit
     *
     * @return array
     */
    public function findByIdProduit($id_produit)
    {
        $query = $this->select()
			->from( array("i" => "gevu_interventions") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('mc' => 'gevu_motsclefs'),
                'mc.id_motclef = i.interv',array("libInterv"=>'titre'))
            ->joinInner(array('mc1' => 'gevu_motsclefs'),
                'mc1.id_motclef = i.unite',array("libUnite"=>'titre'))
            ->where( "i.id_produit = ?", $id_produit);

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $interv
     *
     * @return array
     */
    public function findByInterv($interv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_interventions") )                           
                    ->where( "g.interv = ?", $interv );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $unite
     *
     * @return array
     */
    public function findByUnite($unite)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_interventions") )                           
                    ->where( "g.unite = ?", $unite );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $frequence
     *
     * @return array
     */
    public function findByFrequence($frequence)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_interventions") )                           
                    ->where( "g.frequence = ?", $frequence );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param decimal $cout
     *
     * @return array
     */
    public function findByCout($cout)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_interventions") )                           
                    ->where( "g.cout = ?", $cout );

        return $this->fetchAll($query)->toArray(); 
    }

    /**
     * Recherche une entrée Gevu_interventions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $ids
     *
     * @return array
     */
    public function findLienByIds($ids=false)
    {
        $query = $this->select()
			->from( array("i" => "gevu_interventions"),array("id_interv", frequence, cout, 'lib'=>"CONCAT(description,' : ',mc.titre,' ',cout,' € par ',mc1.titre, ' tout les ',frequence, ' an(s)') AS lib") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('mc' => 'gevu_motsclefs'),
                'mc.id_motclef = i.interv',array("libInterv"=>'titre'))
            ->joinInner(array('mc1' => 'gevu_motsclefs'),
                'mc1.id_motclef = i.unite',array("libUnite"=>'titre'))
            ->joinInner(array('p' => 'gevu_produits'),
                'p.id_produit = i.id_produit',array("description"))
            ->order(array("description", "mc.titre"));
        if($ids) $query->where( "i.id_interv IN (".$ids.")");

        return $this->fetchAll($query)->toArray(); 
    }    
    
}
