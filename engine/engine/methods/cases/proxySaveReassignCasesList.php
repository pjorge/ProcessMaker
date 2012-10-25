<?php
/**
 * proxySaveReassignCasesList.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
 $aData = G::json_decode($_POST['data']);
 $appSelectedUids = array ();
 $items = explode(",",$_POST['APP_UIDS']);
 foreach ($items as $item) {
   $dataUids = explode("|",$item);
   $appSelectedUids[] = $dataUids[0];
 }

// var_dump($aData);
//var_dump($appSelectedUids);
      $casesReassignedCount = 0;
      $serverResponse = array();
      G::LoadClass ('case');
      $oCases = new Cases();
      require_once ('classes/model/Task.php');
      require_once ('classes/model/AppCacheView.php');
      $oAppCacheView      = new AppCacheView();
      $oCasesReassignList = $oAppCacheView->getToReassignListCriteria();
      if (isset($_POST['selected'])&&$_POST['selected']=='true'){
        $oCasesReassignList->add(AppCacheViewPeer::APP_UID,$appSelectedUids,Criteria::IN);
      }
      // if there are no records to save return -1
      if (empty($aData)){
        $serverResponse['TOTAL']=-1;
        echo G::json_encode($serverResponse);
        die();
      }
//      $params = array ();
//      $sql = BasePeer::createSelectSql($oCasesReassignList, $params);
//      var_dump($sql);
      if ( is_array($aData) ) {
        $currentCasesReassigned=0;
        foreach ($aData as $data){
          $oTmpReassignCriteria = $oCasesReassignList;
          $oTmpReassignCriteria->add(AppCacheViewPeer::TAS_UID,$data->TAS_UID);
          $rs = AppCacheViewPeer::doSelectRS($oTmpReassignCriteria);
          $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $rs->next();
          $row = $rs->getRow();          
          $aCase = $oCases->loadCaseInCurrentDelegation($data->APP_UID);
          $oCases->reassignCase($aCase['APP_UID'], $aCase['DEL_INDEX'], $aCase['USR_UID'], $data->APP_REASSIGN_USER_UID);
          $currentCasesReassigned++;
          $casesReassignedCount++;
          $serverResponse[] = array ('APP_REASSIGN_USER' => $data->APP_REASSIGN_USER,
                                     'APP_TITLE'         => $data->APP_TITLE,
                                     'TAS_TITLE'         => $data->APP_TAS_TITLE,
                                     'REASSIGNED_CASES'  => $currentCasesReassigned);
        }
      } 
      else {
        $oTmpReassignCriteria = $oCasesReassignList;
        $oTmpReassignCriteria->add(AppCacheViewPeer::TAS_UID,$aData->TAS_UID);
        $rs = AppCacheViewPeer::doSelectRS($oTmpReassignCriteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $currentCasesReassigned=0;
        while (is_array($row)) {
          $APP_UID = $row['APP_UID'];
          $aCase = $oCases->loadCaseInCurrentDelegation($APP_UID);
          $oCases->reassignCase($aCase['APP_UID'], $aCase['DEL_INDEX'], $aCase['USR_UID'], $aData->APP_REASSIGN_USER_UID);
          $currentCasesReassigned++;
          $casesReassignedCount++;
//              var_dump($aCase);
//              echo ("<br>");
          $rs->next();
          $row = $rs->getRow();
        }
        $serverResponse[] = array ('TAS_TITLE'=>$aData->APP_TAS_TITLE,'REASSIGNED_CASES'=>$currentCasesReassigned);
      }

      $serverResponse['TOTAL'] = $casesReassignedCount;
      echo G::json_encode($serverResponse);
//      $oTask  = new Task();
//      $oCases = new Cases();
//      $aCases = Array();
//
//      if( isset($_POST['items']) && trim($_POST['items']) != '' ){
//        $sItems = $_POST['items'];
//        $aItems = explode(',', $sItems);
//        $FROM_USR_UID = $_POST['USR_UID'];
//
//        foreach($aItems as $item){
//          list($APP_UID, $USR_UID) = explode('|', $item);
//          $aCase = $oCases->loadCaseInCurrentDelegation($APP_UID);
//          $oCase->reassignCase($aCase['APP_UID'], $aCase['DEL_INDEX'], $FROM_USR_UID, $USR_UID);
//          array_push($aCases, $aCase);
//        }
//        require_once 'classes/model/Users.php';
//		    $oUser = new Users();
//		    $sText = '';
//		    foreach ($aCases as $aCase) {
//		      $aCaseUpdated  = $oCases->loadCaseInCurrentDelegation($aCase['APP_UID']);
//		      $aUser  = $oUser->load($aCaseUpdated['USR_UID']);
//		      $sText .= $aCaseUpdated['APP_PRO_TITLE'] .' - '. ' Case: ' . $aCaseUpdated['APP_NUMBER'] . '# (' . $aCaseUpdated['APP_TAS_TITLE'] . ') <b> => Reassigned to => </b> <font color="blue">' . $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' [' . $aUser['USR_USERNAME'] . ']' . '</font><br />';
//		    }
//
//		    $G_PUBLISH = new Publisher;
//		    $aMessage['MESSAGE'] = $sText;
//		    $aMessage['URL']     = 'cases_ReassignByUser?REASSIGN_USER=' . $_POST['USR_UID'];
//		    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ReassignShowInfo', '', $aMessage);
//		    G::RenderPage('publish', 'raw');
//      }
?>
