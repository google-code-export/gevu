<?php
/**
 * Ce fichier contient la classe Gevu_contacts.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_contacts'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_contacts extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_contacts';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_contact';


    /**
     * retourne une connexion à une base de donnée suivant son nom
     * @param string $idBase
     * @return Zend_Db_Adapter_Abstract
     */
    public function setDb($idBase){
    	 
    	$db = Zend_Db_Table::getDefaultAdapter();
    	if($idBase){
    		//change la connexion à la base
    		$arr = $db->getConfig();
    		$arr['dbname']=$idBase;
    		$db = Zend_Db::factory('PDO_MYSQL', $arr);
    	}
    	$this->_db = self::_setupAdapter($db);
    }
    
    /**
     * Vérifie si une entrée Gevu_contacts existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_contact'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_contact; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_contacts.
     *
     * @param array $data
     * @param boolean $existe
     * @param string $idBase
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true, $idBase=false)
    {
    	if($idBase)$this->setDb($idBase);
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_contacts avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_contacts.id_contact = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_contacts avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_contacts.id_contact = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_contacts avec certains critères
     * de tri, intervalles
     */
    public function getAll($idBase=false, $order=null, $limit=0, $from=0)
    {
    	if($idBase)$this->setDb($idBase);
    	 
        $query = $this->select()
                    ->from( array("gevu_contacts" => "gevu_contacts") );
                    
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
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_contact
     */
    public function findById_contact($id_contact)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.id_contact = ?", $id_contact );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $prenom
     */
    public function findByPrenom($prenom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.prenom = ?", $prenom );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fixe
     */
    public function findByFixe($fixe)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.fixe = ?", $fixe );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mobile
     */
    public function findByMobile($mobile)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.mobile = ?", $mobile );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $fax
     */
    public function findByFax($fax)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.fax = ?", $fax );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_contacts avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $mail
     */
    public function findByMail($mail)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_contacts") )                           
                    ->where( "g.mail = ?", $mail );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Retourne toute les entrées en concaténant le prénom et le nom
     * utiliser pour les combobox.
     *
     */
    public function getAllNomPrenom()
    {
        $query = $this->select()
			->from( array("g" => "gevu_contacts"),array("nom"=>"CONCAT(UCASE(nom),' ',prenom)","id_contact") );                           
		
        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
