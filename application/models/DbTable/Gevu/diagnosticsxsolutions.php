<?php
/**
 * Ce fichier contient la classe Gevu_diagnosticsxsolutions.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Model_DbTable_Gevu_diagnosticsxsolutions extends Zend_Db_Table_Abstract
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
     *  
     * @return integer
     */
    public function ajouter($data, $idExi, $idBase)
    {
    	if($idBase)$this->setDb($idBase);
    	 
    	//création d'un nouvel instant
    	$dbI = new Models_DbTable_Gevu_instants($this->_db);
    	$idInst = $dbI->ajouter(array("id_exi"=>$idExi, "nom"=>"Gevu_diagnosticsxsolutions - ajouter"));
    	$data["id_instant"] = $idInst;
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_diagnosticsxsolutions.id_diagsolus = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagnosticsxsolutions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     * @param integer $idBase
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
    	if($idBase)$this->setDb($idBase);
    	 
    	$query = $this->select()
         	->from( array("g" => "gevu_diagnosticsxsolutions") )                           
            ->where( "g.id_diag = ?", $id_diag );

        return $this->fetchAll($query)->toArray(); 
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
