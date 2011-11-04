<?php
/**
 * Ce fichier contient la classe Gevu_batiments.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_batiments'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_batiments extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_batiments';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_batiment';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_batiments existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_batiment'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_batiment; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_batiments.
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
     * Recherche une entrée Gevu_batiments avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_batiments.id_batiment = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_batiments avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_batiments.id_batiment = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_batiments avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_batiments" => "gevu_batiments") );
                    
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
     * Récupère les spécifications des colonnes Gevu_batiments 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_batiment","champ"=>"id_batiment","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"adresse","champ"=>"adresse","visible"=>true),
    	array("titre"=>"commune","champ"=>"commune","visible"=>true),
    	array("titre"=>"pays","champ"=>"pays","visible"=>true),
    	array("titre"=>"code_postal","champ"=>"code_postal","visible"=>true),
    	array("titre"=>"contact_proprietaire","champ"=>"contact_proprietaire","visible"=>true),
    	array("titre"=>"contact_delegataire","champ"=>"contact_delegataire","visible"=>true),
    	array("titre"=>"contact_gardien","champ"=>"contact_gardien","visible"=>true),
    	array("titre"=>"horaires_gardien","champ"=>"horaires_gardien","visible"=>true),
    	array("titre"=>"horaires_batiment","champ"=>"horaires_batiment","visible"=>true),
    	array("titre"=>"superficie_parcelle","champ"=>"superficie_parcelle","visible"=>true),
    	array("titre"=>"superficie_batiment","champ"=>"superficie_batiment","visible"=>true),
    	array("titre"=>"date_achevement","champ"=>"date_achevement","visible"=>true),
    	array("titre"=>"date_depot_permis","champ"=>"date_depot_permis","visible"=>true),
    	array("titre"=>"date_reha","champ"=>"date_reha","visible"=>true),
    	array("titre"=>"reponse_1","champ"=>"reponse_1","visible"=>true),
    	array("titre"=>"reponse_2","champ"=>"reponse_2","visible"=>true),
    	array("titre"=>"reponse_3","champ"=>"reponse_3","visible"=>true),
    	array("titre"=>"reponse_4","champ"=>"reponse_4","visible"=>true),
    	array("titre"=>"reponse_5","champ"=>"reponse_5","visible"=>true),
    	array("titre"=>"reponse_6","champ"=>"reponse_6","visible"=>true),
    	array("titre"=>"reponse_7","champ"=>"reponse_7","visible"=>true),
    	array("titre"=>"reponse_8","champ"=>"reponse_8","visible"=>true),
    	array("titre"=>"reponse_9","champ"=>"reponse_9","visible"=>true),
    	array("titre"=>"reponse_10","champ"=>"reponse_10","visible"=>true),
    	array("titre"=>"reponse_11","champ"=>"reponse_11","visible"=>true),
    	array("titre"=>"reponse_12","champ"=>"reponse_12","visible"=>true),
    	array("titre"=>"reponse_13","champ"=>"reponse_13","visible"=>true),
    	array("titre"=>"reponse_14","champ"=>"reponse_14","visible"=>true),
    	array("titre"=>"reponse_15","champ"=>"reponse_15","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_batiment
     */
    public function findById_batiment($id_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_batiment = ?", $id_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $adresse
     */
    public function findByAdresse($adresse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.adresse = ?", $adresse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $commune
     */
    public function findByCommune($commune)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.commune = ?", $commune );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $pays
     */
    public function findByPays($pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.pays = ?", $pays );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $code_postal
     */
    public function findByCode_postal($code_postal)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.code_postal = ?", $code_postal );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_proprietaire
     */
    public function findByContact_proprietaire($contact_proprietaire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_proprietaire = ?", $contact_proprietaire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_delegataire
     */
    public function findByContact_delegataire($contact_delegataire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_delegataire = ?", $contact_delegataire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_gardien
     */
    public function findByContact_gardien($contact_gardien)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_gardien = ?", $contact_gardien );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $horaires_gardien
     */
    public function findByHoraires_gardien($horaires_gardien)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.horaires_gardien = ?", $horaires_gardien );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $horaires_batiment
     */
    public function findByHoraires_batiment($horaires_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.horaires_batiment = ?", $horaires_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $superficie_parcelle
     */
    public function findBySuperficie_parcelle($superficie_parcelle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.superficie_parcelle = ?", $superficie_parcelle );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $superficie_batiment
     */
    public function findBySuperficie_batiment($superficie_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.superficie_batiment = ?", $superficie_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_achevement
     */
    public function findByDate_achevement($date_achevement)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_achevement = ?", $date_achevement );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_depot_permis
     */
    public function findByDate_depot_permis($date_depot_permis)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_depot_permis = ?", $date_depot_permis );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_reha
     */
    public function findByDate_reha($date_reha)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_reha = ?", $date_reha );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_3
     */
    public function findByReponse_3($reponse_3)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_3 = ?", $reponse_3 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_4
     */
    public function findByReponse_4($reponse_4)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_4 = ?", $reponse_4 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_5
     */
    public function findByReponse_5($reponse_5)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_5 = ?", $reponse_5 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_6
     */
    public function findByReponse_6($reponse_6)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_6 = ?", $reponse_6 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_7
     */
    public function findByReponse_7($reponse_7)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_7 = ?", $reponse_7 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_8
     */
    public function findByReponse_8($reponse_8)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_8 = ?", $reponse_8 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_9
     */
    public function findByReponse_9($reponse_9)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_9 = ?", $reponse_9 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_10
     */
    public function findByReponse_10($reponse_10)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_10 = ?", $reponse_10 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_11
     */
    public function findByReponse_11($reponse_11)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_11 = ?", $reponse_11 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_12
     */
    public function findByReponse_12($reponse_12)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_12 = ?", $reponse_12 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_13
     */
    public function findByReponse_13($reponse_13)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_13 = ?", $reponse_13 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_14
     */
    public function findByReponse_14($reponse_14)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_14 = ?", $reponse_14 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_15
     */
    public function findByReponse_15($reponse_15)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_15 = ?", $reponse_15 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
