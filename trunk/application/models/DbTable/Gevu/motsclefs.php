<?php
/**
 * Ce fichier contient la classe Gevu_motsclefs.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_motsclefs'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_motsclefs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_motsclefs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_motclef';

    
    
    /*
     * Recherche une entrée gevu_motsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_mot
     */
    public function findById_motclef($id_mot)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_motsclefs") )                           
                    ->where( "g.id_motclef = ?", $id_mot );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Recherche une entrée gevu_motsclefs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $type
     */
public function getAllByType($type)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_motsclefs") )                           
                    ->where( "g.type = ?", $type );

        return $this->fetchAll($query)->toArray(); 
    }
}
