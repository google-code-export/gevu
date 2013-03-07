<?php
/**
 * Ce fichier contient la classe Gevu_solutions.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_solutions'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_solutions extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_solutions';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_solution';
    
    /*
     * dépendance avec les autres tables.
     */
    protected $_dependentTables = array(
		'Models_DbTable_Gevu_metiersxsolutions'
		,'Models_DbTable_Gevu_typesxsolutions'
	);
    
    /**
     * Vérifie si une entrée Gevu_solutions existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_solution'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_solution; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_solutions.
     *
     * @param array $data
     * @param boolean $existe
     * 
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    		$data['maj'] = new Zend_Db_Expr('CURDATE()');
    	 	$id = $this->insert($data);
    	}
    	//on crée un cout pour la solution
		$s = new Models_DbTable_Gevu_couts();
		$data = array("unite"=>0);
		$idC = $s->ajouter($data);
		$s = new Models_DbTable_Gevu_solutionsxcouts();
		$data = array("id_solution"=>$id,"id_cout"=>$idC);
		$s->ajouter($data,false);
    	
    	return $id;
    } 
       
    /**
     * Recherche une entrée Gevu_solutions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_solutions.id_solution = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_solutions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$dbE = new Models_DbTable_Gevu_solutionsxcouts();
    	$dbE->remove($id);
    	$dbE = new Models_DbTable_Gevu_solutionsxcriteres();
    	$dbE->remove($id);
    	$dbE = new Models_DbTable_Gevu_solutionsxmetiers();
    	$dbE->remove($id);
    	$dbE = new Models_DbTable_Gevu_solutionsxproduits();
    	$dbE->remove($id);
    	$this->delete('gevu_solutions.id_solution = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_solutions avec certains critères
     * de tri, intervalles
     * @param string 	$order
     * @param integer 	$limit
     * @param integer 	$from
     * @return object
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        	->from( array("g" => "gevu_solutions"))
			->joinInner(array('l' => 'gevu_typesxsolutions'),
            	'g.id_type_solution = l.id_type_solution',array('LibTypeSolution'=>'lib'));
                    
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
     * Récupère les spécifications des colonnes Gevu_solutions 
     * @return array
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    		array("titre"=>"Mise à jour","champ"=>"maj","visible"=>true)
    		, array("titre"=>"Id. solution","champ"=>"id_solution","visible"=>true)
    		, array("titre"=>"Libellé","champ"=>"lib","visible"=>true)
    		, array("titre"=>"Type de solution","champ"=>"id_type_solution","visible"=>true,"objName"=>"Models_DbTable_Gevu_typesxsolutions")
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_solutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     */
    public function findById_solution($id_solution)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutions") )                           
                    ->where( "g.id_solution = " . $id_solution );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutions") )                           
                    ->where( "g.lib = " . $lib );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_solution
     */
    public function findById_type_solution($id_type_solution)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutions") )                           
                    ->where( "g.id_type_solution = " . $id_type_solution );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutions") )                           
                    ->where( "g.maj = " . $maj );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutions") )                           
                    ->where( "g.ref = " . $ref );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
