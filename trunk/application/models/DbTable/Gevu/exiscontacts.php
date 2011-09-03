<?php
/**
 * Ce fichier contient la classe Gevu_exiscontacts.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_exiscontacts'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_exiscontacts extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_exiscontacts';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_exi';

    
    /**
     * Vérifie si une entrée Gevu_exiscontacts existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_exi'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_exi; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_exiscontacts.
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
     * Recherche une entrée Gevu_exiscontacts avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_exiscontacts.id_exi = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_exiscontacts avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_exiscontacts.id_exi = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_exiscontacts avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_exiscontacts" => "gevu_exiscontacts") );
                    
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
     * Recherche une entrée Gevu_exiscontacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findByIdExi($id_exi)
    {
        $query = $this->select()                           
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("ec" => "gevu_exiscontacts") )
            ->joinInner(array('c' => 'gevu_contacts'),
            	'c.id_contact = ec.id_contact')
			->where( "ec.id_exi = ?", $id_exi );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exiscontacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exiscontacts") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
