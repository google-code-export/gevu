<?php
/**
 * Ce fichier contient la classe Gevu_contactsxantennes.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_contactsxantennes'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_contactsxantennes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_contactsxantennes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_contact';

    
    /**
     * Vérifie si une entrée Gevu_contactsxantennes existe.
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
     * Ajoute une entrée Gevu_contactsxantennes.
     *
     * @param array $params
     *  
     * @return integer
     */
    public function ajouterContact($params, $existe=true)
    {
    	$data = array("id_contact"=>$params['idCtc'], "id_antenne"=>$params['idLien']);
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_contactsxantennes avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_contactsxantennes.id_contact = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_contactsxantennes avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_contactsxantennes.id_contact = ' . $id);
    }

    /**
     * Recherche des entrées Gevu_contactsxantennes avec la clef primaire spécifiée
     * et supprime ces entrées.
     *
     * @param integer $id
     *
     * @return void
     */
    public function removeAntenne($id)
    {
        $this->delete('gevu_contactsxantennes.id_antenne = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_contactsxantennes avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_contactsxantennes" => "gevu_contactsxantennes") );
                    
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
     * Récupère les spécifications des colonnes Gevu_contactsxantennes 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_contact","champ"=>"id_contact","visible"=>true),
    	array("titre"=>"id_antenne","champ"=>"id_antenne","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_contactsxantennes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
			->from( array("ca" => "gevu_contactsxantennes") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('c' => 'gevu_contacts'),
                'ca.id_contact = c.id_contact',array('nom','prenom'))
            ->where( "ca.id_contact = ?", $id_contact );
                                
        return $this->fetchRow($query)->toArray(); 
    }
    /**
     * Recherche une entrée Gevu_contactsxantennes avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param array $params
     */
    public function getContact($params)
    {
        $query = $this->select()
			->from( array("ca" => "gevu_contactsxantennes") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('c' => 'gevu_contacts'),
                'ca.id_contact = c.id_contact',array('nom','prenom','fixe','mobile','fax','mail'))
    		->where( "ca.id_antenne = ?", $params["id"] );

        return $this->fetchAll($query)->toArray();
    }
    
    /**
     * Recherche une entrée Gevu_contactsxantennes avec la valeur spécifiée
     * et supprime cette entrée.
     *
     * @param array $params
     */
    public function removeContact($params)
    {
        $this->delete('id_contact = ' . $params["idCtc"].' AND id_antenne = ' . $params["idLien"]);
    }
    
}
