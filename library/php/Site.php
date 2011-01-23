<?php
class Site{
  public $id;
  public $idParent;
  public $scope;
  public $NbsTopics;
  public $XmlParam;
  public $sites;
  public $trace;
  public $infos;
  
  function __tostring() {
    return "Cette classe permet de d�finir et manipuler un site.<br/>";
    }

  function __construct($sites, $id, $complet=true) {

  	//echo "new Site $sites, $id, $scope<br/>";
    $this->trace = TRACE;
  	
    $this->sites = $sites;
    $this->id = $id;
    $this->infos = $this->sites[$this->id];
	$this->XmlParam = new XmlParam(XmlParam);
	
	if($this->infos["SITE_PARENT"]!=-1){
		$Parent = array_keys($this->infos["SITE_PARENT"]);
		$this->idParent = $Parent[0];
	}else{
		$this->idParent = -1;
	}

	//echo "FIN new Site <br/>";
		
    }

	function RequeteSelect($function,$arrVarVal,$obj=false){
         
	$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='".$function."']";
	$Q = $this->XmlParam->GetElements($Xpath);
	$select=$Q[0]->select;
	$from=$Q[0]->from;
	$where=$Q[0]->where;
	if($obj){
		foreach($obj as $t=>$v){
			$select=str_replace($t, $v,$select);    
			$from=str_replace($t, $v,$from);
			$where=str_replace($t, $v,$where);
			//vérifie s'il faut ajouter des sous from
			if($v!="" && $v!="Cherche"){
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Cherche".$t."']";
				$sQ = $this->XmlParam->GetElements($Xpath);
				$sfrom=$sQ[0]->from;
				if($sfrom){
					$from.=$this->RequeteGetFiltre($v,$t,$sfrom);
				}				
			}
		}
	}else{
		foreach($arrVarVal as $VarVal){
			$select=str_replace($VarVal[0], $VarVal[1],$select);    
			$from=str_replace($VarVal[0], $VarVal[1],$from);        
			$where=str_replace($VarVal[0], $VarVal[1],$where);      
		}
	}
	$order="";
	if(isset($_POST['orderField']) && $_POST['orderField']!="" && !strpos($where, "ORDER BY")){
		$order = " ORDER BY ".$_POST['orderField']." ".$_POST['orderDirection'];
	}
	
	
	$sql = $select.$from.$where.$order;
	$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
	$link=$db->connect();   
	$result = $db->query($sql);
	$db->close($link);
	
