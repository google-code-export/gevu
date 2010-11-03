<?php
/**
 * Ce fichier contient la classe Gevu_solutionsxmetiers.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_solutionsxmetiers'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_solutionsxmetiers extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_solutionsxmetiers';
      
    /*
     * dépendance avec les autres tables.
     */
    protected $_referenceMap    = array(
        'Metiers' => array(
            'columns'           => 'id_solution',
            'refTableClass'     => 'Model_DbTable_Gevu_solutions',
            'refColumns'        => 'id_concept'
        )
        ,'Solutions' => array(
            'columns'           => 'id_metier',
            'refTableClass'     => 'Model_DbTable_Gevu_metiers',
            'refColumns'        => 'id_metier'
        )
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
		$select->from($this, array("id_metier"));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]["id_metier"]; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_solutions.
     *
     * @param integer $idSolution
     * @param integer $idMetier
     * @param boolean $existe
     * 
     * @return integer
     */
    public function ajouter($idSolution, $idMetier, $existe=true)
    {
    	$id=false;
    	$data = array("id_solution"=>$idSolution,"id_metier"=>$idMetier);
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
    
    /**
     * Recherche une entrée Gevu_solutionsxmetiers avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_solutionsxmetiers.id_solution = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_solutionsxmetiers avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $idSolution
     * @param integer $idMetier
     * 
     * @return void
     */
    public function remove($idSolution, $idMetier)
    {
        $this->delete('gevu_solutionsxmetiers.id_solution = ' . $idSolution.' AND gevu_solutionsxmetiers.id_metier = ' . $idMetier);
    }
    
    /**
     * Récupère toutes les entrées Gevu_solutionsxmetiers avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutionsxmetiers") );
                    
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
     * Récupère les spécifications des colonnes Gevu_solutionsxmetiers 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
			array("titre"=>"id_solution","champ"=>"id_solution","visible"=>true),
			array("titre"=>"id_metier","champ"=>"id_metier","visible"=>true)
   		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_solutionsxmetiers avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     */
    public function findByIdSolution($id_solution)
    {
        $query = $this->select()
        			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
                    ->from( array("g" => "gevu_solutionsxmetiers"),
                          array('id_solution', 'id_metier') )                           
                   ->joinInner(array('l' => 'gevu_metiers'),
                          'g.id_metier = l.id_metier','lib')
                   ->where( "g.id_solution = " . $id_solution );
        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutionsxmetiers avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_metier
     */
    public function findById_metier($id_metier)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutionsxmetiers") )                           
                    ->where( "g.id_metier = " . $id_metier );

        return $this->fetchRow($query); 
    }
    
    
}
