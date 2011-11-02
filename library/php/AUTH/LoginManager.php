<?php
/*
* LoginManager
*
* Verify's the users login credentials and
checks the users role against the ACL for access rights.
*
* @return Access Privileges
*/
class AUTH_LoginManager {

	private $dbAdapter;
	private $authAdapter;
	
	/**
	* @return mixed
	*/
	public function __construct() {
	
		// Get a reference to the singleton instance of Zend_Auth
		$this->auth = Zend_Auth::getInstance();
	}
	
	// test
	
	/**
	* @return void
	*/
	public function test() {
		return "Success! Test Completed Normally";
	}
	
	/**
	*
	* Authenticates the user
	*
	* @todo add routine to verify using SSO
    * @param mixed $user
	* 	
	* @return mixed
	*
	*/
	public function verifyUser($user) {
	
		$userRole='';
		// Configure the instance with constructor parameters…
		$authAdapter = new Zend_Auth_Adapter_DbTable();//on prend le table adapter par défaut
		$authAdapter->setTableName('gevu_exis')
            ->setIdentityColumn('nom')
            ->setCredentialColumn('mdp')
			//->setCredentialTreatment('MD5(?)')
            //->setCredentialTreatment('MD5(CONCAT(?, mdp_sel))')
			;
		$usr=htmlspecialchars($user->username);
		$pwd=htmlspecialchars($user->password);
	
		if($usr == ''){
			$authAdapter
				->setIdentity('guest')
				->setCredential('guest');
		}else{
			$authAdapter
				->setIdentity($usr)
				->setCredential($pwd);
		}
		$result = $authAdapter->authenticate();
	
		switch ($result->getCode()) {
	
			case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
				$userRole = "guest";
				break;
		
			case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
				$userRole = "guest";
				return "FAILURE_CREDENTIAL_INVALID";
				break;
	
			case Zend_Auth_Result::FAILURE:
				$userRole = 'guest';
				break;
	
			case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
				$userRole = 'guest';
				break;
	
			case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
				$userRole = 'guest';
				break;
		
			case Zend_Auth_Result::SUCCESS:
	
				// We need to return the authenticated users role, this will be passed into the Zend_Acl
				// getResultRowObject returns a stdClass object so we need to dereference the role in this manner.
				$r=$authAdapter->getResultRowObject(array('id_exi','role'));
				$userRole = $r->role;
				$userId = $r->id_exi;
				break;
	
			default:
				return "Internal Error! If this problem persist, please contact your network administrator";
				break;
		}
	
		// Set up the ACL (Access Control List)
		$acl = new Zend_Acl();
		// Add groups to the Role registry using Zend_Acl_Role
		// Guest does not inherit access controls.
		// Order matters here, we go from the most	restricted to the least restricted
		$dbRole = new Models_DbTable_Gevu_roles();
		$rs = $dbRole->getAll();
		foreach ($rs as $r){
			if($r['inherit']!=""){
				$acl->addRole(new Zend_Acl_Role($r['lib'],$r['inherit']));							
			}else{
				$acl->addRole(new Zend_Acl_Role($r['lib']));							
			}
			$res = json_decode($r['params']);
			if($res==""){
				$acl->allow($r['lib']);	
			}else{
				foreach ($res as $re){
					// setup the resource privs
					if(!$acl->has($re->lib))	$acl->addResource($re->lib);
					// application de la ressource
					$acl->allow($r['lib'], null, $re->lib);				
				}
			}
		}
		
		//création du tableau des droits
		$userRolePrivs = array();		
		$userRolePrivs["idExi"] = $userId;
		$userRolePrivs["ExiRole"] = $userRole;
		//ajoute les autorisations liées au role
		$rs = $acl->getResources();
		foreach ($rs as $r){
			$userRolePrivs[$r] = $acl->isAllowed($userRole, null, $r);			
		}
		//ajoute les autorisations liées au droit de l'utilisateur
		$dbDroits = new Models_DbTable_Gevu_exisxdroits();
		$rs = $dbDroits->findByIdExi($userId);
		foreach ($rs as $r){
			$userRolePrivs["droit_".$r['id_droit']] = $r['params'];						
		}
		
		return $userRolePrivs;
	
	}
}