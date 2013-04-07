<?php
/**
 * Ce fichier contient la classe Gevu_docsxrapports.
 *
 * @copyright  2013 Samuel Szoniecky
 * @license    "New" BSD License
*/
class Models_DbTable_Gevu_docsxrapports extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_docsxrapports';
    
    /**
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
     * Vérifie si une entrée Gevu_docsxrapports existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_doc'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_doc; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_docsxrapports.
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
     * Recherche une entrée Gevu_docsxrapports avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_docsxrapports.id_doc = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_docsxrapports avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_docsxrapports.id_doc = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_docsxrapports avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_docsxrapports avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_docsxrapports" => "gevu_docsxrapports") );
                    
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
     * Recherche une entrée Gevu_docsxrapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_doc
     *
     * @return array
     */
    public function findById_doc($id_doc)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docsxrapports") )                           
                    ->where( "g.id_doc = ?", $id_doc );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_docsxrapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_rapport
     *
     * @return array
     */
    public function findById_rapport($id_rapport)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docsxrapports") )                           
                    ->where( "g.id_rapport = ?", $id_rapport );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_docsxrapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docsxrapports") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
