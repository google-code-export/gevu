<?php
/**
 * Ce fichier contient la classe Gevu_parcelles.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_parcelles'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_parcelles extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_parcelles';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_parcelle';

    
    /**
     * Vérifie si une entrée Gevu_parcelles existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_parcelle'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_parcelle; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_parcelles.
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
     * Recherche une entrée Gevu_parcelles avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_parcelles.id_parcelle = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_parcelles avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_parcelles.id_parcelle = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_parcelles avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_parcelles" => "gevu_parcelles") );
                    
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
     * Récupère les spécifications des colonnes Gevu_parcelles 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_parcelle","champ"=>"id_parcelle","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"adresse","champ"=>"adresse","visible"=>true),
    	array("titre"=>"commune","champ"=>"commune","visible"=>true),
    	array("titre"=>"pays","champ"=>"pays","visible"=>true),
    	array("titre"=>"code_postal","champ"=>"code_postal","visible"=>true),
    	array("titre"=>"contact_proprietaire","champ"=>"contact_proprietaire","visible"=>true),
    	array("titre"=>"reponse_1","champ"=>"reponse_1","visible"=>true),
    	array("titre"=>"reponse_2","champ"=>"reponse_2","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_parcelle
     */
    public function findById_parcelle($id_parcelle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.id_parcelle = ?", $id_parcelle );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $adresse
     */
    public function findByAdresse($adresse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.adresse = ?", $adresse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $commune
     */
    public function findByCommune($commune)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.commune = ?", $commune );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $pays
     */
    public function findByPays($pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.pays = ?", $pays );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $code_postal
     */
    public function findByCode_postal($code_postal)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.code_postal = ?", $code_postal );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_proprietaire
     */
    public function findByContact_proprietaire($contact_proprietaire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.contact_proprietaire = ?", $contact_proprietaire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_parcelles avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_parcelles") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
