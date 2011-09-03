<?php
/**
 * Ce fichier contient la classe Gevu_metiersxsolutions.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_metiersxsolutions'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_metiersxsolutions extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_metiersxsolutions';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_solution';
    
    /**
     * Recherche une entrée Gevu_metiersxsolutions avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update('gevu_metiersxsolutions', $data, 'gevu_metiersxsolutions.id_solution = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_metiersxsolutions avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_metiersxsolutions', 'gevu_metiersxsolutions.id_solution = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_metiersxsolutions avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("%ftable%" => "gevu_metiersxsolutions") );
                    
        if($order != null)
        {
            $query->order($order);
        }

        if($limit != 0)
        {
            $query->limit($limit, $from);
        }

        return $this->fetchAll($query);
    }

    /**
     * Récupère les spécifications des colonnes Gevu_metiersxsolutions 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   /**/
	array("titre"=>"id_solution","champ"=>"id_solution","visible"=>true),

    /**/
	array("titre"=>"id_metier","champ"=>"id_metier","visible"=>true),

        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_metiersxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     */
    public function findById_solution($id_solution)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_metiersxsolutions") )                           
                    ->where( "g.id_solution = " . $id_solution );

        return $this->fetchRow($query); 
    }
    /*
     * Recherche une entrée Gevu_metiersxsolutions avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_metier
     */
    public function findById_metier($id_metier)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_metiersxsolutions") )                           
                    ->where( "g.id_metier = " . $id_metier );

        return $this->fetchRow($query); 
    }
    
    
}
