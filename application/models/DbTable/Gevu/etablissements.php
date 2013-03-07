<?php
/**
 * Ce fichier contient la classe Gevu_etablissements.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_etablissements'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_etablissements extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_etablissements';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_etablissement';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * retourn le nom de la table.
     *
     * @return string
     */
    public function getN($data)
    {
		$select = $this->select();
		$select->from($this, array('id_etablissement'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_etablissement; else $id=false;
        return $id;
    } 
        
    /**
     * Vérifie si une entrée Gevu_etablissements existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_etablissement'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_etablissement; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_etablissements.
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
     * Recherche une entrée Gevu_etablissements avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_etablissements.id_etablissement = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_batiments avec la clef de lieu
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
     * Recherche une entrée Gevu_etablissements avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_etablissements.id_etablissement = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_etablissements avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_etablissements" => "gevu_etablissements") );
                    
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
    
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_etablissement
     */
    public function findById_etablissement($id_etablissement)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.id_etablissement = ?", $id_etablissement );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $adresse
     */
    public function findByAdresse($adresse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.adresse = ?", $adresse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $commune
     */
    public function findByCommune($commune)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.commune = ?", $commune );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $pays
     */
    public function findByPays($pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.pays = ?", $pays );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $code_postal
     */
    public function findByCode_postal($code_postal)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.code_postal = ?", $code_postal );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_proprietaire
     */
    public function findByContact_proprietaire($contact_proprietaire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.contact_proprietaire = ?", $contact_proprietaire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_delegataire
     */
    public function findByContact_delegataire($contact_delegataire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.contact_delegataire = ?", $contact_delegataire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_3
     */
    public function findByReponse_3($reponse_3)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.reponse_3 = ?", $reponse_3 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_4
     */
    public function findByReponse_4($reponse_4)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.reponse_4 = ?", $reponse_4 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_5
     */
    public function findByReponse_5($reponse_5)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.reponse_5 = ?", $reponse_5 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_etablissements avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_etablissements") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
