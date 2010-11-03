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
			->setCredentialTreatment('MD5(CONCAT(?, mdp_sel))');
			
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
		$acl->addRole(new Zend_Acl_Role('guest'));
		$acl->addRole(new Zend_Acl_Role('manager'), 'guest');
		$acl->addRole(new Zend_Acl_Role('admin'), 'manager');
		
		// Administrator does not inherit access controls, All access is granted
		$acl->addRole(new Zend_Acl_Role('Super'));
		
		// setup the resource privs
		$acl->add(new Zend_Acl_Resource('viewPublicUI'));
		$acl->add(new Zend_Acl_Resource('viewRestrictedUI'));
		$acl->add(new Zend_Acl_Resource('viewLogs'));
		$acl->add(new Zend_Acl_Resource('createManager'));
		
		// Guest may only view the public interface
		$acl->allow('guest', null, 'viewPublicUI');
		
		// manager inherits viewPublicUI privilege from guest,but also needs additional
		// privileges
		$acl->allow('manager', null, array('viewRestrictedUI'));
		
		// admin inherits viewRestrictedUI privilege from
		// manager, but also needs additional privileges
		$acl->allow('admin', null, array('createManager'));
		
		// Super inherits nothing, but is allowed all privileges
		$acl->allow('Super');
		
		// userRoleVO to Privs Map
		$userRolePrivs = new AUTH_AccessPrivsVO();
		$userRolePrivs->idExi = $userId;
		$userRolePrivs->ExiRole = $userRole;
		$userRolePrivs->viewPublicUI =
		$acl->isAllowed($userRole, null, 'viewPublicUI') ?	"allowed" : "denied";
		$userRolePrivs->viewRestrictedUI =
			$acl->isAllowed($userRole, null, 'viewRestrictedUI') ? "allowed" : "denied";
		$userRolePrivs->createManager =
			$acl->isAllowed($userRole, null, 'createManager') ?	"allowed" : "denied";
		$userRolePrivs->viewLogs =
			$acl->isAllowed($userRole, null, 'viewLogs') ?	"allowed" : "denied";
		
		return $userRolePrivs;
	
	}
}