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
       ,"Models_DbTable_Gevu_antennes"
       ,"Models_DbTable_Gevu_groupes"
       ,"Models_DbTable_Gevu_logements"
       ,"Models_DbTable_Gevu_locaux"
       ,"Models_DbTable_Gevu_partiescommunes"
       ,"Models_DbTable_Gevu_stats"
       ,"Models_DbTable_Gevu_lieuxinterventions"
       ,"Models_DbTable_Gevu_chainesdeplacements"
       ,"Models_DbTable_Gevu_lieuxchainedeplacements"
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
			if($k!="id_instant")
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
    		//récupère les information du parent
    		$arrP = $this->findById_lieu($data["lieu_parent"]);
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
    		$data['niv'] = $arrP[0]['niv']+1;
    	 	$data["id_lieu"] = $this->insert($data);
    	}else{
	    	$data["id_lieu"] = $id;   		
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
     * @param boolean $hierarchie
     *
     * @return void
     */
    public function remove($id, $hierarchie=true)
    {    	
        $lieu = $this->findById_lieu($id);
        
        if(count($lieu)==0)return;
    	
		$ids = $id;
        if($hierarchie){
	    	//récupère tous les enfants
	    	$ids = $this->getFullChildIds($id);
			$ids = $ids[0]['ids'];
			if($ids) $ids.=','.$id;
        }
		//suppression des données lieés
        $dt = $this->getDependentTables();
        foreach($dt as $t){
        	$dbT = new $t($this->_db);
        	$dbT->delete('id_lieu IN ('.$ids.')');
        }        
        $this->delete('id_lieu IN ('.$ids.')');
        
        /** TODO
         * supprimer les documents sur le serveur
         */
        
        if($hierarchie){        
	        //mis à jour des droites et gauches
	        $sql = 'UPDATE gevu_lieux SET rgt = rgt-1, lft = lft - 1 WHERE lft BETWEEN '.$lieu[0]['lft'].' AND '.$lieu[0]['rgt'];
			$stmt = $this->_db->query($sql);
	        $sql = 'UPDATE gevu_lieux SET rgt=rgt-2 WHERE rgt > '.$lieu[0]['rgt'];
			$stmt = $this->_db->query($sql);
	        $sql = 'UPDATE gevu_lieux SET lft=lft - 2 WHERE lft > '.$lieu[0]['rgt'];
			$stmt = $this->_db->query($sql);
        }
    }
    
    /**
     * Récupère toutes les entrées Gevu_lieux avec certains critères
     * de tri, intervalles
     * @param string $order
     * @param int $limit
     * @param int $from
	 *
     * @return array
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
    
    /**
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
    /**
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
    /**
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
        
    /**
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
            ->where( "g.lieu_parent = ?", $lieu_parent )
            ->order("g.lft DESC");

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
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
    /**
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
    /**
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
    /**
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
    /**
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

     /**
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

     /**
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
            ->from(array('node' => 'gevu_lieux'),array('libO'=>'lib', 'id_lieu0'=>'id_lieu'))
            ->joinInner(array('enfants' => 'gevu_lieux'),
                'enfants.lft BETWEEN node.lft AND node.rgt',array('lib', 'id_lieu', 'lieu_parent', 'niv', 'id_type_controle'))
            ->where( "node.id_lieu = ?", $idLieu)
           	->order("enfants.".$order);        
                $result = $this->fetchAll($query);
        return $result->toArray(); 
    }

    /**
     * Recherche une entrée Gevu_lieux avec la valeur spécifiée
     * et retourne la liste de tous ses enfants au format csv
     *
     * @param integer $idLieu
     * @param string $order
     * @return array
     */
    public function getFullChildIds($idLieu, $order="lft")
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'),array("ids"=>"GROUP_CONCAT(enfants.id_lieu)"))
            ->joinInner(array('enfants' => 'gevu_lieux'),
                'enfants.lft BETWEEN node.lft AND node.rgt',array('lib', 'id_lieu'))
            ->where( "node.id_lieu = ?", $idLieu)
            ->group("node.id_lieu");        
        $result = $this->fetchAll($query);
        return $result->toArray(); 
    }
        
     /**
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

     /**
     * Recherche une entrée Gevu_lieux correspondant au parent d'un lieu pour un type de controle
     * et retourne cette entrée.
     *
     * @param integer $idLieu
     * @param string $objTypeControle
     * @param int $idTypeControle
     * 
     * @return array
     */
    public function getParentForTypeControle($idLieu, $objTypeControle, $idTypeControle=-1)
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('node' => 'gevu_lieux'))
            ->joinInner(array('parent' => 'gevu_lieux'),
                'node.lft BETWEEN parent.lft AND parent.rgt')
            ->where( "node.id_lieu = ?", $idLieu);        
    	if($objTypeControle && $objTypeControle!=""){
	    	//récupère les infos de la table liée
			$dbObj = new $objTypeControle;
			$info = $dbObj->info();
            $query->joinInner(array('t' => $info["name"]),'t.id_lieu = parent.id_lieu');
    	}
    	if($idTypeControle && $idTypeControle!=-1  && $idTypeControle!=0){
            $query->where( "parent.id_type_controle = ?", $idTypeControle);        	
    	}
        $result = $this->fetchAll($query);
        return $result->toArray(); 
        
    }
    
    /**
     * récupère les types de données liées pour un lieu
     *
     * @param int $idLieu
     *  
     * @return integer
     */
    public function getTypeRelatedData($idLieu)
    {
    	$sql = 'SELECT l.lib, l.id_lieu, l.lieu_parent, COUNT(a.id_antenne) antenne, COUNT(b.id_batiment) batiment, COUNT(c.id_geo) geo, COUNT(d.id_diag) diag, COUNT(e.id_doc) doc
    			, COUNT(f.id_espace) espace, COUNT(g.id_espace_ext) espace_ext, COUNT(h.id_espace_int) espace_int, COUNT(i.id_etablissement) etablissement, COUNT(j.id_niveau) niveau
    			, COUNT(k.id_objet_ext) objet_ext, COUNT(m.id_objet_int) objet_int, COUNT(n.id_objet_voirie) objet_voirie, COUNT(p.id_parcelle) parcelle
    			, COUNT(q.id_probleme) probleme, COUNT(s.id_groupe) groupe, COUNT(t.id_logement) logement, COUNT(u.id_local) local, COUNT(v.id_part_commu) part_commu
					  FROM gevu_lieux AS l
						  LEFT JOIN gevu_antennes AS a ON a.id_lieu = l.id_lieu
						  LEFT JOIN gevu_batiments AS b ON b.id_lieu = l.id_lieu
						  LEFT JOIN gevu_geos AS c ON c.id_lieu = l.id_lieu
						  LEFT JOIN gevu_diagnostics AS d ON d.id_lieu = l.id_lieu
						  LEFT JOIN gevu_docsxlieux AS e ON e.id_lieu = l.id_lieu
						  LEFT JOIN gevu_espaces AS f ON f.id_lieu = l.id_lieu
						  LEFT JOIN gevu_espacesxexterieurs AS g ON g.id_lieu = l.id_lieu
						  LEFT JOIN gevu_espacesxinterieurs AS h ON h.id_lieu = l.id_lieu
						  LEFT JOIN gevu_etablissements AS i ON i.id_lieu = l.id_lieu
						  LEFT JOIN gevu_niveaux AS j ON j.id_lieu = l.id_lieu
						  LEFT JOIN gevu_objetsxexterieurs AS k ON k.id_lieu = l.id_lieu
						  LEFT JOIN gevu_objetsxinterieurs AS m ON m.id_lieu = l.id_lieu
						  LEFT JOIN gevu_objetsxvoiries AS n ON n.id_lieu = l.id_lieu
						  LEFT JOIN gevu_parcelles AS p ON p.id_lieu = l.id_lieu
						  LEFT JOIN gevu_problemes AS q ON q.id_lieu = l.id_lieu
						  LEFT JOIN gevu_groupes AS s ON s.id_lieu = l.id_lieu
						  LEFT JOIN gevu_logements AS t ON t.id_lieu = l.id_lieu
						  LEFT JOIN gevu_locaux AS u ON u.id_lieu = l.id_lieu
						  LEFT JOIN gevu_partiescommunes AS v ON v.id_lieu = l.id_lieu
					  WHERE l.id_lieu = '.$idLieu.' 
					  GROUP BY l.id_lieu
					  ORDER BY l.id_lieu' ;
    			$stmt = $this->_db->query($sql);

    			return $stmt->fetch();
    }     
}
