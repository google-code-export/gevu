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
class Models_DbTable_Gevu_parcelles extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_parcelles';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_parcelle';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
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
     * Récupère ou Ajoute une entrée avec le lieu associé.
     *
     * @param string $ref
     * @param int $idInst
     * @param int $idLieuParent
     * @param string $lib
     * @param array $data
     *  
     * @return integer
     */
    public function getByRef($ref, $idInst, $idLieuParent, $lib="", $data=false)
    {    	
		//vérification de l'existence de l'antenne
	    $arr = $this->findByRef($ref);
	    if(count($arr)==0){
	    	if($lib=="")$lib="Parcelle - ".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, false, $lib, true, false);
	    	$data["id_lieu"] = $idLieu;
	    	$data["id_instant"] = $idInst;
	    	$data["ref"] = $ref;
	    	unset($data["id_parcelle"]);	  	
	    	$this->ajouter($data);
	    	$arr = $this->findByRef($ref);
	    }
    	return $arr;
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
    
    /**
     * Recherche un contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param array $params
     * 
	 * @return array
     */
    public function getContact($params)
    {
        $query = $this->select()
			->from( array("b" => "gevu_parcelles"), array($params["type"]))                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinInner(array('c' => 'gevu_contacts'),
                'b.'.$params["type"].' = c.id_contact',array('id_contact','nom','prenom','fixe','mobile','fax','mail'))
    		->where( "b.id_parcelle = ?", $params["id"]);

        return $this->fetchAll($query)->toArray();
    }

    /**
     * Ajoute un contacts avec la valeur spécifiée
     *
     * @param array $params
     *  
     */
    public function ajouterContact($params)
    {
    	$data = array($params['type']=>$params['idCtc']);
		$this->update($data, 'gevu_parcelles.id_parcelle = ' . $params['idLien']);
    } 

    /**
     * Supprime un contact avec la valeur spécifiée
     *
     * @param array $params
     */
    public function removeContact($params)
    {
    	$data = array($params['type']=>-1);
		$this->update($data, 'gevu_parcelles.id_parcelle = ' . $params['idLien']);
    }
        
    
}
