<?php
/**
 * Ce fichier contient la classe Gevu_criteresxtypesxdeficiences.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_criteresxtypesxdeficiences'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_criteresxtypesxdeficiences extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_criteresxtypesxdeficiences';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_deficience';

    
    /**
     * Vérifie si une entrée Gevu_criteresxtypesxdeficiences existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_deficience'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_deficience; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_criteresxtypesxdeficiences.
     *
     * @param int $idCritere
     * @param int $idType
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($idCritere, $idType, $existe=true)
    {
    	$id=false;
    	$data = array("id_critere"=>$idCritere,"id_type_deficience"=>$idType);
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_criteresxtypesxdeficiences avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_criteresxtypesxdeficiences.id_type_deficience = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_criteresxtypesxdeficiences avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $idDef
     * @param integer $idCrit
     *
     * @return void
     */
    public function remove($idCrit, $idDef)
    {
        $this->delete('gevu_criteresxtypesxdeficiences.id_type_deficience = ' . $idDef.' AND gevu_criteresxtypesxdeficiences.id_critere = '.$idCrit);
    }
    
    /**
     * Récupère toutes les entrées Gevu_criteresxtypesxdeficiences avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_criteresxtypesxdeficiences" => "gevu_criteresxtypesxdeficiences") );
                    
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
     * Récupère les spécifications des colonnes Gevu_criteresxtypesxdeficiences 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_deficience","champ"=>"id_type_deficience","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_criteresxtypesxdeficiences avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_deficience
     */
    public function findById_type_deficience($id_type_deficience)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteresxtypesxdeficiences") )                           
                    ->where( "g.id_type_deficience = " . $id_type_deficience );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteresxtypesxdeficience avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findByIdCritere($id_critere)
    {
        $query = $this->select()
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_criteresxtypesxdeficiences"),
            	array('id_critere', 'id_type_deficience') )                           
            ->joinInner(array('l' => 'gevu_typesxdeficiences'),
            	'g.id_type_deficience = l.id_type_deficience','lib')
            ->where( "g.id_critere = " . $id_critere);
        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
