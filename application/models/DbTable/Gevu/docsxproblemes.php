<?php
/**
 * Ce fichier contient la classe Gevu_docsxproblemes.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_docsxproblemes'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_docsxproblemes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_docsxproblemes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_doc';

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
     * Vérifie si une entrée Gevu_docsxproblemes existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_doc'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_doc; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_docsxproblemes.
     *
     * @param array $data
     * @param string $idBase
     *  
     * @return integer
     */
    public function ajouter($data, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
   	 	$id = $this->insert($data);

    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_docsxproblemes avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     * @param string $idBase
     *
     * @return void
     */
    public function edit($id, $data, $idBase="")
    {        
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
    	$this->update($data, 'gevu_docsxproblemes.id_doc = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_docsxproblemes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
    	$this->delete('gevu_docsxproblemes.id_doc = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_docsxproblemes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);
    	
    	$query = $this->select()
                    ->from( array("gevu_docsxproblemes" => "gevu_docsxproblemes") );
                    
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
     * Recherche une entrée Gevu_docsxproblemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_doc
     *
     * @return array
     */
    public function findById_doc($id_doc, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);

        $query = $this->select()
                    ->from( array("g" => "gevu_docsxproblemes") )                           
                    ->where( "g.id_doc = ?", $id_doc );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_docsxproblemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_probleme
     *
     * @return array
     */
    public function findByIdProbleme($id_probleme, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);

        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        	->from( array("dp" => "gevu_docsxproblemes") )
            ->joinInner(array("d" => "gevu_docs") , "dp.id_doc = d.id_doc")                           
            ->where( "dp.id_probleme = ?", $id_probleme );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_docsxproblemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant, $idBase="")
    {
    	//gestion des bases multiples
    	$this->_setupDatabaseAdapter($idBase);

        $query = $this->select()
                    ->from( array("g" => "gevu_docsxproblemes") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