	return $result;
	        
	}    
	public function GetCurl($url)
	{
	
		$oCurl = curl_init($url);
		// set options
	   // curl_setopt($oCurl, CURLOPT_HEADER, true);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		//echo $sCmd."<br/>";
		//$arrInfos = curl_getinfo($ch);
		//print_r($arrInfos);
		//echo "sResult=<br/>";
		//print_r($sResult);
		//echo "<br/>";
		//fin ajout samszo
		
		// request URL
		$sResult = curl_exec($oCurl);
				
		// close session
		curl_close($oCurl);

		return $sResult;
	
	}
    
	public function GetFile($path){

	    if(!$_SESSION['ForceCalcul'] && file_exists($path)){
	    	$contents = file_get_contents($path);
			return $contents;
		}else{
			return false;	
		}
    }
    
    public function SaveFile($path,$texte){

		$fic = fopen($path, "w");
		if($fic){
			fwrite($fic, $texte);		
	    	fclose($fic);
		}

    }
    
	public function Synchronise($siteSrc, $siteDst=-1){
		if($siteDst==-1)
			$siteDst=$this->id;
    	
		//r�cup�re les mots clefs de la source
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetMotsClef']";
		if($this->trace)
			echo "Site:Synchronise:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$sql = $Q[0]->select.$Q[0]->from;
		$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Site:Synchronise:sql=".$sql."<br/>";
		while ($row =  $db->fetch_assoc($rows)) {
			//v�rifie l'existence dans la destination
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifMotsClef']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idGroupe-", $row['id_groupe'], $Q[0]->where);
			$where = str_replace("-titre-", $row['titre'], $where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
			$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$rowsVerif = $db->query($sql);
			$db->close();
			$rowVerif =  $db->fetch_assoc($rowsVerif);
			if($rowVerif['nb']==0){
				//ajoute le mot clef
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AjoutMotsClef']";
				$Q = $this->site->XmlParam->GetElements($Xpath);
				$values = str_replace("-idGroupe-", $row['id_groupe'], $Q[0]->values);
				$values = str_replace("-titre-", $row['titre'], $values);
				$sql = $Q[0]->insert.$values;
				$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
				$db->connect();
				$db->query($sql);
				$db->close();
			}
			
		}
			
    	
    }
    
    public function  Synchronise2($siteSrc, $siteDst=-1, $idAuteur=6){
    	
    	
    	if($siteDst==-1)
			$siteDst=$this->id;
    	
		//r�cup�re les rubriques de l'auteur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetRubriquesAuteur']";
		if($this->trace)
			echo "Site:Synchronise2:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idAuteur-", $idAuteur, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Site:Synchronise2:sql=".$sql."<br/>";
			
    }
    
    public function EstParent($id)
	{
		$arrParent = split("[".DELIM."]", $this->GetParentIds());
		//print_r($arrParent); 
		//echo $id."<br/>";	
		return in_array($id, $arrParent);	
	}

	public function GetParentIds($id = "")
	{
		if($id =="")
			$id = $this->id;
		//echo "GetParentIds = ".$id."<br/>";
			
		if($this->sites[$id]["SITE_PARENT"]!=-1){
			$Parent = array_keys($this->sites[$id]["SITE_PARENT"]);
			$idParent = $Parent[0];
			$valeur .= $this->GetParentIds($idParent);
			$valeur .= $id.DELIM;
		}
		//echo $valeur."<br/>";	
		return $valeur;

	}
	
	public function GetNomSiteParent($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		//print_r($this->sites[$id_site]["SITE_PARENT"]);
		if(is_array($this->sites[$id_site]["SITE_PARENT"])){
			foreach($this->sites[$id_site]["SITE_PARENT"] as $siteparent=>$type)
			{
				//echo $siteparent."=>".$type."<br/>";
				$valeur .=" ".$this->sites[$siteparent]["NOM"]." ";
				
			}
		}
		return $valeur;	
	}

	public function NextSiteEnfant($id_site)
	{
		$valeur=-1;
		if($this->infos["SITE_ENFANT"]!=-1){		
			$next=false;
			foreach($this->infos["SITE_ENFANT"] as $siteenfant=>$type)
			{
				//echo $this->id." NextSiteEnfant:".$siteenfant."=".$id_site." ".$next."<br/>"; 
				if($next){
					$valeur = $siteenfant;
					break;
				}
				if($siteenfant==$id_site)
					$next=true;				
			}
		}
		return $valeur;
	}

	public function GetSiteEnfant($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		foreach($this->sites[$id_site]["SITE_ENFANT"] as $siteenfant)
		{
			print_r($siteenfant);
			//$valeur .= $this->GetSiteEnfant($siteenfant=>id);
			//$valeur .= $r['id_rubrique'].DELIM;
			
		}	
	}

	public function GetFilAriane($id=-1)
	{
		if($id==-1)
			$id=$this->id;
			
		$valeur="";
		//echo $this->id." SiteParent=".$this->sites[$id_site]["SITE_PARENT"].'<br/>';
		if($this->sites[$id]["SITE_PARENT"]!=-1){		
			foreach($this->sites[$id]["SITE_PARENT"] as $SiteParent=>$titre)
			{
				$valeur .= $this->GetFilAriane($SiteParent);
			}
		}
		$lien =  "themes.php?site=".$id;
		$valeur .= "<a href='".$lien."'>".$this->sites[$id]["NOM"]."</a> | "."\n";

		return $valeur;		

	}

	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{

		if($theType=="html"){
		  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
		  $theValue = htmlentities($theValue);
		  $theType="text";
		}
		$theValue = str_replace("'","''",$theValue);
	  		
	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "''";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}


	public function GetLien($url, $type_select, $new_val, $arrSup=false)
	{
		if($this->scope!=-1){		
			foreach($this->scope as $param=>$val)
			{
				//prise en compte du tableau des valeurs de param�tre � modifier
				if(is_array($type_select)){
					$i = 0;
					$change = false;
					foreach($type_select as $type)
					{
						if($type==$param){
							$url .= $param."=".$new_val[$i]."&";
							$change = true;
						}
						$i ++;
					}
					if(!$change){
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}else{			
					if($type_select==$param)
						$url .= $param."=".$new_val."&";
					else{
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}
			}
		}
		//enl�ve la derni�re virgule
		$url = substr($url, 0, -1);
		
		return $url;
	}

	function GetSiteResult($site){
	
		$DBSearch = new DatabaseSearch(
			$site->infos["SQL_HOST"]
			,$site->infos["SQL_DB"]
			,$site->infos["SQL_LOGIN"]
			,$site->infos["SQL_PWD"]
			, false
			);
		//echo "DBSearch->needle=".$DBSearch->needle."<br/>";

		$recherche = $DBSearch->needle;

		//Search in table news, return data from column id, search in column tresc
		//It will use value from form (if defined) as needle.
		//$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("titre","texte"),"","AND");
		$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("texte"),"","AND");
		//print_r($search_result);
		if($search_result){
			$rstRub = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			/*	
			$search_result = $DBSearch->DoSearch("spip_mots m INNER JOIN spip_mots_rubriques mr ON mr.id_mot = m.id_mot","id_rubrique",array("titre"),"","AND");
			$rstMot = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			*/
			return array("site"=>$site->id,"recherche"=>$recherche,"rstRub"=>$rstRub);
		}
		
	}

	function GetAllResult($site=-1)
	{
		if($site==-1)
			$site = $this;
		
		$SitesEnfants = $site->infos["SITE_ENFANT"];
		//echo "v�rifie le calcul des sites enfants ".$SitesEnfants."<br/>";
		$NbT = 0;
		if(is_array($SitesEnfants)){
			//boucle sur les enfants
			$i = 0;
			foreach($SitesEnfants as  $SiteEnfant=>$type)
			{
				//echo "boucle sur les enfants ".$type." : ".$SiteEnfant." ".$this->site->sites[$SiteEnfant]."<br/>";
				$siteEnf = new Site($site->sites, $SiteEnfant, false);
				$R = $this->GetSiteResult($siteEnf);
				if($R){
					$Result[$i] = $R;
					//enregistre le r�sultat
					$site->NbsTopics[$SiteEnfant]=$Result[$i]["rstRub"]["nb"];
					//additionne le nombre de topic du site enfant
					//$NbT += $site->NbsTopics[$SiteEnfant];
					$i ++;
				}else
					$site->NbsTopics[$SiteEnfant]=0;

			}	
		}
		// enregistre le r�sultat
		//ajoute le nb de TOPIC du scope
		//$NbT += $site->NbsTopics[$site->id];
		$R = $this->GetSiteResult($site);
		if($R){
			$Result[$i] = $R;
			$site->NbsTopics[$site->id]=$Result[$i]["rstRub"]["nb"];
		}
		//print_r($site->NbsTopics);

		return $Result;
		
	}	
	
	function GetJs($Xpath, $arrParam, $nodesJs=-1)
	{
		if($nodesJs==-1){
			$nodesJs = $this->XmlParam->GetElements($Xpath);
		}		
		$js = "";
		foreach($nodesJs as $nodeJs)
		{
			$i=0;
			$function = $nodeJs["function"];
			if(count($arrParam[$i])>0){
				foreach($arrParam as $Param)
				{
					$function = str_replace("-param".$i."-", $Param, $function);
					$i++;	
				}
			}
			$js .= " ".$nodeJs["evt"]."=\"".$function."\"";
		}
		return $js;
	}
	
	function GetTreeChildren($type, $Cols=-1, $id=-1){

	    if($this->trace)
	    	echo ":GetTreeChildren: type = $type Cols = $Cols, id= $id<br/>";
		
	    if($Cols==-1){
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
			$Cols = $this->XmlParam->GetElements($Xpath);	
		}
		
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->XmlParam->GetElements($Xpath);
		if($id==-1){
			//r�cup�re la valeur par defaut
			$attrs = $Q[0]->where[0]->attributes();
			if($attrs["def"])
				$id = $attrs["def"];
			//echo $id." def<br/>";
		}
	
		$where = str_replace("-parent-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		
	    if($this->trace)
			echo "site::GetTreeChildren:".$this->infos["SQL_DB"]." ".$sql."<br/>";

		$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$nb = mysql_num_rows($req);

		$hierEnfant = "";
		$tree = '<treechildren >'.EOL;
		while($r = mysql_fetch_row($req))
		{
			$tree .= '<treeitem id="'.$type.'_'.$r[0].'" container="true" empty="false" open="true" >'.EOL;
			$tree .= '<treerow>'.EOL;
			$i= 0;
			//colonne de l'identifiant
			//$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
			foreach($Cols as $Col)
			{
				$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
				$i ++;
			}
			$tree .= '</treerow>'.EOL;
			$tree .= $this->GetTreeChildren($type, $Cols, $r[0]);
			$tree .= '</treeitem>'.EOL;
		}

		if($nb>0)
			$tree .= '</treechildren>'.EOL;
		else
			$tree = '';
		
		return $tree;

	}

 function stripAccents($string)
  {
    return strtr($string,'���������������������������������������������������',
		 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
  }
  function strtokey($str)
  {
    for ($iii = 0; $iii < strlen($str); $iii++)
      if (ord($str[$iii]) == 146 || ord($str[$iii]) == 156)
	$str[$iii] = '-';
    $key = str_replace("_", "-", $str);
    $key = str_replace("'", "-", $key);
    $key = str_replace("`", "-", $key);
    $key = str_replace(".", "-", $key);
    $key = str_replace(" ", "-", $key);
    $key = str_replace(",", "-", $key);
    $key = str_replace("{}", "_", $key);
    $key = str_replace("(", "_", $key);
    $key = str_replace(")", "_", $key);
    $key = str_replace("--", "-", $key);
    $key = str_replace("- -", "-", $key);
    $key = str_replace("<i>", "", $key);
    $key = str_replace("</i>", "", $key);
    $key = str_replace(":", "", $key);
    $key = str_replace("�", "", $key);
    $key = str_replace("�", "", $key);
    $key = str_replace("/", "", $key);
    $key = str_replace("�", "", $key);
    $key = str_replace("�", "", $key);
        
    $key = strtolower($key);
    return $this->stripAccents($key);
  }
		
	
  }


?>