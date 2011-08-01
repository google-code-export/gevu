<?php
/**
 * Ce fichier contient la classe Gevu_instants.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_instants'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_instants extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_instants';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_instant';

    
    /**
     * Vérifie si une entrée Gevu_instants existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_instant'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $k);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_instant; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_instants.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	$id=false;
    	if(!isset($data['ici']))$data['ici']=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR'] : -1;
		if(!isset($data['maintenant']))$data['maintenant']= new Zend_Db_Expr('NOW()');
    	
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_instants avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_instants.id_instant = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_instants avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_instants.id_instant = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_instants avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_instants" => "gevu_instants") );
                    
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
     * Récupère les spécifications des colonnes Gevu_instants 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_instant","champ"=>"id_instant","visible"=>true),
    	array("titre"=>"maintenant","champ"=>"maintenant","visible"=>true),
    	array("titre"=>"ici","champ"=>"ici","visible"=>true),
    	array("titre"=>"id_exi","champ"=>"id_exi","visible"=>true),
    	array("titre"=>"nom","champ"=>"nom","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_instants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_instants") )                           
                    ->where( "g.id_instant = " . $id_instant );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_instants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maintenant
     */
    public function findByMaintenant($maintenant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_instants") )                           
                    ->where( "g.maintenant = " . $maintenant );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_instants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ici
     */
    public function findByIci($ici)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_instants") )                           
                    ->where( "g.ici = " . $ici );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_instants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findById_exi($id_exi)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_instants") )                           
                    ->where( "g.id_exi = " . $id_exi );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_instants avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_instants") )                           
                    ->where( "g.nom = " . $nom );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
