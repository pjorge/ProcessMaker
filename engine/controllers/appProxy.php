<?php
/**
 * App controller
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @herits Controller
 * @access public
 */

class AppProxy extends HttpProxyController
{
  /**
   * Get Notes List
   * @param int $httpData->start
   * @param int $httpData->limit
   * @param string $httpData->appUid (optionalif it is not passed try use $_SESSION['APPLICATION'])
   * @return array containg the case notes
   */
  function getNotesList($httpData)
  {
    require_once ( "classes/model/AppNotes.php" );
    $appUid = null;
    
    if (isset($httpData->appUid) && trim($httpData->appUid) != "") {
      $appUid = $httpData->appUid;
    } 
    else {
      if (isset($_SESSION['APPLICATION'])) {
        $appUid = $_SESSION['APPLICATION'];
      }
    }

    if (!isset($appUid)) {
      throw new Exception('Can\'t resolve the Apllication ID for this request.');
    }

    $usrUid   = isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : "";
    $appNotes = new AppNotes();
    $response = $appNotes->getNotesList($appUid, '', $httpData->start, $httpData->limit);
    
    return $response['array'];
  }

  /**
   * post Note Action
   * @param string $httpData->appUid (optional, if it is not passed try use $_SESSION['APPLICATION'])
   * @return array containg the case notes
   */
  function postNote($httpData) 
  {
    //extract(getExtJSParams());
    if (isset($httpData->appUid) && trim($httpData->appUid) != "") {
      $appUid = $httpData->appUid;
    } 
    else {
      $appUid = $_SESSION['APPLICATION'];
    }
    
    if (!isset($appUid)) {
      throw new Exception('Can\'t resolve the Apllication ID for this request.');
    }

    $usrUid = (isset($_SESSION['USER_LOGGED'])) ? $_SESSION['USER_LOGGED'] : "";
    require_once ( "classes/model/AppNotes.php" );

    $appNotes = new AppNotes();
    $noteContent = addslashes($httpData->noteText);

    $result = $appNotes->postNewNote($appUid, $usrUid, $noteContent, false);

    // Disabling the controller response because we handle a special behavior
    $this->setSendResponse(false);

    //send the response to client
    @ini_set('implicit_flush', 1);
    ob_start();
    echo G::json_encode($result);
    @ob_flush();
    @flush();
    @ob_end_flush();
    ob_implicit_flush(1);

    //send notification in background
    $noteRecipientsList = array();
    G::LoadClass('case');
    $oCase = new Cases();

    $p = $oCase->getUsersParticipatedInCase($appUid);
    foreach($p['array'] as $key => $userParticipated){
      $noteRecipientsList[] = $key;
    }
    $noteRecipients = implode(",", $noteRecipientsList);

    $appNotes->sendNoteNotification($appUid, $usrUid, $noteContent, $noteRecipients);
  }

  /**
   * request to open the case summary
   * @param string $httpData->appUid
   * @param string $httpData->delIndex
   * @return object bool $result->succes, string $result->message(is an exception was thrown), string $result->dynUid 
   */
  function requestOpenSummary($httpData)
  {
    global $RBAC;
    $this->success = true;
    $this->dynUid = '';

    switch ($RBAC->userCanAccess('PM_CASES')) {
      case -2:
        throw new Exception(G::LoadTranslation('ID_USER_HAVENT_RIGHTS_SYSTEM'));
      break;
      case -1:
        throw new Exception(G::LoadTranslation('ID_USER_HAVENT_RIGHTS_PAGE'));
      break;
    }

    G::LoadClass('case');
    $case = new Cases();

    if ($RBAC->userCanAccess('PM_ALLCASES') < 0 && $case->userParticipatedInCase($httpData->appUid, $_SESSION['USER_LOGGED']) == 0) {
      throw new Exception(G::LoadTranslation('ID_NO_PERMISSION_NO_PARTICIPATED'));
    }

    $applicationFields = $case->loadCase($httpData->appUid, $httpData->delIndex);
    $process = new Process();
    $processData = $process->load($applicationFields['PRO_UID']);
    
    if (isset($processData['PRO_DYNAFORMS']['PROCESS'])) {
      $this->dynUid = $processData['PRO_DYNAFORMS']['PROCESS'];
    }

    $_SESSION['_applicationFields']   = $applicationFields;
    $_SESSION['_processData']         = $processData;
    $_SESSION['APPLICATION']          = $httpData->appUid;
    $_SESSION['INDEX']                = $httpData->delIndex;
    $_SESSION['PROCESS']              = $applicationFields['PRO_UID'];
    $_SESSION['TASK']                 = $applicationFields['TAS_UID'];
    $_SESSION['STEP_POSITION']        = '';
  }

