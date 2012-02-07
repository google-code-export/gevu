<?php
/**
 * Ce fichier contient la classe Gevu_observations.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_observations'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_observations extends Zend_Db_Table_Abstract
{
	
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_observations';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_observations';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
	
    protected $_adapter;

    /**
     * initialisation de la base de donnée

     * @param string $idBase
     *
     */
    protected function _setupDatabaseAdapter($idBase="") 
	{
		if($idBase!=""){
			$this->_adapter=$idBase;
			$this->_db = Zend_Registry::get($this->_adapter);			
		}else{
			$this->_db = $this->getDefaultAdapter();
		}
	}
    
    /**
     * Vérifie si une entrée Gevu_observations existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_observations'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_observations; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_observations.
     *
     * @param array $data
     * @param int $idExi
     * @param string $idBase
     *  
     * @return integer
     */
    public function ajouter($data, $idExi=-1, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	    	
		//création d'un nouvel instant
    	$dbI = new Models_DbTable_Gevu_instants($this->getAdapter());
    	$idI = $dbI->ajouter(array("id_exi"=>$idExi));
    	$data['id_instant']=$idI;
    	$data['maj']= new Zend_Db_Expr('NOW()');
    	$id = $this->insert($data);

    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_observations avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data, $idBase="")
    {        
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
    	$this->update($data, 'gevu_observations.id_observations = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_observations avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     * @param string $idBase
     *  
     * @return void
     */
    public function remove($id, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	    	
    	$this->delete('gevu_observations.id_observations = ' . $id);

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
     * Récupère toutes les entrées Gevu_observations avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	    	    	
		$query = $this->select()
                    ->from( array("gevu_observations" => "gevu_observations") );
                    
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
   
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_observations
     */
    public function findById_observations($id_observations)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_observations = ?", $id_observations );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse
     */
    public function findById_reponse($id_reponse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_reponse = ?", $id_reponse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $num_marker
     */
    public function findByNum_marker($num_marker)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.num_marker = ?", $num_marker );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Recherche une entrée Gevu_observations avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param integer $idDiag
     * @param string $idBase
     * 
     * @return array
     */
    public function findByIdDiag($IdDiag, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
    	$query = $this->select()
                    ->from( array("g" => "gevu_observations") )                           
                    ->where( "g.id_diag = ?", $IdDiag);

        return $this->fetchAll($query)->toArray(); 
    }
    
}
