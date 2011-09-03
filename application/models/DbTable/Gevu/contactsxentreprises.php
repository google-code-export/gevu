<?php
/**
 * Ce fichier contient la classe Gevu_contactsxentreprises.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_contactsxentreprises'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_contactsxentreprises extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_contactsxentreprises';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_contact';

    
    /**
     * Vérifie si une entrée Gevu_contactsxentreprises existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_contact'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_contact; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_contactsxentreprises.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_contactsxentreprises avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_contactsxentreprises.id_contact = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_contactsxentreprises avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_contactsxentreprises.id_contact = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_contactsxentreprises avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_contactsxentreprises" => "gevu_contactsxentreprises") );
                    
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
     * Récupère les spécifications des colonnes Gevu_contactsxentreprises 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_contact","champ"=>"id_contact","visible"=>true),
    	array("titre"=>"id_entreprise","champ"=>"id_entreprise","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_contactsxentreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contactsxentreprises") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contactsxentreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_entreprise
     */
    public function findById_entreprise($id_entreprise)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contactsxentreprises") )                           
                    ->where( "g.id_entreprise = ?", $id_entreprise );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
