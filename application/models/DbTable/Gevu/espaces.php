<?php
/**
 * Ce fichier contient la classe Gevu_espaces.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_espaces'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_espaces extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_espaces';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_espace';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_espaces existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_espace'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_espace; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_espaces.
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
     * Recherche une entrée Gevu_espaces avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_espaces.id_espace = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_espaces avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_espaces.id_espace = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_espaces avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_espaces" => "gevu_espaces") );
                    
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
     * Récupère les spécifications des colonnes Gevu_espaces 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_espace","champ"=>"id_espace","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"id_type_espace","champ"=>"id_type_espace","visible"=>true),
    	array("titre"=>"reponse_1","champ"=>"reponse_1","visible"=>true),
    	array("titre"=>"reponse_2","champ"=>"reponse_2","visible"=>true),
    	array("titre"=>"id_type_specifique_int","champ"=>"id_type_specifique_int","visible"=>true),
    	array("titre"=>"id_type_specifique_ext","champ"=>"id_type_specifique_ext","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_espace
     */
    public function findById_espace($id_espace)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_espace = ?", $id_espace );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_espace
     */
    public function findById_type_espace($id_type_espace)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_type_espace = ?", $id_type_espace );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_specifique_int
     */
    public function findById_type_specifique_int($id_type_specifique_int)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_type_specifique_int = ?", $id_type_specifique_int );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_specifique_ext
     */
    public function findById_type_specifique_ext($id_type_specifique_ext)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_type_specifique_ext = ?", $id_type_specifique_ext );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espaces avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espaces") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
