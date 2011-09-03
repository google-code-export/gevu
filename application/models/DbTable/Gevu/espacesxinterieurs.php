<?php
/**
 * Ce fichier contient la classe Gevu_espacesxinterieurs.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_espacesxinterieurs'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_espacesxinterieurs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_espacesxinterieurs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_espace_int';

    
    /**
     * Vérifie si une entrée Gevu_espacesxinterieurs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_espace_int'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_espace_int; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_espacesxinterieurs.
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
     * Recherche une entrée Gevu_espacesxinterieurs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_espacesxinterieurs.id_espace_int = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_espacesxinterieurs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_espacesxinterieurs.id_espace_int = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_espacesxinterieurs avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_espacesxinterieurs" => "gevu_espacesxinterieurs") );
                    
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
     * Récupère les spécifications des colonnes Gevu_espacesxinterieurs 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_espace_int","champ"=>"id_espace_int","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"fonction","champ"=>"fonction","visible"=>true),
    	array("titre"=>"id_type_specifique_int","champ"=>"id_type_specifique_int","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_espace_int
     */
    public function findById_espace_int($id_espace_int)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.id_espace_int = ?", $id_espace_int );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fonction
     */
    public function findByFonction($fonction)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.fonction = ?", $fonction );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_specifique_int
     */
    public function findById_type_specifique_int($id_type_specifique_int)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.id_type_specifique_int = ?", $id_type_specifique_int );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_espacesxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_espacesxinterieurs") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
