<?php

/**
 * class.pmTrAlfresco.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */
////////////////////////////////////////////////////
// pmTrAlfresco PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////


/**
 * Alfresco Triggers that allow ProcessMaker to perform common DM actions
 * @class pmTrAlfresco
 * @name Alfresco DM Triggers v. 0.1
 * @icon /images/triggers/alfrescoIcon.png
 * @className class.pmTrAlfresco.pmFunctions.php
 */

/**
 * @method
 *
 * Cancel Checkedout document/file
 *
 * @name cancelCheckout
 * @label Cancel Checkedout document/file
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $docUid | Document Uid
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
G::LoadSystem('restClient');
// Validation left
function cancelCheckout($alfrescoServerUrl, $docUid, $user="", $pwd="") {
  //require_once(PATH_CORE. 'classes' . PATH_SEP.'triggers' . PATH_SEP . 'class.pmTrAlfresco.php');
$alfresco_url = "$alfrescoServerUrl/s/cmis/pwc/s/workspace:SpacesStore/i/$docUid";
$domapi_exec = RestClient::delete($alfresco_url,$user,$pwd,"application/atom+xml;type=entry");
//$alfrescoMessage = $domapi_exec['header'];
$domapi_res = json_decode($domapi_exec->getResponse());
return $domapi_res;
}

/**
 * @method
 *
 * Checkin document/file
 *
 * @name Checkin
 * @label Checkin document/file
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $docUid | Document Uid
 * @param string | $comments | Comments
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
// Validation done
function checkIn($alfrescoServerUrl, $docUid, $comments, $user="", $pwd="") {
$alfresco_url = "$alfrescoServerUrl/s/cmis/pwc/i/$docUid?checkin=true&checkinComment=$comments";
$xmlData = array();
$xmlData = '<?xml version="1.0" encoding="utf-8"?><entry xmlns="http://www.w3.org/2005/Atom"/>';

$alfresco_exec = RestClient::put($alfresco_url,$xmlData,$user,$pwd,"application/atom+xml");
$alfrescoMessage = $alfresco_exec->getResponseMessage();
if($alfrescoMessage === 'OK' )
    return "The Document has been Checkedin";
elseif ($alfrescoMessage === 'Internal Server Error')
        return "Please enter a Valid Document Id";
else
return $alfrescoMessage;
}


/**
 * @method
 *
 * Checkout document/file
 *
 * @name Checkout
 * @label Checkout document/file
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $docUid | Document Uid
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
// Validation done
function checkOut($alfrescoServerUrl, $docUid, $user="", $pwd="") {
$alfresco_url = "$alfrescoServerUrl/s/cmis/checkedout";
$xmlData = array();
$xmlData = '<?xml version="1.0" encoding="utf-8"?>'.'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/" xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/">'.'<cmisra:object>'.'<cmis:properties>'.'<cmis:propertyId propertyDefinitionId="cmis:objectId">'.'<cmis:value>workspace://SpacesStore/'.$docUid.'</cmis:value>'.'</cmis:propertyId>'.'</cmis:properties>'.'</cmisra:object>'.'</entry>';

$alfresco_exec = RestClient::post($alfresco_url,$xmlData,$user,$pwd,"application/atom+xml;type=entry");
$alfrescoMessage = $alfresco_exec->getResponseMessage();
if($alfrescoMessage === 'Created' )
    return "The Document has been Checkedout";
elseif ($alfrescoMessage === 'Conflict')
        return "The Document you are trying to checkout has already been Checkedout";
else
return $alfrescoMessage;
}

/**
 * @method
 *
 * Create a folder in Alfresco Repository
 *
 * @name createFolder
 * @label Create a folder in Alfresco Repository
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $parentFolder | Parent Folder Name
 * @param string | $folderName | Name of the Folder to be created
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function createFolder($alfrescoServerUrl, $parentFolder, $folderName, $user, $pwd) {
  //$domapi_url = "http://localhost:8086/alfresco/service/api/path/workspace/SpacesStore/9ee86211-cc3c-4348-beb0-5320635c2dcb/children";
    $parentFolder = "$parentFolder/";
    $alfresco_url = "$alfrescoServerUrl/s/cmis/p/".$parentFolder."children";
    $xmlData = array();
    $xmlData = '<?xml version="1.0" encoding="utf-8"?>'.'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/" xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/">'.'<title>'.$folderName.'</title>'.'<cmisra:object>'.'<cmis:properties>'.'<cmis:propertyId propertyDefinitionId="cmis:objectTypeId"><cmis:value>cmis:folder</cmis:value></cmis:propertyId>'.'</cmis:properties>'.'</cmisra:object>'.'</entry>';
    $alfresco_exec = RestClient::post($alfresco_url,$xmlData,$user,$pwd,"application/atom+xml");
    $alfrescoMessage = $alfresco_exec->getResponseMessage();
    if($alfrescoMessage === 'Created')
        return "The Folder has been Created";
    else{
        return $alfrescoMessage;
    }
}

/**
 * @method
 *
 * Delete an object(Folder/File) from Alfresco Repository
 *
 * @name deleteObject
 * @label Delete an object(Folder/File) from Alfresco Repository
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $objetcId | Id of the Object(Folder/File) to be deleted
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function deleteObject($alfrescoServerUrl, $objetcId, $user, $pwd) {
    $alfresco_url = "$alfrescoServerUrl/s/cmis/s/workspace:SpacesStore/i/$objetcId";
    $alfresco_exec = RestClient::delete($alfresco_url,$user,$pwd,"application/atom+xml");

    $alfresco_res = json_decode($alfresco_exec->getResponse());
    echo($alfresco_res);
    return $alfresco_res;
}

/**
 * @method
 *
 * Download Document/File from Alfresco Repository
 *
 * @name downloadDoc
 * @label Download Document/File from Alfresco Repository
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $objetcId | Id of the Object(Folder/File) to be deleted
 * @param string | $parentFolder | Parent Folder Name
 * @param string | $folderName | Name of the Folder to be created
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function downloadDoc($alfrescoServerUrl, $parentFolder, $folderName, $user, $pwd) {
    $alfresco_url = "$alfrescoServerUrl/s/cmis/s/workspace:SpacesStore/i/$objetcId";
    $alfresco_exec = RestClient::delete($alfresco,$user,$pwd,"application/atom+xml");
    $alfresco_res = json_decode($alfresco_exec->getResponse());
    echo($alfresco_res);
    return $alfresco_res;
}

/**
 * @method
 *
 * Get a list of Checkedout Document/File from Alfresco Repository
 *
 * @name getCheckedoutFiles
 * @label Get a list of Checkedout Document/File from Alfresco Repository
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function getCheckedoutFiles($alfrescoServerUrl, $user, $pwd) {
   $getChildrenUrl = "$alfrescoServerUrl/s/cmis/checkedout";

   $domapi_exec = RestClient::get($getChildrenUrl,$user,$pwd,'application/atom+xml');
   $sXmlArray = json_decode($domapi_exec->getResponse());
   $sXmlArray = trim($sXmlArray);
   $xXmlArray = simplexml_load_string($sXmlArray);
   $aXmlArray = @json_decode(@json_encode($xXmlArray),1);
   var_dump($aXmlArray);

   return $alfresco_res;
}

/**
 * @method
 *
 * Get Children of the given folder
 *
 * @name getFolderChildren
 * @label Get Children of the given folder
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $folderId | FolderId of the Folder whose children is to be listed
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function getFolderChildren($alfrescoServerUrl, $folderId,  $user, $pwd) {
    $getChildrenUrl = "$alfrescoServerUrl/service/api/node/workspace/SpacesStore/$folderId/children";
    $alfresco_exec = RestClient::get($getChildrenUrl,$user,$pwd);
    $sXmlArray = $alfresco_exec->getResponse();
    $sXmlArray = trim($sXmlArray);
    $xXmlArray = simplexml_load_string($sXmlArray);
    $aXmlArray = @json_decode(@json_encode($xXmlArray),1);
    //var_dump($aXmlArray);
    var_dump($aXmlArray);

    return $aXmlArray;
}

/**
 * @method
 *
 * Uplaod file/document in Alfresco Repository
 *
 * @name uploadDoc
 * @label Uplaod file/document in Alfresco Repository
 *
 * @param string | $alfrescoServerUrl | Server name and port where Alfresco exists | http://localhost:8080/alfresco
 * @param string | $fileSource | File Source
 * @param string | $title | File Title
 * @param string | $description | Description about File
 * @param string | $docType | Type of document to be Uploaded
 * @param string | $user | Valid Admin username to connect to Alfresco server
 * @param string | $pwd | Valid Admin password to connect to Alfresco server
 *
 * @return string | $result | Response
 *
 */
