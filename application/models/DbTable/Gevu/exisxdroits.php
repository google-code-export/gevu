<?php
/**
 * Ce fichier contient la classe Gevu_exisxdroits.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_exisxdroits'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_exisxdroits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_exisxdroits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = array('id_exi','id_droit');

    
    /**
     * Vérifie si une entrée Gevu_exisxdroits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($idExi, $idDroit)
    {
		$select = $this->select();
		$select->from($this, array('id_exi'));
		$select->where('id_exi = ?', $idExi);
		$select->where('id_droit = ?', $idDroit);
		$rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_exi; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_exisxdroits.
     *
     * @param integer $idDroit
     * @param integer $idExi
     *  
     * @return integer
     */
    public function ajouter($idExi, $idDroit, $existe=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($idExi, $idDroit);
    	if(!$id){
    	 	$id = $this->insert(array("id_exi"=>$idExi, "id_droit"=>$idDroit));
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $idDroit
     * @param integer $idExi
     * @param string $data
     *
     * @return void
     */
    public function edit($idExi, $idDroit, $data)
    {        
        $this->update($data, 'gevu_exisxdroits.id_exi = '.$idExi.' AND gevu_exisxdroits.id_droit = '.$idDroit);
    }
    
    /**
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_exisxdroits.id_exi = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_exisxdroits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_exisxdroits" => "gevu_exisxdroits") );
                    
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
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findByIdExi($id_exi)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("ed" => "gevu_exisxdroits"))                           
            ->joinInner(array('d' => 'gevu_droits'),
            	'd.id_droit = ed.id_droit',array("id_droit", "lib"))
			->where( "ed.id_exi = ?", $id_exi );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_droit
     */
    public function findById_droit($id_droit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.id_droit = ?", $id_droit );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
