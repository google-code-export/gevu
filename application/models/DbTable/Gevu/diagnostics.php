<?php
/**
 * Ce fichier contient la classe Gevu_diagnostics.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_diagnostics'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_diagnostics extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_diagnostics';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_diag';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_diagnostics existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_diag'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_diag; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_diagnostics.
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
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_diagnostics avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_diagnostics" => "gevu_diagnostics") );
                    
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
     * Récupère les spécifications des colonnes Gevu_diagnostics 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_diag","champ"=>"id_diag","visible"=>true),
    	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
    	array("titre"=>"id_reponse","champ"=>"id_reponse","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diag
     */
    public function findById_diag($id_diag)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_diag = ?", $id_diag );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse
     */
    public function findById_reponse($id_reponse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_reponse = ?", $id_reponse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
