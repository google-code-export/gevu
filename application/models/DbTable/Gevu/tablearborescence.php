<?php
/**
 * Ce fichier contient la classe Gevu_lieux.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieux'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_tablearborescence extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_tablearborescence';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_arbo';

    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_table
     */
    public function findById_table($id_table)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_tablearborescence") )                           
                    ->where( "g.id_table = ?", $id_table );

        return $this->fetchAll($query)->toArray(); 
    }
}
