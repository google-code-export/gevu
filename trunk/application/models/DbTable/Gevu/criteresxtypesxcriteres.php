<?php
/**
 * Ce fichier contient la classe Gevu_criteresxtypesxcriteres.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_criteresxtypesxcriteres'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_criteresxtypesxcriteres extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_criteresxtypesxcriteres';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_critere';

    
    /**
     * Vérifie si une entrée Gevu_criteresxtypesxcriteres existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_critere'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_critere; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_criteresxtypesxcriteres.
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
    	$data = array("id_critere"=>$idCritere,"id_type_critere"=>$idType);
       	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_criteresxtypesxcriteres avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_criteresxtypesxcriteres.id_type_critere = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_criteresxtypesxcriteres avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $idType
     * @param integer $idCrit
     *
     * @return void
     */
    public function remove($idCrit, $idType)
    {
        $this->delete('gevu_criteresxtypesxcriteres.id_type_critere = ' . $idType.' AND gevu_criteresxtypesxcriteres.id_critere ='.$idCrit );
    }
    
    /**
     * Récupère toutes les entrées Gevu_criteresxtypesxcriteres avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_criteresxtypesxcriteres" => "gevu_criteresxtypesxcriteres") );
                    
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
     * Récupère les spécifications des colonnes Gevu_criteresxtypesxcriteres 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_critere","champ"=>"id_type_critere","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_criteresxtypesxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_critere
     */
    public function findById_type_critere($id_type_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteresxtypesxcriteres") )                           
                    ->where( "g.id_type_critere = " . $id_type_critere );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteresxtypesxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findByIdCritere($id_critere)
    {
        $query = $this->select()
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_criteresxtypesxcriteres"),
            	array('id_critere', 'id_type_critere') )                           
            ->joinInner(array('l' => 'gevu_typesxcriteres'),
            	'g.id_type_critere = l.id_type_critere','lib')
            ->where( "g.id_critere = " . $id_critere);
        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
