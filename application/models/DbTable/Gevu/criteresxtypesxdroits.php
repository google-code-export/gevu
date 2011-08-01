<?php
/**
 * Ce fichier contient la classe Gevu_criteresxtypesxdroits.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_criteresxtypesxdroits'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_criteresxtypesxdroits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_criteresxtypesxdroits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_droit';

    
    /**
     * Vérifie si une entrée Gevu_criteresxtypesxdroits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_droit'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_droit; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_criteresxtypesxdroits.
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
    	$data = array("id_critere"=>$idCritere,"id_type_droit"=>$idType);
       	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_criteresxtypesxdroits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_criteresxtypesxdroits.id_type_droit = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_criteresxtypesxdroits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $idCrit
     * @param integer $idDroit
     *
     * @return void
     */
    public function remove($idCrit, $idDroit)
    {
        $this->delete('gevu_criteresxtypesxdroits.id_type_droit = ' . $idDroit.' AND gevu_criteresxtypesxdroits.id_critere = '.$idCrit);
    }

    /**
     * Récupère toutes les entrées Gevu_criteresxtypesxdroits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_criteresxtypesxdroits" => "gevu_criteresxtypesxdroits") );
                    
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
     * Récupère les spécifications des colonnes Gevu_criteresxtypesxdroits 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_droit","champ"=>"id_type_droit","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_criteresxtypesxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_droit
     */
    public function findById_type_droit($id_type_droit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteresxtypesxdroits") )                           
                    ->where( "g.id_type_droit = " . $id_type_droit );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteresxtypesxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findByIdCritere($id_critere)
    {
        $query = $this->select()
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_criteresxtypesxdroits"),
            	array('id_critere', 'id_type_droit') )                           
            ->joinInner(array('l' => 'gevu_typesxdroits'),
            	'g.id_type_droit = l.id_type_droit','lib')
            ->where( "g.id_critere = " . $id_critere);
        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
