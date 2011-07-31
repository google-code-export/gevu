<?php
/**
 * Ce fichier contient la classe Gevu_exis.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_exis'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_exis extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_exis';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_exi';

    
    /**
     * Vérifie si une entrée Gevu_exis existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_exi'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $val);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_exi; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_exis.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_exis avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_exis.id_exi = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_exis avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_exis.id_exi = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_exis avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_exis" => "gevu_exis") );
                    
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
     * Récupère les spécifications des colonnes Gevu_exis 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_exi","champ"=>"id_exi","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
    	array("titre"=>"url","champ"=>"url","visible"=>true),
    	array("titre"=>"mail","champ"=>"mail","visible"=>true),
    	array("titre"=>"mdp","champ"=>"mdp","visible"=>true),
    	array("titre"=>"mdp_sel","champ"=>"mdp_sel","visible"=>true),
    	array("titre"=>"role","champ"=>"role","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findById_exi($id_exi)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.id_exi = " . $id_exi );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.nom = " . $nom );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $url
     */
    public function findByUrl($url)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.url = " . $url );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mail
     */
    public function findByMail($mail)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.mail = " . $mail );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mdp
     */
    public function findByMdp($mdp)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.mdp = " . $mdp );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mdp_sel
     */
    public function findByMdp_sel($mdp_sel)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.mdp_sel = " . $mdp_sel );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_exis avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $role
     */
    public function findByRole($role)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_exis") )                           
                    ->where( "g.role = " . $role );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
