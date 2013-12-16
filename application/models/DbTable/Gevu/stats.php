<?php
/**
 * Ce fichier contient la classe Gevu_stats.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_stats'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
//ATTENTION le "s" de Models est nécessaire pour une compatibilité entre application et serveur
class Models_DbTable_Gevu_stats extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_stats';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_stat';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_stats existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_stat'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_stat; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_stats.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=false)
    {
    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 

    /**
     * Ajoute une entrée Gevu_stats à partir d'un fichier d'import.
     *
     * @param array $arr
     * @param int $idLieu
     * @param int $idInst
     *  
     * @return integer
     */
    public function ajouterByImport($arr, $idLieu, $idInst)
    {
    	/*	
    	return $this->ajouter(array("id_lieu"=>$idLieu, "id_instant"=>$idInst, "LKLO"=>$arr[5], "TYPE"=>$arr[6], "ETAGE"=>$arr[7], "SURRE"=>$arr[8], "SURAP"=>$arr[9], "LOYER"=>$arr[10]
    			, "SLS"=>$arr[11], "CHARGES"=>$arr[12], "OCCUPATION"=>$arr[13], "MOTIF_VACANCE"=>$arr[14], "CDECL"=>$arr[15], "CDSCL"=>$arr[16], "AGE1"=>$arr[17], "AGE2"=>$arr[18]
    			, "NBENF1"=>$arr[19], "NBENF2"=>$arr[20], "NBENF3"=>$arr[21], "AUTPERS"=>$arr[22], "REIMP1"=>$arr[22], "REIMP2"=>$arr[23], "REIMPAUT"=>$arr[24], "CATMEN"=>$arr[25]
    			, "PLAF"=>$arr[26], "RESS"=>$arr[27], "POURC"=>$arr[28], "NATIO1"=>$arr[29], "NATIO2"=>$arr[30], "SOCPRO1"=>$arr[31], "SOCPRO2"=>$arr[32], "EMPLOYEUR1"=>$arr[33]
    			, "EMPLOYEUR2"=>$arr[34], "SITFAM1"=>$arr[35], "SITFAM2"=>$arr[36], "RESERVAT"=>$arr[37], "ISOLE"=>$arr[38], "STAB"=>$arr[39], "FRAGI"=>$arr[40], "ETUDI"=>$arr[41]
    			, "RMI"=>$arr[42], "API"=>$arr[43], "AAH"=>$arr[44], "FNS"=>$arr[45], "ASCENSEUR"=>$arr[46], "MODCHAUF"=>$arr[47], "RESAN"=>$arr[48]
    			));
    	*/
    	
    	return $this->ajouter(array("id_lieu"=>$idLieu, "id_instant"=>$idInst
    		, "Antenne_rattachement"=>$arr[0], "Code_groupe"=>$arr[1], "Code_Batiment"=>$arr[4]
    		, "Tranche"=>$arr[3], "Code_Escalier"=>$arr[6], "Indicateur_Zus"=>$arr[12], "Code_Logement"=>$arr[13]
    		, "Categorie_Module"=>$arr[14], "Logement_Individuel"=>$arr[16], "Type_Logement"=>$arr[17], "Nombre_pieces"=>$arr[19], "Etage"=>$arr[20], "Surface_Reelle"=>$arr[21], "Surface_Appliquee"=>$arr[22]
    		, "Type_financement"=>$arr[23], "Annee_Construction"=>$arr[24], "Contrat"=>$arr[25], "Type_Reception_TV"=>$arr[26], "Occupation"=>$arr[27], "Motif_Vacance"=>$arr[28], "Copropriete"=>$arr[30]
    		, "DPE_Date"=>$arr[31], "DPE_consommation_reelle"=>$arr[32], "DPE_Categorie_Consommation"=>$arr[33], "DPE_emissions_GES"=>$arr[34], "DPE_Categorie_Emissions_GES"=>$arr[35]
    		, "CREP_Date"=>$arr[36], "CREP_presence_Plomb"=>$arr[37], "CREP_Seuil_Plomb_depasse"=>$arr[38]
    		, "DTA_Date"=>$arr[39], "DTA_Presence_Amiante"=>$arr[40], "DTA_Presence_Amiante_Degradee"=>$arr[41], "DTA_Mesure_Conservatoire"=>$arr[42], "DTA_Date_Travaux"=>$arr[43]
    		, "Gardien"=>$arr[44], "Peupl_CSP"=>$arr[45], "Peupl_AHH"=>$arr[46], "Peupl_Famille_mono_parentale"=>$arr[47], "Peupl_Famille_Nombreuse"=>$arr[48], "Peupl_Celibataire"=>$arr[49]
    		, "Peupl_Foyer_0_2Enf"=>$arr[50], "Peupl_Nb_Occupants"=>$arr[51], "Peupl_Age_Signataire_1"=>$arr[52], "Peupl_Age_Signataire_2"=>$arr[53], "Peupl_nb_enfants"=>$arr[54]
    		, "Peupl_nb_enfants_0_10_ans"=>$arr[55], "Peupl_nb_enfants_11_17_ans"=>$arr[56], "Peupl_nb_enfants_sup18_ans"=>$arr[57], "Peupl_Provenance"=>$arr[58], "Peupl_Anciennete"=>$arr[59], "Peupl_Surpeuplement"=>$arr[60]
    		, "Montant_Impaye"=>$arr[61], "Montant_Quittance"=>$arr[61]
    		));    		
    		
    } 
    
    
    /**
     * Recherche une entrée Gevu_stats avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
   	
    	$this->update($data, 'gevu_stats.id_stat = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_stats avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
    	$this->delete('gevu_stats.id_stat = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_stats avec la clef de lieu
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
     * Récupère toutes les entrées Gevu_stats avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
   	
    	$query = $this->select()
                    ->from( array("gevu_stats" => "gevu_stats") );
                    
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
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_stat
     *
     * @return array
     */
    public function findById_stat($id_stat)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.id_stat = ?", $id_stat );

        return $this->fetchAll($query)->toArray(); 
    }
    
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param string $Code_Logement
     *
     * @return array
     */
    public function findIdLieuByCode_Logement($Code_Logement)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats"), array("id_lieu") )                           
                    ->where( "g.Code_Logement = '".$Code_Logement."'");

        return $this->fetchAll($query)->toArray(); 
    }
    
    
    /**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     *
     * @return array
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     *
     * @return array
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SURRE
     *
     * @return array
     */
    public function findBySURRE($SURRE)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SURRE = ?", $SURRE );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SURAP
     *
     * @return array
     */
    public function findBySURAP($SURAP)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SURAP = ?", $SURAP );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $LOYER
     *
     * @return array
     */
    public function findByLOYER($LOYER)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.LOYER = ?", $LOYER );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SLS
     *
     * @return array
     */
    public function findBySLS($SLS)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SLS = ?", $SLS );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $CHARGES
     *
     * @return array
     */
    public function findByCHARGES($CHARGES)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.CHARGES = ?", $CHARGES );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $OCCUPATION
     *
     * @return array
     */
    public function findByOCCUPATION($OCCUPATION)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.OCCUPATION = ?", $OCCUPATION );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $MOTIF_VACANCE
     *
     * @return array
     */
    public function findByMOTIF_VACANCE($MOTIF_VACANCE)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.MOTIF_VACANCE = ?", $MOTIF_VACANCE );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $CDECL
     *
     * @return array
     */
    public function findByCDECL($CDECL)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.CDECL = ?", $CDECL );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $CDSCL
     *
     * @return array
     */
    public function findByCDSCL($CDSCL)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.CDSCL = ?", $CDSCL );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $AGE1
     *
     * @return array
     */
    public function findByAGE1($AGE1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.AGE1 = ?", $AGE1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $AGE2
     *
     * @return array
     */
    public function findByAGE2($AGE2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.AGE2 = ?", $AGE2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $NBENF1
     *
     * @return array
     */
    public function findByNBENF1($NBENF1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.NBENF1 = ?", $NBENF1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $NBENF2
     *
     * @return array
     */
    public function findByNBENF2($NBENF2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.NBENF2 = ?", $NBENF2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $NBENF3
     *
     * @return array
     */
    public function findByNBENF3($NBENF3)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.NBENF3 = ?", $NBENF3 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $AUTPERS
     *
     * @return array
     */
    public function findByAUTPERS($AUTPERS)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.AUTPERS = ?", $AUTPERS );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $REIMP1
     *
     * @return array
     */
    public function findByREIMP1($REIMP1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.REIMP1 = ?", $REIMP1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $REIMP2
     *
     * @return array
     */
    public function findByREIMP2($REIMP2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.REIMP2 = ?", $REIMP2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $REIMPAUT
     *
     * @return array
     */
    public function findByREIMPAUT($REIMPAUT)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.REIMPAUT = ?", $REIMPAUT );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $CATMEN
     *
     * @return array
     */
    public function findByCATMEN($CATMEN)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.CATMEN = ?", $CATMEN );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $PLAF
     *
     * @return array
     */
    public function findByPLAF($PLAF)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.PLAF = ?", $PLAF );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $RESS
     *
     * @return array
     */
    public function findByRESS($RESS)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.RESS = ?", $RESS );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $POURC
     *
     * @return array
     */
    public function findByPOURC($POURC)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.POURC = ?", $POURC );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $NATIO1
     *
     * @return array
     */
    public function findByNATIO1($NATIO1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.NATIO1 = ?", $NATIO1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $NATIO2
     *
     * @return array
     */
    public function findByNATIO2($NATIO2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.NATIO2 = ?", $NATIO2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SOCPRO1
     *
     * @return array
     */
    public function findBySOCPRO1($SOCPRO1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SOCPRO1 = ?", $SOCPRO1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SOCPRO2
     *
     * @return array
     */
    public function findBySOCPRO2($SOCPRO2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SOCPRO2 = ?", $SOCPRO2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $EMPLOYEUR1
     *
     * @return array
     */
    public function findByEMPLOYEUR1($EMPLOYEUR1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.EMPLOYEUR1 = ?", $EMPLOYEUR1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $EMPLOYEUR2
     *
     * @return array
     */
    public function findByEMPLOYEUR2($EMPLOYEUR2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.EMPLOYEUR2 = ?", $EMPLOYEUR2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SITFAM1
     *
     * @return array
     */
    public function findBySITFAM1($SITFAM1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SITFAM1 = ?", $SITFAM1 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $SITFAM2
     *
     * @return array
     */
    public function findBySITFAM2($SITFAM2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.SITFAM2 = ?", $SITFAM2 );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $RESERVAT
     *
     * @return array
     */
    public function findByRESERVAT($RESERVAT)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.RESERVAT = ?", $RESERVAT );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ISOLE
     *
     * @return array
     */
    public function findByISOLE($ISOLE)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.ISOLE = ?", $ISOLE );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $STAB
     *
     * @return array
     */
    public function findBySTAB($STAB)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.STAB = ?", $STAB );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $FRAGI
     *
     * @return array
     */
    public function findByFRAGI($FRAGI)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.FRAGI = ?", $FRAGI );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ETUDI
     *
     * @return array
     */
    public function findByETUDI($ETUDI)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.ETUDI = ?", $ETUDI );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $RMI
     *
     * @return array
     */
    public function findByRMI($RMI)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.RMI = ?", $RMI );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $API
     *
     * @return array
     */
    public function findByAPI($API)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.API = ?", $API );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $AAH
     *
     * @return array
     */
    public function findByAAH($AAH)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.AAH = ?", $AAH );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $FNS
     *
     * @return array
     */
    public function findByFNS($FNS)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.FNS = ?", $FNS );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ASCENSEUR
     *
     * @return array
     */
    public function findByASCENSEUR($ASCENSEUR)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.ASCENSEUR = ?", $ASCENSEUR );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $MODCHAUF
     *
     * @return array
     */
    public function findByMODCHAUF($MODCHAUF)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.MODCHAUF = ?", $MODCHAUF );

        return $this->fetchAll($query)->toArray(); 
    }
    	/**
     * Recherche une entrée Gevu_stats avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $RESAN
     *
     * @return array
     */
    public function findByRESAN($RESAN)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_stats") )                           
                    ->where( "g.RESAN = ?", $RESAN );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
