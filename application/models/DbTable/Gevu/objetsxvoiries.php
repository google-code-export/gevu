<?php
/**
 * Ce fichier contient la classe Gevu_objetsxvoiries.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_objetsxvoiries'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_objetsxvoiries extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_objetsxvoiries';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_objet_voirie';

    
    /**
     * Vérifie si une entrée Gevu_objetsxvoiries existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_objet_voirie'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_objet_voirie; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_objetsxvoiries.
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
     * Recherche une entrée Gevu_objetsxvoiries avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_objetsxvoiries.id_objet_voirie = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_objetsxvoiries avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_objetsxvoiries.id_objet_voirie = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_objetsxvoiries avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_objetsxvoiries" => "gevu_objetsxvoiries") );
                    
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
     * Récupère les spécifications des colonnes Gevu_objetsxvoiries 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_objet_voirie","champ"=>"id_objet_voirie","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"id_type_objet_voirie","champ"=>"id_type_objet_voirie","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_objet_voirie
     */
    public function findById_objet_voirie($id_objet_voirie)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.id_objet_voirie = ?", $id_objet_voirie );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $id_type_objet_voirie
     */
    public function findById_type_objet_voirie($id_type_objet_voirie)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.id_type_objet_voirie = ?", $id_type_objet_voirie );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxvoiries avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxvoiries") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
