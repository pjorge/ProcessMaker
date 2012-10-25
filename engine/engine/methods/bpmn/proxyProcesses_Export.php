<?php
/**
 * processes_Export.php
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

G::LoadThirdParty('pear/json','class.json');

try {

  if ( isset($_GET['pro_uid']))
    $sProUid = $_GET['pro_uid'];
  else
    throw ( new Exception ( 'the process uid is not defined!.' ) );

/* Includes */
G::LoadClass('processes');
G::LoadClass('xpdl');
$oProcess  = new Processes();
$oXpdl     = new Xpdl();
$proFields = $oProcess->serializeProcess( $sProUid );
$Fields = $oProcess->saveSerializedProcess ( $proFields );
$xpdlFields = $oXpdl->xmdlProcess($sProUid);
$Fields['FILENAMEXPDL'] = $xpdlFields['FILENAMEXPDL'];
$Fields['FILENAME_LINKXPDL'] = $xpdlFields['FILENAME_LINKXPDL'];

 if (G::is_https ())
    $http = 'https://';
  else
    $http = 'http://';

$Fields['FILENAME_LINK']     = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/processes/' . $Fields['FILENAME_LINK'];
$Fields['FILENAME_LINKXPDL'] = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/processes/' . $Fields['FILENAME_LINKXPDL'];

$result = G::json_encode( $Fields );
$result = str_replace("\\/","/",'{success:true,data:'.$result.'}'); // unescape the slashes
echo $result;

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish', 'raw' );
}
