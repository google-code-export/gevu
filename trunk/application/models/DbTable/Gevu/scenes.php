<?php
/**
 * Ce fichier contient la classe Gevu_scenes.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui reprÃ©sente la table 'gevu_scenes'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_scenes extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_scenes';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_scene';

    
    var $xml;
    
    /**
     * VÃ©rifie si une entrÃ©e Gevu_scenes existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_scene'));
		foreach($data as $k=>$v){
			if($k!=maj)
				$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_scene; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrÃ©e Gevu_scenes.
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
    		$data['maj'] = new Zend_Db_Expr('NOW()');
    		$id = $this->insert($data);
    	}
    	return $id;
    } 
    /**
     * Copie une entrÃ©e Gevu_scenes.
     *
     * @param array $data
     *  
     * @return integer
     */
    public function copier($data)
    {
		$str = "INSERT INTO gevu_scenes (id_scenario, lib, params, maj, type, xml)
			SELECT ".$data["id_scenario"]
			.",lib, params, now(), type, xml
			FROM gevu_scenes
			WHERE id_scene = ".$data["id_scene"];			
        $db = Zend_Db_Table::getDefaultAdapter();
    	$stmt = $db->query($str);
    	$id = $db->lastInsertId();
    	return $id;
    } 
    
    /**
     * Copie colle une entrÃ©e Gevu_scenes.
     *
     * @param string $copieUI
     * @param string $colleUI
     *  
     * @return xml
     */
    public function copiecolle($copieUI, $colleUI)
    {
    	
    	//rÃ©cupÃ¨re l'arboressence complÃ¨te du scÃ©nario Ã  copier
    	$sceneCopie = $this->getArboScenar($copieUI);
    	//rÃ©cupÃ¨re l'arboressence complÃ¨te du scÃ©nario oÃ¹ coller
    	$sceneColle = $this->getArboScenar($colleUI);
    	
    	//recherche le noeud Ã  copier
		$path = "//node[@uid='".$copieUI."']";
		$xml = $sceneCopie["xml"]; 
		$result = $xml->xpath($path);
    	
		//copie toute l'arboressence du noeud
		$this->xml =  new DOMDocument();
		$xmlArbo = $this->copieScene($result[0],$sceneCopie["rs"]["id_scenario"],$sceneColle["rs"]["id_scenario"]);
		$this->xml->appendChild($xmlArbo);
		
		return $this->xml;
    } 

    /**
     * Copie une scene.
     *
     * @param xmlnode $node
     * @param int $idScenarSrc
     * @param int $idScenarDst
     *  
     * @return xml
     */
    public function copieScene($node, $idScenarSrc, $idScenarDst)
    {
    	
		//recherche le dÃ©tail de la scÃ¨ne
		$sc = $this->findByIdScenarioType($idScenarSrc, $node["uid"], true);

		//modifie les donnÃ©es
		$uId = $sc[0]["type"];
		$uId = explode("_", $uId);
		unset($sc[0]["id_scene"]);
		unset($sc[0]["maj"]);
		$sc[0]["id_scenario"]=$idScenarDst;
		$nUid = uniqid();
		$sc[0]["type"]=$uId[0]."_".$uId[1]."_".$nUid;

		//ajoute une nouvelle scene
		$idScene = $this->ajouter($sc[0],false);

		//crÃ©ation du xml
		$nn = $this->xml->createElement("node");
		$nn->setAttribute("idCtrl", $node["idCtrl"]);
		$nn->setAttribute("lib", $node["lib"]);
		$nn->setAttribute("objZend", $node["objZend"]);
		$nn->setAttribute("uid", $nUid);
		$att = $this->xml->createAttribute('isBranch');
		if($node->count()){
			$nn->setAttribute("isBranch", "true");
			foreach($node->children() as $n){
				$nScene = $this->copieScene($n, $idScenarSrc, $idScenarDst);
				$nn->appendChild($nScene);
			}
		}else{
			$nn->setAttribute("isBranch", "false");
		}

		/*vérifie la valeur du xml
		$s = new GEVU_Site();
		$object = new stdClass();
		$s->getDomElementToObject($nn, $object);
		*/
		
		return $nn;

    }
    
    
    /**
     * renvoie l'arboressence gÃ©nÃ©rale d'un scenario Ã  partir d'une scÃ¨ne
     *
     * @param string $ui
     *  
     * @return array
     */
    public function getArboScenar($ui)
    {
    	//rÃ©cupÃ¨re l'arboressence complÃ¨te du scÃ©nario
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sÃ©lectionner des colonnes dans une autre table
            ->from(array('se' => 'gevu_scenes'),array())
            ->joinInner(array('so' => 'gevu_scenario'),
                'so.id_scenario = se.id_scenario',array())
            ->joinInner(array('set' => 'gevu_scenes'),
                'set.id_scene = so.params',array("id_scene", "type", "paramsCtrl", "id_scenario"))
            ->where( "se.type like '%".$ui."%'");    	
        $result = $this->fetchAll($query)->toArray();
        $params = json_decode($result[0]["paramsCtrl"]);
		$xmlScene = simplexml_load_string($params[0]->idCritSE);		
    	
		return array("xml"=>$xmlScene, "rs"=>$result[0]);
    }
    
    /**
     * Recherche une entrée Gevu_scenes avec la clef primaire spÃ©cifiÃ©e
     * et modifie cette entrÃ©e avec les nouvelles donnÃ©es.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_scenes.id_scene = ' . $id);
    }
    
    /**
     * Recherche une entrÃ©e Gevu_scenes avec la clef primaire spÃ©cifiÃ©e
     * et supprime cette entrÃ©e.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_scenes.id_scene = ' . $id);
        return -1;
    }

    /**
    * Recherche une entrÃ©e Gevu_scenes avec la clef secondaire spÃ©cifiÃ©e
     * et supprime cette entrÃ©e.
     *
     * @param integer $id_scenario
     *
     * @return integer
     */
    public function removeScenario($id_scenario)
    {
        $this->delete('gevu_scenes.id_scenario = ' . $id_scenario);
        return -1;
    }
    
    /**
    * Recherche une entrÃ©e Gevu_scenes avec les paramÃ¨tres spÃ©cifiÃ©s
     * et supprime cette entrÃ©e.
     *
     * @param integer $id_scenario
     * @param string $type
     *
     * @return integer
     */
    public function removeByScenarioType($id_scenario, $type)
    {
        return $this->delete('gevu_scenes.id_scenario = '.$id_scenario.' AND gevu_scenes.type = "'.$type.'"');
    }
    
    /**
     * RÃ©cupÃ¨re toutes les entrÃ©es Gevu_scenes avec certains critÃ¨res
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_scenes" => "gevu_scenes") );
                    
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
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param int $id_scene
     */
    public function findByIdScene($id_scene)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.id_scene = ?", $id_scene );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param int $id_scenario
     */
    public function findById_scenario($id_scenario)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.id_scenario = ?", $id_scenario );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param int $id_scenario
     * @param string $type
     * @param boolean $like
     * 
     * @return array()
     */
    public function findByIdScenarioType($id_scenario, $type, $like=false)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.id_scenario = ?", $id_scenario );
		if($like){
			$query->where( "g.type LIKE '%".$type."%'");
		}else{
			$query->where( "g.type = ?", $type);
		}
        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param longtext $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrÃ©e Gevu_scenes avec la valeur spÃ©cifiÃ©e
     * et retourne cette entrÃ©e.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenes") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }

	/**
	* vÃ©rifie si les noeuds d'un scÃ©nario existe ou sont en trop
	*  
    * @param int $idScene
    * 
    */
	function verifIsNodeExiste($idScene){

		//rÃ©cupÃ¨re la scÃ¨ne de dÃ©part du scÃ©nario
        $scene = $this->findByIdScene($idScene);
        $params = json_decode($scene[0]['paramsCtrl']);
		$xmlScene = simplexml_load_string($params[0]->idCritSE);
			
		$arrScene = $this->findById_scenario($scene[0]['id_scenario']);
		foreach ($arrScene as $sc) {
			if($idScene != $sc['id_scene']){
				//vÃ©rifie si le noeud est dans l'arbre
				$arr = explode("_",$sc["type"]);
		        $result = $xmlScene->xpath("//node[@uid='".$arr[2]."']");
		        if(count($result)>0){
		        	$toto = true;
		        }else{
					$this->removeByScenarioType(13, $sc["type"]);				
		        	$toto = false;
		        }
			}
		}
	}
    
    
}
