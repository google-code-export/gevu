<?php
/**
 * Ce fichier contient la classe Gevu_objetsxinterieurs.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_objetsxinterieurs'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_objetsxinterieurs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_objetsxinterieurs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_objet_int';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
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
     * Vérifie si une entrée Gevu_objetsxinterieurs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_objet_int'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_objet_int; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_objetsxinterieurs.
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
     * Recherche une entrée Gevu_objetsxinterieurs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_objetsxinterieurs.id_objet_int = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_objetsxinterieurs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_objetsxinterieurs.id_objet_int = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_objetsxinterieurs avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_objetsxinterieurs" => "gevu_objetsxinterieurs") );
                    
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
     * Récupère les spécifications des colonnes Gevu_objetsxinterieurs 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_objet_int","champ"=>"id_objet_int","visible"=>true),
    	array("titre"=>"id_lieu","champ"=>"id_lieu","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"ref","champ"=>"ref","visible"=>true),
    	array("titre"=>"fonctions","champ"=>"fonctions","visible"=>true),
    	array("titre"=>"reponse_1","champ"=>"reponse_1","visible"=>true),
    	array("titre"=>"reponse_2","champ"=>"reponse_2","visible"=>true),
    	array("titre"=>"id_type_objet","champ"=>"id_type_objet","visible"=>true),
    	array("titre"=>"id_donnee","champ"=>"id_donnee","visible"=>true),
    	array("titre"=>"maj","champ"=>"maj","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_objet_int
     */
    public function findById_objet_int($id_objet_int)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.id_objet_int = ?", $id_objet_int );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fonctions
     */
    public function findByFonctions($fonctions)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.fonctions = ?", $fonctions );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $id_type_objet
     */
    public function findById_type_objet($id_type_objet)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.id_type_objet = ?", $id_type_objet );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_objetsxinterieurs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_objetsxinterieurs") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }

    /**
     * Recherche les type de controle autorisés
     * et retourne ces entrées.
     *
     * @param int $idScenar
     * @param int $idLieu
     * 
     */
    public function getTypeControle($idScenar, $idLieu)
    {
    	$diag = new GEVU_Diagnostique();
    	$arrCtl = $diag->getLieuCtl($idLieu, $idScenar, false, "[@idCtrl='58']/node");
        return $arrCtl; 
    }
    

    /**
     * Création des diagnostiques
     *
     * @param int $idExi
     * @param int $idScenar
     * @param int $idLieu
     * @param int $idTypeCtrl
     * @param string $idBase
     * 
     */
    public function ajoutDiag($idExi, $idScenar, $idLieu, $idTypeCtrl, $idBase=false)
    {
    	$diag = new GEVU_Diagnostique($idBase);
		$dbL = new Models_DbTable_Gevu_lieux($diag->db);
    	
		//récupère la liste des contrôles à effectuer
    	$arrCtl = $diag->getLieuCtl($idLieu, $idScenar, false, "/node[@idCtrl='".$idTypeCtrl."']/node");
    	$arrResult = array();
    	foreach ($arrCtl['ctrl'] as $ctl) {
    		//création d'un lieu pour chaque type de controles
    		//ou récupération du lieu existant : $existe = true 
			$idNewLieu = $diag->ajoutLieu($idLieu, $idExi, $idBase, $ctl["lib"],false, false);
			$dbL->edit($idNewLieu, array("id_type_controle"=>$ctl["id_type_controle"]));
    	}
    	
		//récupère le diagnostic du lieu
		$arrResult[] = $diag->calculDiagForLieu($idLieu);    		
    	
        return $arrResult; 
    }    
    
   /**
     * Récupère ou Ajoute une entrée avec le lieu associé.
     *
     * @param string $ref
     * @param int $idInst
     * @param int $idLieuParent
     * @param string $lib
     *  
     * @return integer
     */
    public function getByRef($ref, $idInst, $idLieuParent, $lib="")
    {    	
		//vérification de l'existence de l'antenne
	    $arr = $this->findByRef($ref);
	    if(count($arr)==0){
	    	if($lib=="")$lib="Obj. Ext. - ".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, false, $lib, true, false);
		    $this->ajouter(array("id_lieu"=>$idLieu, "id_instant"=>$idInst, "ref"=> $ref));
		    $arr = $this->findByRef($ref);
	    }
    	return $arr[0];
    } 
      
}
