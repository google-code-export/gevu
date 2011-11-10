<?php
/**
 * Ce fichier contient la classe Gevu_diagnostics.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_diagnostics'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_diagnostics extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_diagnostics';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_diag';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_diagnostics existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_diag'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_diag; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_diagnostics.
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
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_diagnostics avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_diagnostics.id_diag = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_diagnostics avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_diagnostics" => "gevu_diagnostics") );
                    
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
     * Recherche la description totale d'un diagnostic à partir d'un lieu
     * et retourne cette entrée.
     *
     * @param int $idLieu
    * @return array
     */
    public function getAllDesc($idLieu)
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('diag' => 'gevu_diagnostics'))
        	->joinInner(array('crit' => 'gevu_criteres'),
            	'diag.id_critere = crit.id_critere',array('id_type_controle','ref','handicateur_moteur','handicateur_auditif','handicateur_visuel','handicateur_cognitif','criteres'))
        	->joinInner(array('inst' => 'gevu_instants'),
            	'diag.id_instant = inst.id_instant',array('instant'=>"DATE_FORMAT(maintenant,'%W %d %M %Y')",'ici','nom'))
        	->joinInner(array('exi' => 'gevu_exis'),
            	'inst.id_exi = exi.id_exi',array('exis'=>'nom'))
        	->joinInner(array('mc' => 'gevu_motsclefs'),
            	'diag.id_reponse = mc.id_motclef',array('reponse'=>'titre'))
        	->joinLeft(array('prob' => 'gevu_problemes'),
            	'diag.id_lieu = prob.id_lieu AND diag.id_instant = prob.id_instant AND diag.id_critere = prob.id_critere',array('id_probleme','num_marker','mesure','observations','fichier','doc'))
        	->joinLeft(array('obs' => 'gevu_observations'),
            	'diag.id_lieu = obs.id_lieu AND diag.id_instant = obs.id_instant AND diag.id_critere = obs.id_critere',array('id_observations','id_reponse','num_marker','lib'))
        	->where( "diag.id_lieu = ?", $idLieu)
			->group(array('id_instant','id_critere'))
        	->order(array('id_instant','id_critere'));        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }

    /*
     * récupère la liste des réponses
     * et retourne un tableau.
     *
    * @return array
     */
    public function getReponses()
    {
    	$query = $this->select()
        	->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
            ->from(array('diag' => 'gevu_diagnostics'))
        	->joinInner(array('crit' => 'gevu_criteres'),
            	'diag.id_critere = crit.id_critere',array('ref','handicateur_moteur','handicateur_auditif','handicateur_visuel','handicateur_cognitif','criteres'))
        	->joinInner(array('mc' => 'gevu_motsclefs'),
            	'diag.id_reponse = mc.id_motclef',array('reponse'=>'titre'))
        	->joinInner(array('l' => 'gevu_lieux'),
            	'l.id_lieu = diag.id_lieu',array('lib','id_lieu'))
        	->order(array('diag.id_instant','l.id_lieu'));        
		$result = $this->fetchAll($query);
        return $result->toArray(); 
    }    
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_diag
     */
    public function findById_diag($id_diag)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_diag = ?", $id_diag );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_critere
     */
    public function findById_critere($id_critere)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_critere = ?", $id_critere );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_reponse
     */
    public function findById_reponse($id_reponse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_reponse = ?", $id_reponse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_diagnostics avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_diagnostics") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
