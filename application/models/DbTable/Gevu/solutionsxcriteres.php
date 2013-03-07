<?php
/**
 * Ce fichier contient la classe Gevu_solutionsxcriteres.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_solutionsxcriteres'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_solutionsxcriteres extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_solutionsxcriteres';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_solution';

    
    /**
     * Vérifie si une entrée Gevu_solutionsxcriteres existe.
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
     * Ajoute une entrée Gevu_solutionsxcriteres.
     *
     * @param int $idSolution
     * @param int $idCritere
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($idSolution, $idCritere, $existe=true)
    {
    	$id=false;
    	$data = array("id_solution"=>$idSolution,"id_critere"=>$idCritere);
       	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_solutionsxcriteres avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_solutionsxcriteres.id_solution = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_solutionsxcriteres avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($idSolution, $idCritere=null)
    {
    	if($idCritere == null){
	        $this->delete('gevu_solutionsxcriteres.id_solution = '.$idSolution);
    	}else{
	        $this->delete('gevu_solutionsxcriteres.id_solution = '.$idSolution.' AND gevu_solutionsxcriteres.id_critere = '.$idCritere);
    	}
    }
    
    /**
     * Récupère toutes les entrées Gevu_solutionsxcriteres avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_solutionsxcriteres" => "gevu_solutionsxcriteres") );
                    
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
     * Récupère les spécifications des colonnes Gevu_solutionsxcriteres 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_solution","champ"=>"id_solution","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_solutionsxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     */
    public function findByIdSolution($id_solution)
    {
        $query = $this->select()
        			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
                    ->from( array("g" => "gevu_solutionsxcriteres"),
                          array('id_solution', 'id_critere') )                           
                   ->joinInner(array('l' => 'gevu_criteres'),
                          'g.id_critere = l.id_critere','ref')
                   ->where( "g.id_solution = " . $id_solution );
                    
        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_solutionsxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_solutionsxcriteres") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
