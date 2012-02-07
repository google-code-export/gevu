<?php
/**
 * Ce fichier contient la classe Gevu_lieux.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_lieux'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_lieux extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_lieux';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_lieu';

    protected $_dependentTables = array(
       "Models_DbTable_Gevu_batiments"
       ,"Models_DbTable_Gevu_georss"
       ,"Models_DbTable_Gevu_geos"
       ,"Models_DbTable_Gevu_diagnostics"
       ,"Models_DbTable_Gevu_diagnosticsxvoirie"
       ,"Models_DbTable_Gevu_docsxlieux"
       ,"Models_DbTable_Gevu_espaces"
       ,"Models_DbTable_Gevu_espacesxexterieurs"
       ,"Models_DbTable_Gevu_espacesxinterieurs"
       ,"Models_DbTable_Gevu_etablissements"
       ,"Models_DbTable_Gevu_niveaux"
       ,"Models_DbTable_Gevu_objetsxexterieurs"
       ,"Models_DbTable_Gevu_objetsxinterieurs"
       ,"Models_DbTable_Gevu_objetsxvoiries"
       ,"Models_DbTable_Gevu_observations"
       ,"Models_DbTable_Gevu_parcelles"
       ,"Models_DbTable_Gevu_problemes"
       );
    
	protected $LibTablesLiees = array(
       "Models_DbTable_Gevu_batiments"=>"bâtiments"
       ,"Models_DbTable_Gevu_diagnostics"=>"diagnostics"
       ,"Models_DbTable_Gevu_diagnosticsxvoirie"=>"diagnostics de voirie"
       ,"Models_DbTable_Gevu_docsxlieux"=>"documents"
       ,"Models_DbTable_Gevu_espaces"=>"expaces"
       ,"Models_DbTable_Gevu_espacesxexterieurs"=>"espaces extérieurs"
       ,"Models_DbTable_Gevu_espacesxinterieurs"=>"espaces intérieurs"
       ,"Models_DbTable_Gevu_etablissements"=>"&tablissements"
       ,"Models_DbTable_Gevu_georss"=>"géo rss"
       ,"Models_DbTable_Gevu_geos"=>"données géographiques"
       ,"Models_DbTable_Gevu_niveaux"=>"niveaux"
       ,"Models_DbTable_Gevu_objetsxexterieurs"=>"objets extérieurs"
       ,"Models_DbTable_Gevu_objetsxinterieurs"=>"objets intérieurs"
       ,"Models_DbTable_Gevu_objetsxvoiries"=>"objets de voiries"
       ,"Models_DbTable_Gevu_observations"=>"observations"
       ,"Models_DbTable_Gevu_parcelles"=>"parcelles"
       ,"Models_DbTable_Gevu_problemes"=>"problèmes"
       );
       
    /**
     * Vérifie si une entrée Gevu_lieux existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_lieu'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_lieu; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_lieux.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true, $rData=false)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    		//gestion des hiérarchies gauche droite
    		//http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
    		//vérifie si le parent à des enfants
    		$arr = $this->findByLieu_parent($data["lieu_parent"]);
    		if(count($arr)>0){
    			//met à jour les niveaux pour les lieux
    			$sql = 'UPDATE gevu_lieux SET rgt = rgt + 2 WHERE rgt >'.$arr[0]['rgt'];
    			$stmt = $this->_db->query($sql);
    			$sql = 'UPDATE gevu_lieux SET lft = lft + 2 WHERE lft >'.$arr[0]['rgt'];
    			$stmt = $this->_db->query($sql);
    			//
    			$data['lft'] = $arr[0]['rgt']+1;
    			$data['rgt'] = $arr[0]['rgt']+2;
    		}else{
    			//récupère les informations du parent
    			$arr = $this->findById_lieu($data["lieu_parent"]);
    			//met à jour les niveaux pour les lieux
    			$sql = 'UPDATE gevu_lieux SET rgt = rgt + 2 WHERE rgt >'.$arr[0]['lft'];
    			$stmt = $this->_db->query($sql);
    			$sql = 'UPDATE gevu_lieux SET lft = lft + 2 WHERE lft >'.$arr[0]['lft'];
    			$stmt = $this->_db->query($sql);
    			//
    			$data['lft'] = $arr[0]['lft']+1;
    			$data['rgt'] = $arr[0]['lft']+2;
    		}    		
    		$data['niv'] = $arr[0]['niv']+1;
    	 	$data["id_lieu"] = $this->insert($data);
    	}
    	if($rData)
	    	return $data;
    	else
	    	return $data["id_lieu"];
    } 
           
    /**
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return integer
     */
    public function edit($id, $data)
    {        
        return $this->update($data, 'gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_lieux avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     * @param Zend_Db_Adapter_Abstract $db
     *
     * @return void
     */
    public function remove($id, $db)
    {
        //récupération les tables liées
        foreach($this->_dependentTables as $t){
        	$dbT = new $t($db);
        	$dbT->removeLieu($id);
        }
    	
        $arrEnfant = $this->getFullChild($id);
        foreach ($arrEnfant as $enf){
        	if($enf["id_lieu"]!=$id){
	        	$this->remove($enf["id_lieu"], $db);
        	}
        }
        $this->delete('gevu_lieux.id_lieu = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_lieux avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_lieux" => "gevu_lieux") );
                    
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
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
	 *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Compte le nombre d'enfant d'une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne ce nombre.
     *
     * @param int $id_lieu
     * 
     * @return integer
     */
    public function getNbEnfant($id_lieu)
    {
        $select = $this->select()
        	->from($this, array('count(*) as amount'))
            ->where( "lieu_parent = ?", $id_lieu );
        
        $rows = $this->fetchAll($select);
       
        return($rows[0]->amount);       
    }    
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_rubrique
     */
    public function findById_rubrique($id_rubrique)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_rubrique = ?", $id_rubrique );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lib LIKE '%".$lib."%'");

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_parent
     */
    public function findById_parent($id_parent)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_parent = ?", $id_parent );

        return $this->fetchAll($query)->toArray(); 
    }
        
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lieu_parent
     */
    public function findByLieu_parent($lieu_parent)
    {
        $query = $this->select()
			->from( array("g" => "gevu_lieux") )                           
			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->joinLeft(array('diag' => 'gevu_diagnostics'),
                'diag.id_lieu = g.id_lieu',array('nbDiag'=>'COUNT(diag.id_diag)'))
			->group("id_lieu")
            ->where( "g.lieu_parent = ?", $lieu_parent );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $lft
     */
    public function findByLft($lft)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.lft = ?", $lft );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $rgt
     */
    public function findByRgt($rgt)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.rgt = ?", $rgt );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $niv
     */
    public function findByNiv($niv)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.niv = ?", $niv );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_lieux") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }

     /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne la liste de tous ses parents
     *
     * @param integer $idLieu
     * @param string $order
     * @return array
     */
    public function getFullPath($idLieu, $order="lft")
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'),array("parLib"=>"lib"))
            ->joinInner(array('parent' => 'gevu_lieux'),
                'node.lft BETWEEN parent.lft AND parent.rgt',array('lib', 'id_lieu', 'niv'))
            ->where( "node.id_lieu = ?", $idLieu)
                        ->order("parent.".$order);        
                $result = $this->fetchAll($query);
        return $result->toArray(); 
    }

     /*
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne la liste de tous ses enfants
     *
     * @param integer $idLieu
     * @param string $order
     * @return array
     */
    public function getFullChild($idLieu, $order="lft")
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'))
            ->joinInner(array('enfants' => 'gevu_lieux'),
                'enfants.lft BETWEEN node.lft AND node.rgt',array('lib', 'id_lieu'))
            ->where( "node.id_lieu = ?", $idLieu)
           	->order("enfants.".$order);        
                $result = $this->fetchAll($query);
        return $result->toArray(); 
    }
    
     /*
     * Recherche une entrée Gevu_lieux correspondant à l'enfant d'un lieu pour un type de controle
     * création de ce lieu s'il n'exite pas  
     * et retourne cette entrée.
     *
     * @param integer $idLieu
     * @param integer $idTypeControle
     * @param integer $idInst
     * 
     * @return array
     */
    public function getEnfantForTypeControle($idLieu, $idTypeControle, $idInst)
    {
    	//on recherche l'enfant pour le type de controle
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
    		->from(array('l' => 'gevu_lieux'),array("id_lieu"))
            ->joinInner(array('d' => 'gevu_diagnostics'),'d.id_lieu = l.id_lieu',array("nbDiag"=>"COUNT(d.id_diag)"))
            ->joinInner(array('c' => 'gevu_criteres'),'c.id_critere = d.id_critere and c.id_type_controle = '.$idTypeControle,array("nbCtl"=>"COUNT(c.id_critere)"))
            ->where( "l.lieu_parent = ?", $idLieu)
            ->group("l.id_lieu");          
        $result = $this->fetchAll($query)->toArray();
        
        //s'il n'existe pas on le crée
        if(!$result[0]['id_lieu']){
        	//récupération du libelle
        	$dbC = new Models_DbTable_Gevu_criteres();
	        $result = $dbC->findByIdTypeControle($idTypeControle);
        	
        	//création de l'enfant s'il n'existe pas
        	$result = $this->ajouter(array("lib"=>$result[0]["lib"], "id_instant"=>$idInst, "lieu_parent"=>$idLieu));
        }else{
        	$result = $result[0]["id_lieu"];
        }
        return $result; 
        
    }

     /*
     * Recherche une entrée Gevu_lieux correspondant au parent d'un lieu pour un type de controle
     * et retourne cette entrée.
     *
     * @param integer $idLieu
     * @param string $TypeControle
     * 
     * @return array
     */
    public function getParentForTypeControle($idLieu, $TypeControle)
    {
    	//récupère les infos de la table liée
		$dbObj = new $TypeControle;
		$info = $dbObj->info();
    	
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'))
            ->joinInner(array('parent' => 'gevu_lieux'),
                'node.lft BETWEEN parent.lft AND parent.rgt')
            ->joinInner(array('t' => $info["name"]),'t.id_lieu = parent.id_lieu')
            ->where( "node.id_lieu = ?", $idLieu);        
        $result = $this->fetchAll($query);
        return $result->toArray(); 
        
    }
    
}
