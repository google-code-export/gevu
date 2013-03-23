<?php
/**
 * Ce fichier contient la classe Gevu_diagnosticsxsolutions.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Models_DbTable_Gevu_diagnosticsxsolutions extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_diagnosticsxsolutions';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_diagsolus';

    protected $_referenceMap    = array(
        'Diag' => array(
            'columns'           => 'id_diag',
            'refTableClass'     => 'Models_DbTable_Gevu_diagnostics',
            'refColumns'        => 'id_diag'
        ),
        'Solution' => array(
            'columns'           => 'id_solution',
            'refTableClass'     => 'Models_DbTable_Gevu_solutions',
            'refColumns'        => 'id_solution'
        ),
        'Produit' => array(
            'columns'           => 'id_produit',
            'refTableClass'     => 'Models_DbTable_Gevu_produits',
            'refColumns'        => 'id_produit'
        ),
   		'Cout' => array(
   				'columns'           => 'id_cout',
   				'refTableClass'     => 'Models_DbTable_Gevu_couts',
   				'refColumns'        => 'id_cout'
   		),
		'Instant' => array(
    		'columns'           => 'id_instant',
    		'refTableClass'     => 'Models_DbTable_Gevu_instants',
    		'refColumns'        => 'id_instant'
    	)	
    );	
    
    /**
     * retourne une connexion à une base de donnée suivant son nom
     * @param string $idBase
     * @return Zend_Db_Adapter_Abstract
     */
    public function setDb($idBase){
    
    	$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
    		$arr = $db->getConfig();
    		$arr['dbname']=$idBase;
    		$db = Zend_Db::factory('PDO_MYSQL', $arr);
    	}
    	$this->_db = self::_setupAdapter($db);
    }
    
    /**
     * Vérifie si une entrée Gevu_diagnosticsxsolutions existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_diagsolus'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_diagsolus; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_diagnosticsxsolutions.
     *
     * @param array 	$data
     * @param int 		$idExi
     * @param string 	$idBase
     * @param int 		$idInst
     *  
     * @return integer
     */
    public function ajouter($data, $idExi, $idBase, $idInst=0)
    {
    	if($idBase)$this->setDb($idBase);
    	 
    	//création d'un nouvel instant
    	if(!$idInst){
	    	$dbI = new Models_DbTable_Gevu_instants($this->_db);
	    	$idInst = $dbI->ajouter(array("id_exi"=>$idExi, "nom"=>"Gevu_diagnosticsxsolutions - ajouter"));
    	}
	    $data["id_instant"] = $idInst;
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 

    /**
     * Ajouter une liste d'entrées Gevu_diagnosticsxsolutions.
     *
     * @param array 	$diags
     * @param array 	$data
     * @param int 		$idExi
     * @param string 	$idBase
     *
     * @return integer
     */
    public function ajouterDiags($diags, $data, $idExi, $idBase)
    {
    	if($idBase)$this->setDb($idBase);
    
    	//création d'un nouvel instant
    	$dbI = new Models_DbTable_Gevu_instants($this->_db);
    	$idInst = $dbI->ajouter(array("id_exi"=>$idExi, "nom"=>"Gevu_diagnosticsxsolutions - ajouter"));
    	    	
    	foreach ($diags as $d) {
    		$data["id_diag"]=$d;
    		$id = $this->ajouter($data, $idExi,false,$idInst);
    	}
    	return $id;
    }
    
    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     * @param string $idBase
     *
     * @return void
     */
    public function edit($id, $data, $idBase)
    {        
    	if($idBase)$this->setDb($idBase);
    	 
    	$this->update($data, 'gevu_diagnosticsxsolutions.id_diagsolus = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     * @param string $idBase
     *
     * @return void
     */
    public function remove($id, $idBase)
    {
    	if($idBase)$this->setDb($idBase);
    	 
    	$this->delete('gevu_diagnosticsxsolutions.id_diagsolus = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_diagnosticsxsolutions avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_diagnosticsxsolutions" => "gevu_diagnosticsxsolutions") );
                    
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
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diagsolus
     *
     * @return array
     */
    public function findById_diagsolus($id_diagsolus)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnosticsxsolutions") )                           
                    ->where( "g.id_diagsolus = ?", $id_diagsolus );

        return $this->fetchAll($query)->toArray(); 
    }

    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diag
     * @param sting $idBase
     *
     * @return array
     */
    public function findByIdDiag($id_diag, $idBase)
    {
    	//Récupère la configuration de la base de ref
    	$arrDB = $this->_db->getConfig();
    	 
    	$query = "SELECT
		  ds.id_diagsolus, ds.id_diag, ds.unite dsunite, ds.pose dspose, ds.metre_lineaire dsmetre_lineaire, ds.metre_carre dsmetre_carre, ds.achat dsachat, ds.cout dscout
		  , '' critRef
    	  , s.id_solution, s.lib AS solution, s.ref AS refSolu
		  , c.id_cout, c.unite, c.metre_lineaire, c.metre_carre, c.achat, c.pose
		  , p.id_produit, p.ref AS refProd, p.description produit, p.marque, p.modele
		FROM ".$idBase.".gevu_diagnosticsxsolutions ds 
			INNER JOIN ".$arrDB['dbname'].".gevu_couts as c ON c.id_cout = ds.id_cout
		  	LEFT JOIN ".$arrDB['dbname'].".gevu_solutions AS s ON s.id_solution = ds.id_solution
			LEFT JOIN ".$arrDB['dbname'].".gevu_produits as p ON p.id_produit = ds.id_produit
		WHERE ds.id_diag = ".$id_diag;
    	 
    	$adpt = $this->getAdapter();
    	$result = $adpt->query($query);

        return $result->fetchAll();
    	  
    }

    /**
     * Recherche les entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne ces entrées.
     *
     * @param int $id_lieu
     * @param sting $idBase
     *
     * @return array
     */
    public function findByIdLieuFait($id_lieu, $idBase)
    {
    	//Récupère la configuration de la base de ref
    	$arrDB = $this->_db->getConfig();
    
		//on ne récupère que les réponses NON et N-A du dernier diag
    	$query = "SELECT
      		ld.id_lieu 
		  , ds.id_diagsolus, ds.id_diag, ds.unite dsunite, ds.pose dspose, ds.metre_lineaire dsmetre_lineaire, ds.metre_carre dsmetre_carre, ds.achat dsachat, ds.cout dscout
		  , crit.ref critRef, crit.id_critere
    	  , s.id_solution, s.lib AS solution, s.ref AS refSolu
		  , c.id_cout, c.unite, c.metre_lineaire, c.metre_carre, c.achat, c.pose
		  , p.id_produit, p.ref AS refProd, p.description produit, p.marque, p.modele
		FROM ".$idBase.".gevu_lieux as ld 
			INNER JOIN ".$idBase.".gevu_lieux as l ON l.id_lieu = ".$id_lieu."	AND ld.lft BETWEEN l.lft AND l.rgt 				
			INNER JOIN ".$idBase.".gevu_diagnostics as d ON d.id_lieu = ld.id_lieu AND d.id_reponse IN (124,2) AND d.last = 1
			INNER JOIN ".$arrDB['dbname'].".gevu_criteres as crit ON crit.id_critere = d.id_critere AND crit.affirmation != ''
			INNER JOIN ".$idBase.".gevu_diagnosticsxsolutions ds ON ds.id_diag = d.id_diag
			INNER JOIN ".$arrDB['dbname'].".gevu_couts as c ON c.id_cout = ds.id_cout
		  	INNER JOIN ".$arrDB['dbname'].".gevu_solutions AS s ON s.id_solution = ds.id_solution
			LEFT JOIN ".$arrDB['dbname'].".gevu_produits as p ON p.id_produit = ds.id_produit
		ORDER BY crit.ref";
    	 
    	$adpt = $this->getAdapter();
    	$result = $adpt->query($query);
    
    	return $result->fetchAll();
    	 
    }

    /**
     * Recherche les entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne ces entrées.
     *
     * @param int $id_lieu
     * @param sting $idBase
     *
     * @return array
     */
    public function findByIdLieuAFaire($id_lieu, $idBase)
    {
    
    	if($idBase)$this->setDb($idBase);
    	 
		//on ne récupère que les réponses NON et N-A du dernier diag
    	$query = "SELECT
	    	 GROUP_CONCAT(DISTINCT d.id_diag) diags
	    	, GROUP_CONCAT(DISTINCT ld.id_lieu) lieux
	      	, COUNT(DISTINCT d.id_diag) nbDiag
	    	, d.id_critere
	      	, c.ref
	      	, ds.id_solution
    	FROM gevu_lieux as ld
    	INNER JOIN gevu_lieux as l ON l.id_lieu = ".$id_lieu." AND ld.lft BETWEEN l.lft AND l.rgt
    	INNER JOIN gevu_diagnostics as d ON d.id_lieu = ld.id_lieu AND d.id_reponse IN (124,2) AND d.last = 1
    	INNER JOIN gevu_criteres as c ON c.id_critere = d.id_critere AND c.affirmation != ''
		LEFT JOIN gevu_diagnosticsxsolutions ds ON ds.id_diag = d.id_diag
    	WHERE ds.id_solution is null
		GROUP BY d.id_critere";
    
    	$adpt = $this->getAdapter();
    	$result = $adpt->query($query);
    
    	return $result->fetchAll();
    
    }
    
    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     *
     * @return array
     */
    public function findById_solution($id_solution)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnosticsxsolutions") )                           
                    ->where( "g.id_solution = ?", $id_solution );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_produit
     *
     * @return array
     */
    public function findById_produit($id_produit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnosticsxsolutions") )                           
                    ->where( "g.id_produit = ?", $id_produit );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_cout
     *
     * @return array
     */
    public function findById_cout($id_cout)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnosticsxsolutions") )                           
                    ->where( "g.id_cout = ?", $id_cout );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnosticsxsolutions") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
