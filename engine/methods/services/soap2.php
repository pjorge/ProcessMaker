<?php
  ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

  define ('WEB_SERVICE_VERSION' , '2.0' );

  //$wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos.wsdl";
  $wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos2.wsdl";

  require_once ( "classes/model/Application.php" );
  require_once ( "classes/model/AppDelegation.php" );
  require_once ( "classes/model/AppThread.php" );
  require_once ( "classes/model/Dynaform.php" );
  require_once ( "classes/model/Department.php" );
  require_once ( "classes/model/Groupwf.php" );
  require_once ( "classes/model/InputDocument.php" );
  require_once ( "classes/model/Language.php" );
  require_once ( "classes/model/OutputDocument.php" );
  require_once ( "classes/model/Process.php" );
  require_once ( "classes/model/ReportTable.php");
  require_once ( "classes/model/ReportVar.php");
  require_once ( "classes/model/Step.php" );
  require_once ( "classes/model/StepTrigger.php" );
  require_once ( "classes/model/Task.php" );
  require_once ( "classes/model/TaskUser.php" );
  require_once ( "classes/model/Triggers.php" );
  require_once ( "classes/model/Users.php" );
  require_once ( "classes/model/Session.php" );
  require_once ( "classes/model/Content.php" );
  G::LoadClass('wsResponse');
  G::LoadClass('wsBase');

  function login( $params ) {

    $ws = new wsBase ();
    $res = $ws->login( $params->userid, $params->password );
    return array(
      'status_code' => $res->status_code ,
	  'message'     => $res->message,
	  'version'     => WEB_SERVICE_VERSION,
	  'timestamp'	=> $res->timestamp
    );
  }

  function ProcessList( $params ) { 

    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("processes" => $o);
    }
    
    if (ifPermission( $params->sessionId, 'PM_CASES') != 0 ){
       $ws = new wsBase();
       $res = $ws->processList();
       return array("processes" => $res );
    } 

    if( ifPermission( $params->sessionId, 'PM_FACTORY') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("processes" => $o);
    }

    /** if you are not an admin user, then this function will return only your valid process **/
    if ( ifPermission( $params->sessionId, 'PM_FACTORY') == 0 ) {
      G::LoadClass('sessions');
      $oSessions = new Sessions();
      $session   = $oSessions->getSessionUser($params->sessionId);
      $userId    = $session['USR_UID'];

      $ws = new wsBase ();
      $res = $ws->processListVerified( $userId ); 
      return array("processes" => $res );
    }

    $ws = new wsBase();
    $res = $ws->processList();
    
    return array("processes" => $res );
  }

  function RoleList( $params ) {

    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("roles" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("roles" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->roleList();
    return array("roles" => $res );
  }

  function GroupList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("groups" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("groups" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->groupList();
    return array("groups" => $res );
  }

  function DepartmentList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("departments" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("departments" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->departmentList();
    return array("departments" => $res );
  }

  function CaseList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      $o->status = '';
      $o->delIndex = '';
      return array("cases" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      $o->status = '';
      $o->delIndex = '';
      return array("cases" => $o);
    }

    G::LoadClass('sessions');
    $oSessions = new Sessions();
    $session   = $oSessions->getSessionUser($params->sessionId);
    $userId    = $session['USR_UID'];

    $ws = new wsBase ();
    $res = $ws->caseList( $userId );
    return array("cases" => $res );
  }

  function UnassignedCaseList( $params ) { 
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      $o->delIndex = '';
      return array("cases" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      $o->delIndex = '';
      return array("cases" => $o);
    }

    G::LoadClass('sessions');
    $oSessions = new Sessions();
    $session   = $oSessions->getSessionUser($params->sessionId);
    $userId    = $session['USR_UID'];

    $ws = new wsBase ();
    $res = $ws->unassignedCaseList( $userId );
    return array("cases" => $res );
  }

  function UserList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("users" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("users" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->userList();
    return array("users" => $res );
  }

  function triggerList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      $o->processId = '';
      return array("triggers" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      $o->processId = '';
      return array("triggers" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->triggerList();
    return array("triggers" => $res );
  }

  function outputDocumentList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->filename = '';
      $o->docId = '';
      $o->version = '';
      $o->createDate = '';
      $o->createBy = '';
      $o->type = '';
      $o->index = '';
      $o->link = '';
      return array("documents" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->filename = '';
      $o->docId = '';
      $o->version = '';
      $o->createDate = '';
      $o->createBy = '';
      $o->type = '';
      $o->index = '';
      $o->link = '';
      return array("documents" => $o);
    }

    G::LoadClass('sessions');
    $oSessions = new Sessions();
    $session   = $oSessions->getSessionUser($params->sessionId);
    $userId    = $session['USR_UID'];


    $ws = new wsBase ();
    $res = $ws->outputDocumentList( $params->caseId, $userId );
    return array("documents" => $res );
  }

  function inputDocumentList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->filename = '';
      $o->docId = '';
      $o->version = '';
      $o->createDate = '';
      $o->createBy = '';
      $o->type = '';
      $o->index = '';
      $o->link = '';
      return array("documents" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->filename = '';
      $o->docId = '';
      $o->version = '';
      $o->createDate = '';
      $o->createBy = '';
      $o->type = '';
      $o->index = '';
      $o->link = '';
      return array("documents" => $o);
    }

    G::LoadClass('sessions');
    $oSessions = new Sessions();
    $session   = $oSessions->getSessionUser($params->sessionId);
    $userId    = $session['USR_UID'];

    $ws = new wsBase ();
    $res = $ws->inputDocumentList( $params->caseId, $userId );
    return array("documents" => $res );
  }

  function inputDocumentProcessList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      $o->description = '';
      return array("documents" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      $o->description = '';
      return array("documents" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->inputDocumentProcessList( $params->processId );
    return array("documents" => $res);
  }

  function removeDocument( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $result = new wsResponse (2, "Insufficient privileges to execute this function");
      return $result;
    }

    $ws = new wsBase ();
    $res = $ws->removeDocument( $params->appDocUid );
    return  $res;
  }

  function SendMessage( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult->getPayloadArray();
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ) {
      $result = new wsResponse (2, "You do not have privileges");
      return $result->getPayloadArray();
    }

    $ws = new wsBase ();
    $res = $ws->sendMessage(
      $params->caseId,
      $params->from,
      $params->to,
      $params->cc,
      $params->bcc,
      $params->subject,
      $params->template
    );
    return  $res->getPayloadArray() ;
  }

  function getCaseInfo( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ) {
      $result = new wsResponse (2, "You do not have privileges");
      return $result;
    }

    $ws = new wsBase ();
    $res = $ws->getCaseInfo( $params->caseId, $params->delIndex );
    return  $res;
  }

  function SendVariables( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ) {
      $result = new wsResponse (2, "You do not have privileges");
      return $result;
    }

    $ws = new wsBase();
    $variables = $params->variables;
    $Fields = Array();
    if ( is_object($variables) ) {
      $Fields[$variables->name] = $variables->value;
    } else if ( is_array($variables) ) {
      foreach ( $variables as $index=>$obj ) {
      	if ( is_object($obj) && isset($obj->name) && isset($obj->value)){
          $Fields[$obj->name] = $obj->value;
        }
      }
    }

    $params->variables = $Fields;
    $res = $ws->sendVariables($params->caseId, $params->variables);

    return  $res->getPayloadArray();

  }

  function GetVariables( $params ) {    
  	if (!is_array($params->variables)) {
      $params->variables = array($params->variables);
    }
    
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $result = new wsGetVariableResponse (2, "You do not have privileges", NULL );
      return $result;
    }

    $ws = new wsBase ();

    $res = $ws->getVariables($params->caseId, $params->variables);
    return $res;
  }

  function DerivateCase( $params ) {
    $oSession = new Sessions();

    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $result = new wsResponse (2, "You do not have privileges");
      return $result;
    }

    $user  = $oSession->getSessionUser($params->sessionId);

    $oStd->stored_system_variables = true;
    $oStd->wsSessionId = $params->sessionId;
    $ws = new wsBase($oStd);
    $res = $ws->derivateCase($user['USR_UID'], $params->caseId, $params->delIndex);
    return $res;
  }

  function RouteCase( $params ) {
    $oSession = new Sessions();

    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $result = new wsResponse (2, "You do not have privileges");
      return $result;
    }

    $user  = $oSession->getSessionUser($params->sessionId);

    $oStd->stored_system_variables = true;
    $oStd->wsSessionId = $params->sessionId;
    $ws = new wsBase($oStd);
    $res = $ws->derivateCase($user['USR_UID'], $params->caseId, $params->delIndex);
    return $res;

    //return  $res->getPayloadArray ( );
  }

  function executeTrigger ( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $result = new wsResponse (2, "You do not have privileges");
      return $result;
    }

    $oSession = new Sessions();
    $user  = $oSession->getSessionUser($params->sessionId);


    $ws = new wsBase ();
    $delIndex = ( isset ( $params->delIndex ) ) ? $params->delIndex : 1 ;
    $res = $ws->executeTrigger( $user['USR_UID'], $params->caseId, $params->triggerIndex, $delIndex);
    return  $res->getPayloadArray ( );
  }

  function NewCaseImpersonate($params) {
    $vsResult = isValidSession($params->sessionId);
    
    if ($vsResult->status_code !== 0) {
      return $vsResult;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
      $result = new wsResponse(2, "You do not have privileges");
      return $result;
    }

    ///////
    $variables = $params->variables;
    
    $field = array();
    
    if (is_object($variables)) {
      $field[$variables->name] = $variables->value;
    }
    else {
      if (is_array($variables)) {
        foreach ($variables as $index => $obj) {
          if (is_object($obj) && isset($obj->name) && isset($obj->value)) {
            $field[$obj->name] = $obj->value;
          }
        }
      }
    }

    $params->variables = $field;
    
    ///////
    $ws = new wsBase();
    $res = $ws->newCaseImpersonate($params->processId, $params->userId, $params->variables);
    
    return $res;
  }

  function NewCase($params) {
    G::LoadClass("sessions");

    $vsResult = isValidSession($params->sessionId);
    
    if ($vsResult->status_code !== 0) {
      return $vsResult;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
      $result = new wsResponse(2, "You do not have privileges");
      return $result;
    }

    $oSession = new Sessions();
    $session   = $oSession->getSessionUser($params->sessionId);
    $userId    = $session["USR_UID"];
    $variables = $params->variables;

/* this code is for previous version of ws, and apparently this will work for grids inside the variables..
    if (!isset($params->variables) ) {
      $variables = array();
      $field = array();
    }
    else {
      if ( is_object ($variables) ) {
        $field[ $variables->name ]= $variables->value ;
      }

      if ( is_array ( $variables) ) {
        foreach ( $variables as $key=>$val ) {
          $name  = $val->name;
          $value = $val->value;
          if (!is_object($val->value))
          {
            eval('$field[ ' . $val->name . ' ]= $val->value ;');
          }
          else
          {
            if (is_array($val->value->item)) {
              $i = 1;
              foreach ($val->value->item as $key1 => $val1) {
                if (isset($val1->value)) {
                  if (is_array($val1->value->item)) {
                    foreach ($val1->value->item as $key2 => $val2) {
                      $field[$val->name][$i][$val2->key] = $val2->value;
                    }
                  }
                }
                $i++;
              }
            }
          }
        }
      }
    }
*/
    $variables = $params->variables;
    
    $field = array();

    if (is_object($variables)) {
      $field[$variables->name]= $variables->value;
    }
    if (is_array($variables)) {
      foreach ($variables as $key => $val) {
        if (!is_object($val->value)) {
            eval("\$field[" . $val->name . "]= \$val->value;");
        }
      }
    }
    
    $params->variables = $field;
    $ws = new wsBase();
    $res = $ws->newCase($params->processId, $userId, $params->taskId, $params->variables);

    ######################################################################
    # we need to register the case id for a stored session variable. like a normal Session.
    ######################################################################
    $oSession->registerGlobal("APPLICATION", $res->caseId);
    ######################################################################

    return  $res;
  }

  function AssignUserToGroup( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult->getPayloadArray();
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $result = new wsResponse (2, "You do not have privileges");
      return $result->getPayloadArray();
    }
    G::LoadClass('sessions');
    $sessions = new Sessions;
    $user=$sessions->getSessionUser($params->sessionId);

    if(!is_array($user)){
      return new wsResponse (3, 'User not registered in the system');
    }


    $ws = new wsBase ();
    $res = $ws->assignUserToGroup( $params->userId, $params->groupId);
    return  $res->getPayloadArray();
  }

  function AssignUserToDepartment( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult->getPayloadArray();
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ){
      $result = new wsResponse (2, "You do not have privileges");
      return $result->getPayloadArray();
    }
    G::LoadClass('sessions');
    $sessions = new Sessions;
    $user=$sessions->getSessionUser($params->sessionId);

    if(!is_array($user)){
      return new wsResponse (3, 'User not registered in the system');
    }


    $ws = new wsBase ();
    $res = $ws->AssignUserToDepartment( $params->userId, $params->departmentId, $params->manager );
    return  $res->getPayloadArray();
  }

  function CreateUser( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0){
      $result = new wsCreateUserResponse (2, "You do not have privileges");
      return $result;
    }

    $ws = new wsBase ();
    $res = $ws->createUser( $params->userId, $params->firstname, $params->lastname, $params->email, $params->role, $params->password);
    return  $res;
  }

  function CreateGroup( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $result = new wsCreateGroupResponse ($vsResult->status_code , $vsResult->message, '' );
      return $result;
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0 ) {
      $result = new wsCreateGroupResponse (2, "You do not have privileges", '');
      return $result;
    }

    $ws = new wsBase ();
    $res = $ws->createGroup( $params->name );
    return  $res;
  }

  function CreateDepartment( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    if( ifPermission( $params->sessionId, 'PM_USERS') == 0){
      $result = new wsCreateUserResponse (2, "You do not have privileges");
      return $result;
    }

    $ws = new wsBase ();
    $res = $ws->CreateDepartment( $params->name, $params->parentUID );
    return  $res;
  }

  function TaskList( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("tasks" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("tasks" => $o);
    }

    G::LoadClass('sessions');
    $ws = new wsBase ();
    $oSessions = new Sessions();
    $session   = $oSessions->getSessionUser($params->sessionId);
    $userId    = $session['USR_UID'];
    $res = $ws->taskList( $userId );
    return array("tasks" => $res );
  }

  function TaskCase( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      $o->guid = $vsResult->status_code. ' ' . $vsResult->message;
      $o->name = '';
      return array("taskCases" => $o);
    }

    if( ifPermission( $params->sessionId, 'PM_CASES') == 0 ){
      $o->guid = "2 Insufficient privileges to execute this function";
      $o->name = '';
      return array("taskCases" => $o);
    }

    $ws = new wsBase ();
    $res = $ws->taskCase( $params->caseId );
    return array("taskCases" => $res );
  }

  function ReassignCase( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    $ws = new wsBase ();
    $res = $ws->reassignCase($params->sessionId, $params->caseId, $params->delIndex, $params->userIdSource, $params->userIdTarget );
    return $res ;
  }

  function systemInformation( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    $ws = new wsBase ();
    $res = $ws->systemInformation( );
    return $res;
  }

  function importProcessFromLibrary( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    $ws = new wsBase ();
    $res = $ws->importProcessFromLibrary( $params->processId ,$params->version ,$params->importOption ,$params->usernameLibrary ,$params->passwordLibrary  );
    return $res;
  }

  function getCaseNotes( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    $ws = new wsBase ();
    $res = $ws->getCaseNotes( $params->applicationID ,$params->userUid);
    return $res;
  }

  /*************/

  #added By Erik AO <erik@colosa.com> in datetime 26.06.2008 10:00:00
  # modified 12-01-2010 by erik

  function isValidSession($sessionId){
    G::LoadClass('sessions');
    $oSessions = new Sessions();
    $session   = $oSessions->verifySession($sessionId);
    if( is_array($session) ) {
      return new wsResponse (0, 'Session active');
    } else {

      return new wsResponse (9, 'Session expired');
    }
  }