  /**
   * get the case summary data
   * @param string $httpData->appUid 
   * @param string $httpData->delIndex
   * @return array containg the case summary data
   */
  function getSummary($httpData)
  {
    $labels = array();
    $form = new Form('cases/cases_Resume', PATH_XMLFORM, SYS_LANG);
    G::LoadClass('case');
    $case = new Cases();

    foreach($form->fields as $fieldName => $field) {
      $labels[$fieldName] = $field->label;
    }

    if (isset($_SESSION['_applicationFields']) && $_SESSION['_processData']) {
      $applicationFields = $_SESSION['_applicationFields'];
      unset($_SESSION['_applicationFields']);
      $processData       = $_SESSION['_processData'];
      unset($_SESSION['_processData']);
    }
    else {
      $applicationFields = $case->loadCase($httpData->appUid, $httpData->delIndex);
      $process = new Process();
      $processData = $process->load($applicationFields['PRO_UID']);
    }

    $data = array();
    $task = new Task();
    $taskData = $task->load($applicationFields['TAS_UID']);
    $currentUser = $applicationFields['CURRENT_USER'] != '' ? $applicationFields['CURRENT_USER'] : '[' . G::LoadTranslation('ID_UNASSIGNED') . ']';


    $data[] = array('label'=>$labels['PRO_TITLE'] ,      'value' => $processData['PRO_TITLE'],        'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['TITLE'] ,          'value' => $applicationFields['TITLE'],      'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['APP_NUMBER'] ,     'value' => $applicationFields['APP_NUMBER'], 'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['STATUS'] ,         'value' => $applicationFields['STATUS'],     'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['APP_UID'] ,        'value' => $applicationFields['APP_UID'],    'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['CREATOR'] ,        'value' => $applicationFields['CREATOR'],    'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['CREATE_DATE'] ,    'value' => $applicationFields['CREATE_DATE'],'section'=>$labels['TITLE1']);
    $data[] = array('label'=>$labels['UPDATE_DATE'] ,    'value' => $applicationFields['UPDATE_DATE'],'section'=>$labels['TITLE1']);

    // note added by krlos pacha carlos[at]colosa[dot]com
    //getting this field if it doesn't exist. Related 7994 bug
    $taskData['TAS_TITLE'] = (array_key_exists('TAS_TITLE', $taskData))?$taskData['TAS_TITLE']:Content::Load("TAS_TITLE", "", $applicationFields['TAS_UID'], SYS_LANG);
    
    $data[] = array('label'=>$labels['TAS_TITLE'] ,         'value' => $taskData['TAS_TITLE'],                 'section'=>$labels['TITLE2']);
    $data[] = array('label'=>$labels['CURRENT_USER'] ,      'value' => $currentUser,                           'section'=>$labels['TITLE2']);
    $data[] = array('label'=>$labels['DEL_DELEGATE_DATE'] , 'value' => $applicationFields['DEL_DELEGATE_DATE'],'section'=>$labels['TITLE2']);
    $data[] = array('label'=>$labels['DEL_INIT_DATE'] ,     'value' => $applicationFields['DEL_INIT_DATE'],    'section'=>$labels['TITLE2']);
    $data[] = array('label'=>$labels['DEL_TASK_DUE_DATE'] , 'value' => $applicationFields['DEL_TASK_DUE_DATE'],'section'=>$labels['TITLE2']);
    $data[] = array('label'=>$labels['DEL_FINISH_DATE'] ,   'value' => $applicationFields['DEL_FINISH_DATE'],  'section'=>$labels['TITLE2']);
    //$data[] = array('label'=>$labels['DYN_UID'] ,           'value' => $processData['PRO_DYNAFORMS']['PROCESS'];, 'section'=>$labels['DYN_UID']);

    return $data;
  }

}
