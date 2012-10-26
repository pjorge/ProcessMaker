<?php

class Applications
{

  public function getAll($userUid, $start=null, $limit=null, $action=null, $filter=null, $search=null, $process=null, $user=null, $status=null, $type=null, $dateFrom=null, $dateTo=null, $callback=null, $dir=null, $sort='APP_CACHE_VIEW.APP_NUMBER')
  {
    $callback = isset($callback) ? $callback : 'stcCallback1001';
    $dir      = isset($dir)    ? $dir    : 'DESC';
    $sort     = isset($sort)   ? $sort   : '';
    $start    = isset($start)  ? $start  : '0';
    $limit    = isset($limit)  ? $limit  : '25';
    $filter   = isset($filter) ? $filter : '';
    $search   = isset($search) ? $search : '';
    $process  = isset($process)? $process : '';
    $user     = isset($user)    ? $user : '';
    $status   = isset($status) ? strtoupper($status) : '';
    $action   = isset($action) ? $action : 'todo';
    $type     = isset($type)   ? $type: 'extjs';
    $dateFrom = isset($dateFrom)? $dateFrom : '';
    $dateTo   = isset($dateTo)  ? $dateTo : '';
    
    G::LoadClass("BasePeer" );
    G::LoadClass ( 'configuration' );
    require_once ( "classes/model/AppCacheView.php" );
    require_once ( "classes/model/AppDelegation.php" );
    require_once ( "classes/model/AdditionalTables.php" );
    require_once ( "classes/model/AppDelay.php" );
    require_once ( "classes/model/Fields.php" );

    //$userUid = ( isset($_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] != '' ) ? $_SESSION['USER_LOGGED'] : null; <-- passed by param
    $oAppCache = new AppCacheView();
    
    //get data configuration
    $conf = new Configurations();
    $confCasesList = $conf->getConfiguration('casesList',($action=='search'||$action=='simple_search')?'sent':$action );
  //  var_dump($confCasesList);
    $oAppCache->confCasesList = $confCasesList;

  // get the action based list
    switch ( $action ) {
      case 'draft' :
        $Criteria      = $oAppCache->getDraftListCriteria($userUid);
        $CriteriaCount = $oAppCache->getDraftCountCriteria($userUid);
       break;
      case 'sent' :
        $Criteria      = $oAppCache->getSentListCriteria($userUid);
        $CriteriaCount = $oAppCache->getSentCountCriteria($userUid);
  //         var_dump($Criteria);
       break;
      case 'selfservice' :
      case 'unassigned':
        $Criteria      = $oAppCache->getUnassignedListCriteria($userUid);
        $CriteriaCount = $oAppCache->getUnassignedCountCriteria($userUid);
       break;
      case 'paused' :
        $Criteria      = $oAppCache->getPausedListCriteria($userUid);
        $CriteriaCount = $oAppCache->getPausedCountCriteria($userUid);
       break;
      case 'completed' :
        $Criteria      = $oAppCache->getCompletedListCriteria($userUid);
        $CriteriaCount = $oAppCache->getCompletedCountCriteria($userUid);
       break;
      case 'cancelled' :
        $Criteria      = $oAppCache->getCancelledListCriteria($userUid);
        $CriteriaCount = $oAppCache->getCancelledCountCriteria($userUid);
       break;
      case 'search' :
        $Criteria      = $oAppCache->getSearchListCriteria();
        $CriteriaCount = $oAppCache->getSearchCountCriteria();
        break;
      case 'simple_search' :
        $Criteria      = $oAppCache->getSimpleSearchListCriteria();
        $CriteriaCount = $oAppCache->getSimpleSearchCountCriteria();
        break;
      case 'to_revise' :
        $Criteria      = $oAppCache->getToReviseListCriteria($userUid);
        $CriteriaCount = $oAppCache->getToReviseCountCriteria($userUid);
        break;
      case 'to_reassign' :
        $Criteria      = $oAppCache->getToReassignListCriteria();
        $CriteriaCount = $oAppCache->getToReassignCountCriteria();
        break;
      case 'all' :
        $Criteria      = $oAppCache->getAllCasesListCriteria($userUid);
        $CriteriaCount = $oAppCache->getAllCasesCountCriteria($userUid);
        break;
      // general criteria probably will be deprecated
      case 'gral' :
        $Criteria      = $oAppCache->getGeneralListCriteria();
        $CriteriaCount = $oAppCache->getGeneralCountCriteria();
        break;
      case 'todo' :
      default:
        $Criteria      = $oAppCache->getToDoListCriteria($userUid);
        $CriteriaCount = $oAppCache->getToDoCountCriteria($userUid);
      break;
    }

    if ( !is_array($confCasesList) ) {
        $rows = $this->getDefaultFields( $action );
        $result = $this->genericJsonResponse( '', array(), $rows , 20, '' );
          //$conf->saveObject($result,'casesList',$action,'','','');
    }

    // add the process filter
    if ( $process != '' ) {
      $Criteria->add      (AppCacheViewPeer::PRO_UID, $process, Criteria::EQUAL );
      $CriteriaCount->add (AppCacheViewPeer::PRO_UID, $process, Criteria::EQUAL );
    }

    // add the user filter
    if ( $user != '' ) {
      $Criteria->add      (AppCacheViewPeer::USR_UID, $user, Criteria::EQUAL );
      $CriteriaCount->add (AppCacheViewPeer::USR_UID, $user, Criteria::EQUAL );
    }

    if ( $status != '' ) {
      $Criteria->add      (AppCacheViewPeer::APP_STATUS, $status, Criteria::EQUAL );
      $CriteriaCount->add (AppCacheViewPeer::APP_STATUS, $status, Criteria::EQUAL );
    }

    if ( $dateFrom != '' ) {
      if( $dateTo != '' ){
        $Criteria->add(
          $Criteria->getNewCriterion(
            AppCacheViewPeer::DEL_DELEGATE_DATE,
            $dateFrom, Criteria::GREATER_EQUAL
          )->addAnd($Criteria->getNewCriterion(
            AppCacheViewPeer::DEL_DELEGATE_DATE,
            $dateTo, Criteria::LESS_EQUAL
          ))
        );
        $CriteriaCount->add(
          $CriteriaCount->getNewCriterion(
            AppCacheViewPeer::DEL_DELEGATE_DATE,
            $dateFrom, Criteria::GREATER_EQUAL
          )->addAnd($Criteria->getNewCriterion(
            AppCacheViewPeer::DEL_DELEGATE_DATE,
            $dateTo, Criteria::LESS_EQUAL
          ))
        );
      } else {
        $Criteria->add      (AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
        $CriteriaCount->add (AppCacheViewPeer::DEL_DELEGATE_DATE, $dateFrom, Criteria::GREATER_EQUAL );
      }
    } else if ( $dateTo != '' ) {
      $Criteria->add      (AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
      $CriteriaCount->add (AppCacheViewPeer::DEL_DELEGATE_DATE, $dateTo, Criteria::LESS_EQUAL );
    }

    //add the filter 
    if ( $filter != '' ) {
      switch ( $filter ) {
          case 'read' :
            $Criteria->add      (AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL);
            $CriteriaCount->add (AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNOTNULL);
          break;
      case 'unread' : 
            $Criteria->add      (AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNULL);
            $CriteriaCount->add (AppCacheViewPeer::DEL_INIT_DATE, null, Criteria::ISNULL);
          break;
          case 'started' :
            $Criteria->add      (AppCacheViewPeer::DEL_INDEX, 1, Criteria::EQUAL);
            $CriteriaCount->add (AppCacheViewPeer::DEL_INDEX, 1, Criteria::EQUAL);
          break;
          case 'completed' :
            $Criteria->add      (AppCacheViewPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL);
            $CriteriaCount->add (AppCacheViewPeer::APP_STATUS, 'COMPLETED', Criteria::EQUAL);
          break;
      }
    }  

    //add the search filter
    if ( $search != '' ) {

      $defaultFields = $oAppCache->getDefaultFields();
      $oTmpCriteria = '';
      // if there is PMTABLE for this case list:
      if ( !empty($oAppCache->confCasesList) && isset($oAppCache->confCasesList['PMTable']) && trim($oAppCache->confCasesList['PMTable'])!='' ) {
      // getting the table name
        $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($oAppCache->confCasesList['PMTable']);
        $tableName = $oAdditionalTables->getAddTabName();
        $oNewCriteria = new Criteria( 'workflow' );
        $counter = 0;
        foreach($oAppCache->confCasesList['second']['data'] as $fieldData){
          if ( !in_array($fieldData['name'],$defaultFields) ){
            $fieldName = $tableName.'.'.$fieldData['name'];
            if ( $counter == 0 ) {
              $oTmpCriteria = $oNewCriteria->getNewCriterion ( $fieldName, '%' . $search . '%', Criteria::LIKE );
            } else {
              $oTmpCriteria = $oNewCriteria->getNewCriterion ( $fieldName, '%' . $search . '%', Criteria::LIKE )->addOr($oTmpCriteria);
            }
            $counter++;
          }
        }
        //add the default and hidden DEL_INIT_DATE
      }

      // the criteria adds new fields if there are defined PM Table Fields in the cases list
      if ($oTmpCriteria!='') {
        $Criteria->add(
          $Criteria->getNewCriterion(
            AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($Criteria->getNewCriterion(
            AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($Criteria->getNewCriterion(
            AppCacheViewPeer::APP_NUMBER, $search, Criteria::LIKE
          )->addOr($oTmpCriteria))
        ));
      } else {
        $Criteria->add(
          $Criteria->getNewCriterion(
            AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($Criteria->getNewCriterion(
            AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($Criteria->getNewCriterion(
            AppCacheViewPeer::APP_NUMBER, $search, Criteria::LIKE
          ))
        ));
      }

      // the count query needs to be the normal criteria query if there are defined PM Table Fields in the cases list
      if ($oTmpCriteria!='') {
        $CriteriaCount = $Criteria;
      } else {
        $CriteriaCount->add(
          $CriteriaCount->getNewCriterion(
            AppCacheViewPeer::APP_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($CriteriaCount->getNewCriterion(
            AppCacheViewPeer::APP_TAS_TITLE, '%' . $search . '%', Criteria::LIKE
          )->addOr($CriteriaCount->getNewCriterion(
            AppCacheViewPeer::APP_NUMBER, $search, Criteria::LIKE
          ))
        ));
      }
    }

    //here we count how many records exists for this criteria.
    //BUT there are some special cases, and if we dont optimize them the server will crash.
    $doCountAlreadyExecuted = false;
    //case 1. when the SEARCH action is selected and none filter, search criteria is defined, 
    //we need to count using the table APPLICATION, because APP_CACHE_VIEW takes 3 seconds

    if ( $action == 'search' && $filter == '' && $search == '' && $process == '' && $status == '' && $dateFrom == '' && $dateTo == '') {
      $totalCount = $oAppCache->getSearchAllCount();
      $doCountAlreadyExecuted = true;
    }

    if ( $doCountAlreadyExecuted == false ) {
      // in the case of reassign the distinct attribute shows a diferent count result comparing to the
      // original list
      if ($action == 'to_reassign' || $action == 'todo'){
        $distinct = false;
      } else{
        $distinct = true;
      }
      // first check if there is a PMTable defined within the list,
      // the issue that brokes the normal criteria query seems to be fixed
      if (isset($oAppCache->confCasesList['PMTable']) && !empty($oAppCache->confCasesList['PMTable'])) {
        // then
        $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($oAppCache->confCasesList['PMTable']);
        $tableName = $oAdditionalTables->getAddTabName();
        $tableName = strtolower($tableName);
        $tableNameArray = explode('_',$tableName);
        foreach ($tableNameArray as $item){
          $newTableName[] = ucfirst($item);
        }
        $tableName = implode('',$newTableName);
        // so the pm table class can be invoqued from the pm table model clases
        if (!class_exists($tableName)){
          require_once(PATH_DB.SYS_SYS.PATH_SEP."classes".PATH_SEP.$tableName.".php");
        }
      }
      $totalCount = AppCacheViewPeer::doCount( $CriteriaCount, $distinct );

    }

    //add sortable options    
    if ( $sort != '' ) {
      if ( $dir == 'DESC' )
        $Criteria->addDescendingOrderByColumn( $sort );
      else
        $Criteria->addAscendingOrderByColumn( $sort );
    }

    //limit the results according the interface    
      $Criteria->setLimit( $limit );
      $Criteria->setOffset( $start );


    /*
    // this is the optimal way or query to render the cases search list
    // fixing the bug related to the wrong data displayed in the list
    if ( $action == 'search' ) {
      $oDatasetIndex = AppCacheViewPeer::doSelectRS( $Criteria );
      $oDatasetIndex->setFetchmode( ResultSet::FETCHMODE_ASSOC );
      $oDatasetIndex->next();
      // a list of MAX_DEL_INDEXES is required in order to validate the right row
      while($aRow = $oDatasetIndex->getRow()){
        $maxDelIndexList[] = $aRow['MAX_DEL_INDEX'];
        $oDatasetIndex->next();
      }
      // adding the validation condition in order to get the right row using the group by sentence
      $Criteria->add(AppCacheViewPeer::DEL_INDEX, $maxDelIndexList, Criteria::IN );
      //
      $params = array ( $maxDelIndexList );

    }
    */

    //execute the query
    $oDataset = AppCacheViewPeer::doSelectRS($Criteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
        
    $result = array();
    $result['totalCount'] = $totalCount;
    $rows = array();
    $aPriorities = array('1'=>'VL', '2'=>'L', '3'=>'N', '4'=>'H', '5'=>'VH');
    $index = $start;
    while($aRow = $oDataset->getRow()){
      //$aRow = $oAppCache->replaceRowUserData($aRow);
      
      /* For participated cases, we want the last step in the case, not only
       * the last step this user participated. To do that we get every case
       * information again for the last step. (This could be solved by a subquery,
       * but Propel might not support it and subqueries can be slower for larger
       * datasets).
       */
      if ($action == 'sent' || $action == 'search') {
        $maxCriteria = new Criteria('workflow');
        $maxCriteria->add(AppCacheViewPeer::APP_UID, $aRow['APP_UID'], Criteria::EQUAL);
        $maxCriteria->addDescendingOrderByColumn(AppCacheViewPeer::DEL_INDEX);
        $maxCriteria->setLimit(1);

        $maxDataset = AppCacheViewPeer::doSelectRS( $maxCriteria );
        $maxDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $maxDataset->next();

        $newData = $maxDataset->getRow();
        foreach ($aRow as $col => $value) {
          if (array_key_exists($col, $newData))
            $aRow[$col] = $newData[$col];
        }
        
        $maxDataset->close();
      }
      
      if (!isset($aRow['APP_CURRENT_USER']))
        $aRow['APP_CURRENT_USER'] = "[Unassigned]";
      
      // replacing the status data with their respective translation 
      if( isset($aRow['APP_STATUS']) ){
        $aRow['APP_STATUS'] = G::LoadTranslation("ID_{$aRow['APP_STATUS']}");
      }

      // replacing the priority data with their respective translation
      if( isset($aRow['DEL_PRIORITY']) ){
        $aRow['DEL_PRIORITY'] = G::LoadTranslation("ID_PRIORITY_{$aPriorities[$aRow['DEL_PRIORITY']]}");
      }
      
      $rows[] = $aRow;
      $oDataset->next();
    }

    $result['data'] = $rows;
    
    return $result;
  }

    
 //TODO: Encapsulates these and another default generation functions inside a class
  /**
   * generate all the default fields
   * @return Array $fields
   */
   function setDefaultFields() {
     $fields = array();
     $fields['APP_NUMBER']              = array( 'name' => 'APP_NUMBER' ,             'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_NUMBER') ,            'width' => 40,  'align' => 'left');
     $fields['APP_UID']                 = array( 'name' => 'APP_UID'    ,             'fieldType' => 'key',         'label' => G::loadTranslation('ID_CASESLIST_APP_UID'),                'width' => 80,  'align' => 'left');
     $fields['DEL_INDEX']               = array( 'name' => 'DEL_INDEX'  ,             'fieldType' => 'key' ,        'label' => G::loadTranslation('ID_CASESLIST_DEL_INDEX')  ,            'width' => 50,  'align' => 'left');
     $fields['TAS_UID']                 = array( 'name' => 'TAS_UID'  ,               'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_TAS_UID')    ,            'width' => 80,  'align' => 'left');
     $fields['USR_UID']                 = array( 'name' => 'USR_UID'  ,               'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_USR_UID')    ,            'width' => 80,  'align' => 'left', 'hidden' => true);
     $fields['PREVIOUS_USR_UID']        = array( 'name' => 'PREVIOUS_USR_UID'  ,      'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_PREVIOUS_USR_UID')   ,    'width' => 80,  'align' => 'left', 'hidden' => true);
     $fields['APP_TITLE']               = array( 'name' => 'APP_TITLE'  ,             'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_TITLE')  ,            'width' => 140, 'align' => 'left');
     $fields['APP_PRO_TITLE']           = array( 'name' => 'APP_PRO_TITLE'  ,         'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_PRO_TITLE') ,         'width' => 140, 'align' => 'left');
     $fields['APP_TAS_TITLE']           = array( 'name' => 'APP_TAS_TITLE'  ,         'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_TAS_TITLE') ,         'width' => 140, 'align' => 'left');
     $fields['APP_DEL_PREVIOUS_USER']   = array( 'name' => 'APP_DEL_PREVIOUS_USER'  , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DEL_PREVIOUS_USER') , 'width' => 120, 'align' => 'left');
     $fields['APP_CURRENT_USER']        = array( 'name' => 'APP_CURRENT_USER'       , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_CURRENT_USER')  ,     'width' => 120, 'align' => 'left');
     $fields['DEL_TASK_DUE_DATE']       = array( 'name' => 'DEL_TASK_DUE_DATE'      , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_DEL_TASK_DUE_DATE') ,     'width' => 100, 'align' => 'left');
     $fields['APP_UPDATE_DATE']         = array( 'name' => 'APP_UPDATE_DATE'        , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_UPDATE_DATE') ,       'width' => 100, 'align' => 'left');
     $fields['DEL_PRIORITY']            = array( 'name' => 'DEL_PRIORITY'           , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_DEL_PRIORITY')    ,       'width' => 80,  'align' => 'left');
     $fields['APP_STATUS']              = array( 'name' => 'APP_STATUS'             , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_STATUS') ,            'width' => 80,  'align' => 'left');
     $fields['APP_FINISH_DATE']         = array( 'name' => 'APP_FINISH_DATE'        , 'fieldType' => 'case field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_FINISH_DATE') ,       'width' => 100, 'align' => 'left');
     $fields['APP_DELAY_UID']           = array( 'name' => 'APP_DELAY_UID'          , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DELAY_UID') ,       'width' => 100, 'align' => 'left');
     $fields['APP_THREAD_INDEX']        = array( 'name' => 'APP_THREAD_INDEX'       , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_THREAD_INDEX') ,       'width' => 100, 'align' => 'left');
     $fields['APP_DEL_INDEX']           = array( 'name' => 'APP_DEL_INDEX'          , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DEL_INDEX') ,       'width' => 100, 'align' => 'left');
     $fields['APP_TYPE']                = array( 'name' => 'APP_TYPE'               , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_TYPE') ,       'width' => 100, 'align' => 'left');
     $fields['APP_DELEGATION_USER']     = array( 'name' => 'APP_DELEGATION_USER'    , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DELEGATION_USER') ,       'width' => 100, 'align' => 'left');
     $fields['APP_ENABLE_ACTION_USER']  = array( 'name' => 'APP_ENABLE_ACTION_USER' , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_ENABLE_ACTION_USER') ,       'width' => 100, 'align' => 'left');
     $fields['APP_ENABLE_ACTION_DATE']  = array( 'name' => 'APP_ENABLE_ACTION_DATE' , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_AAPP_ENABLE_ACTION_DATE') ,       'width' => 100, 'align' => 'left');
     $fields['APP_DISABLE_ACTION_USER'] = array( 'name' => 'APP_DISABLE_ACTION_USER', 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DISABLE_ACTION_USER') ,       'width' => 100, 'align' => 'left');
     $fields['APP_DISABLE_ACTION_DATE'] = array( 'name' => 'APP_DISABLE_ACTION_DATE', 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_DISABLE_ACTION_DATE') ,       'width' => 100, 'align' => 'left');
     $fields['APP_AUTOMATIC_DISABLED_DATE'] = array( 'name' => 'APP_AUTOMATIC_DISABLED_DATE' , 'fieldType' => 'delay field' , 'label' => G::loadTranslation('ID_CASESLIST_APP_AUTOMATIC_DISABLED_DATE') ,       'width' => 100, 'align' => 'left');
     return $fields;

   }

 /**
  * this function return the default fields for a default case list
  * @param $action
  * @return an array with the default fields for an specific case list (action)
  */
  function getDefaultFields ( $action ) {
    $rows = array();
    switch ( $action ) {
      case 'todo' : // #, Case, task, process, sent by, due date, Last Modify, Priority
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['DEL_TASK_DUE_DATE'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['DEL_PRIORITY'];
        break;

      case 'draft' :    //#, Case, task, process, due date, Last Modify, Priority },
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['DEL_TASK_DUE_DATE'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['DEL_PRIORITY'];
        break;
      case 'sent' : // #, Case, task, process, current user, sent by, Last Modify, Status
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_CURRENT_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['APP_STATUS'];
        break;
      case 'unassigned' :  //#, Case, task, process, completed by user, finish date
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        break;
      case 'paused' : //#, Case, task, process, sent by
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['APP_THREAD_INDEX'];
        $rows[] = $fields['APP_DEL_INDEX'];
        break;
      case 'completed' : //#, Case, task, process, completed by user, finish date
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        break;

      case 'cancelled' : //#, Case, task, process, due date, Last Modify
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        break;

      case 'to_revise' : //#, Case, task, process, due date, Last Modify
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_CURRENT_USER'];
        $rows[] = $fields['DEL_PRIORITY'];
        $rows[] = $fields['APP_STATUS'];
        break;

      case 'to_reassign' : //#, Case, task, process, due date, Last Modify
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['TAS_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_CURRENT_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['APP_STATUS'];


        break;

      case 'all' : //#, Case, task, process, due date, Last Modify
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_CURRENT_USER'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['APP_STATUS'];
        break;

      case 'gral' : //#, Case, task, process, due date, Last Modify
        $fields = $this->setDefaultFields();
        $rows[] = $fields['APP_UID'];
        $rows[] = $fields['DEL_INDEX'];
        $rows[] = $fields['USR_UID'];
        $rows[] = $fields['PREVIOUS_USR_UID'];
        $rows[] = $fields['APP_NUMBER'];
        $rows[] = $fields['APP_TITLE'];
        $rows[] = $fields['APP_PRO_TITLE'];
        $rows[] = $fields['APP_TAS_TITLE'];
        $rows[] = $fields['APP_CURRENT_USER'];
        $rows[] = $fields['APP_DEL_PREVIOUS_USER'];
        $rows[] = $fields['APP_UPDATE_DATE'];
        $rows[] = $fields['APP_STATUS'];
        break;
    }
    return $rows;
  }

 /**
  * set the generic Json Response, using two array for the grid stores and a string for the pmtable name
  * @param string $pmtable
  * @param array $first
  * @param array $second
  * @return $response a json string
  */
  function genericJsonResponse($pmtable, $first, $second, $rowsperpage, $dateFormat ) {
    $firstGrid['totalCount']  = count($first);
    $firstGrid['data']        = $first;
    $secondGrid['totalCount'] = count($second);
    $secondGrid['data']       = $second;
    $result = array();
    $result['first']   = $firstGrid;
    $result['second']  = $secondGrid;
    $result['PMTable'] = isset($pmtable) ? $pmtable : '';
    $result['rowsperpage'] = isset($rowsperpage) ? $rowsperpage : 20;
    $result['dateformat']  = isset($dateFormat) && $dateFormat != '' ? $dateFormat : 'M d, Y';
    return $result;
  }

  public function getSteps($appUid, $index, $tasUid, $proUid)
  {
    require_once 'classes/model/Step.php';
    require_once 'classes/model/Content.php';
    require_once 'classes/model/AppDocument.php';
    require_once 'classes/model/InputDocumentPeer.php';
    require_once 'classes/model/OutputDocument.php';
    require_once 'classes/model/Dynaform.php';

    G::LoadClass('pmScript');
    G::LoadClass('case');
    
    $steps = Array();
    $case = new Cases;
    $step = new Step;
    $appDocument = new AppDocument;

    $caseSteps = $step->getAllCaseSteps($proUid, $tasUid, $appUid);

    //getting externals steps
    $oPluginRegistry = &PMPluginRegistry::getSingleton();
    $eSteps          = $oPluginRegistry->getSteps();
    $externalSteps   = array();

    foreach ($eSteps as $externalStep) {
      $externalSteps[$externalStep->sStepId] = $externalStep;
    }

    //getting the case record
    if ($appUid) {
      $caseData = $case->loadCase($appUid);
      $pmScript = new PMScript();
      $pmScript->setFields($caseData['APP_DATA']);
    }

    $externalStepCount = 0;

    foreach ($caseSteps as $caseStep) {

      if (trim($caseStep->getStepCondition()) != '') { // if it has a condition
        $pmScript->setScript($caseStep->getStepCondition());
        
        if (!$pmScript->evaluate()) { //evaluate
          //evaluated false, jump & continue with the others steps
          continue;
        }
      }

      $stepUid      = $caseStep->getStepUidObj();
      $stepType     = $caseStep->getStepTypeObj();
      $stepPosition = $caseStep->getStepPosition();

      $stepItem = array();
      $stepItem['id']   = $stepUid;
      $stepItem['type'] = $stepType;

      switch ($stepType) {
        case 'DYNAFORM':
          $oDocument = DynaformPeer::retrieveByPK($stepUid);

          $stepItem['title'] = $oDocument->getDynTitle();
          $stepItem['url']   = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=EDIT";
          break;

        case 'OUTPUT_DOCUMENT':
          $oDocument = OutputDocumentPeer::retrieveByPK($caseStep->getStepUidObj());
          $outputDoc = $appDocument->getObject($appUid, $index, $caseStep->getStepUidObj(), 'OUTPUT');

          $stepItem['title']    = $oDocument->getOutDocTitle();
                    
          if ($outputDoc['APP_DOC_UID']) {
            $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=VIEW&DOC={$outputDoc['APP_DOC_UID']}"; 
          }
          else {
            $stepItem['url'] = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=GENERATE";         
          }
          break;

        case 'INPUT_DOCUMENT':
          $oDocument = InputDocumentPeer::retrieveByPK($stepUid);
          
          $stepItem['title'] = $oDocument->getInpDocTitle();
          $stepItem['url']  = "cases/cases_Step?UID=$stepUid&TYPE=$stepType&POSITION=$stepPosition&ACTION=ATTACH";
          break;

        case 'EXTERNAL':
          $stepTitle       = 'unknown ' . $caseStep->getStepUidObj();
          $oPluginRegistry = PMPluginRegistry::getSingleton();
    
          $externalStep      = $externalSteps[$caseStep->getStepUidObj()];
          $stepItem['id']    = $externalStep->sStepId;
          $stepItem['title'] = $externalStep->sStepTitle;
          $stepItem['url']   = "cases/cases_Step?UID={$externalStep->sStepId}&TYPE=EXTERNAL&POSITION=$stepPosition&ACTION=EDIT";          
        break;
      }

      $steps[] = $stepItem;
    }

    //last, assign task
    $stepItem          = array();
    $stepItem['id']    = '-1';
    $stepItem['type']  = '';
    $stepItem['title'] = G::LoadTranslation('ID_ASSIGN_TASK');
    $stepItem['url']   = "cases/cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN";
    
    $steps[] = $stepItem;    

    return $steps;
  }
}