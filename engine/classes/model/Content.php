<?php
/**
 * Content.php
 * @package    workflow.engine.classes.model
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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

require_once 'classes/model/om/BaseContent.php';

/**
 * Skeleton subclass for representing a row from the 'CONTENT' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Content extends BaseContent {
  
  /*
  * Load the content row specified by the parameters: 
  * @param string $sUID
  * @return variant
  */
  function load($ConCategory, $ConParent, $ConId, $ConLang) {
    $content = ContentPeer::retrieveByPK ( $ConCategory, $ConParent, $ConId, $ConLang );
    if (is_null ( $content )) {
      //we dont find any value for this field and language in CONTENT table
      $ConValue = Content::autoLoadSave ( $ConCategory, $ConParent, $ConId, $ConLang );
    } else {
      //krumo($content);
      $ConValue = $content->getConValue ();
      if ($ConValue == "") { //try to find a valid translation
        $ConValue = Content::autoLoadSave ( $ConCategory, $ConParent, $ConId, $ConLang );
      }
    }
    return $ConValue;
  }
  /*
  * Find a valid Lang for current Content. The most recent 
  * @param string $ConCategory
  * @param string  $ConParent
  * @param string $ConId  
  * @return string
  * 
  */
  function getDefaultContentLang($ConCategory, $ConParent, $ConId, $destConLang) {
    $Criteria = new Criteria ( 'workflow' );
    $Criteria->clearSelectColumns ()->clearOrderByColumns ();
    
    $Criteria->addSelectColumn ( ContentPeer::CON_CATEGORY );
    $Criteria->addSelectColumn ( ContentPeer::CON_PARENT );
    $Criteria->addSelectColumn ( ContentPeer::CON_ID );
    $Criteria->addSelectColumn ( ContentPeer::CON_LANG );
    $Criteria->addSelectColumn ( ContentPeer::CON_VALUE );
    
    $Criteria->add ( ContentPeer::CON_CATEGORY, $ConCategory, CRITERIA::EQUAL );
    $Criteria->add ( ContentPeer::CON_PARENT, $ConParent, CRITERIA::EQUAL );
    $Criteria->add ( ContentPeer::CON_ID, $ConId, CRITERIA::EQUAL );
    
    $Criteria->add ( ContentPeer::CON_LANG, $destConLang, CRITERIA::NOT_EQUAL );
    
    $rs = ContentPeer::doSelectRS ( $Criteria );
    $rs->setFetchmode ( ResultSet::FETCHMODE_ASSOC );
    $rs->next ();
    
    if (is_array ( $row = $rs->getRow () )) {
      $defaultLang = $row ['CON_LANG'];
    
    } else {
      $defaultLang = "";
    }
    return ($defaultLang);
  }
  /*
  * Load the content row and the Save automatically the row for the destination language 
  * @param string $ConCategory
  * @param string  $ConParent
  * @param string $ConId 
  * @param string $destConLang
  * @return string
  * if the row doesn't exist, it will be created automatically, even the default 'en' language
  */
  function autoLoadSave($ConCategory, $ConParent, $ConId, $destConLang) {
    //search in 'en' language, the default language
    $content = ContentPeer::retrieveByPK ( $ConCategory, $ConParent, $ConId, 'en' );
    
    if ((is_null ( $content )) || ($content->getConValue () == "")) {
      $differentLang = Content::getDefaultContentLang ( $ConCategory, $ConParent, $ConId, $destConLang );
      $content = ContentPeer::retrieveByPK ( $ConCategory, $ConParent, $ConId, $differentLang );
    }
    
    //to do: review if the $destConLang is a valid language/
    if (is_null ( $content ))
      $ConValue = ''; //we dont find any value for this field and language in CONTENT table
    else
      $ConValue = $content->getConValue ();
    
    try {
      $con = ContentPeer::retrieveByPK ( $ConCategory, $ConParent, $ConId, $destConLang );
      if (is_null ( $con )) {
        $con = new Content ( );
      }
      $con->setConCategory ( $ConCategory );
      $con->setConParent ( $ConParent );
      $con->setConId ( $ConId );
      $con->setConLang ( $destConLang );
      $con->setConValue ( $ConValue );
      if ($con->validate ()) {
        $res = $con->save ();
      }
    } catch ( Exception $e ) {
      throw ($e);
    }
    
    return $ConValue;
  }
  
  /*
  * Insert a content row  
  * @param string $ConCategory
  * @param string $ConParent
  * @param string $ConId
  * @param string $ConLang
  * @param string $ConValue 
  * @return variant
  */
  function addContent($ConCategory, $ConParent, $ConId, $ConLang, $ConValue) {
    try {
      if ($ConLang != 'en') {
        $baseLangContent = ContentPeer::retrieveByPk($ConCategory, $ConParent, $ConId, 'en');
        if ($baseLangContent === null) {      
          Content::addContent($ConCategory, $ConParent, $ConId, 'en', $ConValue);
        }
      }
      
      $con = ContentPeer::retrieveByPK ( $ConCategory, $ConParent, $ConId, $ConLang );
      
      if (is_null ( $con )) {
        $con = new Content ( );
      } else {
        if ($con->getConParent () == $ConParent && $con->getConCategory () == $ConCategory && $con->getConValue () == $ConValue && $con->getConLang () == $ConLang && $con->getConId () == $ConId)
          return true;
      }
      $con->setConCategory ( $ConCategory );
      if ($con->getConParent () != $ConParent)
        $con->setConParent ( $ConParent );
      $con->setConId ( $ConId );
      $con->setConLang ( $ConLang );
      $con->setConValue ( $ConValue );
      if ($con->validate ()) {
        $res = $con->save ();
        return $res;
      } else {
        $e = new Exception ( "Error in addcontent, the row $ConCategory, $ConParent, $ConId, $ConLang is not Valid" );
        throw ($e);
      }
    } catch ( Exception $e ) {
      throw ($e);
    }
  }
  
  /*
  * Insert a content row  
  * @param string $ConCategory
  * @param string $ConParent
  * @param string $ConId
  * @param string $ConLang
  * @param string $ConValue 
  * @return variant
  */
  function insertContent($ConCategory, $ConParent, $ConId, $ConLang, $ConValue) {
    try {
      $con = new Content ( );
      $con->setConCategory ( $ConCategory );
      $con->setConParent ( $ConParent );
      $con->setConId ( $ConId );
      $con->setConLang ( $ConLang );
      $con->setConValue ( $ConValue );
      if ($con->validate ()) {
        $res = $con->save ();
        return $res;
      } else {
        $e = new Exception ( "Error in addcontent, the row $ConCategory, $ConParent, $ConId, $ConLang is not Valid" );
        throw ($e);
      }
    } catch ( Exception $e ) {
      throw ($e);
    }
  }
  
  /*
  * remove a content row  
  * @param string $ConCategory
  * @param string $ConParent
  * @param string $ConId
  * @param string $ConLang
  * @param string $ConValue 
  * @return variant
  */
  function removeContent($ConCategory, $ConParent, $ConId) {
    try {
      $c = new Criteria ( );
      $c->add ( ContentPeer::CON_CATEGORY, $ConCategory );
      $c->add ( ContentPeer::CON_PARENT, $ConParent );
      $c->add ( ContentPeer::CON_ID, $ConId );
      $result = ContentPeer::doSelectRS ( $c );
      $result->next ();
      $row = $result->getRow ();
      while ( is_array ( $row ) ) {
        ContentPeer::doDelete ( array ($ConCategory, $ConParent, $ConId, $row [3] ) );
        $result->next ();
        $row = $result->getRow ();
      }
    } catch ( Exception $e ) {
      throw ($e);
    }
  
  }

  /*
  * Reasons if the record already exists
  *
  * @param  string  $ConCategory
  * @param  string  $ConParent
  * @param  string  $ConId
  * @param  string  $ConLang
  * @param  string  $ConValue 
  * @return boolean true or false
  */
  function Exists ($ConCategory, $ConParent, $ConId, $ConLang) 
  {
    try {
      $oPro = ContentPeer::retrieveByPk($ConCategory, $ConParent, $ConId, $ConLang);
      if (is_object($oPro) && get_class ($oPro) == 'Content' ) {
        return true;
      } else {
        return false;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
  
  function regenerateContent($langId)
  {
    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn(ContentPeer::CON_CATEGORY);
    $oCriteria->addSelectColumn(ContentPeer::CON_ID);
    $oCriteria->addSelectColumn(ContentPeer::CON_VALUE);
    $oCriteria->add(ContentPeer::CON_LANG, 'en');
    $oCriteria->add(ContentPeer::CON_VALUE, '', Criteria::NOT_EQUAL );
    $oDataset = ContentPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $oContent = new Content();
    while ($aRow = $oDataset->getRow()) {
      $oContent->load($aRow['CON_CATEGORY'], '', $aRow['CON_ID'], $langId);
      $oDataset->next();
    }
  }

  function removeLanguageContent($lanId) {
    try {
      $c = new Criteria ( );
      $c->addSelectColumn(ContentPeer::CON_CATEGORY);
      $c->addSelectColumn(ContentPeer::CON_PARENT);
      $c->addSelectColumn(ContentPeer::CON_ID);
      $c->addSelectColumn(ContentPeer::CON_LANG);

      $c->add ( ContentPeer::CON_LANG, $lanId );
      
      $result = ContentPeer::doSelectRS ( $c );
      $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $result->next ();
      $row = $result->getRow ();

      while ( is_array ( $row ) ) {
        $content = ContentPeer::retrieveByPK( $row['CON_CATEGORY'], '', $row['CON_ID'], $lanId);

        if( $content !== null )
          $content->delete();
        
        $result->next ();
        $row = $result->getRow ();
      }

    } catch ( Exception $e ) {
      throw ($e);
    }
  }
  //Added by Enrique at Feb 9th,2011
  //Gets all Role Names by Role 
  function getAllContentsByRole($sys_lang=SYS_LANG){
  	if (!isset($sys_lang)) $sys_lang = 'en';
  	$oCriteria = new Criteria('workflow');
  	$oCriteria->clearSelectColumns();
  	$oCriteria->addSelectColumn(ContentPeer::CON_ID);
  	$oCriteria->addAsColumn('ROL_NAME', ContentPeer::CON_VALUE);
  	//$oCriteria->addAsColumn('ROL_UID', ContentPeer::CON_ID);
  	$oCriteria->add(ContentPeer::CON_CATEGORY,'ROL_NAME');
  	$oCriteria->add(ContentPeer::CON_LANG, $sys_lang);
  	$oDataset = ContentPeer::doSelectRS($oCriteria);
  	$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  	$aRoles = Array();
  	while ($oDataset->next()){
  		$xRow = $oDataset->getRow();
  		$aRoles[$xRow['CON_ID']] = $xRow['ROL_NAME']; 
  	}
  	return $aRoles;
  }

} // Content
