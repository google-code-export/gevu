<?php
/**
 * Ce fichier contient la classe Gevu_typesxcontroles.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_typesxcontroles'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_typesxcontroles extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_typesxcontroles';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_controle';

    
    /**
     * Vérifie si une entrée Gevu_typesxcontroles existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_controle'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_controle; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_typesxcontroles.
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
     * Recherche une entrée Gevu_typesxcontroles avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_typesxcontroles.id_type_controle = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_typesxcontroles avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_typesxcontroles.id_type_controle = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_typesxcontroles avec certains critères
     * de tri, intervalles
     */
    public function getAll($order="lib", $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_typesxcontroles" => "gevu_typesxcontroles") );
                    
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
     * Recherche une entrée Gevu_typesxcontroles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_controle
     */
    public function findById_type_controle($id_type_controle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typesxcontroles") )                           
                    ->where( "g.id_type_controle = " . $id_type_controle );

        return $this->fetchRow($query)->toArray(); 
    }
    
    /**
     * Recherche une entrée Gevu_typesxcontroles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idLieu
     * @param int $id_type_controle
     */
    public function findParentById_type_controle($idLieu, $id_type_controle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typesxcontroles") )                           
                    ->where( "g.id_type_controle = " . $id_type_controle );

        return $this->fetchRow($query)->toArray(); 
    }
    
    /*
     * Recherche une entrée Gevu_typesxcontroles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
			->from( array("g" => "gevu_typesxcontroles") )                           
            ->where( "g.lib = " . $lib );
        return $this->fetchRow($query)->toArray(); 
    }  
    
}
