<?php
/**
 * Ce fichier contient la classe Gevu_exisxdroits.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_exisxdroits'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_exisxdroits extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_exisxdroits';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = array('id_exi','id_droit');

    
    /**
     * Vérifie si une entrée Gevu_exisxdroits existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($idExi, $idDroit)
    {
		$select = $this->select();
		$select->from($this, array('id_exi'));
		$select->where('id_exi = ?', $idExi);
		$select->where('id_droit = ?', $idDroit);
		$rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_exi; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_exisxdroits.
     *
     * @param integer $idDroit
     * @param integer $idExi
     *  
     * @return integer
     */
    public function ajouter($idExi, $idDroit, $existe=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($idExi, $idDroit);
    	if(!$id){
    	 	$id = $this->insert(array("id_exi"=>$idExi, "id_droit"=>$idDroit));
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $idDroit
     * @param integer $idExi
     * @param string $data
     *
     * @return void
     */
    public function edit($idExi, $idDroit, $data)
    {
		$c = str_replace("::", "_", __METHOD__)."$idExi, $idDroit, $data";
    	//vérifie s'il faut créer les utilisateurs dans les bases de données
    	//dans le cas du droit application
    	if($idDroit==3){
    		$diag = new GEVU_Diagnostique();
    		$arr = json_decode($data["params"]);
    		foreach ($arr as $v) {
	    		$c.=$diag->copieExi(false, substr($v->id, 2), $idExi);
	    	}
	    	$c.="KO";
    	}        
        $this->update($data, 'gevu_exisxdroits.id_exi = '.$idExi.' AND gevu_exisxdroits.id_droit = '.$idDroit);
    	return $c;
    }
    
    /**
     * Recherche une entrée Gevu_exisxdroits avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $idExi
     * @param integer $idDroit
     *
     * @return void
     */
    public function remove($idExi, $idDroit)
    {
        $this->delete('gevu_exisxdroits.id_exi = '.$idExi.' AND gevu_exisxdroits.id_droit ='.$idDroit);
    }
    
    /**
     * Récupère toutes les entrées Gevu_exisxdroits avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_exisxdroits" => "gevu_exisxdroits") );
                    
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
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findByIdExi($id_exi)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from( array("ed" => "gevu_exisxdroits"))                           
            ->joinInner(array('d' => 'gevu_droits'),
            	'd.id_droit = ed.id_droit',array("id_droit", "lib"))
			->where( "ed.id_exi = ?", $id_exi );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_droit
     */
    public function findById_droit($id_droit)
    {
        $query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        	->from( array("ed" => "gevu_exisxdroits") )                           
            ->joinInner(array('e' => 'gevu_exis'),
            	'e.id_exi = ed.id_exi',array("nom"))
        	->where( "ed.id_droit = ?", $id_droit );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * récupère la liste des utilisateur autorisé à faire un diagnostic dans une base
     * @param string $droit
     * @param string $valeur
     *
     */
    public function getUtiDroit($droit, $valeur){
    	$arrD = $this->findById_droit($droit);
    	$result = array();
    	foreach ($arrD as $ed) {
    		$oD = json_decode($ed["params"]);
    		foreach ($oD as $d) {
    			if($d->id==$droit."_".$valeur){
    				$result[]= array("nom"=>$ed["nom"],"idExi"=>$ed["id_exi"]);
    			}
    		}
    	}
    	return $result;
    }
    
    
    /*
     * Recherche une entrée Gevu_exisxdroits avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }

	 /*
     * Recherche l'entrée Gevu_exisxdroits pour un utilisateur et un droit
     * et retourne cette entrée.
     *
     * @param int $idExi
     * @param int $idDroit
     * 
     * @return array
     */
    public function findByExiDroit($idExi, $idDroit)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exisxdroits") )                           
                    ->where( "g.id_exi = ?", $idExi)
                    ->where( "g.id_droit = ?", $idDroit);
                    
        return $this->fetchAll($query)->toArray(); 

    }
        
    
}
