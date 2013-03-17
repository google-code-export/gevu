<?php
class GEVU_Rapport{

    /**
     * récupère la liste des diagnostics pour un lieu
     *
     * @param int $idLieu
     * @param string $idBase
     * 
     * @return array
     */
	public function getSolusProb($idLieu, $idBase){

	try {
		
			$diag = new GEVU_Diagnostique();
			$dbD = new Models_DbTable_Gevu_diagnostics();		
			
			$arrDB = $diag->db->getConfig();
			$arrD = $dbD->getDiagSolus($idLieu, $idBase, $arrDB['dbname']);
			$arrR = array();
			$idCrit = -1; 
			$idSolus = -1;
			$idProd = -1;
			$i=-1;$j=-1;$k=-1;
			foreach ($arrD as $d) {
				if($idCrit != $d['id_critere']){
					$i ++;
					$idCrit = $d['id_critere'];
					$arrR[$i] = array("id_diag"=>$d['id_diag'],"id_critere"=>$d['id_critere'],"id_type_critere"=>$d['id_type_critere'],"id_reponse"=>$d['id_reponse'],"reponse"=>$d['reponse'],"diagIdLieu"=>$d['diagIdLieu'],"diagLieu"=>$d['diagLieu'],"id_lieu"=>$d['id_lieu'],"lib"=>$d['lib'],"ref"=>$d['ref'],"affirmation"=>$d['affirmation'],"controle"=>$d['controle']);
					$idSolus = -1;
					$idProd = -1;
					$j=-1;$k=-1;
				}
				if($d['id_solution'] && $idSolus != $d['id_solution']){
					$j ++;
					$idSolus = $d['id_solution'];
					$arrR[$i]["solutions"][$j] = array("id_solution"=>$d['id_solution'],"solution"=>$d['solution'], "ref"=>$d['refSolu']);
					$idProd = -1;
					$k=-1;
				}
				if($d['id_produit'] && $idProd != $d['id_produit']){
					$k ++;
					$idProd = $d['id_produit'];
					$arrR[$i]["solutions"][$j]["produits"][$k] = array("id_produit"=>$d['id_produit'],"ref"=>$d['refProd'],"description"=>$d['description'], "marque"=>$d['marque'], "modele"=>$d['modele']);
				}
				if($d['id_cout']){
					$arrR[$i]["solutions"][$j]["produits"][$k]["couts"][] = array("id_cout"=>$d['id_cout'],"unite"=>$d['unite'],"metre_lineaire"=>$d['metre_lineaire'],"metre_carre"=>$d['metre_carre'],"achat"=>$d['achat'],"pose"=>$d['pose'],"solution"=>$d['solution']);
				}						
				if($d['Sid_cout']){
					$arrR[$i]["solutions"][$j]["couts"][] = array("id_cout"=>$d['Sid_cout'],"unite"=>$d['Sunite'],"metre_lineaire"=>$d['Smetre_lineaire'],"metre_carre"=>$d['Smetre_carre'],"achat"=>$d['Sachat'],"pose"=>$d['Spose'],"solution"=>$d['Ssolution']);
				}						
			}
			
			return $arrR;
		    
		}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
		    echo "Message: " . $e->getMessage() . "\n";
		}
		return "";
    }    
  	
}	
