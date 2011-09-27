<?php
/**
 * Ce fichier contient la classe Gevu_entreprises.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_entreprises'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_entreprises extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_entreprises';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_entreprise';

    
    /**
     * Vérifie si une entrée Gevu_entreprises existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_entreprise'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_entreprise; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_entreprises.
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
     * Recherche une entrée Gevu_entreprises avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_entreprises.id_entreprise = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_entreprises avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_entreprises.id_entreprise = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_entreprises avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_entreprises" => "gevu_entreprises") );
                    
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
     * Récupère les spécifications des colonnes Gevu_entreprises 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_entreprise","champ"=>"id_entreprise","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"num","champ"=>"num","visible"=>true),
    	array("titre"=>"voie","champ"=>"voie","visible"=>true),
    	array("titre"=>"code_postal","champ"=>"code_postal","visible"=>true),
    	array("titre"=>"ville","champ"=>"ville","visible"=>true),
    	array("titre"=>"pays","champ"=>"pays","visible"=>true),
    	array("titre"=>"telephone","champ"=>"telephone","visible"=>true),
    	array("titre"=>"fax","champ"=>"fax","visible"=>true),
    	array("titre"=>"mail","champ"=>"mail","visible"=>true),
    	array("titre"=>"observations","champ"=>"observations","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_entreprise
     */
    public function findById_entreprise($id_entreprise)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.id_entreprise = " . $id_entreprise );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.nom = " . $nom );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $num
     */
    public function findByNum($num)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.num = " . $num );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $voie
     */
    public function findByVoie($voie)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.voie = " . $voie );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $code_postal
     */
    public function findByCode_postal($code_postal)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.code_postal = " . $code_postal );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ville
     */
    public function findByVille($ville)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.ville = " . $ville );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $pays
     */
    public function findByPays($pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.pays = " . $pays );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $telephone
     */
    public function findByTelephone($telephone)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.telephone = " . $telephone );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fax
     */
    public function findByFax($fax)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.fax = " . $fax );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mail
     */
    public function findByMail($mail)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.mail = " . $mail );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_entreprises avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $observations
     */
    public function findByObservations($observations)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_entreprises") )                           
                    ->where( "g.observations = " . $observations );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