//add removeUserFromGroup
  function removeUserFromGroup( $params ) {
    $vsResult = isValidSession($params->sessionId);
    if( $vsResult->status_code !== 0 ){
      return $vsResult;
    }

    $ws = new wsBase ();
    $res = $ws->removeUserFromGroup($params->userId, $params->groupId);
    return $res ;
  }
    //end add
  function ifPermission( $sessionId, $permission ){
      global $RBAC;
            $RBAC->initRBAC();
            G::LoadClass('sessions');
            $oSession = new Sessions();
            $user  = $oSession->getSessionUser($sessionId);

            $oRBAC = RBAC::getSingleton();
      $oRBAC->loadUserRolePermission($oRBAC->sSystem, $user['USR_UID']);
      $aPermissions = $oRBAC->aUserInfo[$oRBAC->sSystem]['PERMISSIONS'];
      $sw=0;
      foreach ($aPermissions as $aPermission) {
        if ($aPermission['PER_CODE'] == $permission)
        {
          $sw=1;
        }
      }
      return $sw;
  }


$server = new SoapServer( $wsdl );
$server->addFunction("Login");
$server->addFunction("ProcessList");
$server->addFunction("CaseList");
$server->addFunction("UnassignedCaseList");
$server->addFunction("RoleList");
$server->addFunction("GroupList");
$server->addFunction("DepartmentList");
$server->addFunction("UserList");
$server->addFunction("TriggerList");
$server->addFunction("outputDocumentList");
$server->addFunction("inputDocumentList");
$server->addFunction("inputDocumentProcessList");
$server->addFunction("removeDocument");
$server->addFunction("SendMessage");
$server->addFunction("SendVariables");
$server->addFunction("GetVariables");
$server->addFunction("DerivateCase");
$server->addFunction("RouteCase");
$server->addFunction("executeTrigger");
$server->addFunction("NewCaseImpersonate");
$server->addFunction("NewCase");
$server->addFunction("AssignUserToGroup");
$server->addFunction("AssignUserToDepartment");
$server->addFunction("CreateGroup");
$server->addFunction("CreateDepartment");
$server->addFunction("CreateUser");
$server->addFunction("getCaseInfo");
$server->addFunction("TaskList");
$server->addFunction("TaskCase");
$server->addFunction("ReassignCase");
$server->addFunction("systemInformation");
$server->addFunction("importProcessFromLibrary");
$server->addFunction("removeUserFromGroup");
$server->addFunction("getCaseNotes");
$server->handle();