function uploadDoc($alfrescoServerUrl, $fileSource, $title, $description, $docType, $user, $pwd) {
    $filep          =  fopen($fileSource,"r");
    $fileLength     =  filesize($fileSource);
    $fileContent    =  fread($filep,$fileLength);
    $fileContent    =  base64_encode($fileContent);

    $alfresco_url = "$alfrescoServerUrl/s/cmis/p/Sites/children";
    $xmlData = array();
    $xmlData = '<?xml version="1.0" encoding="utf-8"?><entry xmlns="http://www.w3.org/2005/Atom" xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/" xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/"><title>'.$title.'</title><summary>'.$description.'</summary><content type="application/'.$docType.'">'.$fileContent.'</content><cmisra:object><cmis:properties><cmis:propertyId propertyDefinitionId="cmis:objectTypeId"><cmis:value>cmis:document</cmis:value></cmis:propertyId></cmis:properties></cmisra:object></entry>';

    $alfresco_exec = RestClient::post($alfresco_url,$xmlData,$user,$pwd,"application/atom+xml");
    $sXmlArray = $alfresco_exec->getResponse();
    $sXmlArray = trim($sXmlArray);
    $xXmlArray = simplexml_load_string($sXmlArray);
    $aXmlArray = @json_decode(@json_encode($xXmlArray),1);
    var_dump($aXmlArray);
    return $aXmlArray;
}