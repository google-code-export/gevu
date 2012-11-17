<?php
/**
 * Ce fichier contient la classe Gevu_chainesdeplacements.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_chainesdeplacements'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_chainesdeplacements extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_chainesdeplacements';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_chainedepla';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_chainesdeplacements existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_chainedepla'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_chainedepla; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_chainesdeplacements.
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
     * Recherche une entrée Gevu_chainesdeplacements avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_chainesdeplacements.id_chainedepla = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_chainesdeplacements avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $dbT = new Models_DbTable_Gevu_lieuxchainedeplacements($this->_db);
        $dbT->delete('id_chainedepla = '.$id);
    	
    	$this->delete('gevu_chainesdeplacements.id_chainedepla = ' . $id);
    }

    /**
     * Récupère toutes les entrées Gevu_chainesdeplacements avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_chainesdeplacements" => "gevu_chainesdeplacements") );
                    
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
     * Recherche une entrée Gevu_chainesdeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_chainedepla
     *
     * @return array
     */
    public function findById_chainedepla($id_chainedepla)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_chainesdeplacements") )                           
                    ->where( "g.id_chainedepla = ?", $id_chainedepla );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_chainesdeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     *
     * @return array
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_chainesdeplacements") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_chainesdeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     *
     * @return array
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_chainesdeplacements") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
  
}
