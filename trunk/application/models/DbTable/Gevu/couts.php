<?php
/**
 * Ce fichier contient la classe Gevu_cout.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_cout'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_couts extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_couts';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_cout';

    
    /**
     * Vérifie si une entrée Gevu_cout existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_cout'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_cout; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_cout.
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
     * Recherche une entrée Gevu_cout avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_couts.id_cout = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_cout avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_couts.id_cout = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_cout avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_couts" => "gevu_cout") );
                    
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
     * Récupère les spécifications des colonnes Gevu_cout 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_cout","champ"=>"id_cout","visible"=>true),
    	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"unite","champ"=>"unite","visible"=>true),
    	array("titre"=>"metre_lineaire","champ"=>"metre_lineaire","visible"=>true),
    	array("titre"=>"metre_carre","champ"=>"metre_carre","visible"=>true),
    	array("titre"=>"achat","champ"=>"achat","visible"=>true),
    	array("titre"=>"pose","champ"=>"pose","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_cout
     */
    public function findById_cout($id_cout)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.id_cout = ?", $id_cout );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $unite
     */
    public function findByUnite($unite)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.unite = ?", $unite );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $metre_lineaire
     */
    public function findByMetre_lineaire($metre_lineaire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.metre_lineaire = ?", $metre_lineaire );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $metre_carre
     */
    public function findByMetre_carre($metre_carre)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.metre_carre = ?", $metre_carre );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $achat
     */
    public function findByAchat($achat)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.achat = ?", $achat );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $pose
     */
    public function findByPose($pose)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_couts") )                           
                    ->where( "g.pose = ?", $pose );

        return $this->fetchRow($query)->toArray(); 
    }
    
	/*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idProduit
     */
    public function findByIdProduit($idProduit)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_couts"))
            ->joinInner(array('l' => 'gevu_produitsxcouts'),
            	'g.id_cout = l.id_cout','id_produit')
            ->where( "l.id_produit = ?", $idProduit );
    
        return $this->fetchAll($query)->toArray(); 
    }
    
	/*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idSolution
     */
    public function findByIdSolution($idSolution)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_couts"))
            ->joinInner(array('l' => 'gevu_solutionsxcouts'),
            	'g.id_cout = l.id_cout','id_solution')
            ->where( "l.id_solution = ?", $idSolution );
    
        return $this->fetchAll($query)->toArray(); 
    }
    
	/*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $refsCrits
     */
    public function findSolusByIdsCriteres($refsCrits)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_couts"))
            ->joinInner(array('s' => 'gevu_solutionsxcouts'),
            	'g.id_cout = s.id_cout','id_solution')
            ->joinInner(array('sc' => 'gevu_solutionsxcriteres'),
            	's.id_solution = sc.id_solution','id_critere')
            ->joinInner(array('c' => 'gevu_criteres'),
            	'sc.id_critere = c.id_critere','ref')
            ->joinInner(array('so' => 'gevu_solutions'),
            	'so.id_solution = s.id_solution',array('ref'=>'ref','lib'=>'lib'))
            ->where( "c.ref IN ($refsCrits)");
    
        return $this->fetchAll($query)->toArray(); 
    }

	/*
     * Recherche une entrée Gevu_cout avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $idSolus
     */
    public function findProduitsByIdSolution($idSolus)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("g" => "gevu_couts"))
            ->joinInner(array('pc' => 'gevu_produitsxcouts'),
            	'g.id_cout = pc.id_cout','id_produit')
            ->joinInner(array('sp' => 'gevu_solutionsxproduits'),
            	'sp.id_produit = pc.id_produit AND sp.id_solution = '.$idSolus,'id_solution')
            ->joinInner(array('p' => 'gevu_produits'),
            	'p.id_produit = pc.id_produit')
            ;
    
        return $this->fetchAll($query)->toArray(); 
    }
    
}
