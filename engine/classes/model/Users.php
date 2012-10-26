<?php
/**
 * Users.php
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

require_once 'classes/model/om/BaseUsers.php';
require_once 'classes/model/IsoCountry.php';
require_once 'classes/model/IsoSubdivision.php';
require_once 'classes/model/IsoLocation.php';

/**
 * Skeleton subclass for representing a row from the 'USERS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class Users extends BaseUsers {
  function create ($aData)
  {
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $result=$this->save();
      }
      else
      {
        $e=new Exception("Failed Validation in class ".get_class($this).".");
        $e->aValidationFailures=$this->getValidationFailures();
        throw($e);
      }
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  
public function userExists($UsrUid)
  {
    try {
      $oRow = UsersPeer::retrieveByPK( $UsrUid );
      if (!is_null($oRow))
      {
        return true;
      }
      else {
        return false;
      }
    }
    catch (Exception $oError) {
      return false;
    }
  }
  
  public function load($UsrUid)
  {
    try {
      $oRow = UsersPeer::retrieveByPK( $UsrUid );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        return $aFields;
      }
      else {
        throw(new Exception( "The row '" . $UsrUid . "' in table USER doesn't exist!" ));
      }
    }
    catch (PropelException $e){  //capture invalid birthday date and replace by null
    	$msg = $e->getMessage();
    	if (strpos('Unable to parse value of [usr_birthday]', $msg) != -1) {
    		$oRow->setUsrBirthday(null);
    		$oRow->save();
    		return $this->load($UsrUid);
    	}
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function loadDetails($UsrUid)
  {
    try {
      $result = array();
      $oUser = UsersPeer::retrieveByPK( $UsrUid );
      if (!is_null($oUser))       {
        $result['USR_UID'] = $oUser->getUsrUid();
        $result['USR_USERNAME'] = $oUser->getUsrUsername();
        $result['USR_FULLNAME'] = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname() ;
        $result['USR_EMAIL']    = $oUser->getUsrEmail();
        return $result;
      }
      else {
//        return $result;
        throw(new Exception( "The row '" . $UsrUid . "' in table USER doesn't exist!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function loadDetailed($UsrUid)
  {
    try {
      $result = array();
      $oUser = UsersPeer::retrieveByPK( $UsrUid );
      if (!is_null($oUser))       {

        $aFields = $oUser->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);

        $aIsoCountry     = IsoCountry::findById($aFields['USR_COUNTRY']);
        $aIsoSubdivision = IsoSubdivision::findById($aFields['USR_COUNTRY'], $aFields['USR_CITY']);
        $aIsoLocation    = IsoLocation::findById($aFields['USR_COUNTRY'], $aFields['USR_CITY'], $aFields['USR_LOCATION']);

        $aFields['USR_COUNTRY_NAME']  = $aIsoCountry['IC_NAME'];
        $aFields['USR_CITY_NAME']     = $aIsoSubdivision['IS_NAME'];
        $aFields['USR_LOCATION_NAME'] = $aIsoLocation['IL_NAME'];

        $result = $aFields;

        return $result;
      }
      else {
//        return $result;
        throw(new Exception( "The row '" . $UsrUid . "' in table USER doesn't exist!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function update($fields)
  {
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['USR_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $result=$this->save();
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  function remove($UsrUid)
  {
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->setUsrUid($UsrUid);
      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }

  function loadByUsername($sUsername)
  {
    $c = new Criteria('workflow');
    $del = DBAdapter::getStringDelimiter();

    $c->clearSelectColumns();
    $c->addSelectColumn( UsersPeer::USR_UID );
    $c->addSelectColumn( UsersPeer::USR_USERNAME );
    $c->addSelectColumn( UsersPeer::USR_STATUS );

    $c->add(UsersPeer::USR_USERNAME, $sUsername);
    return $c;
  }
  
  function loadByUsernameInArray($sUsername){
    echo $sUsername;
    $c  = $this->loadByUsername($sUsername);
    $rs = UsersPeer::doSelectRS($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    print_r($row);
    return $row;
  }

  function getAllInformation($UsrUid)
  {
  if( !isset($UsrUid) or $UsrUid == '' ) {
    throw new Exception('$UsrUid is empty.');
  }
  try {

    require_once 'classes/model/IsoCountry.php';
    require_once 'classes/model/IsoLocation.php';
    require_once 'classes/model/IsoSubdivision.php';
    require_once 'classes/model/Language.php';

    $aFields = $this->load($UsrUid);
    $c = new Criteria('workflow');
    $c->add(IsoCountryPeer::IC_UID, $aFields['USR_COUNTRY']);
    $rs = IsoCountryPeer::doSelectRS($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $Crow = $rs->getRow();

    $c->clearSelectColumns();
    $c->add(IsoSubdivisionPeer::IC_UID, $aFields['USR_COUNTRY']);
    $c->add(IsoSubdivisionPeer::IS_UID, $aFields['USR_CITY']);
    $rs = IsoSubdivisionPeer::doSelectRS($c);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $Srow = $rs->getRow();

    $aRet['username']   = $aFields['USR_USERNAME'];
    $aRet['firstname']  = $aFields['USR_FIRSTNAME'];
    $aRet['lastname'] = $aFields['USR_LASTNAME'];
    $aRet['mail']     = $aFields['USR_EMAIL'];
    $aRet['status']   = $aFields['USR_STATUS'];
    $aRet['address']  = $aFields['USR_ADDRESS'];
    $aRet['phone']    = $aFields['USR_PHONE'];
    $aRet['fax']    = $aFields['USR_FAX'];
    $aRet['cellular']   = $aFields['USR_CELLULAR'];
    $aRet['birthday']   = $aFields['USR_BIRTHDAY'];
    $aRet['position']     = $aFields['USR_POSITION'];
    $aRet['duedate']    = $aFields['USR_DUE_DATE'];
    $aRet['country']    = $Crow['IC_NAME'];
    $aRet['city']     = $Srow['IS_NAME'];
    

    return $aRet;
  }
  catch (Exception $oException) {
    throw $oException;
  }
  }
  
  function getAvailableUsersCriteria($sGroupUID = '')
  {
    try {

      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(UsersPeer::USR_UID);
      $oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
      $oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
      $oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE');

      return $oCriteria;
    }
    catch (exception $oError) {
      throw ($oError);
    }
  }


  /**
   * Get all Active users
   * 
   * @return array of all active users
   */
  function getAll($start=null, $limit=null, $search=null)
  {
    $totalCount = 0;
    $criteria = new Criteria('workflow');
    $criteria->addSelectColumn(UsersPeer::USR_UID);
    $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
    $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
    $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
    $criteria->add(UsersPeer::USR_STATUS, 'ACTIVE');
    $criteria->addAscendingOrderByColumn ( UsersPeer::USR_LASTNAME );

    if( $search ) {
      $criteria->add(
        $criteria->getNewCriterion(
          UsersPeer::USR_USERNAME,
          "%$search%", Criteria::LIKE
        )->addOr(
          $criteria->getNewCriterion(
            UsersPeer::USR_FIRSTNAME,
            "%$search%", Criteria::LIKE
          )
        )->addOr(
          $criteria->getNewCriterion(
            UsersPeer::USR_LASTNAME,
            "%$search%", Criteria::LIKE
          )
        )
      );
    }

    $c = clone $criteria;
    $c->clearSelectColumns();
    $c->addSelectColumn('COUNT(*)');
    $dataset = UsersPeer::doSelectRS($c);
    $dataset->next();
    $rowCount = $dataset->getRow();

    if( is_array($rowCount) )
      $totalCount = $rowCount[0];
    
    if( $start )
      $criteria->setOffset($start);
    if( $limit )
      $criteria->setLimit($limit);
      
    $rs = UsersPeer::doSelectRS($criteria);
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    $rows = Array();
    while( $rs->next() )
      $rows[] = $rs->getRow();

    $result->data = $rows;
    $result->totalCount = $totalCount;

    return $result;
  }
} // Users

?>
