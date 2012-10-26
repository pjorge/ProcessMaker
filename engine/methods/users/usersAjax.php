<?php

global $RBAC;

switch($_POST['action'])
{
  case 'countryList' :   
    require_once ("classes/model/IsoCountry.php");
    $c = new Criteria();
    $c->add(IsoCountryPeer::IC_UID, NULL, Criteria::ISNOTNULL);    
    
    $countries = IsoCountryPeer::doSelect($c);
    foreach ( $countries as $rowid => $row ) {
      $oData[] = Array('IC_UID'=>$row->getICUid(),'IC_NAME'=>$row->getICName());  
    }
    print(G::json_encode($oData));
    break;
  
  case 'stateList' :
    require_once ("classes/model/IsoSubdivision.php");
    $c       = new Criteria();
    $country = $_POST['IC_UID'];        
    $c->add(IsoSubdivisionPeer::IC_UID, $country, Criteria::EQUAL);    
    $locations = IsoSubdivisionPeer::doSelect($c);

    foreach( $locations as $rowid => $row ) {
      if (($row->getISUid() != '') && ($row->getISName() != ''))
        $oData[] = Array('IS_UID' => $row->getISUid(), 'IS_NAME' => $row->getISName());  
    }
    print(G::json_encode($oData));
    break;

  case 'locationList' :
    require_once ("classes/model/IsoLocation.php");
    $c       = new Criteria();
    $country = $_POST['IC_UID'];
    $state   = $_POST['IS_UID'];
    $c->add(IsoLocationPeer::IC_UID, $country, Criteria::EQUAL);
    $c->add(IsoLocationPeer::IS_UID, $state, Criteria::EQUAL); 
    $locations = IsoLocationPeer::doSelect($c);

    foreach ( $locations as $rowid => $row ) {
      if (($row->getILUid() != '') && ($row->getILName() != ''))
        $oData[] = Array('IL_UID' => $row->getILUid(), 'IL_NAME' => $row->getILName());  
    }
    print(G::json_encode($oData));
    break;
  case 'usersList' :
    require_once 'classes/model/Users.php';
    $oCriteria = new Criteria();
    $oCriteria->addSelectColumn(UsersPeer::USR_UID);
    $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);
    $oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
    $oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
    $oCriteria->addSelectColumn(UsersPeer::USR_EMAIL);
    $oCriteria->add(UsersPeer::USR_STATUS,'ACTIVE');    
    $oDataset = UsersPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    G::loadClass('configuration');
    $oConf = new Configurations;
    $oConf->loadConfig($obj, 'ENVIRONMENT_SETTINGS','');

    $defaultOption = isset($oConf->aConfig['format']) ? $oConf->aConfig['format'] : '';

    $aUserInfo = array();
    while ( $oDataset->next()) {
      $aRow1 = $oDataset->getRow();

      $infoUser    = G::getFormatUserList($defaultOption,$aRow1);
      $aUserInfo[] = array(
                    'USR_UID'       => $aRow1['USR_UID'],
                    'USER_FULLNAME' => $infoUser
                   );
    }
    print(G::json_encode($aUserInfo));

    break;

  case 'availableCalendars' :
    G::LoadClass ( 'calendar' );
    $calendar    = new Calendar ( );
    $calendarObj = $calendar->getCalendarList ( true, true );
    $oData[]     = array('CALENDAR_UID' => '', 'CALENDAR_NAME' => '- None -');
    foreach ( $calendarObj ['array'] as $rowid => $row ) {
      if ($rowid > 0)      
        $oData[] = array('CALENDAR_UID' => $row['CALENDAR_UID'], 'CALENDAR_NAME' => $row['CALENDAR_NAME']);
    }
    print(G::json_encode($oData));
    break;
  case 'rolesList':
    require_once PATH_RBAC."model/Roles.php";
    $roles     = new Roles();
    $rolesData = $roles->getAllRoles();
    foreach( $rolesData as $rowid => $row ) {
       $oData[] = array('ROL_UID' => $row['ROL_CODE'],'ROL_CODE' => $row['ROL_CODE']);
    }  
    print(G::json_encode($oData));
    break;
  case 'saveUser' :
    try {
      $form = $_POST;

      if ( isset($_POST['USR_UID'])) {
        $form['USR_UID'] = $_POST['USR_UID'];
      }
      else {
        $form['USR_UID'] = '';
      }
/*     
      if ( isset($_FILES['USR_RESUME']['name']) ) {
        if ($_FILES['USR_RESUME']['tmp_name'] != '') {
          $form['USR_RESUME'] = $_FILES['USR_RESUME']['name'];
        }
        else {
          $form['USR_RESUME'] = '';
        }
      }
*/
      if (!isset($form['USR_NEW_PASS'])) {
        $form['USR_NEW_PASS'] = '';
      }
      if ($form['USR_NEW_PASS'] != '') {
        $form['USR_PASSWORD'] = md5($form['USR_NEW_PASS']);
      }
      if (!isset($form['USR_CITY'])) {
        $form['USR_CITY'] = '';
      }
      if (!isset($form['USR_LOCATION'])) {
        $form['USR_LOCATION'] = '';
      }
      if (!isset($form['USR_AUTH_USER_DN'])) {
        $form['USR_AUTH_USER_DN'] = '';
      }
  
      if ($form['USR_UID'] == '') {
        $aData['USR_USERNAME']     = $form['USR_USERNAME'];
        $aData['USR_PASSWORD']     = $form['USR_PASSWORD'];
        $aData['USR_FIRSTNAME']    = $form['USR_FIRSTNAME'];
        $aData['USR_LASTNAME']     = $form['USR_LASTNAME'];
        $aData['USR_EMAIL']        = $form['USR_EMAIL'];
        $aData['USR_DUE_DATE']     = $form['USR_DUE_DATE'];
        $aData['USR_CREATE_DATE']  = date('Y-m-d H:i:s');
        $aData['USR_UPDATE_DATE']  = date('Y-m-d H:i:s');
        $aData['USR_BIRTHDAY']     = date('Y-m-d');
        $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
        //fixing bug in inactive user when the admin create a new user.
        $statusWF                  = $form['USR_STATUS'];
        $aData['USR_STATUS']       = $form['USR_STATUS'] == 'ACTIVE' ? 1 : 0;
        $sUserUID                  = $RBAC->createUser($aData, $form['USR_ROLE'] );
        $aData['USR_STATUS']       = $statusWF;
        $aData['USR_UID']          = $sUserUID;
        $aData['USR_PASSWORD']     = md5($sUserUID);//fake :p
        $aData['USR_COUNTRY']      = $form['USR_COUNTRY'];
        $aData['USR_CITY']         = $form['USR_CITY'];
        $aData['USR_LOCATION']     = $form['USR_LOCATION'];
        $aData['USR_ADDRESS']      = $form['USR_ADDRESS'];
        $aData['USR_PHONE']        = $form['USR_PHONE'];
        $aData['USR_ZIP_CODE']     = $form['USR_ZIP_CODE'];
        $aData['USR_POSITION']     = $form['USR_POSITION'];
//        $aData['USR_RESUME']       = $form['USR_RESUME'];
        $aData['USR_ROLE']         = $form['USR_ROLE'];
        $aData['USR_REPLACED_BY']  = $form['USR_REPLACED_BY'];

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $oUser->create($aData);

        if ($_FILES['USR_PHOTO']['error'] != 1) {
          //print (PATH_IMAGES_ENVIRONMENT_USERS);
          if ($_FILES['USR_PHOTO']['tmp_name'] != '') {          
            G::uploadFile($_FILES['USR_PHOTO']['tmp_name'], PATH_IMAGES_ENVIRONMENT_USERS, $sUserUID . '.gif');
          }
        }
        else {
          $result->success   = false;
          $result->fileError = true;
          print(G::json_encode($result));
          die;
        }
/*
        if ($_FILES['USR_RESUME']['error'] != 1) {
          if ($_FILES['USR_RESUME']['tmp_name'] != '') {
            G::uploadFile($_FILES['USR_RESUME']['tmp_name'], PATH_IMAGES_ENVIRONMENT_FILES . $sUserUID . '/', $_FILES['USR_RESUME']['name']);
          }
        }
        else {
          $result->success   = false;
          $result->fileError = true;
          print(G::json_encode($result));
          die;
        }
*/
      }
      else {

        $aData['USR_UID']      = $form['USR_UID'];
        $aData['USR_USERNAME'] = $form['USR_USERNAME'];

        if (isset($form['USR_PASSWORD'])) {
           
          if ($form['USR_PASSWORD'] != '') {
            $aData['USR_PASSWORD'] = $form['USR_PASSWORD'];
            require_once 'classes/model/UsersProperties.php';
            $oUserProperty = new UsersProperties();
            $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($form['USR_UID'], array('USR_PASSWORD_HISTORY' => serialize(array(md5($form['USR_PASSWORD'])))));
            
            $memKey = 'rbacSession' . session_id();
            $memcache = & PMmemcached::getSingleton(defined('SYS_SYS') ? SYS_SYS : '');      
            if ( ($RBAC->aUserInfo = $memcache->get($memKey)) === false ) {
              $RBAC->loadUserRolePermission($RBAC->sSystem, $_SESSION['USER_LOGGED'] );
              $memcache->set( $memKey, $RBAC->aUserInfo, PMmemcached::EIGHT_HOURS );    
            }
            if( $RBAC->aUserInfo[ 'PROCESSMAKER' ]['ROLE']['ROL_CODE']=='PROCESSMAKER_ADMIN'){
              $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
              $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
              $oUserProperty->update($aUserProperty);
            }
            
            $aErrors = $oUserProperty->validatePassword($form['USR_NEW_PASS'], $aUserProperty['USR_LAST_UPDATE_DATE'], 0);


            if (count($aErrors) > 0) {
              $sDescription = G::LoadTranslation('ID_POLICY_ALERT').':,';
              foreach ($aErrors as $sError)  {
                switch ($sError) {
                  case 'ID_PPP_MINIMUN_LENGTH':
                    $sDescription .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MINIMUN_LENGTH . ',';
                  break;
                  case 'ID_PPP_MAXIMUN_LENGTH':
                    $sDescription .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MAXIMUN_LENGTH . ',';
                  break;
                  case 'ID_PPP_EXPIRATION_IN':
                    $sDescription .= ' - ' . G::LoadTranslation($sError).' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . ',';
                  break;
                  default:
                    $sDescription .= ' - ' . G::LoadTranslation($sError).',';
                  break;
                }
              }
              $sDescription .=  ''.G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');
              $result->success = false;
              $result->msg = $sDescription;
              print(G::json_encode($result));
              die;


            }
            $aHistory      = unserialize($aUserProperty['USR_PASSWORD_HISTORY']);
            if (!is_array($aHistory)) {
              $aHistory = array();
            }
            if (!defined('PPP_PASSWORD_HISTORY')) {
              define('PPP_PASSWORD_HISTORY', 0);
            }
            if (PPP_PASSWORD_HISTORY > 0) {
              //it's looking a password igual into aHistory array that was send for post in md5 way
              $c  = 0;
              $sw = 1;
              while (count($aHistory) >= 1 && count($aHistory) > $c && $sw ){
               if (strcmp(trim($aHistory[$c]), trim($form['USR_PASSWORD'])) == 0){
                 $sw = 0;
               }
               $c++;
              }
              if ($sw == 0) {
                $sDescription = G::LoadTranslation('ID_POLICY_ALERT').':<br /><br />';
                $sDescription .= ' - ' . G::LoadTranslation('PASSWORD_HISTORY').': ' . PPP_PASSWORD_HISTORY . '<br />';
                $sDescription .= '<br />' . G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY').'';
                G::SendMessageText($sDescription, 'warning');
                G::header('Location: ' . $_SERVER['HTTP_REFERER']);
                die;   
              }

              if (count($aHistory) >= PPP_PASSWORD_HISTORY) {
                $sLastPassw = array_shift($aHistory);
              }
              $aHistory[] = $form['USR_PASSWORD'];
            }
            $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
            $aUserProperty['USR_PASSWORD_HISTORY'] = serialize($aHistory);
            $oUserProperty->update($aUserProperty);
          }
        }
        $aData['USR_FIRSTNAME']   = $form['USR_FIRSTNAME'];
        $aData['USR_LASTNAME']    = $form['USR_LASTNAME'];
        $aData['USR_EMAIL']       = $form['USR_EMAIL'];
        $aData['USR_DUE_DATE']    = $form['USR_DUE_DATE'];
        $aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
        if (isset($form['USR_STATUS'])) {
          $aData['USR_STATUS']      = $form['USR_STATUS'];
          }
          if (isset($form['USR_ROLE'])) {
          $RBAC->updateUser($aData, $form['USR_ROLE']);
        }
        else {
          $RBAC->updateUser($aData);
        }
        $aData['USR_COUNTRY']     = $form['USR_COUNTRY'];
        $aData['USR_CITY']        = $form['USR_CITY'];
        $aData['USR_LOCATION']    = $form['USR_LOCATION'];
        $aData['USR_ADDRESS']     = $form['USR_ADDRESS'];
        $aData['USR_PHONE']       = $form['USR_PHONE'];
        $aData['USR_ZIP_CODE']    = $form['USR_ZIP_CODE'];
        $aData['USR_POSITION']    = $form['USR_POSITION'];
/*
        if ($form['USR_RESUME'] != '') {
          $aData['USR_RESUME'] = $form['USR_RESUME'];
        }
*/
        if (isset($form['USR_ROLE'])) {
          $aData['USR_ROLE'] = $form['USR_ROLE'];
        }

        if(isset($form['USR_REPLACED_BY'])){
          $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];
        }
        if(isset($form['USR_AUTH_USER_DN'])){
          $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
        }

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $oUser->update($aData);
        if ($_FILES['USR_PHOTO']['error'] != 1) {
          if ($_FILES['USR_PHOTO']['tmp_name'] != '') {
            $aAux = explode('.', $_FILES['USR_PHOTO']['name']);
            G::uploadFile($_FILES['USR_PHOTO']['tmp_name'], PATH_IMAGES_ENVIRONMENT_USERS, $aData['USR_UID'] . '.' . $aAux[1]);
            G::resizeImage(PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.gif');
          }
        }
        else {
          $result->success   = false;
          $result->fileError = true;
          print(G::json_encode($result));
          die;        
        }
/*
        if ($_FILES['USR_RESUME']['error'] != 1) {
          if ($_FILES['USR_RESUME']['tmp_name'] != '') {
            G::uploadFile($_FILES['USR_RESUME']['tmp_name'], PATH_IMAGES_ENVIRONMENT_FILES . $aData['USR_UID'] . '/', $_FILES['USR_RESUME']['name']);
          }
        }
        else {
          $result->success = false;
          $result->fileError = true;
          print(G::json_encode($result));
          die;
        }
*/
        /* Saving preferences */
        $def_lang       = $form['PREF_DEFAULT_LANG'];
        $def_menu       = $form['PREF_DEFAULT_MENUSELECTED'];
        $def_cases_menu = $form['PREF_DEFAULT_CASES_MENUSELECTED'];

        G::loadClass('configuration');

        $oConf = new Configurations;
        $aConf = Array(
          'DEFAULT_LANG'       => $def_lang,
          'DEFAULT_MENU'       => $def_menu,
          'DEFAULT_CASES_MENU' => $def_cases_menu
        );

        /*UPDATING SESSION VARIABLES*/
        $aUser = $RBAC->userObj->load($_SESSION['USER_LOGGED']);
        //$_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME']; 

        $oConf->aConfig = $aConf;
        $oConf->saveConfig('USER_PREFERENCES', '', '',$_SESSION['USER_LOGGED']);

      }

      if ( $_SESSION['USER_LOGGED'] == $form['USR_UID'] ) {
        /* UPDATING SESSION VARIABLES */
        $aUser                    = $RBAC->userObj->load($_SESSION['USER_LOGGED']);
        $_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
      }
      
      
      //Save Calendar assigment
      if ((isset($form['USR_CALENDAR']))) {
        //Save Calendar ID for this user       
        G::LoadClass("calendar");  
        $calendarObj = new Calendar();
        $calendarObj->assignCalendarTo($aData['USR_UID'], $form['USR_CALENDAR'], 'USER');
      }
      $result->success = true;  
      print(G::json_encode($result));
    }catch (Exception $e) {
      $result->success = false;
      $result->error   = $e->getMessage();
      print(G::json_encode($result));
    }
    break;
  
  case 'userData':
    require_once 'classes/model/Users.php';
    $_SESSION['CURRENT_USER'] = $_POST['USR_UID'];
    $oUser                    = new Users();
    $aFields                  = $oUser->loadDetailed($_POST['USR_UID']);

    //Load Calendar options and falue for this user
    G::LoadClass ( 'calendar' );
    $calendar = new Calendar ( );
    $calendarInfo = $calendar->getCalendarFor ( $_POST['USR_UID'], $_POST['USR_UID'], $_POST['USR_UID'] );
    //If the function returns a DEFAULT calendar it means that this object doesn't have assigned any calendar
    $aFields ['USR_CALENDAR'] = $calendarInfo ['CALENDAR_APPLIED'] != 'DEFAULT' ? $calendarInfo ['CALENDAR_UID'] : "";

    #verifying if it has any preferences on the configurations table
    G::loadClass('configuration');
    $oConf = new Configurations; 
    $oConf->loadConfig($x, 'USER_PREFERENCES', '', '', $_SESSION['USER_LOGGED'], '');

    $aFields['PREF_DEFAULT_MENUSELECTED']='';
    $aFields['PREF_DEFAULT_CASES_MENUSELECTED']='';
    if( sizeof($oConf->Fields) > 0){ #this user has a configuration record
      $aFields['PREF_DEFAULT_LANG']               = $oConf->aConfig['DEFAULT_LANG'];
      $aFields['PREF_DEFAULT_MENUSELECTED']       = isset($oConf->aConfig['DEFAULT_MENU']) ? $oConf->aConfig['DEFAULT_MENU']: '';
      $aFields['PREF_DEFAULT_CASES_MENUSELECTED'] = isset($oConf->aConfig['DEFAULT_CASES_MENU']) ? $oConf->aConfig['DEFAULT_CASES_MENU']: '';
    } else {
      switch($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE']){
        case 'PROCESSMAKER_ADMIN':
          $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_SETUP';
          break;

        case 'PROCESSMAKER_OPERATOR':
          $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_CASES';
          break;

      }
      $aFields['PREF_DEFAULT_LANG'] = SYS_LANG;
    }
    if ($aFields['USR_REPLACED_BY'] != ''){
      $user        = new Users();
      $u           = $user->load($aFields['USR_REPLACED_BY']);
      $c           = new Configurations();
      $replaced_by = $c->usersNameFormat($u['USR_USERNAME'], $u['USR_FIRSTNAME'], $u['USR_LASTNAME']);
    }else{
      $replaced_by = '';
    }
    $aFields['REPLACED_NAME'] = $replaced_by;   
    if ($aFields['PREF_DEFAULT_MENUSELECTED'] != ''){
      $menuSelected = '';
      foreach ( $RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission ) {
        if($aFields['PREF_DEFAULT_MENUSELECTED']==$permission['PER_CODE']){
          switch($permission['PER_CODE']){
            case 'PM_USERS' :
            case 'PM_SETUP' :

               $menuSelected = strtoupper(G::LoadTranslation('ID_SETUP'));
            break;
            case 'PM_CASES' :
              $menuSelected = strtoupper(G::LoadTranslation('ID_CASES'));           
            break;
            case 'PM_FACTORY' :
              $menuSelected = strtoupper(G::LoadTranslation('ID_APPLICATIONS'));
            break;
          }
        }
      }
    }
    $aFields['MENUSELECTED_NAME'] =  $menuSelected;    
    $oMenu = new Menu();
    $oMenu->load('cases');
    if ($aFields['PREF_DEFAULT_CASES_MENUSELECTED'] != ''){
      foreach($oMenu->Id as $i => $item){
  
        if ($aFields['PREF_DEFAULT_CASES_MENUSELECTED'] == $item)
          $casesMenuSelected =$oMenu->Labels[$i];
      }
    }
    else {
      $casesMenuSelected = '';
    }
    $aFields['CASES_MENUSELECTED_NAME'] = $casesMenuSelected;

    $result->success = true;
    $result->user    = $aFields;

    print(G::json_encode($result));
    break;

  case 'defaultMainMenuOptionList';    
    foreach($RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission){      
      switch($permission['PER_CODE']){
        case 'PM_USERS':  
        case 'PM_SETUP':  
           $rows[] = Array('id'=>'PM_SETUP', 'name'=>strtoupper(G::LoadTranslation('ID_SETUP'))); 
        break;
        case 'PM_CASES':  
           $rows[] = Array('id'=>'PM_CASES', 'name'=>strtoupper(G::LoadTranslation('ID_CASES'))); 
        break;
        case 'PM_FACTORY':  
           $rows[] = Array('id'=>'PM_FACTORY', 'name'=>strtoupper(G::LoadTranslation('ID_APPLICATIONS'))); 
        break;
      }
    }
    print(G::json_encode($rows));
    break;
  case 'defaultCasesMenuOptionList';

    $oMenu = new Menu();
    $oMenu->load('cases');
    
    foreach($oMenu->Id as $i=>$item){
      if( $oMenu->Types[$i] != 'blockHeader' ){
        $rowsCasesMenu[] = Array('id'=>$item, 'name' => $oMenu->Labels[$i]);
      }
    }
    print(G::json_encode($rowsCasesMenu));
    break;
  
}
