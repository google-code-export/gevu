<?php
/**
 * Ce fichier contient la classe Gevu_docs.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_docs'.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Model_DbTable_Gevu_docs extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_docs';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_doc';

    
    /**
     * Vérifie si une entrée Gevu_docs existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_doc'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $k);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_doc; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_docs.
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
     * Recherche une entrée Gevu_docs avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_docs.id_doc = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_docs avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $infos = $this->findByIdDoc($id);     
        $this->delete('gevu_docs.id_doc = ' . $id);
        unlink($infos['path_source']);
        
    }
    
    /**
     * Récupère toutes les entrées Gevu_docs avec certains critères
     * de tri, intervalles
     * @param string $order
     * @param integer $limit
     * @param integer $from
     *  
     * @return array
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_docs" => "gevu_docs") );
                    
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
     * Récupère les spécifications des colonnes Gevu_docs 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_doc","champ"=>"id_doc","visible"=>true),
    	array("titre"=>"url","champ"=>"url","visible"=>true),
    	array("titre"=>"titre","champ"=>"titre","visible"=>true),
    	array("titre"=>"branche","champ"=>"branche","visible"=>true),
    	array("titre"=>"tronc","champ"=>"tronc","visible"=>true),
    	array("titre"=>"content_type","champ"=>"content_type","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_doc
     */
    public function findByIdDoc($id_doc)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.id_doc = " . $id_doc );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $url
     */
    public function findByUrl($url)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.url = " . $url );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $titre
     */
    public function findByTitre($titre)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.titre = " . $titre );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $branche
     */
    public function findByBranche($branche)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.branche = " . $branche );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $tronc
     */
    public function findByTronc($tronc)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.tronc = ?",$tronc );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_docs avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $content_type
     */
    public function findByContent_type($content_type)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_docs") )                           
                    ->where( "g.content_type = " . $content_type );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
