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
class Models_DbTable_Gevu_espacesxinterieurs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_espacesxinterieurs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_espace_int';

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
	    	if($lib=="")$lib="Esp. Int. - ".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, false, $lib, true, false, array("id_type_controle"=>$data["id_type_specifique_int"]));
	    	$data["id_lieu"] = $idLieu;
	    	$data["id_instant"] = $idInst;
	    	$data["ref"] = $ref;
	    	unset($data["id_espace_int"]);
	    	$this->ajouter($data);
	    	$arr = $this->findByRef($ref);
	    }
    	return $arr[0];
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
    /**
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
    /**
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
    /**
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
    /**
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
    /**
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
    /**
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
    /**
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
    /**
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
    	$arrCtl = $diag->getLieuCtl($idLieu, $idScenar, false, true);
        return $arrCtl; 
    }

    /**
     * Création des diagnostiques
     *
     * @param int $idExi
     * @param int $idScenar
     * @param int $idLieu
     * @param int $idTypeCtrl
     * 
     */
    public function ajoutDiag($idExi, $idScenar, $idLieu, $idTypeCtrl)
    {
    	$diag = new GEVU_Diagnostique();
    	//récupère la liste des contrôles à effectuer
    	$arrCtl = $diag->getLieuCtl($idLieu, $idScenar, false, "/node[@idCtrl='".$idTypeCtrl."']/node");
    	$arrResult = array();
    	foreach ($arrCtl as $ctl) {
    		//création d'un lieu pour chaque type de controles
    		//ou récupération du lieu existant : $existe = true 
			$idNewLieu = $diag->ajoutLieu($idLieu, $idExi, false, $ctl["lib"],true,false);			    		
    	}
    	
		//récupère le diagnostic du lieu
		$arrResult[] = $diag->calculDiagForLieu($idLieu);    		
    	
        return $arrResult; 
    }
    
}
