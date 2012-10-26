<?php
/**
 * authSources_Edit.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.23
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

global $RBAC;
if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') != 1) {
  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
	G::header('location: ../login/login');
	die;
}

if (!isset($_GET['sUID'])) {
  G::SendTemporalMessage('ID_ERROR_OBJECT_NOT_EXISTS', 'error', 'labels');
	G::header('location: authSources_List');
	die;
}

if ($_GET['sUID'] == '') {
  G::SendTemporalMessage('ID_ERROR_OBJECT_NOT_EXISTS', 'error', 'labels');
	G::header('location: authSources_List');
	die;
}

$G_MAIN_MENU            = 'processmaker';
$G_SUB_MENU             = 'users';
$G_ID_MENU_SELECTED     = 'USERS';
$G_ID_SUB_MENU_SELECTED = 'AUTH_SOURCES';

$fields = $RBAC->getAuthSource($_GET['sUID']);

if (is_array($fields['AUTH_SOURCE_DATA'])) {
  foreach($fields['AUTH_SOURCE_DATA'] as $field => $value) {
    $fields[$field] = $value;
  }
}
unset($fields['AUTH_SOURCE_DATA']);

//fixing a problem with dropdown with int values,
//the problem : the value was integer, but the dropdown was expecting a string value, and they returns always the first item of dropdown
if (isset($fields['AUTH_SOURCE_ENABLED_TLS'])) {
  $fields['AUTH_SOURCE_ENABLED_TLS'] = sprintf('%d', $fields['AUTH_SOURCE_ENABLED_TLS']);
}
if (isset($fields['AUTH_ANONYMOUS'])) {
  $fields['AUTH_ANONYMOUS'] = sprintf('%d', $fields['AUTH_ANONYMOUS']);
}

$G_PUBLISH = new Publisher();
if ($fields['AUTH_SOURCE_PROVIDER'] == 'ldap' ) {
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addExtJsScript('authSources/authSourcesEdit', false);
  $oHeadPublisher->assign('sUID',$_GET['sUID']);
  G::RenderPage('publish', 'extJs');
}
else {
  if (file_exists(PATH_PLUGINS . $fields['AUTH_SOURCE_PROVIDER'] . PATH_SEP . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml')) {
    $G_PUBLISH->AddContent('xmlform', 'xmlform', $fields['AUTH_SOURCE_PROVIDER'] . PATH_SEP . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml', '', $fields, '../authSources/authSources_Save');
  }
  else {
    if (file_exists(PATH_XMLFORM . 'authSources/' . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml')) {
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'authSources/' . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit', '', $fields, '../authSources/authSources_Save');
    }
    else {
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', array('MESSAGE' => 'File: ' . $fields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml' . ' not exists.'));
    }
  }
  G::RenderPage('publish', 'blank');
}