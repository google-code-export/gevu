<?php
/**
 * Ce fichier contient la classe Gevu_problemes.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_problemes'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_problemes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_problemes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_probleme';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
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
     * Vérifie si une entrée Gevu_problemes existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_probleme'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_probleme; else $id=false;
        return $id;
    } 
        
     /**
     * Ajoute un probleme
     *
     * @param array $data
     * @param int $idExi
     * @param string $idBase
     *  
     * @return integer
     */
    public function ajouter($data, $idExi=-1, $idBase=false)
    {
    	if($idBase)$this->setDb($idBase);
    	 
		//création d'un nouvel instant
    	$dbI = new Models_DbTable_Gevu_instants($this->_db);
    	$idI = $dbI->ajouter(array("id_exi"=>$idExi));
    	$data['id_instant']=$idI;
    	$data['maj']= new Zend_Db_Expr('NOW()');
    	$id = $this->insert($data);

    	return $id;
    } 
    
    /**
     * Recherche une entrée Gevu_problemes avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_problemes.id_probleme = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_problemes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param int $id
     * @param string $idBase
     *  
     * @return void
     */
    public function remove($id, $idBase=false)
    {
    	if($idBase)$this->setDb($idBase);
    	 
        $this->delete('gevu_problemes.id_probleme = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_problemes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
    	$query = $this->select()
                    ->from( array("gevu_problemes" => "gevu_problemes") );
                    
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
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_probleme
     */
    public function findById_probleme($id_probleme)
    {
        $query = $this->select()
		->from( array("g" => "gevu_problemes") )                           
        ->where( "g.id_probleme IN (".$id_probleme.")");

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $num_marker
     */
    public function findByNum_marker($num_marker)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.num_marker = ?", $num_marker );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mesure
     */
    public function findByMesure($mesure)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.mesure = ?", $mesure );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $observations
     */
    public function findByObservations($observations)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.observations = ?", $observations );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fichier
     */
    public function findByFichier($fichier)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.fichier = ?", $fichier );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $doc
     */
    public function findByDoc($doc)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.doc = ?", $doc );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Recherche une entrée Gevu_problemes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param integer $idDiag
	 * @param string $idBase
     * 
     * @return array
     */
    public function findByIdDiag($idDiag, $idBase=false)
    {
    	if($idBase)$this->setDb($idBase);
    	
    	$query = $this->select()
                    ->from( array("g" => "gevu_problemes") )                           
                    ->where( "g.id_diag = ?", $idDiag);

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche les documents associés à un problème
     * et retourne ces entrées.
     *
     * @param integer $idProb
	 * @param string $idBase
     * 
     * @return array
     */
    public function findDocs($IdProb, $idBase=false)
    {
    	if($idBase)$this->setDb($idBase);
    	 
    	$dbDocProb = new Models_DbTable_Gevu_docsxproblemes($this->_db);
    	
    	return $dbDocProb->findByIdProbleme($IdProb);

    }
        
}
