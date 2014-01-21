<?php
/**
 * Ce fichier contient la classe Gevu_lieux.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_docsxlieux'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_docsxlieux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_docsxlieux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_doc';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	    
    
    /**
     * Recherche les entrées de Gevu_batiments avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
        $this->delete('id_lieu = ' . $idLieu);
    }
        
    /**
     * supprime une entrée
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('id_doc = ' . $id);
    }
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idLieu
     * @param string $type
     * 
     * return array
     */
    public function findByIdLieu($idLieu, $types="")
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("dl" => "gevu_docsxlieux") )                           
        	->joinInner(array('d' => 'gevu_docs'),
            	'd.id_doc = dl.id_doc',array('titre','content_type','url','path_source','branche'))
        	->where( "dl.id_lieu = ?", $idLieu)
        	->group("d.id_doc");
        if($types){
        	$query->where( "d.content_type IN (".$types.")");
        }            
                    
        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Ajoute un document à une entrée gevu_docsxlieux.
     *
     * @param array $data
     *  
     * @return integer
     */
    public function ajouter($data)
    {
   	 	$id = $this->insert($data);

    	return $id;
    } 
    
}
