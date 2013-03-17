<?php
/**
 * Ce fichier contient la classe Gevu_produits.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_produits'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_produits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_produits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_produit';

    
    /**
     * Vérifie si une entrée Gevu_produits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_produit'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_produit; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_produits.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true, $creaLiee=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	if($creaLiee){
    		//on crée un cout pour le produit
    		$s = new Models_DbTable_Gevu_couts();
    		$data = array("unite"=>0);
    		$idC = $s->ajouter($data);
    		$s = new Models_DbTable_Gevu_produitsxcouts();
    		$data = array("id_produit"=>$id,"id_cout"=>$idC);
    		$s->ajouter($data,false);
    	}
		
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_produits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_produits.id_produit = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_produits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_produits.id_produit = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_produits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order="description", $limit=0, $from=0)
    {
        $query = $this->select()
        			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
                    ->from( array("g" => "gevu_produits"))
                   	->joinInner(array('l' => 'gevu_entreprises'),
                          'g.id_entreprise = l.id_entreprise',array('LibEntreprise'=>'nom'));
                    
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
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_produit
     */
    public function findById_produit($id_produit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.id_produit = " . $id_produit );
		$s = $this->fetchRow($query);
        if($s){							
			$arr = $s->toArray();
		}else{
			$arr = array("ref"=>-1,"description"=>"pas trouvé");
		}                    
        return $arr; 
    }
    /*
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_entreprise
     */
    public function findById_entreprise($id_entreprise)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.id_entreprise = " . $id_entreprise );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $description
     */
    public function findByDescription($description)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.description = " . $description );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $technique
     */
    public function findByTechnique($technique)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.technique = " . $technique );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $preconisation
     */
    public function findByPreconisation($preconisation)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.preconisation = " . $preconisation );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_produits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $marque
     */
    public function findByMarque($marque)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_produits") )                           
                    ->where( "g.marque = " . $marque );

        return $this->fetchRow($query)->toArray(); 
    }

    /**
     * Recherche les intervention et leur caractéristiques pour un produit
    * et retourne ces entrées.
    *
    * @param int $idProduit
    * 
    */
    public function getProduitInterv($idProduit)
    {
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sÃ©lectionner des colonnes dans une autre table
            ->from(array('p' => 'gevu_produits'),array("id_produit", "ref", "description"))
            ->joinInner(array('i' => 'gevu_interventions'),
                'i.id_produit = p.id_produit', array("id_interv", "interv", "unite", "frequence", "cout"))
            ->joinInner(array('mcI' => 'gevu_motsclefs'),
                'mcI.id_motclef = i.interv', array("lblInterv"=>"titre"))
            ->joinInner(array('mcU' => 'gevu_motsclefs'),
                'mcU.id_motclef = i.unite', array("lblUnite"=>"titre"))
            ->where('p.id_produit ='.$idProduit);
            
        return  $this->fetchAll($query)->toArray();
    	    
    }    
    
}
