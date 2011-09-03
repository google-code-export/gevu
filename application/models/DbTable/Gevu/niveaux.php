<?php
/**
 * Ce fichier contient la classe Gevu_niveaux.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_niveaux'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_niveaux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_niveaux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_niveau';

    
    /**
     * Vérifie si une entrée Gevu_niveaux existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_niveau'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_niveau; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_niveaux.
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
     * Recherche une entrée Gevu_niveaux avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_niveaux.id_niveau = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_niveaux avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_niveaux.id_niveau = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_niveaux avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_niveaux" => "gevu_niveaux") );
                    
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
     * Récupère les spécifications des colonnes Gevu_niveaux 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_niveau","champ"=>"id_niveau","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"id_reponse_1","champ"=>"id_reponse_1","visible"=>true),
    	array("titre"=>"id_reponse_2","champ"=>"id_reponse_2","visible"=>true),
    	array("titre"=>"id_reponse_3","champ"=>"id_reponse_3","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_niveau
     */
    public function findById_niveau($id_niveau)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_niveau = ?", $id_niveau );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse_1
     */
    public function findById_reponse_1($id_reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_reponse_1 = ?", $id_reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse_2
     */
    public function findById_reponse_2($id_reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_reponse_2 = ?", $id_reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse_3
     */
    public function findById_reponse_3($id_reponse_3)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_reponse_3 = ?", $id_reponse_3 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_niveaux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_niveaux") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
