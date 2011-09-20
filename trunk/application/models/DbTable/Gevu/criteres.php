<?php
/**
 * Ce fichier contient la classe Gevu_criteres.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_criteres'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_criteres extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_criteres';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_critere';

    
    /**
     * Vérifie si une entrée Gevu_criteres existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_critere'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_critere; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_criteres.
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
     * Recherche une entrée Gevu_criteres avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_criteres.id_critere = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_criteres avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_criteres.id_critere = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_criteres avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        	->from( array("g" => "gevu_criteres"))
			->joinInner(array('l' => 'gevu_typesxcontroles'),
            	'g.id_type_controle = l.id_type_controle',array('LibTypControle'=>'lib'));
                    
                    
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
     * Récupère toutes les entrées Gevu_criteres pour les afficher dans une lsite
     * 
     */
    public function getListe()
    {
        $query = $this->select()
        	->from(array("g" => "gevu_criteres"), array('id_critere','criteres', "label"=>"CONCAT(UCASE(ref),' ',SUBSTRING(criteres,1,16))"));

        return $this->fetchAll($query)->toArray();
    }
        
    
    /**
     * Récupère les spécifications des colonnes Gevu_criteres 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_critere","champ"=>"id_critere","visible"=>true),
    	array("titre"=>"id_type_controle","champ"=>"id_type_controle","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"handicateur_moteur","champ"=>"handicateur_moteur","visible"=>true),
    	array("titre"=>"handicateur_auditif","champ"=>"handicateur_auditif","visible"=>true),
    	array("titre"=>"handicateur_visuel","champ"=>"handicateur_visuel","visible"=>true),
    	array("titre"=>"handicateur_cognitif","champ"=>"handicateur_cognitif","visible"=>true),
    	array("titre"=>"criteres","champ"=>"criteres","visible"=>true),
    	array("titre"=>"affirmation","champ"=>"affirmation","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.id_critere = " . $id_critere );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_controle
     */
    public function findByIdTypeControle($id_type_controle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.id_type_controle = " . $id_type_controle );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.ref = " . $ref );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $handicateur_moteur
     */
    public function findByHandicateur_moteur($handicateur_moteur)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.handicateur_moteur = " . $handicateur_moteur );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $handicateur_auditif
     */
    public function findByHandicateur_auditif($handicateur_auditif)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.handicateur_auditif = " . $handicateur_auditif );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $handicateur_visuel
     */
    public function findByHandicateur_visuel($handicateur_visuel)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.handicateur_visuel = " . $handicateur_visuel );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $handicateur_cognitif
     */
    public function findByHandicateur_cognitif($handicateur_cognitif)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.handicateur_cognitif = " . $handicateur_cognitif );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $criteres
     */
    public function findByCriteres($criteres)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.criteres = " . $criteres );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_criteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $affirmation
     */
    public function findByAffirmation($affirmation)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_criteres") )                           
                    ->where( "g.affirmation = " . $affirmation );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
