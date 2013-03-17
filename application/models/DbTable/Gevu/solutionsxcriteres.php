<?php
/**
 * Ce fichier contient la classe Gevu_solutionsxcriteres.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_solutionsxcriteres'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_solutionsxcriteres extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_solutionsxcriteres';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_solution';

    
    /**
     * Vérifie si une entrée Gevu_solutionsxcriteres existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_solution'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_solution; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_solutionsxcriteres.
     *
     * @param int $idSolution
     * @param int $idCritere
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($idSolution, $idCritere, $existe=true)
    {
    	$id=false;
    	$data = array("id_solution"=>$idSolution,"id_critere"=>$idCritere);
       	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_solutionsxcriteres avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_solutionsxcriteres.id_solution = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_solutionsxcriteres avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($idSolution, $idCritere=null)
    {
    	if($idCritere == null){
	        $this->delete('gevu_solutionsxcriteres.id_solution = '.$idSolution);
    	}else{
	        $this->delete('gevu_solutionsxcriteres.id_solution = '.$idSolution.' AND gevu_solutionsxcriteres.id_critere = '.$idCritere);
    	}
    }
    
    /**
     * Récupère toutes les entrées Gevu_solutionsxcriteres avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_solutionsxcriteres" => "gevu_solutionsxcriteres") );
                    
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
     * Recherche une entrée Gevu_solutionsxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_solution
     */
    public function findByIdSolution($id_solution)
    {
        $query = $this->select()
        			->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
                    ->from( array("g" => "gevu_solutionsxcriteres"),
                          array('id_solution', 'id_critere') )                           
                   ->joinInner(array('l' => 'gevu_criteres'),
                          'g.id_critere = l.id_critere','ref')
                   ->where( "g.id_solution = " . $id_solution );
                    
        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrée Gevu_solutionsxcriteres avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     * @param boolean $bRetObj
     * 
     * @return array
     */
    public function findByIdCritere($id_critere, $bRetObj=false)
    {
        $query = "SELECT
	        s.id_solution, s.lib AS solution, s.ref AS refSolu
	        , cS.id_cout, cS.unite, cS.metre_lineaire, cS.metre_carre, cS.achat, cS.pose
	        , p.id_produit, p.ref AS refProd, p.description, p.marque, p.modele
	        , cP.id_cout, cP.unite, cP.metre_lineaire, cP.metre_carre, cP.achat, cP.pose
        FROM gevu_solutions AS s
	        INNER JOIN gevu_solutionsxcriteres as sc ON s.id_solution = sc.id_solution AND sc.id_critere = ".$id_critere."
	        INNER JOIN gevu_solutionsxcouts as sco ON sco.id_solution = s.id_solution
	        INNER JOIN gevu_couts as cS ON cS.id_cout = sco.id_cout
	        LEFT JOIN gevu_solutionsxproduits as sp ON sp.id_solution = sc.id_solution
	        LEFT JOIN gevu_produits as p ON p.id_produit = sp.id_produit
	        LEFT JOIN gevu_produitsxcouts as pc ON pc.id_produit = p.id_produit
	        LEFT JOIN gevu_couts as cP ON cP.id_cout = pc.id_cout";

    	$adpt = $this->getAdapter();
    	$result = $adpt->query($query);
    	$arr = $result->fetchAll();
    	
    	if($bRetObj){
    		$idSolus=-1;$j=-1;$idProd = -1;$k=-1;
    		foreach ($arr as $d) {
				if($idSolus != $d['id_solution']){
					$j ++;
					$idSolus = $d['id_solution'];
					$arrR[$j] = array("id_solution"=>$d['id_solution'],"solution"=>$d['solution'], "ref"=>$d['refSolu']);
					$idProd = -1;
					$k=-1;
				}
				if($d['id_produit'] && $idProd != $d['id_produit']){
					$k ++;
					$idProd = $d['id_produit'];
					$arrR[$j]["produits"][$k] = array("id_produit"=>$d['id_produit'],"ref"=>$d['refProd'],"description"=>$d['description'], "marque"=>$d['marque'], "modele"=>$d['modele']);
				}
				if($d['id_cout']){
					$arrR[$j]["produits"][$k]["cout"][] = array("id_cout"=>$d['id_cout'],"unite"=>$d['unite'],"metre_lineaire"=>$d['metre_lineaire'],"metre_carre"=>$d['metre_carre'],"achat"=>$d['achat'],"pose"=>$d['pose'],"solution"=>$d['solution']);
				}						
				if($d['Sid_cout']){
					$arrR[$j]["cout"][] = array("id_cout"=>$d['Sid_cout'],"unite"=>$d['Sunite'],"metre_lineaire"=>$d['Smetre_lineaire'],"metre_carre"=>$d['Smetre_carre'],"achat"=>$d['Sachat'],"pose"=>$d['Spose'],"solution"=>$d['Ssolution']);
				}						
    		}
    	}else{
    		$arrResult = $arr;
    	}
    	
    	return $arrResult;
        
    }
    
    
}
