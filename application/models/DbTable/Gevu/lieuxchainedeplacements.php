<?php
/**
 * Ce fichier contient la classe Gevu_lieuxchainedeplacements.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieuxchainedeplacements'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_lieuxchainedeplacements extends Zend_Db_Table_Abstract
{
    
    /**
     * Nom de la table.
     */
    protected $_name = 'gevu_lieuxchainedeplacements';
    
    /**
     * Clef primaire de la table.
     */
    protected $_primary = 'id_lieuchainedepla';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_lieuxchainedeplacements existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_lieuchainedepla'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_lieuchainedepla; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_lieuxchainedeplacements.
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
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_lieuxchainedeplacements.id_lieuchainedepla = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_lieuxchainedeplacements.id_lieuchainedepla = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_lieuxchainedeplacements avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_lieuxchainedeplacements avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_lieuxchainedeplacements" => "gevu_lieuxchainedeplacements") );
                    
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
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_chainedepla
     *
     * @return array
     */
    public function findById_chainedepla($id_chainedepla)
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('cd' => 'gevu_chainesdeplacements'),array())
            ->joinInner(array('lcd' => 'gevu_lieuxchainedeplacements'),
                'lcd.id_chainedepla = cd.id_chainedepla',array('ordre'))
            ->joinInner(array('l' => 'gevu_lieux'),
                'l.id_lieu = lcd.id_lieu',array('lib','id_lieu'))
            ->joinInner(array('g' => 'gevu_geos'),
                'g.id_lieu = l.id_lieu',array('lat', 'lng', 'adresse'))
            ->where( "cd.id_chainedepla = ?", $id_chainedepla)
            ->order("lcd.ordre");        
        $result = $this->fetchAll($query);
        return $result->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxchainedeplacements") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lft
     *
     * @return array
     */
    public function findByLft($lft)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxchainedeplacements") )                           
                    ->where( "g.lft = ?", $lft );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $rgt
     *
     * @return array
     */
    public function findByRgt($rgt)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxchainedeplacements") )                           
                    ->where( "g.rgt = ?", $rgt );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $niv
     *
     * @return array
     */
    public function findByNiv($niv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxchainedeplacements") )                           
                    ->where( "g.niv = ?", $niv );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_lieuxchainedeplacements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieuchainedepla
     *
     * @return array
     */
    public function findById_lieuchainedepla($id_lieuchainedepla)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieuxchainedeplacements") )                           
                    ->where( "g.id_lieuchainedepla = ?", $id_lieuchainedepla );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
