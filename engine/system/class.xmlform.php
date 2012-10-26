<?php
/**
 * class.xmlform.php
 * @package gulliver.system 
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
/**
 * Class XmlForm_Field
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field {
  var $name             = '';
  var $type             = 'field';
  var $label            = '';
  var $owner;
  var $language;
  var $group            = 0;
  var $mode             = '';
  var $defaultValue     = NULL;
  var $gridFieldType    = '';
  var $gridLabel        = '';
  /* Hint value generic declaration */
  var $hint             = '';       
  /*to change the presentation*/
  var $enableHtml       = false;
  var $style            = '';
  var $withoutLabel     = false;
  var $className        = '';
  /*attributes for paged table*/
  var $colWidth         = 140;
  var $colAlign         = 'left';
  var $colClassName     = '';
  var $titleAlign       = '';
  var $align            = '';
  var $showInTable      = '';
  /*Events*/
  var $onclick          = '';
  /*attributes for data filtering*/
  /*dataCompareField = field to be compared with*/
  var $dataCompareField = '';
  /* $dataCompareType : '=' ,'<>' , 'like', ... , 'contains'(== ' like "%value%"')
   */
  var $dataCompareType  = '=';
  var $sql              = '';
  var $sqlConnection    = '';
  //Attributes for PM Tables integration (only ProcessMaker for now)
  var $pmtable          = '';
  var $keys             = '';
  var $pmconnection     = '';
  var $pmfield          = '';

  // For mode cases Grid 
  var $modeGrid         = '';
  var $modeForGrid      = '';
  /**
   * Function XmlForm_Field
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string xmlNode
   * @param string lang
   * @param string home
   * @return string
   */
  function XmlForm_Field($xmlNode, $lang = 'en', $home = '', $owner = NULL)
  {
    //Loads any attribute that were defined in the xmlNode
    //except name and label.
    $myAttributes = get_class_vars ( get_class ( $this ) );
    foreach ( $myAttributes as $k => $v )
      $myAttributes [$k] = strtoupper ( $k );
    //$data: Includes labels and options.
    $data = &$xmlNode->findNode ( $lang );
    if(!isset($data->value)){ //It seems that in the actual language there are no value for the current field, so get the value in English
      $data = &$xmlNode->findNode ( "en" );
      if(!isset($data->value)){ //It seems that in the actual language there are no value for the current field, so get the value in First language
        if(isset($xmlNode->children[0])){//Lets find first child
          if((isset($xmlNode->children[0]->name))&&(strlen($xmlNode->children[0]->name)==2)){//Just to be sure that the node ocrresponds to a valid lang
            $data = &$xmlNode->findNode ( $xmlNode->children[0]->name );
          }
        }
      }
    }
    @$this->label = $data->value;


    /*Loads the field attributes*/
    foreach ( $xmlNode->attributes as $k => $v ) {
      $key = array_search ( strtoupper ( $k ), $myAttributes );
      if ($key)
        eval ( '$this->' . $key . '=$v;' );
    }
    //Loads the main attributes
    $this->name = $xmlNode->name;
    $this->type = strtolower ( $xmlNode->attributes ['type'] );
    preg_match ( '/\s*([^\s][\w\W]*)?/m', $xmlNode->value, $matches );
    $this->sql = (isset ( $matches [1] )) ? $matches [1] : '';
    //List Options
    if (isset ( $data->children ))
      foreach ( $data->children as $k => $v ) {
        if ($v->type !== 'cdata')
          $this->{$v->name} [$v->attributes ["name"]] = $v->value;
      }
    $this->options = (isset ( $this->option )) ? $this->option : array ();
    //Sql Options : cause warning because values are not setted yet.
    //if ($this->sql!=='') $this->executeSQL();
    //maybe $ownerMode is not defined..
    $ownerMode = isset ( $owner->mode ) ? $owner->mode : 'edit';
    if ($this->mode === '')
      $this->mode = $ownerMode !== '' ? $ownerMode : 'edit';
    $this->modeForGrid = $this->mode ;
  }

  /**
   * validate if a value is setted
   * @param  $value
   * @return boolean true/false
   */
  function validateValue($value)
  {
    return isset ( $value );
  }

  /**
   * execute a xml query
   * @param &$owner reference of owner
   * @param $row
   * @return $result array of results
   */
  private function executeXmlDB(&$owner, $row = -1)
  {
    if (! $this->sqlConnection)
      $dbc = new DBConnection ( );
    else {

      if (defined ( 'DB_' . $this->sqlConnection . '_USER' )) {
        if (defined ( 'DB_' . $this->sqlConnection . '_HOST' ))
          eval ( '$res[\'DBC_SERVER\'] = DB_' . $this->sqlConnection . '_HOST;' );
        else
          $res ['DBC_SERVER'] = DB_HOST;
        if (defined ( 'DB_' . $this->sqlConnection . '_USER' ))
          eval ( '$res[\'DBC_USERNAME\'] = DB_' . $this->sqlConnection . '_USER;' );
        if (defined ( 'DB_' . $this->sqlConnection . '_PASS' ))
          eval ( '$res[\'DBC_PASSWORD\'] = DB_' . $this->sqlConnection . '_PASS;' );
        else
          $res ['DBC_PASSWORD'] = DB_PASS;
        if (defined ( 'DB_' . $this->sqlConnection . '_NAME' ))
          eval ( '$res[\'DBC_DATABASE\'] = DB_' . $this->sqlConnection . '_NAME;' );
        else
          $res ['DBC_DATABASE'] = DB_NAME;
        if (defined ( 'DB_' . $this->sqlConnection . '_TYPE' ))
          eval ( '$res[\'DBC_TYPE\'] = DB_' . $this->sqlConnection . '_TYPE;' );
        else
          $res ['DBC_TYPE'] = defined ( 'DB_TYPE' ) ? DB_TYPE : 'mysql';
        $dbc = new DBConnection ( $res ['DBC_SERVER'], $res ['DBC_USERNAME'], $res ['DBC_PASSWORD'], $res ['DBC_DATABASE'], $res ['DBC_TYPE'] );
      } else {
        $dbc0 = new DBConnection ( );
        $dbs0 = new DBSession ( $dbc0 );
        $res  = $dbs0->execute ( "select * from  DB_CONNECTION WHERE DBC_UID=" . $this->sqlConnection );
        $res  = $res->read ();
        $dbc  = new DBConnection ( $res ['DBC_SERVER'], $res ['DBC_USERNAME'], $res ['DBC_PASSWORD'], $res ['DBC_DATABASE'] );
      }
    }
    $query  = G::replaceDataField ( $this->sql, $owner->values );
    $dbs    = new DBSession ( $dbc );
    $res    = $dbs->execute ( $query );
    $result = array ();
    while ( $row = $res->Read () ) {
      $result [] = $row;
    }
    return $result;
  }
  /**
   * Execute a propel query
   * @param &$owner reference
   * @param $row
   * @return $result array of
   */
  private function executePropel(&$owner, $row = -1)
  {
    //g::pr($row);
    if (! isset ( $owner->values [$this->name] )) {
      if ($row > - 1) {
        $owner->values [$this->name] = array ();
      } else {
        $owner->values [$this->name] = '';
      }
    }
    if (! is_array ( $owner->values [$this->name] )) {
      //echo '1';
      $query = G::replaceDataField ( $this->sql, $owner->values );
    } else {
      $aAux = array ();
      foreach ( $owner->values as $key => $data ) {
        if (is_array ( $data )) {
          //echo '3:'.$key.' ';
          if (isset ( $data [$row] )){
            $qValue = $data [$row];
          }else{
            if (isset($owner->fields[$key]->selectedValue)){
              $qValue = $owner->fields[$key]->selectedValue;
            }else{
              $qValue = '';
            }
          }
          $aAux [$key] = $qValue;
          //$aAux [$key] = isset ( $data [$row] ) ? $data [$row] : '';
        } else {
          //echo '4'.$key.' ';
          $aAux [$key] = $data;
        }
      }
      
      //echo '2';
      //g::pr($aAux);
      $query = G::replaceDataField ( $this->sql, $aAux );
    }
    //echo $query;

    $result = array ();
    if ($this->sqlConnection == 'dbarray') {
      try {
        $con  = Propel::getConnection ( $this->sqlConnection );
        $stmt = $con->createStatement ();
        $rs   = $con->executeQuery ( $query, ResultSet::FETCHMODE_NUM );
      }
      catch ( Exception $e ) {  //dismiss error because dbarray shouldnt be defined in some contexts.
        return $result;
      }
    }
    else {
      try {
        $con  = Propel::getConnection ( $this->sqlConnection );
        $stmt = $con->createStatement ();
        $rs   = $stmt->executeQuery ( $query, ResultSet::FETCHMODE_NUM );
      }
      catch  ( Exception $e ) {  //dismiss error because dbarray shouldnt be defined in some contexts.
        return $result;
      }
    }

    $rs->next ();
    $row = $rs->getRow ();
    while ( is_array ( $row ) ) {
      $result [] = $row;
      $rs->next ();
      $row = $rs->getRow ();
    }
    return $result;
  }
  /**
   * Function executeSQL
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string owner
   * @return string
   */
  function executeSQL(&$owner, $row = -1) {
    if (! isset ( $this->sql ))
      return 1;
    if ($this->sql === '')
      return 1;
    if (! $this->sqlConnection)
      $this->sqlConnection = 'workflow';

    //Read the result of the query
    if ($this->sqlConnection === "XMLDB") {
      $result = $this->executeXmlDB ( $owner, $row );
    } else {
      $result = $this->executePropel ( $owner, $row );
    }
    $this->sqlOption = array ();
    $this->options   = array ();
    if (isset ( $this->option )) {
      foreach ( $this->option as $k => $v )
        $this->options [$k] = $v;
    }
    for($r = 0; $r < sizeof ( $result ); $r ++) {
      $key = reset ( $result [$r] );
      $this->sqlOption [$key] = next ( $result [$r] );
      $this->options [$key]   = $this->sqlOption [$key];
    }

    if ($this->type != 'listbox') {
      if (isset ( $this->options ) && isset ( $this->owner ) && isset ( $this->owner->values [$this->name] )) {
        if ((! is_array ( $this->owner->values [$this->name] )) && ! ((is_string ( $this->owner->values [$this->name] ) || is_int ( $this->owner->values [$this->name] )) && array_key_exists ( $this->owner->values [$this->name], $this->options ))) {
          reset ( $this->options );
          $firstElement = key ( $this->options );
          if (isset ( $firstElement ))
            $this->owner->values [$this->name] = $firstElement;
          else
            $this->owner->values [$this->name] = '';
        }
      }
    }
    return 0;
  }

  /**
   * return the html entities of a value
   * @param <any> $value
   * @param <type> $flags
   * @param <String> $encoding
   * @return <any>
   */

  function htmlentities($value, $flags = ENT_QUOTES, $encoding = 'utf-8')
  {

    if ($this->enableHtml){
    return $value;}
    else{
    return htmlentities ( $value, $flags, $encoding );
    }
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL)
  {
    //this is an unknown field type.
    return $this->htmlentities ( $value != '' ? $value : $this->name, ENT_COMPAT, 'utf-8' );
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @return string
   */
  function renderGrid($values = array(), $owner = NULL, $onlyValue = false, $therow = -1)
  {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $result [] = $this->render ( $v, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue, $r, $therow );
      $r ++;
    }
    return $result;
  }
  /**
   * render the field in a table
   * @param $values
   * @param $owner
   * @param <Boolean> $onlyValue
   * @return <String> $result
   */
  function renderTable($values = '', $owner = NULL, $onlyValue = false)
  {
    $r      = 1;
    $result = $this->render ( $values, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue );
    return $result;
  }

  /**
   * Function dependentOf
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return array
   */
  function dependentOf()
  {
    $fields = array ();
    if (isset ( $this->formula )) {
      preg_match_all ( "/\b[a-zA-Z][a-zA-Z_0-9]*\b/", $this->formula, $matches, PREG_PATTERN_ORDER );
      /*      if ($this->formula!=''){
        var_dump($this->formula);
        var_dump($matches);
        var_dump(array_keys($this->owner->fields));
        die;
      }*/
      foreach ( $matches [0] as $field ) {
        //if (array_key_exists( $this->owner->fields, $field ))
        $fields [] = $field;
      }
    }
    return $fields;
  }
  /**
   * Function mask
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string format
   * @param string value
   * @return string
   */
  function mask($format, $value)
  {
    $value = explode ( '', $value );
    for($j = 0; $j < strlen ( $format ); $j ++) {
      $result  = '';
      $correct = TRUE;
      for($i = $j; $i < strlen ( $format ); $i ++) {
        $a = substr ( $format, $i, 1 );
        $e = $i < strlen ( $value ) ? substr ( $value, $i, 1 ) : '';
        //$e=$i<strlen($format)?substr($format, $i+1,1):'';
        switch ($a) {
          case '0' :
            if ($e === '')
              $e = '0';
          case '#' :
            if ($e === '')
              break 3;
            if (strpos ( '0123456789', $e ) !== FALSE) {
              $result .= $e;
            } else {
              $correct = FALSE;
              break 3;
            }
            break;
          case '.' :
            if ($e === '')
              break 3;
            if ($e === $a)
              break 1;
            if ($e !== $a)
              break 2;
          default :
            if ($e === '')
              break 3;
            $result .= $e;
        }
      }
    }
    if ($e !== '')
      $correct = FALSE;

    //##,###.##   --> ^...$ no parece pero no, o mejor si, donde # es \d?, en general todos
    // es valida cuando no encuentra un caracter que no deberia estar, puede no terminar la mascara
    // pero si sobran caracteres en el value entonces no se cumple la mascara.
    return $correct ? $result : $correct;
  }
  /**
   * Function getAttributes
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function getAttributes()
  {
    $attributes = array ();
    $json = new Services_JSON ( );
    foreach ( $this as $attribute => $value ) {
      switch ($attribute) {
        case 'sql' :
        case 'sqlConnection' :
        case 'name' :
        case 'type' :
        case 'owner' :
          break;
        default :
          if (substr ( $attribute, 0, 2 ) !== 'on')
            $attributes [$attribute] = $value;
      }
    }
    if (sizeof ( $attributes ) < 1)
      return '{}';
    return $json->encode ( $attributes );
  }
  /**
   * Function getEvents
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function getEvents()
  {
    $events = array ();
    $json   = new Services_JSON ( );
    foreach ( $this as $attribute => $value ) {
      if (substr ( $attribute, 0, 2 ) === 'on')
        $events [$attribute] = $value;
    }
    if (sizeof ( $events ) < 1)
      return '{}';
    return $json->encode ( $events );
  }
  /**
   * Function attachEvents: Attaches events to a control using
   *   leimnud.event.add
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @param $elementRef
   * @access public
   */
  function attachEvents($elementRef)
  {
    $events = '';
    foreach ( $this as $attribute => $value ) {
      if (substr ( $attribute, 0, 2 ) == 'on') {
        $events = 'if (' . $elementRef . ') leimnud.event.add(' . $elementRef . ',"' . substr ( $attribute, 2 ) . '",function(){' . $value . '});' . "\n";
      }
    }
  }
  /**
   * Function createXmlNode: Creates an Xml_Node object storing
   *   the data of $this Xml_Field.
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return Xml_Node
   */
  function createXmlNode($includeDefaultValues = false)
  {
    /* Start Comment: Creates the corresponding XML Tag for $this
     *    object.
     */
    $attributesList = $this->getXmlAttributes ( $includeDefaultValues );
    $node = new Xml_Node ( $this->name, 'open', $this->sql, $attributesList );
    /* End Comment */
    /* Start Comment: Creates the languages nodes and options
     *   if exist.
     */
    $node->addChildNode ( new Xml_Node ( '', 'cdata', "\n" ) );
    $node->addChildNode ( new Xml_Node ( $this->language, 'open', $this->label ) );
    if (isset ( $this->option )) {
      foreach ( $this->option as $k => $v )
        $node->children [1]->addChildNode ( new Xml_Node ( 'option', 'open', $v, array ('name' => $k ) ) );
    }
    /* End Comment */
    return $node;
  }
  /**
   * Function updateXmlNode: Updates and existing Xml_Node
   *   with the data of $this Xml_Field.
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return Xml_Node
   */
  function &updateXmlNode(&$node, $includeDefaultValues = false)
  {
    /* Start Comment: Modify the node's attributes and value.
     */
    $attributesList   = $this->getXmlAttributes ( $includeDefaultValues );
    $node->name       = $this->name;
    $node->value      = $this->sql;
    $node->attributes = $attributesList;
    /* End Comment */
    /* Start Comment: Modifies the languages nodes
     */
    $langNode = & $node->findNode ( $this->language );
    $langNode->value = $this->label;
    if (isset ( $this->option )) {
      $langNode->children = array ();
      foreach ( $this->option as $k => $v )
        $langNode->addChildNode ( new Xml_Node ( 'option', 'open', $v, array ('name' => $k ) ) );
    }
    /* End Comment */
    return $node;
  }
  /**
   * Function getXmlAttributes: Returns an associative array
   *   with the attributes of $this Xml_field (only the modified ones).
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param boolean includeDefaultValues  Includes attributes
   *   with default values.
   * @return Xml_Node
   */
  function getXmlAttributes($includeDefaultValues = false)
  {
    $attributesList = array ();
    $class          = get_class ( $this );
    $default        = new $class ( new Xml_Node ( 'DEFAULT', 'open', '', array ('type' => $this->type ) ) );
    foreach ( $this as $k => $v ) {
      switch ($k) {
        case 'owner' :
        case 'name' :
        case 'type' :
        case 'language' :
        case 'sql' :
          break;
        default :
          if (($v !== $default->{$k}) || $includeDefaultValues)
            $attributesList [$k] = $v;
      }
    }
    return $attributesList;
  }
  /** Used in Form::validatePost
   * @param $value
   * @param &$owner
   * @return $value
   */
  function maskValue($value, &$owner)
  {
    return $value;
  }
  /*Close this object*/
  /**
   * clone the current object
   * @return <Object>
   */
  function cloneObject()
  {
    //return unserialize( serialize( $this ) );//con este cambio los formularios ya no funcionan en php4
    return clone ($this);
  }

  /**
   * get a value from a PM Table
   * @param  <Object> $oOwner
   * @return <String> $sValue
   */
  function getPMTableValue($oOwner)
  {
    $sValue = '';
    if (isset($oOwner->fields[$this->pmconnection])) {
      if (defined('PATH_CORE')) {
        if (file_exists(PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php')) {
          require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
          $oAdditionalTables = new AdditionalTables();
          try {
            $aData = $oAdditionalTables->load($oOwner->fields[$this->pmconnection]->pmtable, true);
          }
          catch (Exception $oError) {
            $aData = array('FIELDS' => array());
          }
          $aKeys   = array();
          $aValues = explode('|', $oOwner->fields[$this->pmconnection]->keys);
          $i       = 0;
          foreach ($aData['FIELDS'] as $aField) {
            if ($aField['FLD_KEY'] == '1') {
              // note added by gustavo cruz gustavo[at]colosa[dot]com
              // this additional [if] checks if a case variable has been set
              // in the keys attribute, so it can be parsed and replaced for
              // their respective value.
              if (preg_match("/@#/", $aValues[$i])) {
                // check if a case are running in order to prevent that preview is
                // erroneous rendered.
                if (isset($_SESSION['APPLICATION'])){
                    G::LoadClass('case');
                    $oApp= new Cases();
                    if ($oApp->loadCase($_SESSION['APPLICATION'])!=null){
                        $aFields = $oApp->loadCase($_SESSION['APPLICATION']);
                        $formVariable = substr($aValues[$i], 2);
                        if(isset($aFields['APP_DATA'][$formVariable])){
                            $formVariableValue = $aFields['APP_DATA'][$formVariable];
                            $aKeys[$aField['FLD_NAME']] = (isset($formVariableValue) ? G::replaceDataField($formVariableValue, $oOwner->values) : '');
                        } else {
                            $aKeys[$aField['FLD_NAME']] = '';
                        }
                    } else {
                        $aKeys[$aField['FLD_NAME']] = '';
                    }
                } else {
                    $aKeys[$aField['FLD_NAME']] = '';
                }
              } else {
                $aKeys[$aField['FLD_NAME']] = (isset($aValues[$i]) ? G::replaceDataField($aValues[$i], $oOwner->values) : '');
              }
              $i++;
            }
          }
          try {
            $aData = $oAdditionalTables->getDataTable($oOwner->fields[$this->pmconnection]->pmtable, $aKeys);
          }
          catch (Exception $oError) {
            $aData = array();
          }
          if (isset($aData[$this->pmfield])) {
            $sValue = $aData[$this->pmfield];
          }
        }
      }
    }
    return $sValue;
  }
 
  /**
   * Prepares NS Required Value
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param boolean optional (true = always show, false = show only if not empty)
   * @return string
   */
  
  function NSRequiredValue($show = false){
    if (isset($this->required)){
      $req = ($this->required)? '1':'0';
    }else{
      $req = '0';
    }  
    $idv = 'pm:required="'.$req.'"';
    if ($show){
      return $idv;
    }else{
      return ($req != '0')? $idv : '';
    }
  }
  
  
  /**
   * Prepares NS Required Value
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param boolean optional (true = always show, false = show only if not empty)
   * @return string 
   */
  
  function NSGridLabel($show = false){
    $idv = 'pm:label="'.$this->label.'"';
    if ($show){
      return $idv;
    }else{
      return ($this->label != '')? $idv : '';
    }
  }
  
  
  /**
   * Prepares NS Default Text
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param boolean optional (true = always show, false = show only if not empty)
   * @return string
   */
  function NSDefaultValue($show = false){
    $idv = 'pm:defaultvalue="'.$this->defaultValue.'"';
    if ($show){
      return $idv;
    }else{
      return ($this->defaultValue != '')? $idv : '';
    }
  }
  
  /**
   * Prepares NS Grid Type
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param boolean optional (true = always show, false = show only if not empty)
   * @return string
   */
  function NSGridType($show = false){
    $igt = 'pm:gridtype="'.$this->gridFieldType.'"';
    if ($show){
      return $igt;
    }else{
      return ($this->gridFieldType != '')? $igt : '';
    }
  }
  
  /**
   * Prepares NS Grid Type
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param boolean optional (true = always show, false = show only if not empty)
   * @return string
   */
  function NSDependentFields($show = false){
    $idf = 'pm:dependent="'.(($this->dependentFields != '')? '1':'0').'"';
    if ($show){
      return $idf;
    }else{
      return ($this->dependentFields != '')? $idf : '';
    }
  }
  
  /**
   * Prepares Hint HTML if hint value is defined
   * @author Enrique Ponce de Leon <enrique@colosa.com>
   * @param void
   * @return string
   **/
  
  function renderHint(){
    $_outHint = '';
    if ($this->hint != '' && $this->mode=='edit'){
      $_outHint = '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
                     <image src="/images/help4.gif" width="13" height="13" border="0"/>
                   </a>';
    }
    return $_outHint;
  }
  
}  
/**
 * Class XmlForm_Field_Title
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Title extends XmlForm_Field
{
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, &$owner) {
    $this->label = G::replaceDataField ( $this->label, $owner->values );
    return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\' >' . $this->htmlentities ( $this->label ) . '</span>';
  }
  /**
   * A title node has no value
   * @param $value
   * @return false
   */
  function validateValue($value) {
    return false;
  }
}
/**
 * Class XmlForm_Field_Subtitle
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Subtitle extends XmlForm_Field
{
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL)
  {
    return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\' >' . $this->htmlentities ( $this->label ) . '</span>';
  }
  /**
   * A subtitle node has no value
   * @param $value
   * @return false
   */
  function validateValue($value)
  {
    return false;
  }
}
/**
 * Class XmlForm_Field_SimpleText
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_SimpleText extends XmlForm_Field
{
  var $size       = 15;
  var $maxLength  = '';
  var $validate   = 'Any';
  var $mask       = '';
  /* Additional events */
  var $onkeypress = '';
  var $renderMode  = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, &$owner)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' '.$this->NSRequiredValue().' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      else
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' '.$this->NSRequiredValue().' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
    } elseif ($this->mode === 'view') {
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' '.$this->NSRequiredValue().' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @param string owner
   * @return string
   */
  function renderGrid($values = array(), $owner)
  {
    $result = array ();
    $r = 1;
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;
    
    foreach ( $values as $v ) {
      $html = '';
      if ($this->renderMode === 'edit'){ //EDIT MODE
        $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
        $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'type="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" ';
        $html .= 'value="'.$this->htmlentities($v, ENT_QUOTES, 'utf-8').'" ';
        $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= $this->NSRequiredValue().' ';
        $html .= $this->NSGridType().' ';
        $html .= $this->NSGridLabel().' ';
        $html .= '/>';
      }else{ //VIEW MODE
        $html .= $this->htmlentities($v, ENT_QUOTES, 'utf-8');
        $html .= '<input ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'type="hidden" value="'.$this->htmlentities($v, ENT_QUOTES, 'utf-8').'" />';
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Text
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Text extends XmlForm_Field_SimpleText
{
  var $size             = 15;
  var $maxLength        = 64;
  var $validate         = 'Any';
  var $mask             = '';
  var $defaultValue     = '';
  var $required         = false;
  var $dependentFields  = '';
  var $linkField        = '';
  //Possible values:(-|UPPER|LOWER|CAPITALIZE)
  var $strTo            = '';
  var $readOnly         = false;
  var $sqlConnection    = 0;
  var $sql              = '';
  var $sqlOption        = array ();
  //Attributes only for grids
  var $formula          = '';
  var $function         = '';
  var $replaceTags      = 0;
  var $renderMode       = '';
  
  
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    if ($this->renderMode =='') $this->renderMode = $this->mode;
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );
      $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $value = $firstElement;
    }

    //NOTE: string functions must be in G class
    if ($this->strTo === 'UPPER') $value = strtoupper ( $value );
    if ($this->strTo === 'LOWER') $value = strtolower ( $value );
    //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    if ($this->replaceTags == 1) {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    
    $html = '';
    if ($this->renderMode == 'edit'){ //EDIT MODE
      $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"': '';
      $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
      $html .= 'id="form['. $this->name . ']" ';
      $html .= 'name="form[' . $this->name . ']" ';
      $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
      $html .= 'value="'.$this->htmlentities ($value, ENT_QUOTES, 'utf-8').'" ';
      $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
      $html .= 'onkeypress="'.$this->htmlentities($onkeypress, ENT_COMPAT, 'utf-8').'" ';
      $html .= $this->NSDefaultValue().' ';
      $html .= $this->NSRequiredValue().' ';
      $html .= '/>';
    }else{ //VIEW MODE
      $html .= $this->htmlentities($value, ENT_QUOTES, 'utf-8');
      $html .= '<input ';
      $html .= 'id="form['. $this->name . ']" ';
      $html .= 'name="form[' . $this->name . ']" ';
      $html .= 'type="hidden" value="'.$this->htmlentities($value, ENT_QUOTES, 'utf-8').'" />';
    }
    
    $html .= $this->renderHint();  
    if (($this->readOnly == 1)&&($this->renderMode == 'edit'))    
      $html = str_replace("class=\"module_app_input___gray\"","class=\"module_app_input___gray_readOnly\"",$html);
 
    return $html;
  }

  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @param string owner
   * @return string
   */
  function renderGrid($values = array(), $owner)
  {
    $result = $aux = array ();
    $r      = 1;
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;

    foreach ( $values as $v ) {
      $this->executeSQL ( $owner, $r );
      $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $v = $firstElement;
      if ($this->replaceTags == 1) {
        $v = G::replaceDataField ( $v, $owner->values );
      }
      $aux [$r] = $v;
      
      $html = '';
      if ($this->renderMode == 'edit'){ //EDIT MODE
        $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"': '';
        $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'type="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" ';
        $html .= 'value="'.$this->htmlentities ($v, ENT_QUOTES, 'utf-8').'" ';
        $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= $this->NSRequiredValue().' ';
        $html .= $this->NSGridLabel().' ';
        $html .= $this->NSGridType().' ';
        $html .= $this->NSDependentFields().' ';
        $html .= '/>';
      }else{ //VIEW MODE
        $html .= $this->htmlentities($v, ENT_QUOTES, 'utf-8');
        $html .= '<input ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= 'type="hidden" value="'.$this->htmlentities($v, ENT_QUOTES, 'utf-8').'" />';
      }
      
      $result [] = $html;
      $r ++;
    }
    $this->options = $aux;
    return $result;
  }

  function renderTable($values = '', $owner) {
    $result = $this->htmlentities ( $values, ENT_COMPAT, 'utf-8' );
    return $result;
  }

}

/**
 * Class XmlForm_Field_Suggest
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Suggest extends XmlForm_Field_SimpleText //by neyek
{
  var $size                  = 15;
  var $maxLength             = 64;
  var $validate              = 'Any';
  var $mask                  = '';
  var $defaultValue          = '';
  var $required              = false;
  var $dependentFields       = '';
  var $linkField             = '';
  //Possible values:(-|UPPER|LOWER|CAPITALIZE)
  var $strTo                 = '';
  var $readOnly              = false;
  var $sqlConnection         = 0;
  var $sql                   = '';
  var $sqlOption             = array ();
  //Atributes only for grids
  var $formula               = '';
  var $function              = '';
  var $replaceTags           = 0;

  var $ajaxServer            = '../gulliver/genericAjax';
  var $maxresults;
  var $savelabel             = 1;
  var $shownoresults;
  var $callback              = '';

  var $store_new_entry       = '';
  var $table                 = '';
  var $table_data            = '';
  var $primary_key           = '';
  var $primary_key_data      = '';
  var $primary_key_type      = '';
  var $primary_key_type_data = '';

  var $field                 = '';


  /**
   * Function render
   * @author Erik A. Ortiz.
   * @param $value
   * @param $owner
   * @return <String>
   */
  function render($value = NULL, $owner = NULL)
  {

    
    if (! $this->sqlConnection)
      $this->sqlConnection = 'workflow';

    //NOTE: string functions must be in G class
    if ($this->strTo === 'UPPER')
      $value = strtoupper ( $value );
    if ($this->strTo === 'LOWER')
      $value = strtolower ( $value );
      //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );

    if ($this->replaceTags == 1) {
      $value = G::replaceDataField ( $value, $owner->values );
    }

    $aProperties = Array(
        'value' =>'""',
        'size'  => '"'.$this->size.'"',
    );

    $storeEntry = '';
    if($this->store_new_entry){
      $storeEntry = 'onchange="storeEntry(this, \''.$this->sqlConnection.'\', \''.$this->table.'\', \''.$this->primary_key.'\', \''.$this->primary_key_type.'\', \''.$this->field.'\')"';
    }

    $formVariableValue    = '';
    $formVariableKeyValue = '';
    G::LoadClass('case');
    $oApp= new Cases();
    if (isset($_SESSION['APPLICATION']) && ($_SESSION['APPLICATION'] != null && $oApp->loadCase($_SESSION['APPLICATION'])!=null) ) {
      $aFields = $oApp->loadCase($_SESSION['APPLICATION']);
      if(isset($aFields['APP_DATA'][$this->name . '_label'])){
          $formVariableValue    = $aFields['APP_DATA'][$this->name . '_label'];
          $formVariableKeyValue = $aFields['APP_DATA'][$this->name];
      }
    }

    if ($this->mode === 'edit') {
      if ($this->readOnly) {
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      } else {
//        $str = '<textarea '.$storeEntry.' class="module_app_input___gray" style="height:16px" rows=1 cols="'.$this->size.'" id="form[' . $this->name . ']" name="form[' . $this->name . ']" >'.$this->htmlentities($value, ENT_COMPAT, 'utf-8').'</textarea>';
        if(strlen(trim($formVariableValue))>0) {
          $value = $formVariableValue;
        }
        $name = "'".$this->name."'";
        $str  = '<input type="text" '.$storeEntry.' class="module_app_input___gray" size="'.$this->size.'" id="form[' . $this->name . '_label]" name="form[' . $this->name . '_label]" value="'.$this->htmlentities($value, ENT_COMPAT, 'utf-8').'" onblur="idSet('. $name . ');"';
        $str .= $this->NSDependentFields(true).' ';
        $str .= '/>';
        $str .= '<input ';
        $str .= 'id="form['. $this->name . ']" ';
        $str .= 'name="form[' . $this->name . ']" ';
        $str .= 'value="' . $this->htmlentities ( $formVariableKeyValue, ENT_COMPAT, 'utf-8' ) . '" ' ;
        $str .= 'type="hidden" />';

        $str .= $this->renderHint();      
        if( trim($this->callback) != '' ) {
          $sCallBack = 'try{'.$this->callback.'}catch(e){alert("Suggest Widget call back error: "+e)}';
        } else {
          $sCallBack = '';
        }

        $hash = str_rot13(base64_encode($this->sql.'@|'.$this->sqlConnection));
//        $sOptions  = 'script:"'.$this->ajaxServer.'?request=suggest&json=true&limit='.$this->maxresults.'&hash='.$hash.'&dependentFields='. $this->dependentFields .'&field=" + getField(\''. $this->name .'\').value + "&",';
        $sSQL = $this->sql;
        $nCount = preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/', $sSQL, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

        $sResult = array();
        if($nCount){
          for($i=0; $i<$nCount; $i++){
            if (isset($match[0][$i][0]) && isset($match[2][$i][0])) {
              $aResult[$match[0][$i][0]] = $match[2][$i][0];
            }
          }
        }

        $depValues = '';
        $i = 1;
        if(isset($aResult) && $aResult ) {
          $sResult     = '"' . implode('","', $aResult) . '"';
          $aResultKeys = array_keys($aResult);
          $sResultKeys = str_rot13(base64_encode(implode('|', $aResultKeys)));

          foreach($aResult as $key=>$field) {
            $depValues .= 'getField(\''.$field.'\').value';
            if($i++<count($aResult))
              $depValues .= '+"|"+';
          }
          $depValues = '+'.$depValues.'+' ;
        } else {
          $sResult     = '';
          $sResultKeys = '';
          $depValues = '+';
        }
        
        $sOptions  = 'script: function (input) {var inputValue = base64_encode(getField(\''. $this->name .'_label\').value); return "'.$this->ajaxServer.'?request=suggest&json=true&limit='.$this->maxresults.'&hash='.$hash.'&dependentFieldsKeys=' . $sResultKeys . '&dependentFieldsValue="'.$depValues.'"&input="+inputValue+"&inputEnconde64=enable"; },';
        $sOptions .= 'json: true,';
        $sOptions .= 'limit: '.$this->maxresults.',';
        // $sOptions .= 'varname: "input",';
        $sOptions .= 'shownoresults: '.($this->shownoresults?'true':'false').',';
        $sOptions .= 'maxresults: '.$this->maxresults.',';
        $sOptions .= 'chache: true,';

        $setValue = ($this->savelabel == '1')? 'obj.value': 'obj.id';

        $sOptions .= 'callback: function(obj){'.$sCallBack.'; getField("'. $this->name. '").value = obj.id; return false;}';

        $str .= '<script type="text/javascript">';
        $str .= 'var as_json = new bsn.AutoSuggest(\'form[' . $this->name . '_label]\', {'.$sOptions.'});';
        $str .= '</script>';

        return $str;
      }
    } else {
      return $this->htmlentities ( $formVariableValue, ENT_COMPAT, 'utf-8' );
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @param string owner
   * @return string
   */
  function renderGrid($values = array(), $owner)
  {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      if ($this->replaceTags == 1) {
        $v = G::replaceDataField ( $v, $owner->values );
      }
      if ($this->mode === 'edit') {
        if ($this->readOnly)
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
        else
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
      } elseif ($this->mode === 'view') {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      } else {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      }
      $r ++;
    }
    return $result;

  }

  /**
   * render in a table
   * @param <any> $values
   * @param <String> $owner
   * @return <String> $result
   */
  function renderTable($values = '', $owner) {
    $result = $this->htmlentities ( $values, ENT_COMPAT, 'utf-8' );
    return $result;
  }

}

/**
 * prepare the field for printing
 * @package gulliver.system
 */
class XmlForm_Field_Print extends XmlForm_Field_SimpleText //by neyek
{
  //Instead of var --> link
  var $link         = '';
  var $value        = '';
  var $target       = '';
  var $colClassName = 'RowLink';

  //properties
  var $width;
  var $height;
  var $top;
  var $left;
  var $resizable;

  /**
   * Function render
   * @param string value
   * @return string
   */
  //750, 450, 10, 32, 1
  function render($value = NULL, $owner = NULL) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $link = G::replaceDataField ( $this->link, $owner->values );
    $target = G::replaceDataField ( $this->target, $owner->values );
    $value = G::replaceDataField ( $this->value, $owner->values );
    $label = G::replaceDataField ( $this->label, $owner->values );


    $html = '<a href="#" onclick="popUp(\'' . $this->htmlentities ( $link, ENT_QUOTES, 'utf-8' ) . '\', '.$this->width.', '.$this->height.', '.$this->left.', '.$this->top.', '.$this->resizable.'); return false;" >
            <image title="'.$this->htmlentities($label, ENT_QUOTES, 'utf-8').'" src="/images/printer.png" width="15" height="15" border="0"/>
       </a>';
    return $html;
  }

}

/*DEPRECATED*/
/**
 * caption field for dynaforms
 * @package gulliver.system
 */
class XmlForm_Field_Caption extends XmlForm_Field {

  var $defaultValue    = '';
  var $required        = false;
  var $dependentFields = '';
  var $readonly        = false;
  var $option          = array ();
  var $sqlConnection   = 0;
  var $sql             = '';
  var $sqlOption       = array ();
  var $saveLabel       = 0;
  //var $hint;

  /**
   * @param $value
   * @param $owner
   * @return true
   */
  function validateValue($value, &$owner) {
    /*$this->executeSQL( $owner );
    return isset($value) && ( array_key_exists( $value , $this->options ) );*/
    return true;
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   * modified
   */
  function render($value = NULL, $owner = NULL, $rowId = '', $onlyValue = false, $row = -1, $therow = -1) {

    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL ) {
      $value = $this->getPMTableValue($owner);
    }
    if ($therow == - 1) {//print_r($this->executeSQL ( $owner, $row ));print"<hr>";
      $this->executeSQL ( $owner, $row );
    } else {
      if ($row == $therow) {
        $this->executeSQL ( $owner, $row );
      }
    }
    $html = '';

    if (! $onlyValue) {
      foreach ( $this->option as $optionName => $option ) {
        if($optionName == $value)
         $value=$option;
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        if($optionName == $value)
          $value=$option;

      }

    } else {
      foreach ( $this->option as $optionName => $option ) {
        if ($optionName == $value) {
          $$value = $option;
        }
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        if ($optionName == $value) {
          $value = $option;
        }
      }
    }
    $pID= "form[$this->name]";
    $htm  = $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    $htm .= '<input type="hidden" id="'.$pID.'" name="'.$pID.'" value="'.$value.'">';
  return $htm;
 }
}
/**
 * Class XmlForm_Field_Password
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Password extends XmlForm_Field {
  var $size         = 15;
  var $maxLength    = 15;
  var $required     = false;
  var $readOnly     = false;
  var $autocomplete = "on";
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL) {
    if($this->autocomplete==='1'){
      $this->autocomplete = "on";
    }
    else{
      if($this->autocomplete==='0') {
        $this->autocomplete ="off";}
    }

    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" autocomplete="'.$this->autocomplete.'" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly"/>';
      else{
        $html='<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" autocomplete="'.$this->autocomplete.'" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\'/>';
        $html .= $this->renderHint();
        return $html;
      }
    } elseif ($this->mode === 'view') {
        $html=  '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="hidden" autocomplete="'.$this->autocomplete.'" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly"/>';
        $html.= $this->htmlentities ( str_repeat ( '*', 10 ), ENT_COMPAT, 'utf-8' );
        return $html; 
    } else {
       return $this->htmlentities ( str_repeat ( '*', 10 ), ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Textarea
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Textarea extends XmlForm_Field {
  var $rows     = 12;
  var $cols     = 40;
  var $required = false;
  var $readOnly = false;
  var $wrap     = 'OFF';
  var $className;
  var $renderMode = '';
  

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner) {
    
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );
      if (isset ( $this->sqlOption ))
        $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $value = $firstElement;
    }

    $className = isset($this->className) ?  $this->className : 'module_app_input___gray';
    
    if ($this->renderMode == '') $this->renderMode = $this->mode;
    
    $html = '';
    $scrollStyle = $this->style . "overflow:scroll;overflow-y:scroll;overflow-x:hidden;overflow:-moz-scrollbars-vertical;";
    if ($this->renderMode == 'edit'){ //EDIT MODE
      $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1')? 'readOnly="readOnly"':'';
      $html .= '<textarea '.$readOnlyText.' ';
      $html .= 'id="form['.$this->name.']" ';
      $html .= 'name="form['.$this->name.']" ';
      $html .= 'wrap="soft" cols="'.$this->cols.'" rows="'.$this->rows.'" ';
      $html .= 'style="'.$scrollStyle.'" wrap="'.$this->htmlentities($this->wrap, ENT_QUOTES, 'UTF-8').'" ';
      $html .= $this->NSDefaultValue().' ';
      $html .= $this->NSRequiredValue().' ';
      $html .= 'class="'.$className.'" >';
      $html .= $this->htmlentities($value, ENT_COMPAT, 'utf-8');
      $html .= '</textarea>';
    }else{ //VIEW MODE
      $html .= '<textarea readOnly ';
      $html .= 'id="form['.$this->name.']" ';
      $html .= 'name="form['.$this->name.']" ';
      $html .= 'wrap="soft" cols="'.$this->cols.'" rows="'.$this->rows.'" ';
      $html .= 'style="border:0px;backgroud-color:inherit;'.$scrollStyle.'" wrap="'.$this->htmlentities($this->wrap, ENT_QUOTES, 'UTF-8').'" ';
      $html .= 'class="FormTextArea" >';
      $html .= $this->htmlentities($value, ENT_COMPAT, 'utf-8');
      $html .= '</textarea>';
    }
 

    $html .= $this->renderHint();
    return $html;
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function renderGrid($values = NULL, $owner) {
    $this->gridFieldType = 'textarea';
    
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;

    $result = array ();
    $r = 1;
    
    foreach ( $values as $v ) {
      $scrollStyle = $this->style . "overflow:scroll;overflow-y:scroll;overflow-x:hidden;overflow:-moz-scrollbars-vertical;";
      $html = '';
      if ($this->renderMode == 'edit'){ //EDIT MODE
        $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1')? 'readOnly="readOnly"':'';
        $html .= '<textarea '.$readOnlyText.' class="module_app_input___gray" ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'wrap="soft" cols="'.$this->cols.'" rows="'.$this->rows.'" ';
        $html .= 'style="'.$scrollStyle.'" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= $this->NSRequiredValue().' ';
        $html .= $this->NSGridType().' ';
        $html .= $this->NSGridLabel().' ';
        $html .= '>';
        $html .= $this->htmlentities($v, ENT_COMPAT, 'utf-8');
        $html .= '</textarea>';
      }else{  //VIEW MODE
        $html .= '<textarea readOnly ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'wrap="soft" cols="'.$this->cols.'" rows="'.$this->rows.'" ';
        $html .= 'style="'.$scrollStyle.'" wrap="'.$this->htmlentities($this->wrap, ENT_QUOTES, 'UTF-8').'" ';
        $html .= 'class="FormTextArea" >';
        $html .= $this->htmlentities($v, ENT_COMPAT, 'utf-8');
        $html .= '</textarea>';
      }
      $result[] = $html;
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Currency
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Currency extends XmlForm_Field_SimpleText {
  var $group     = 0;
  var $size      = 15;
  var $required  = false;
  var $linkField = '';
  var $readOnly  = false;
  var $maxLength = 15;

  var $mask      = '_###,###,###,###;###,###,###,###.## $';
  var $currency  = '$';
  //Atributes only for grids
  var $formula   = '';
  var $function  = '';
  var $gridFieldType = 'currency';
  var $comma_separator = '.';

  /**
   * render the field in a dynaform
   * @param <String> $value
   * @param <String> $owner
   * @return <String>
   */
  function render( $value = NULL, $owner = NULL) {
    
    if ($this->renderMode == '') $this->renderMode = $this->mode;
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    
    $html = '';
    $currency = preg_replace( '/([#,.])/', '',$this->mask);
    if (! $value) $value= $currency;
    
    if ($this->renderMode == 'edit'){ //EDIT MODE
       $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
       $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
       $html .= 'id="form[' . $this->name . ']" ';
       $html .= 'name="form[' . $this->name . ']" ';
       $html .= 'type="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" ';
       $html .= 'value="'.$this->htmlentities($value, ENT_QUOTES, 'utf-8').'" ';
       $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
       $html .= 'onkeypress="'.$this->htmlentities($onkeypress, ENT_COMPAT, 'utf-8').'" ';
       $html .= $this->NSDefaultValue().' ';
       $html .= $this->NSRequiredValue().' ';
       $html .= $this->NSGridType().' ';
       $html .= 'pm:decimal_separator="'.$this->comma_separator.'" ';
       $html .= '/>';
    }else{ //VIEW MODE
       $html .= $this->htmlentities($value, ENT_COMPAT, 'utf-8');
       $html .= '<input ';
       $html .= 'id="form[' . $this->name . ']" ';
       $html .= 'name="form[' . $this->name . ']" ';
       $html .= 'type="hidden" value="'.$this->htmlentities($value, ENT_COMPAT, 'utf-8').'" />';
    }
    if (($this->readOnly == 1) && ($this->renderMode == 'edit')) {
      $html = str_replace("class=\"module_app_input___gray\"", "class=\"module_app_input___gray_readOnly\"", $html);
    }
    $html .= $this->renderHint();
    
    return $html;

  }
  
  /**
   * Function renderGrid
   * @author alvaro campos sanchez  <alvaro@colosa.com>
   * @access public
   * @param string values
   * @param string owner
   * @return string
   */
  function renderGrid($values = array(), $owner)
  {
    $result = array ();
    $r = 1;
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;

    foreach ( $values as $v ) {
      $html = '';
      $currency = preg_replace( '/([#,.])/', '',$this->mask);
      if (! $v) $v= $currency;
      if ($this->renderMode === 'edit'){ //EDIT MODE
        $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
        $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'type="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" ';
        $html .= 'value="'.$this->htmlentities($v, ENT_QUOTES, 'utf-8').'" ';
        $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= $this->NSRequiredValue().' ';
        $html .= $this->NSGridType().' ';
        $html .= $this->NSGridLabel().' ';
        $html .= '/>';
      }else{ //VIEW MODE
        $html .= $this->htmlentities($v, ENT_QUOTES, 'utf-8');
        $html .= '<input ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'type="hidden" value="'.$this->htmlentities($v, ENT_QUOTES, 'utf-8').'" />';
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}

/*DEPRECATED*/
/**
 * @package gulliver.system
 */
class XmlForm_Field_CaptionCurrency extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL) {
    return '$ ' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_Percentage
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Percentage extends XmlForm_Field_SimpleText {
  var $size      = 15;
  var $required  = false;
  var $linkField = '';
  var $readOnly  = false;
  var $maxLength = 15;
  var $mask = '###.## %';
  //Atributes only for grids
  var $formula   = '';
  var $function  = '';
  var $gridFieldType = 'percentage';
  var $comma_separator = '.';

  function render( $value = NULL, $owner = NULL) {
    
    if ($this->renderMode == '') $this->renderMode = $this->mode;
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    
    $html = '';
    
    if ($this->renderMode == 'edit'){ //EDIT MODE
       $readOnlyText = ($this->readOnly == 1 || $this->readOnly == '1') ? 'readOnly="readOnly"' : '';
       $html .= '<input '.$readOnlyText.' class="module_app_input___gray" ';
       $html .= 'id="form[' . $this->name . ']" ';
       $html .= 'name="form[' . $this->name . ']" ';
       $html .= 'type="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" ';
       $html .= 'value="'.$this->htmlentities($value, ENT_QUOTES, 'utf-8').'" ';
       $html .= 'style="'.$this->htmlentities($this->style, ENT_COMPAT, 'utf-8').'" ';
       $html .= 'onkeypress="'.$this->htmlentities($onkeypress, ENT_COMPAT, 'utf-8').'" ';
       $html .= $this->NSDefaultValue().' ';
       $html .= $this->NSRequiredValue().' ';
       $html .= 'pm:decimal_separator="' + $this->comma_separator + '" ';
       $html .= '/>';
    }else{ //VIEW MODE
       $html .= $this->htmlentities($value, ENT_COMPAT, 'utf-8');
       $html .= '<input ';
       $html .= 'id="form[' . $this->name . ']" ';
       $html .= 'name="form[' . $this->name . ']" ';
       $html .= 'type="hidden" value="'.$this->htmlentities($value, ENT_COMPAT, 'utf-8').'" />';
    }

    if (($this->readOnly == 1) && ($this->renderMode == 'edit')) {
      $html = str_replace("class=\"module_app_input___gray\"", "class=\"module_app_input___gray_readOnly\"", $html);
    }
    $html .= $this->renderHint();
    return $html;
    
//    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
//    if ($this->mode === 'edit') {
//      if ($this->readOnly)
//        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
//      else {
//
//        $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
//
//        if($this->hint){
//           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
//                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
//                </a>';
//        }
//
//        return $html;
//      }
//    } elseif ($this->mode === 'view') {
//      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
//    } else {
//      return $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' );
//    }

  }

}

/*DEPRECATED*/
/**
 * @package gulliver.system
 */
class XmlForm_Field_CaptionPercentage extends XmlForm_Field {
  function render($value = NULL) {
    return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_Date
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Date2 extends XmlForm_Field_SimpleText {
  //Instead of size --> startDate
  var $startDate       = '';
  //Instead of maxLength --> endDate
  var $endDate         = '';
  //for dinamically dates,   beforeDate << currentDate << afterDate
  // beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  // afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  // startDate and endDate have priority over beforeDate and AfterDate.
  var $afterDate       = '';
  var $beforeDate      = '';
  var $defaultValue    = NULL;
  var $format          = 'Y-m-d';
  var $required        = false;
  var $readOnly        = false;
  var $mask            = 'yyyy-mm-dd';
  var $dependentFields = '';

  /**
   * Verify the date format
   * @param $date
   * @return Boolean true/false
   */
  function verifyDateFormat($date) {
    $aux = explode ( '-', $date );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
    if ($aux [0] < 1900 || $aux [0] > 2100)
      return false;
    return true;
  }

  /**
   * checks if a date has he correct format
   * @param  $date
   * @return <Boolean>
   */
  function isvalidBeforeFormat($date) {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  /**
   * Calculate the date before the format
   * @param <type> $date
   * @param <type> $sign
   * @return <date> $res date based on the data insert
   */
  function calculateBeforeFormat($date, $sign) {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    switch ($part2) {
      case 'd' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ), date ( 'Y' ) ) );
        break;
      case 'y' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1 ) );
        break;

    }
    return $res;
  }

  /**
   * render the field in a dynaform
   * @param  $value
   * @param  $owner
   * @return <String>
   */
  function render($value = NULL, $owner = NULL) {

    $value      = G::replaceDataField ( $value, $owner->values );
    $startDate  = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate    = G::replaceDataField ( $this->endDate, $owner->values );
    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate  = G::replaceDataField ( $this->afterDate, $owner->values );
    //for backward compatibility size and maxlength
    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }
    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, - 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }

    if ($startDate == '') {
      $startDate = date ( 'Y-m-d' ); // the default is the current date
    }

    //for backward compatibility maxlength
    //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
    //for backward compatibility size and maxlength
    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
         if($endDate){
        $sign='1';
        $date=$afterDate;
        $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );
        $part2 = substr ( $date, strlen ( $date ) - 1 );
        switch ($part2) {
          case 'd' :
            $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
          break;
          case 'm' :
            $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ) - 1, date ( 'Y' ) ) );
          break;
          case 'y' :
            $res = (intVal(date ( 'Y' )) + $part1) . '-' . date ( 'm' ) . '-' . date ( 'd' );
          break;
         }

        $endDate=$res;

        }
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }
    if ($endDate == '') {
      //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
    }
    if ($value == '') {
      $value = date ( 'Y-m-d' );
    }
    $html  = "<input type='hidden' id='form[" . $this->name . "]' name='form[" . $this->name . "]' value='" . $value . "'>";
    $html .= "<span id='span[" . $owner->id . "][" . $this->name . "]' name='span[" . $owner->id . "][" . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $value . " </span> ";
    if ($this->mode == 'edit')
      $html .= "<a href='#' onclick=\"showDatePicker(event,'" . $owner->id . "', '" . $this->name . "', '" . $value . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
    return $html;
  }

  /**
   * render the field in a grid
   * @param  $values
   * @param  $owner
   * @param  $onlyValue
   * @return <String>
   */
  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false) {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $v          = G::replaceDataField ( $v, $owner->values );
      $startDate  = G::replaceDataField ( $this->startDate, $owner->values );
      $endDate    = G::replaceDataField ( $this->endDate, $owner->values );
      $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
      $afterDate  = G::replaceDataField ( $this->afterDate, $owner->values );
      //for backward compatibility size and maxlength
      if ($startDate != '') {
        if (! $this->verifyDateFormat ( $startDate ))
          $startDate = '';
      }
      if ($startDate == '' && isset ( $beforeDate ) && $beforeDate != '') {
        if ($this->isvalidBeforeFormat ( $beforeDate ))
          $startDate = $this->calculateBeforeFormat ( $beforeDate, - 1 );
      }

      if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
        $startDate = $this->size . '-01-01';
      }

      if ($startDate == '') {
        $startDate = date ( 'Y-m-d' ); // the default is the current date
      }

      //for backward compatibility maxlength
      //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
      //for backward compatibility size and maxlength
      if ($endDate != '') {
        if (! $this->verifyDateFormat ( $endDate ))
          $endDate = '';
      }

      if ($endDate == '' && isset ( $afterDate ) && $afterDate != '') {
        if ($this->isvalidBeforeFormat ( $afterDate ))
          $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
      }

      if ($endDate == '' && isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
        $endDate = $this->maxlength . '-01-01';
      }
      if ($endDate == '') {
        //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
        $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
      }
      if ($v == '') {
        $v = date ( 'Y-m-d' );
      }
      if (! $onlyValue) {
        $html = "<input type='hidden' id='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' name='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' value='" . $v . "'>";
        if (isset ( $owner->owner->id )) {
          $html .= "<span id='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
        } else {
          $html .= "<span id='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
        }
        if ($this->mode == 'edit') {
          $html .= "<a href='#' onclick=\"showDatePicker(event,'" . (isset ( $owner->owner ) ? $owner->owner->id : $owner->id) . "', '" . $owner->name . '][' . $r . '][' . $this->name . "', '" . $v . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
        }
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}

/*DEPRECATED*/
/**
 * @package gulliver.system
 */
class XmlForm_Field_DateView extends XmlForm_Field
{
  function render($value = NULL)
  {
    return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_YesNo
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_YesNo extends XmlForm_Field
{
  var $required = false;
  var $readonly = false;
  var $renderMode = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    if ($value == '') $value = '0';    
    if ($this->renderMode == '') $this->renderMode = $this->mode;
    $html = '';
    if ($this->renderMode == 'edit'){ //EDIT MODE
      $readOnlyText = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
      $html .= '<select '.$readOnlyText.' class="module_app_input___gray" ';
      $html .= 'id="form['.$this->name.']" ';
      $html .= 'name="form['.$this->name.']" ';
      $html .= $this->NSDefaultValue().' ';
      $html .= $this->NSRequiredValue().' ';
      $html .= '>';
      $html .= '<option value="0"'.(($value === '0')? ' selected':'').'>'.G::LoadTranslation('ID_NO_VALUE').'</option>';
      $html .= '<option value="1"'.(($value === '1')? ' selected':'').'>'.G::LoadTranslation('ID_YES_VALUE').'</option>';
      $html .= '</select>';
      if ($readOnlyText != ''){
        $html .= '<input ';
        $html .= 'id="form['.$this->name.']" ';
        $html .= 'name="form['.$this->name.']" ';
        $html .= 'type="hidden" value="'.(($value==='0')? '0':'1').'" />';
      }
    }else{ //VIEW MODE
    
      $html .= '<span id="form['. $this->name . ']">';
      $html .= ($value==='0') ? G::LoadTranslation('ID_NO_VALUE') : G::LoadTranslation('ID_YES_VALUE');
      $html .= '<input ';
      $html .= 'id="form['.$this->name.']" ';
      $html .= 'name="form['.$this->name.']" ';
      $html .= 'type="hidden" value="'.(($value==='0')? '0':'1').'" />';
    }

    $html .= $this->renderHint();
    return $html;
  }

  /**
   * render the field in a grid
   * @param  $values
   * @param  $owner
   * @return <array>
   */
  function renderGrid($values = array(), $owner) {
    $this->gridFieldType = 'yesno';
    $result = array ();
    $r      = 1;
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;
    
    foreach ( $values as $v ) {
      $html = '';
      if ($v == '') $v = '0';
      if ($this->renderMode == 'edit'){ //EDIT MODE
        $readOnlyText = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
        $html .= '<select '.$readOnlyText.' class="module_app_input___gray" ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= $this->NSDefaultValue().' ';
        $html .= $this->NSRequiredValue().' ';
        $html .= $this->NSGridLabel().' ';
        $html .= $this->NSGridType().' ';
        $html .= '>';
        $html .= '<option value="0"'.(($v === '0')? ' selected="selected"':'').'>'.G::LoadTranslation('ID_NO_VALUE').'</option>';
        $html .= '<option value="1"'.(($v === '1')? ' selected="selected"':'').'>'.G::LoadTranslation('ID_YES_VALUE').'</option>';
        $html .= '</select>';
        if ($readOnlyText != ''){
          $html .= '<input ';
          $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
          $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
          $html .= 'type="hidden" value="'.(($v==='0')? '0':'1').'" />';
        }
      }else{ //VIEW MODE
        $html .= ($v==='0') ? G::LoadTranslation('ID_NO_VALUE') : G::LoadTranslation('ID_YES_VALUE');
        $html .= '<input ';
        $html .= 'id="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= 'name="form['.$owner->name.']['.$r.']['.$this->name.']" ';
        $html .= $this->NSGridType().' ';
        $html .= 'type="hidden" value="'.(($v==='0')? '0':'1').'" />';
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Link
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Link extends XmlForm_Field {
  //Instead of var --> link
  var $link         = '';
  var $value        = '';
  var $target       = '';
  var $style       = '';
  var $colClassName = 'RowLink';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $link    = G::replaceDataField ( $this->link, $owner->values );
    $target  = G::replaceDataField ( $this->target, $owner->values );
    $value   = G::replaceDataField ( $this->value, $owner->values );
    $label   = G::replaceDataField ( $this->label, $owner->values );
    $html    =  '<a class="tableOption" href=\'' . $this->htmlentities ( $link, ENT_QUOTES, 'utf-8' ) . '\'';
    $html   .= 'id="form[' . $this->name . ']" name="form[' . $this->name . ']" style="' . htmlentities ( $this->style, ENT_QUOTES, 'utf-8' ) .'" ';
    $html   .= (($this->onclick) ? ' onclick="' . htmlentities ( $onclick, ENT_QUOTES, 'utf-8' ) . '"' : '') ;
    $html   .= (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>';
    $html   .= $this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' ) . '</a>';
    return $html;
  }

  /**
   * render the field in a grid
   * @param  $values
   * @param  $owner
   * @return <array>
   */
  function renderGrid($values = array(), $owner = NULL) {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $_aData_   = (isset($owner->values[$owner->name][$r]) ? $owner->values[$owner->name][$r] : array());
      $onclick   = G::replaceDataField ( $this->onclick, $_aData_ );
      $link      = G::replaceDataField ( $this->link, $_aData_ );
      $target    = G::replaceDataField ( $this->target, $_aData_ );
      $value     = G::replaceDataField ( $this->value, $_aData_ );
      $label     = G::replaceDataField ( $this->label, $_aData_ );
      $html      = '<a class="tableOption" href=\'' . $this->htmlentities ( $link, ENT_QUOTES, 'utf-8' ) . '\'';
      $html     .= 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"';
      $html     .= 'name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"';
      $html     .= (($this->onclick) ? ' onclick="' . htmlentities ( $onclick, ENT_QUOTES, 'utf-8' ) . '"' : '');
      $html     .= (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') ;
      if ($this->mode == 'view')
        $html     .= 'style="color: #006699; text-decoration: none;font-weight: normal;"';      
      $html     .=  '>'.$this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' ) . '</a>';
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  /**
   * render the field in a table
   * @param  $values
   * @param  $owner
   * @return <String>
   */
  function renderTable($value = NULL, $owner = NULL) {
    $onclick = $this->htmlentities ( G::replaceDataField ( $this->onclick, $owner->values ), ENT_QUOTES, 'utf-8' );
    $link    = $this->htmlentities ( G::replaceDataField ( $this->link, $owner->values ), ENT_QUOTES, 'utf-8' );
    $target  = G::replaceDataField ( $this->target, $owner->values );
    $value   = G::replaceDataField ( $this->value, $owner->values );
    $label   = G::replaceDataField ( $this->label, $owner->values );
    $aLabel  = $this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' );
    if(isset($aLabel) && strlen($aLabel)>0)
      return '<a class="tableOption" href=\'' . $link . '\'' . (($this->onclick) ? ' onclick="' . $onclick . '"' : '') . (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>' . $aLabel . '</a>';
    else
      return '';
  }
}

/**
 * Class XmlForm_Field_File
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_File extends XmlForm_Field {
  var $required = false;
  var $input    = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL) {
    $mode = ($this->mode == 'view') ? ' disabled="disabled"' : '';
    if($this->mode == 'view'){
      $displayStyle = 'display:none;';
      $html = $value.'<input class="module_app_input___gray_file" ' . $mode .'style='.$displayStyle .' id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'file\' value=\'' . $value . '\' />';   
    }
    else{ 
      $html = '<input class="module_app_input___gray_file" ' . $mode . 'id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'file\' value=\'' . $value . '\'/>';
    }
    
    if( isset($this->input) && $this->input != '') {
      require_once 'classes/model/InputDocument.php';
      $oiDoc = new InputDocument;
      
      try {
        $aDoc  = $oiDoc->load($this->input);
        $aDoc['INP_DOC_TITLE'] = isset($aDoc['INP_DOC_TITLE'])? $aDoc['INP_DOC_TITLE']: '';
        $html .= '<label><img src="/images/inputdocument.gif" width="22px" width="22px"/><font size="1">('.trim($aDoc['INP_DOC_TITLE']).')</font></label>';
      } 
      catch (Exception $e) {
        // then the input document doesn't exits, id referencial broken
        $html .= '&nbsp;<font color="red"><img src="/images/alert_icon.gif" width="16px" width="16px"/><font size="1">('.G::loadTranslation('ID_INPUT_DOC_DOESNT_EXIST').')</font></font>'; 
      }
    }	 
    $html .= $this->renderHint();
    return $html;
  }
}

/**
 * Class XmlForm_Field_Dropdownpt
 * hook, dropdown field for Propel table
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Dropdownpt extends XmlForm_Field {
  var $value;

  function render($value = NULL, $owner = NULL) {
      $this->value = $value;

    $id    = $this->value->id;
    $value = isset($this->value->value)? $this->value->value: '';
    $items = $this->value->items;

      $res = '<select id="form['.$id.']" name="form['.$this->name.']" class="module_app_input___gray"><option value="0"></option>';
      foreach($items as $k=>$v) {
          $res .= '<option value="'.$k.'">'.$v.'</option>';
      }
      $res .= "</select>";
      return $res;
  }

  /* Used in Form::validatePost
   */
  function maskValue($value, &$owner) {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

/**
 * Class XmlForm_Field_Checkboxpt
 * checkbox field for Propel table
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Checkboxpt extends XmlForm_Field {
  var $required     = false;
  var $value        = 'on';
  var $falseValue   = 'off';
  var $labelOnRight = true;

  /**
   * Render the field in a dynaform
   * @param  $value
   * @param  $owner
   * @return <>
   */
  function render($value = NULL, $owner = NULL) {
   if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    $checked = (isset ( $value ) && ($value == $this->value)) ? 'checked' : '';
    $res = "<input id='form[" . $this->name . "][{$this->value}]' value='{$this->value}' name='form[" . $this->name . "][{$this->value}]' type='checkbox' />";
    return $res;
  }


  /**
   * Render the field in a grid
   * @param  $value
   * @param  $owner
   * @return <Array> result
   */
  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $checked   = (($v == $this->value) ? 'checked="checked"' : '');
      $disabled  = (($this->value == 'view') ? 'disabled="disabled"' : '');
      $html      = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled />";
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  /**
   * Used in Form::validatePost
   * @param $value
   * @param &$owner
   * @return either the value or falseValue attributes
   */
  function maskValue($value, &$owner) {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

/**
 * Class XmlForm_Field_Checkbox
 * @author Erik Amaru <erik@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Checkbox extends XmlForm_Field
{
  var $required     = false;
  var $value        = 'on';
  var $falseValue   = 'off';
  var $labelOnRight = true;
  var $readOnly     = false;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
  if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
 
    $disabled = '';
    if($this->readOnly==='readonly' or $this->readOnly==='1' ){
     $readOnly = 'readonly="readonly" onclick="javascript: return false;"';//$disabled = "disabled";
    }
    else{
      $readOnly = '';
   } 
 
    $checked = (isset ( $value ) && ($value == $this->value)) ? 'checked' : '';
 
    if ($this->mode === 'edit') {
      //$readOnly = isset ( $this->readOnly ) && $this->readOnly ? 'disabled' : '';
      if ($this->labelOnRight) {
        $res = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly $disabled><span class='FormCheck'>" . $this->label . '</span></input>';
      } else {
        $res = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly $disabled/>";
      }
      $res .= $this->renderHint();

      //      $res = "<input id='form[" . $this->name . "]' value='" . $this->name . "' name='form[" .$this->name . "]' type='checkbox' $checked $readOnly >" . $this->label ;
      return $res;
    } elseif ($this->mode === 'view') {
    	$checked = (isset ( $value ) && ($value == $this->value)) ? 'checked' : '';
      if ($this->labelOnRight) {
        $html = '';
        $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly disabled >
                 <span class='FormCheck'>" . $this->label . '</span></input>';
      } else {
        $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly disabled/>";
      }
      $html .=  "<input id='form[" . $this->name . "]' value='{$value}' name='form[" . $this->name . "]' type='hidden' />";
//      if($this->hint){
//           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
//                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
//                </a>';
//      }
      return $html;
    }
  }

  /**
   * Render the field in a grid
   * @param  $value
   * @param  $owner
   * @return <Array> result
   */
  function renderGrid($values = array(), $owner)
  {
    $this->gridFieldType = 'checkbox';
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $checked = (($v == $this->value) ? 'checked="checked"' : '');
      if($this->readOnly==='readonly' or $this->readOnly==='1' ) {
        $disabled = "disabled";
      }
      else {
        $disabled = '';
      }
      if ($this->mode==='edit') {        
        $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' falseValue= ".$this->falseValue."  name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled readonly = '{$this->readOnly}' ".$this->NSDefaultValue()." ".$this->NSGridType()."/>";
        $result [] = $html;
        $r ++;      
      }
      else {
        //$disabled = (($this->value == 'view') ? 'disabled="disabled"' : '');
        $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' falseValue= ".$this->falseValue." name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked disabled readonly = '{$this->readOnly}' ".$this->NSDefaultValue()." ".$this->NSGridType()."/>";
        $result [] = $html;
        $r ++;      
      }      
    }
    return $result;
  }
  /**
   * Used in Form::validatePost
   * @param  $value
   * @param  $owner
   * @return either the value or falseValue
   */
  function maskValue($value, &$owner)
  {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

/*DEPRECATED*/
/**
 * @package gulliver.system
 */
class XmlForm_Field_Checkbox2 extends XmlForm_Field {
  var $required = false;
  function render($value = NULL) {
    return '<input class="FormCheck" name="' . $this->name . '" type ="checkbox" disabled>' . $this->label . '</input>';
  }
}
/**
 * Class XmlForm_Field_Button
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Button extends XmlForm_Field
{
  var $onclick = '';
  var $align   = 'center';
  var $style;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $label   = G::replaceDataField ( $this->label, $owner->values );
    if ($this->mode === 'edit') {
      $re = "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
      return $re;
    } elseif ($this->mode === 'view') {
      return "<input style=\"{$this->style};display:none\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Reset
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Reset extends XmlForm_Field
{
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner)
  {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $mode    = ($this->mode == 'view') ? ' disabled="disabled"' : '';
    //return '<input name="'.$this->name.'" type ="reset" value="'.$this->label.'"/>';
//    return "<input style=\"{$this->style}\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    if ($this->mode === 'edit') {
      return "<input style=\"{$this->style}\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } elseif ($this->mode === 'view') {
      return "<input style=\"{$this->style};display:none\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
    
  }
}
/**
 * Class XmlForm_Field_Submit
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Submit extends XmlForm_Field {
  var $onclick = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    if ($this->mode === 'edit') {
      //      if ($this->readOnly)
      //        return '<input id="form['.$this->name.']" name="form['.$this->name.']" type=\'submit\' value=\''. $this->label .'\' disabled/>';
      return "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } elseif ($this->mode === 'view') {
      // return "<input style=\"{$this->style};display:none\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    //$sLinkNextStep = 'window.open("' . $owner->fields['__DYNAFORM_OPTIONS']->xmlMenu->values['NEXT_STEP'] . '", "_self");';
      $html = '';
      if (isset($_SESSION['CURRENT_DYN_UID'])) {
        $sLinkNextStep = 'window.location=("casesSaveDataView?UID='.$_SESSION['CURRENT_DYN_UID'].'");';
        $html  = '<input style="' . $this->style . '" class="module_app_button___gray '. $this->className .'" id="form['. $this->name .']" name="form['. $this->name .']" type="button" value="' .G::LoadTranslation('ID_CONTINUE') . '"  onclick="' . htmlentities ( $sLinkNextStep, ENT_COMPAT, 'utf-8' ) . '" />';
      }
      $html .= '<input ';
      $html .= 'id="form['. $this->name . ']" ';
      $html .= 'name="form[' . $this->name . ']" ';
      $html .= 'type="hidden" value="'. $this->htmlentities ( $this->label, ENT_QUOTES, 'utf-8' ) .'" />';
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Hidden
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Hidden extends XmlForm_Field
{
  var $sqlConnection   = 0;
  var $sql             = '';
  var $sqlOption       = array ();
  var $dependentFields = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );

      if (isset ( $this->sqlOption )) {
        reset ( $this->sqlOption );
        $firstElement = key ( $this->sqlOption );
        if (isset ( $firstElement ))
          $value = $firstElement;
      }
    }
    if ($this->mode === 'edit') {
      return '<input id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'hidden\' value=\'' . $value . '\'/>';
    } elseif ($this->mode === 'view') {
      //a button? who wants a hidden field be showed like a button?? very strange.
      return '<input id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'text\' value=\'' . $value . '\' style="display:none"/>';
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }

  /**
   * Render the field in a grid
   * @param  $value
   * @param  $owner
   * @return <Array> result
   */
  function renderGrid($values = NULL, $owner)
  {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
       $result [] = '<input type="hidden" value="'.$this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ).'" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" />';
       $r ++;
    }

    return $result;
  }

  /**
   * Render the field in a table
   * @param  $value
   * @param  $owner
   * @return <Array> result
   */
  function renderTable($value = '', $owner)
  {
    return '<input id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'hidden\' value=\'' . $value . '\'/>';
  }

}
/**
 * Class XmlForm_Field_Dropdown
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Dropdown extends XmlForm_Field {
  var $defaultValue    = '';
  var $required        = false;
  var $dependentFields = '';
  var $readonly        = false;
  var $option          = array ();
  var $sqlConnection   = 0;
  var $sql             = '';
  var $sqlOption       = array ();
  var $saveLabel       = 0;
  var $modeGridDrop    = '';
  var $renderMode      = '';
  var $selectedValue   = '';
  function validateValue($value, &$owner)
  {
    /*$this->executeSQL( $owner );
    return isset($value) && ( array_key_exists( $value , $this->options ) );*/
    return true;
  }
  
 /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL, $rowId = '', $onlyValue = false, $row = -1, $therow = -1)
  {
    $displayStyle = '';
    
    //Returns value from a PMTable when it is exists. 
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    //Recalculate SQL options if $therow is not defined or the row id equal
    if ($therow == -1) {
      //echo 'Entro:'.$this->dependentFields;
      $this->executeSQL ( $owner, $row );
    } else {
      if ($row == $therow) {
        $this->executeSQL ( $owner, $row );
      }
    }
    
    $html = '';
    $displayLabel = '';
   
    if ($this->renderMode == '') $this->renderMode = $this->mode;
    
    if (!$onlyValue){ //Render Field if not defined onlyValue
      if ($this->renderMode != 'edit') { //EDIT MODE
        $displayStyle = 'display:none;';
      }
      $readOnlyField = ($this->readonly == 1 || $this->readonly == '1') ? 'disabled' : '';
      $html = '<select '.$readOnlyField.' class="module_app_input___gray" ';
      $html .= 'id="form' . $rowId . '[' . $this->name . ']" ';
      $html .= 'name="form' . $rowId . '[' . $this->name . ']" ';
      if ($this->style) $html .= 'style="'. $displayStyle . $this->style.'" ';
      if ($displayStyle != '') $html .= 'style="'. $displayStyle . '" ';
      $html .= $this->NSRequiredValue().' ';
      $html .= $this->NSDefaultValue().' ';
      $html .= $this->NSGridLabel().' ';
      $html .= $this->NSGridType().' ';
      $html .= $this->NSDependentFields(true).' ';
      $html .= '>';
      $findValue = '';
      $firstValue = '';
      $cont=0;
      foreach ($this->option as $optValue => $optName ){
        settype($optValue,'string');
        $html .= '<option value="'.$optValue.'" '.(($optValue === $value)? 'selected="selected"' : '').'>'.$optName.'</option>';
        if ($optValue === $value) {
          $findValue = $optValue;
          $displayLabel = $optName;
        }
        if ($firstValue == '') $firstValue = $optValue;
        $cont++;
      }
      foreach ($this->sqlOption as $optValue => $optName ){
        settype($optValue,'string');
        $html .= '<option value="'.$optValue.'" '.(($optValue === $value)? 'selected="selected"' : '').'>'.$optName.'</option>';
        if ($optValue === $value) {
          $findValue = $optValue;
          $displayLabel = $optName;
        }
        if ($firstValue == '') $firstValue = $optValue;
      }
      $html .= '</select>';
      if ($readOnlyField != ''){
        $html .= '<input type="hidden" ';
        $html .= 'id="form' . $rowId . '[' . $this->name . ']" ';
        $html .= 'name="form' . $rowId . '[' . $this->name . ']" ';
        $html .= 'value="'.(($findValue != '') ? $findValue : $firstValue).'" />';
      }
      $this->selectedValue = ($findValue != '') ? $findValue : ($cont==0)? $firstValue : '';
    
    }else{ //Render Field showing only value;
      foreach ($this->option as $optValue => $optName) {
        if ($optValue == $value) {
          $html = $optName;
        }
      }
      foreach ($this->sqlOption as $optValue => $optName) {
        if ($optValue == $value) {
          $html = $optName;
        }
      }
    }

    if ($this->gridFieldType == '') $html .= $this->renderHint();
    if ($displayStyle != '') $html = $displayLabel . $html;
    return $html;
  }
  
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @return string
   */
  function renderGrid($values = array(), $owner = NULL, $onlyValue = false, $therow = -1)
  {
    $this->gridFieldType = 'dropdown';
    $result = array ();
    $r      = 1;
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;

    foreach ( $values as $v ) {
      $result [] = $this->render ( $v, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue, $r, $therow );
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Listbox
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Listbox extends XmlForm_Field
{
  var $defaultValue  = '';
  var $required      = false;
  var $option        = array ();
  var $sqlConnection = 0;
  var $size          = 4;
  var $width         = '';
  var $sql           = '';
  var $sqlOption     = array ();
  function validateValue($value, $owner)
  {
    $this->executeSQL ( $owner );
    return true; // isset($value) && ( array_key_exists( $value , $this->options ) );
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if (! is_array ( $value ))
      $value = explode ( '|', $value );
    if ($this->mode === 'edit') {
      $itemWidth = '';
      if ($this->width != '') {
        $itemWidth =  'style="width:'.$this->width . '"';
      }
      $html = '<select multiple="multiple" id="form[' . $this->name . ']" name="form[' . $this->name . '][]" size="' . $this->size . '" ' . $itemWidth . ' >';
      foreach ( $this->option as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      $html .= '</select>';

      $html .= $this->renderHint();
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '<select multiple id="form[' . $this->name . ']" name="form[' . $this->name . '][]" size="' . $this->size . '" disabled>';//disabled>';
      foreach ( $this->option as $optionName => $option ) {
      	if((in_array ( $optionName, $value ))==1)
      	  $html .= ' <option  class="module_ListBoxView" value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      	else
      	  $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      } 
      $html .= '</select>';
      foreach ( $this->option as $optionName => $option ) {
      	  $html .= '<input style="color:white;" type="hidden"  id="form[' . $this->name . ']" name="form[' . $this->name . '][]" value="'.((in_array ( $optionName, $value )) ? $optionName : '').'">';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<input type="hidden"  id="form[' . $this->name . ']" name="form[' . $this->name . '][]" value="'.((in_array ( $optionName, $value )) ? $optionName : '').'">';
      }
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }

  /**
   * Render the field in a grid
   * @param  $value
   * @param  $owner
   * @return <Array> result
   */
  function renderGrid($value = NULL, $owner = NULL)
  {
    return $this->render ( $value, $owner );
  }
}
/**
 * Class XmlForm_Field_RadioGroup
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_RadioGroup extends XmlForm_Field {
  var $defaultValue  = '';
  var $required      = false;
  var $option        = array ();
  var $sqlConnection = 0;
  var $sql           = '';
  var $sqlOption     = array ();
  var $viewAlign     = 'vertical';
  var $linkType;
  
  /**
   * validate the execution of a query
   * @param  $value
   * @param  $owner
   * @return $value
   */
  function validateValue($value, $owner)
  {
    $this->executeSQL ( $owner );
    return isset ( $value ) && (array_key_exists ( $value, $this->options ));
  }

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if ($this->mode === 'edit') {
      $html = '';
      $i    = 0;
      foreach ( $this->options as $optionName => $option ) {
        if( isset($this->linkType) && ($this->linkType == 1 || $this->linkType == "1") ){
            $html .= '<input id="form['.$this->name.']['.$optionName.']" name="form['.$this->name.']" type="radio" value="'.$optionName.'" '.(($optionName==$value) ? ' checked' : '') . '><a href="#" onclick="executeEvent(\'form['.$this->name.']['.$optionName.']\', \'click\'); return false;">' . $option . '</a></input>';
        } else {
            $html .= '<input id="form['.$this->name.']['.$optionName.']" name="form['.$this->name.']" type="radio" value="'.$optionName.'" '.(($optionName==$value) ? ' checked' : '') . '>' . $option . '</input>';
        }
        if(++$i==count($this->options)){
          $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->renderHint();
        }
        
        if($this->viewAlign == 'horizontal')
          $html .='&nbsp;';
        else   
          $html .='<br>';

      }
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input class="module_app_input___gray" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" type=\'radio\' value="' . $optionName . '" ' . (($optionName == $value) ? 'checked' : '') . ' disabled><span class="FormCheck">' . $option . '</span></input><br>';
       if($optionName == $value)
         $html .= '<input type="hidden"  id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" value="' . (($optionName == $value) ? $optionName : '') . '">';
      }

      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}

/*DEPRECATED*/
/**
 * @package gulliver.system
*/
class XmlForm_Field_RadioGroupView extends XmlForm_Field
{
  var $defaultValue  = '';
  var $required      = false;
  var $option        = array ();
  var $sqlConnection = 0;
  var $sql           = '';
  var $sqlOption     = array ();
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    $this->executeSQL ( $owner );
    $html = '';
    foreach ( $this->option as $optionName => $option ) {
      $html .= '<input type=\'radio\'`disabled/><span class="FormCheck">' . $option . '</span><br>';
    }
    return $html;
  }
}

/**
 * Class XmlForm_Field_CheckGroup
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_CheckGroup extends XmlForm_Field
{
  var $required      = false;
  var $option        = array ();
  var $sqlConnection = 0;
  var $sql           = '';
  var $sqlOption     = array ();
  /*function validateValue( $value , $owner )
  {
    $this->executeSQL( $owner );
    return isset($value) && ( array_key_exists( $value , $this->options ) );
  }*/
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if (! is_array ( $value ))
      $value = explode ( '|', $value );
    if ($this->mode === 'edit') {
      $i=0;
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][]" type=\'checkbox\' value="' . $optionName . '"' . (in_array ( $optionName, $value ) ? 'checked' : '') . '><span class="FormCheck">' . $option . '</span></input>';
        if(++$i==count($this->options)){
             $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->renderHint();
        }
        $html .= '<br>';
      }//fin for
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input class="FormCheck" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][]" type=\'checkbox\' value="' . $optionName . '"' . (in_array ( $optionName, $value ) ? 'checked' : '') . ' disabled><span class="FormCheck">' . $option . '</span></input><br>';
        $html .= '<input type="hidden"  id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][]"  value="'.((in_array ( $optionName, $value )) ? $optionName : '').'">';
      }
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }

  }
}

/* TODO: DEPRECATED */
/**
 * @package gulliver.system
*/
class XmlForm_Field_CheckGroupView extends XmlForm_Field
{
  var $option        = array ();
  var $sqlConnection = 0;
  var $sql           = '';
  var $sqlOption = array ();
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL)
  {
    $html = '';
    foreach ( $this->option as $optionName => $option ) {
      $html .= '<input type=\'checkbox\' disabled/><span class="FormCheck">' . $option . '</span><br>';
    }
    return $html;
  }
}
/**
 * Class XmlForm_Field_Grid
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_Grid extends XmlForm_Field
{
  var $xmlGrid   = '';
  var $initRows  = 1;
  var $group     = 0;
  var $addRow    = "1";
  var $deleteRow = "1";
  var $editRow   = "0";
  var $sql       = '';
  //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
  var $fillType  = 0;
  var $fields    = array ();
  var $scriptURL;
  var $id = '';

  /**
   * Function XmlForm_Field_Grid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string xmlnode
   * @param string language
   * @param string home
   * @return string
   */
  function XmlForm_Field_Grid($xmlnode, $language, $home)
  {
    parent::XmlForm_Field ( $xmlnode, $language );
    $this->parseFile ( $home, $language );
  }

  /**
   * Function parseFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string home
   * @param string language
   * @return string
   */
  function parseFile($home, $language)
  {
    if (file_exists ( $home . $this->xmlGrid . '.xml' )) {
      $this->xmlform = new XmlForm ( );
      $this->xmlform->home = $home;
      $this->xmlform->parseFile ( $this->xmlGrid . '.xml', $language, false );
      $this->fields = $this->xmlform->fields;
      $this->scriptURL = $this->xmlform->scriptURL;
      $this->id = $this->xmlform->id;
      $this->modeGrid = $this->xmlform->mode;
      unset ( $this->xmlform );
    }
  }

  /**
   * Render the field in a dynaform
   * @param  $value
   * @param  $owner
   * @return <Template Object>
   */
   
  function render($values, $owner = NULL){
    $arrayKeys  = array_keys ( $this->fields );
    $emptyRow   = array ();
    $fieldsSize = 0;
    foreach ( $arrayKeys as $key ){
      if (isset($this->fields[$key]->defaultValue)){
        $emptyValue = $this->fields[$key]->defaultValue;
/**        if (isset($this->fields[$key]->dependentFields)){
          if ($this->fields[$key]->dependentFields != ''){
            $emptyValue = '';
          }
        }*/
      }else{
      
        $emptyValue = '';
      }
      if(isset($this->fields[$key]->size))
        $size = $this->fields[$key]->size;
      if(!isset($size)) $size = 15;
      $fieldsSize +=  $size;
      $emptyRow [$key] = array ($emptyValue);
    }
    if($fieldsSize>100)
      $owner->width = '100%';
  //  else
  //    $owner->width = $fieldsSize . 'em';
    return $this->renderGrid ( $emptyRow, $owner );
    
    
  }

  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @return string
   */
  function renderGrid($values, $owner = NULL, $therow = -1)
  {
    $this->id = $this->owner->id . $this->name;
    $using_template = "grid";

    if( $this->mode == 'view' || $this->modeGrid === 'view' ){
      $using_template = "grid_view";
    }

    $tpl = new xmlformTemplate ( $this, PATH_CORE . "templates/{$using_template}.html" );
    if (! isset ( $values ) || ! is_array ( $values ) || sizeof ( $values ) == 0) {
      $values = array_keys ( $this->fields );
    }
    if ($therow != -1){
      //Check if values arrary is complete to can flip.
      $xValues = array();
      if (isset($values[$therow]))
        $aRow = $values[$therow];
      else
        $aRow = array();
      for ($c=1; $c <= $therow; $c++){
        if ($c == $therow){
          $xValues[$therow] = $aRow;
        }else{
          foreach ($aRow as $key=>$value){
            $xValues[$c][$key] = '';  
          }
        }
      }
      $values = $xValues;
    }
    $aValuekeys = array_keys ( $values );
    if (count ( $aValuekeys ) > 0 && ( int ) $aValuekeys [0] == 1)
      $values = $this->flipValues ( $values );
    //if ($therow == 1)g::pr($values);
    $this->rows = count ( reset ( $values ) );
    if (isset ( $owner->values )) {
      foreach ( $owner->values as $key => $value ) {
        if (! isset ( $values [$key] )) {
          $values [$key] = array ();
          //for($r=0; $r < $this->rows ; $r++ ) {
          $values [$key] = $value;
          //}
        }
      }
    }
    foreach ( $this->fields as $k => $v ) {
      if (isset ( $values ['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k] )) {
        $this->fields [$k]->aggregate = $values ['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k];
      } else {
        $this->fields [$k]->aggregate = '0';
      }
    }
    
    $this->values      = $values;

    $this->NewLabel    = G::LoadTranslation('ID_NEW');
    $this->DeleteLabel = G::LoadTranslation('ID_DELETE');

    $tpl->template     = $tpl->printTemplate ( $this );
    //In the header
    $oHeadPublisher    = & headPublisher::getSingleton ();
    $oHeadPublisher->addScriptFile ( $this->scriptURL );
    $oHeadPublisher->addScriptCode ( $tpl->printJavaScript ( $this ) );
    return $tpl->printObject ( $this, $therow );
  }

  /**
   * Change the columns for rows and rows to columns
   * @param <array> $values
   * @return <array>
   */
  function flipValues($values) {
    $flipped = array ();
    foreach ( $values as $rowKey => $row ) {
      foreach ( $row as $colKey => $cell ) {
        if (! isset ( $flipped [$colKey] ) || ! is_array ( $flipped [$colKey] ))
          $flipped [$colKey] = array ();
        $flipped [$colKey] [$rowKey] = $cell;
      }
    }
    return $flipped;
  }
}

/**
 * Class XmlForm_Field_JavaScript
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm_Field_JavaScript extends XmlForm_Field
{
  var $code        = '';
  var $replaceTags = true;

  /**
   * Function XmlForm_Field_JavaScript
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string xmlNode
   * @param string lang
   * @param string home
   * @return string
   */
  function XmlForm_Field_JavaScript($xmlNode, $lang = 'en', $home = '')
  {
    //Loads any attribute that were defined in the xmlNode
    //except name and label.
    $myAttributes = get_class_vars ( get_class ( $this ) );
    foreach ( $myAttributes as $k => $v )
      $myAttributes [$k] = strtoupper ( $k );
    foreach ( $xmlNode->attributes as $k => $v ) {
      $key = array_search ( strtoupper ( $k ), $myAttributes );
      if ($key)
        eval ( '$this->' . $key . '=$v;' );
    }
    //Loads the main attributes
    $this->name = $xmlNode->name;
    $this->type = strtolower ( $xmlNode->attributes ['type'] );
    //$data: Includes labels and options.
    $this->code = $xmlNode->value;
  }

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @return string
   */
  function render($value = NULL, $owner = NULL)
  {
    $code = ($this->replaceTags) ? G::replaceDataField ( $this->code, $owner->values ) : $this->code;
    return $code;
  }

  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string value
   * @param string owner
   * @return string
   */
  function renderGrid($value, $owner)
  {
    return array ('' );
  }

  /**
   * A javascript node has no value
   * @param $value
   * @return false
   */
  function validateValue($value)
  {
    return false;
  }
}

/**
 * @author      Erik amaru Ortiz <erik@colosa.com>
 * Comment Working for after and before date attributes
 * @package gulliver.system
 */
class XmlForm_Field_Date extends XmlForm_Field_SimpleText
{
  public $required        = false;
  public $readOnly        = false;

  public $startDate       = '';
  public $endDate         = '';
  /*
  * for dinamically dates,   beforeDate << currentDate << afterDate
  * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  * startDate and endDate have priority over beforeDate and AfterDate
  */
  public $afterDate       = '';
  public $beforeDate      = '';
  public $defaultValue    = NULL;
  public $format          = '%Y-%m-%d';

  public $mask            = '%Y-%m-%d';
  public $dependentFields = '';
  public $editable;
  var $onchange;
  var $renderMode = '';
  var $gridFieldType = '';
  
  /*
   * Verify the format of a date
   * @param <Date> $date
   * @return <Boolean> true/false
   */
  function verifyDateFormat($date)
  {
    $dateTime = explode(" ",$date); //To accept the Hour part
    $aux = explode ( '-', $dateTime[0] );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
    if ($aux [0] < 1900 || $aux [0] > 2100)
      return false;
    return true;
  }

  /**
   * Check if a date had a valid format before
   * @param  <Date> $date
   * @return <Boolean> True/False
   */
  function isvalidBeforeFormat($date)
  {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  /**
   * Calculations in Date
   * @param  <Date> $date
   * @param  $sign
   * @return <Date>
   */
  function calculateBeforeFormat($date, $sign)
  {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    switch ($part2) {
      case 'd' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ), date ( 'Y' ) ) );
        break;
      case 'y' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1 ) );
        break;

    }
    return $res;
  }

  /**
   * render the field in a dynaform
   * @param   $value
   * @param   $owner
   * @return  renderized widget
   */
  function render($value = NULL, $owner = NULL)
  {
  	$this->renderMode = $this->mode;
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
    $id        = "form[$this->name]";
    
    if ($this->renderMode != 'edit' && $value == 'today' ){
      $mask = str_replace("%", "", $this->mask);
      $value = date($mask);
      return $value;
    }
    return $this->__draw_widget ( $id, $value, $owner );
  }

  /**
   * render the field in a grid
   * @param  $values
   * @param  $owner
   * @param  $onlyValue
   * @return Array $result
   */
  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false)
  {
    $this->gridFieldType = 'date';
    $result = array ();
    $r      = 1;
/*    if( ! isset($owner->modeGrid)) $owner->modeGrid = '';
    $this->mode = $this->modeForGrid;*/
    if ($owner->mode != 'view') $this->renderMode = $this->modeForGrid;
    foreach ( $values as $v ) {
      $v = G::replaceDataField ( $v, $owner->values );
      if (! $onlyValue) {
        if($this->mode === 'view' || (isset($owner->modeGrid) && $owner->modeGrid === 'view') ) {
          if ($this->required){
            $isRequired = '1';
          } else {
            $isRequired = '0';
          }
          if($v == 'today') {
            $mask = str_replace("%", "", $this->mask);
            $v = date($mask);
          }
          $html = '<input '.$this->NSRequiredValue().' class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" required="' . $isRequired . '" style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities ( $v, ENT_COMPAT, 'utf-8' );
        } else {
          $id        = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
          $html      = $this->__draw_widget ( $id, $v, $owner );
        }
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  /**
   * Returns the html code to draw the widget
   * @param  $pID
   * @param  $value
   * @param  $owner
   * @return <String>
   */
  function __draw_widget($pID, $value, $owner = ''){
    $startDate  = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate    = G::replaceDataField ( $this->endDate, $owner->values );
    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate  = G::replaceDataField ( $this->afterDate, $owner->values );
    $defaultValue=$this->defaultValue;
    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }

    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }
    
    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }

    if ($endDate == '') {
      // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) );
    }

    //validating the mask, if it is not set,
    if( isset($this->mask) && $this->mask != '' ){
      $mask = $this->mask;
    } else {
      $mask = '%Y-%m-%d'; //set default
    }

    if( strpos($mask, '%') === false ) {
      if( strpos($mask, '-') !== false )
        $separator = '-';
      if( strpos($mask, '/') !== false )
        $separator = '/';
      if( strpos($mask, '.') !== false )
        $separator = '.';
      
      $maskparts = explode($separator, $mask);
      $mask = '';
      foreach($maskparts as $part) {
        if($mask != '')
          $mask .= $separator;
        if($part=='yyyy')
          $part='Y';
        if($part=='dd')
          $part='d';
        if($part=='mm')
          $part='m';
        if($part=='yy') 
          $part='y';
        $mask .= '%'.$part;
      }
    }
    
    $tmp = str_replace("%", "", $mask);
    if ( trim ($value) == '' or $value == NULL ) {
      $value = ''; //date ($tmp);
    } else 
      {      
        switch(strtolower($value)){
          case 'today':       
            $value=masktophp ($mask);//   $value = date($tmp);
            break;        
          default:
            if(!$this->verifyDateFormat($value))
              //$value='';
            break;
        }
      }
 
    //onchange
    if( isset($this->onchange) && $this->onchange != '' )
      $onchange = 'onchange="'.$this->onchange.'"';
    else
      $onchange = '';
    
    if ($this->renderMode == 'edit') {
      $maskleng = strlen($mask);
      $hour   = '%H';$min   = '%M';$sec   = '%S';
      $sizehour = strpos($mask, $hour);
      $sizemin = strpos($mask, $hour);
      $sizesec = strpos($mask, $hour);
      $Time = 'false';
      
      if (($sizehour !== false)&&($sizemin !== false)&&($sizesec !== false)) {
        $sizeend = $maskleng + 2;
        $Time = 'true';
      } else
        {        
          $sizeend = $maskleng + 2;
        }
      if ($this->required) 
        $isRequired = '1';
      else      
        $isRequired = '0';
      if ( $this->editable != "0") {
        $html = '<input pm:required="'. $isRequired .'" id="'.$pID.'" name="'.$pID.'" pm:mask="'.$mask.'"'          
              . 'pm:start="'.$startDate.'" pm:end="'.$endDate.'" pm:time="'.$Time.'" '.$onchange.' class="module_app_input___gray" size="'.$sizeend.'"'
              .  'value="'.$value.'" pm:defaultvalue="'.$defaultValue.'"/>'
              . '<a onclick="removeValue(\''.$pID.'\'); return false;" style="position:relative;left:-17px;top:5px;" > '
              . '  <img src="/images/icons_silk/calendar_x_button.png" />'
              . '</a>'
              . '<a id="'.$pID.'[btn]" style="position:relative;left:-22px;top:0px;" >'
              . '  <img src="/images/pmdateicon.png" border="0" width="12" height="14" />'
              . '</a>'
              . '<script>datePicker4("", \''.$pID.'\', \''.$mask.'\', \''.$startDate.'\', \''.$endDate.'\','.$Time.')</script>';
      } else 
        { 
          $html = '<input pm:required="'. $isRequired .'" id="'.$pID.'" name="'.$pID.'" pm:mask="'.$mask.'" pm:start="'.$startDate.'"'
                . 'pm:end="'.$endDate.'" pm:time="'.$Time.'" '.$onchange.' class="module_app_input___gray" size="'.$sizeend.'"'
                . 'value="'.$value.'"pm:defaultvalue="'.$defaultValue.'" readonly="readonly" />'
                . '<a onclick="removeValue(\''.$pID.'\'); return false;" style="position:relative;left:-17px;top:5px;" >'
                . '  <img src="/images/icons_silk/calendar_x_button.png" />'
                . '</a>'
                . '<a id="'.$pID.'[btn]" style="position:relative;left:-22px;top:0px;" >'
                . '  <img src="/images/pmdateicon.png" border="0" width="12" height="14" />'
                . '</a>'
                . '<script>datePicker4("", \''.$pID.'\', \''.$mask.'\', \''.$startDate.'\', \''.$endDate.'\','.$Time.')</script>';
        }
    } else {           
        $html = "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>"
              . '<input type="hidden" id="'.$pID.'" name="'.$pID.'" pm:mask="'.$mask.'" pm:start="'.$startDate.'"'
              . 'pm:end="'.$endDate.'"  '.$onchange.' class="module_app_input___gray" value="'.$value.'"/>';
          
      } 
    /*** Commented because seems is not working well *
    $idIsoDate  = substr($pID,0,strlen($pID)-1).'_isodate]';
    $amask      = explode('-',str_replace('%','',$mask));
    $axDate     = explode('-',$value);
    $valisoDate = '';

    if ( sizeof($amask) == sizeof($axDate) ) {	
      $aisoDate = array_combine($amask, $axDate);
      if ( isset($aisoDate['Y']) && isset($aisoDate['m']) && isset($aisoDate['d']) )
        $valisoDate = $aisoDate['Y'].'-'.$aisoDate['m'].'-'.$aisoDate['d'];
    }
    
    $html .= '<input type="hidden" id="'.$idIsoDate.'" name="'.$idIsoDate.'" value="'.$valisoDate.'"/>';   
    ***/

    if ($this->gridFieldType == '') $html .= $this->renderHint();
    return $html;
  }
}

/**
 * Calendar Widget with Javascript Routines
 * @author      Erik amaru Ortiz <aortiz@gmail.com, erik@colosa.com>
 * @package gulliver.system
 */
class XmlForm_Field_Date5 extends XmlForm_Field_SimpleText
{
  public $required        = false;
  public $readOnly        = false;

  public $startDate       = '';
  public $endDate         = '';
  /*
  * for dinamically dates,   beforeDate << currentDate << afterDate
  * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  * startDate and endDate have priority over beforeDate and AfterDate
  */
  public $afterDate       = '';
  public $beforeDate      = '';
  public $defaultValue    = NULL;
  public $format          = 'Y-m-d';

  public $mask            = 'Y-m-d';
  public $dependentFields = '';

  public $showtime;
  public $onchange;
  public $editable;
  public $relativeDates;

  //var $hint;

  /**
   * Verify the format of a date
   * @param <Date> $date
   * @return <Boolean> true/false
   */
  function verifyDateFormat($date, $mask='')
  {
    $dateTime = explode(" ",$date); //To accept the Hour part
    $aDate    = explode ( '-', str_replace("/", "-", $dateTime[0]) );
    $bResult  = true;

    foreach($aDate as $sDate){
        if( !is_numeric($sDate) ){
            $bResult = false;
            break;
        }
    }

    if( $mask != '' ){
        $aDate    = $this->getSplitDate($dateTime[0], $mask);
        $aDate[0] = ($aDate[0] == '')? date('Y'): $aDate[0];
        $aDate[1] = ($aDate[1] == '')? date('m'): $aDate[1];
        $aDate[2] = ($aDate[2] == '')? date('d'): $aDate[2];

            return true;
        if( checkdate($aDate[1], $aDate[2], $aDate[0]) ){
        } else {
            return false;
        }
    }

    return true;
  }

  /**
   * Check if a date had a valid format before
   * @param  <Date> $date
   * @return <Boolean> True/False
   */
  function isvalidBeforeFormat($date)
  {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  function calculateBeforeFormat($date, $sign)
  {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );

    $part2 = substr ( $date, strlen ( $date ) - 1 );

    #TODO
    # neyek
    /*
     * Because mktime has the restriccion for:
     * The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069 and 70-100 to 1970-2000.
     * On systems where time_t is a 32bit signed integer, as most common today, the valid range for year  is somewhere
     * between 1901 and 2038. However, before PHP 5.1.0 this range was limited from 1970 to 2038 on some systems (e.g. Windows). */
    # improving required

    switch ($part2) {
      case 'd' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ) - 1, date ( 'Y' ) ) );
        break;
      case 'y' :
        //$res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1) );
        //hook
        $res = (intVal(date ( 'Y' )) + $part1) . '-' . date ( 'm' ) . '-' . date ( 'd' );
        break;
    }

    return $res;
  }

  /**
   * render the field in a dynaform
   * @param   $value
   * @param   $owner
   * @return  renderized widget
   */
  function render($value = NULL, $owner = NULL)
  {
    if (($this->pmconnection != '') && ($this->pmfield != '') && $value == NULL) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
    $id        = "form[$this->name]";
    return $this->__draw_widget ( $id, $value, $owner );
  }

  /**
   * render the field in a grid
   * @param  $values
   * @param  $owner
   * @param  $onlyValue
   * @return Array $result
   */
  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false) {
    $result = array ();
    $r      = 1;
    foreach ( $values as $v ) {
      $v = ($v!='')?G::replaceDataField ( $v, $owner->values ):$this->defaultValue;
      if (! $onlyValue) {
        $id        = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
        $html      = $this->__draw_widget ( $id, $v, $owner );
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  /**
   * Returns the html code to draw the widget
   * @param  $pID
   * @param  $value
   * @param  $owner
   * @return <String>
   */
  function __draw_widget($pID, $value, $owner = '') {

    /*for deprecated mask definitions...*/

    #first deprecated simple (yyyy-mm-dd) and personalizes combinations
    $this->mask = str_replace('yyyy', 'Y', $this->mask);
    $this->mask = str_replace('yy', 'y', $this->mask);
    $this->mask = str_replace('mm', 'm', $this->mask);
    $this->mask = str_replace('dd', 'd', $this->mask);

    #second deprecated (%Y-%m-%d) and other combinations
    $this->mask = str_replace('%', '', $this->mask);

    if( isset($this->mask) && $this->mask != '' ){
      $mask = $this->mask;
    } else {
      #Default mask
      $mask = 'Y-m-d';
    }

    // Note added by Gustavo Cruz
    // set the variable isRequired if the needs to be validated
    //
    if ($this->required){
        $isRequired = '1';
    } else {
        $isRequired = '0';
    }

    $startDate  = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate    = G::replaceDataField ( $this->endDate, $owner->values );

    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate  = G::replaceDataField ( $this->afterDate, $owner->values );

    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }
    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }

    if ($startDate == '') {
      //$startDate = date ( 'Y-m-d' ); // the default is the current date
    }

    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }
    if ($endDate == '') {
      //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
    }

  $tmp = str_replace("%", "", $mask);
    if ( trim ($value) == '' or $value == NULL ) {
      $value = '';//date ($tmp);
    } else {
      switch(strtolower($value)){
        case 'today':
          $value = date($tmp);
        break;
        default:
          if(!$this->verifyDateFormat($value,$mask)){
             $value='';
          }
        break;
      }
    }

    if( $value == ''){
      $valueDate = Array(date('Y'), date('m'), date('d'));
    } else {
      $valueDate = $this->getSplitDate($value, $mask);
    }

    $startDate = $this->getSplitDate($startDate, 'Y-m-d');
    //adatation for new js calendar widget
    $startDate[2] = $startDate[2] - 1;

  $endDate = $this->getSplitDate($endDate, 'Y-m-d');
  //adatation for new js calendar widget
  $endDate[2] = $endDate[2] + 1;

  $extra = (defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' )? 'direction:rtl; float:right': 'direction:ltr';

  if(isset($this->showtime) && $this->showtime){
    $mask .= ' h:i';
    $img = (defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' )? 'pmdatetimeiw.png': 'pmdatetime.png';
    $style = 'background-image:url(/images/'.$img.');float:left; width:131px; height:22px;padding:2px 1px 1px 3px;cursor:pointer;color:#000; '.$extra.';';
    $showTime = 'true';
  } else {
      $img = (defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' )? 'pmdateiw.png': 'pmdate.png';
    $style = 'background-image:url(/images/'.$img.');float:left; width:100px; height:22px;padding:2px 1px 1px 3px;cursor:pointer;color:#000; direction:'.$extra.';';
    $showTime = 'false';
  }

    if ( $this->editable == "1") {
        $style = '';
  }

    // Note added by Gustavo Cruz
    // also the fields rendered in a grid needs now have an attribute required set to 0 or 1
    // that it means not required or required respectively.
    if ($this->mode == 'edit' && $this->readOnly != "1") {
      if ( $this->editable != "1") {
        $html = '<input type="text" required="'.$isRequired.'" style="display:none" id="'.$pID.'" name="'.$pID.'" value="'.$value.'" onchange="'.$this->onchange.'"/>';
          $html .= '<div id="'.$pID.'[div]" name="'.$pID.'[div]" onclick="var oc=new NeyekCalendar(\''.$pID.'\');
            oc.picker(
              {\'year\':\''.$valueDate[0].'\',\'month\':\''.$valueDate[1].'\',\'day\':\''.$valueDate[2].'\'},
            \''.$mask.'\',
            \''.SYS_LANG.'\',
            {\'year\':\''.$startDate[0].'\',\'month\':\''.$startDate[1].'\',\'day\':\''.$startDate[2].'\'},
            {\'year\':\''.$endDate[0].'\',\'month\':\''.$endDate[1].'\',\'day\':\''.$endDate[2].'\'},
            '.$showTime.',
            event
          ); return false;" style="'.$style.'">&nbsp;'.$value.'</div>';
      } else {
          $html = '<input id="'.$pID.'" name="'.$pID.'" style="'.$style.'" value="'.$value.'" size="14" class="module_app_input___gray" onchange="'.$this->onchange.'">&nbsp;';
          $html .= '<a href="#" onclick="var oc=new NeyekCalendar(\''.$pID.'\', 1);
            oc.picker(
              {\'year\':\''.$valueDate[0].'\',\'month\':\''.$valueDate[1].'\',\'day\':\''.$valueDate[2].'\'},
            \''.$mask.'\',
            \''.SYS_LANG.'\',
            {\'year\':\''.$startDate[0].'\',\'month\':\''.$startDate[1].'\',\'day\':\''.$startDate[2].'\'},
            {\'year\':\''.$endDate[0].'\',\'month\':\''.$endDate[1].'\',\'day\':\''.$endDate[2].'\'},
            '.$showTime.',
            event
          ); return false;"><img src="/images/pmdateicon.png" width="16px" height="18px" border="0"></a>';
      }

    } else {
      $html = '<input type="hidden" id="'.$pID.'" name="'.$pID.'" value="'.$value.'" onchange="'.$this->onchange.'"/>';
      $html .= "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>";
    }
//    if($this->hint){
//           $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hint.'\');return false;">
//                  <image src="/images/help4.gif" width="15" height="15" border="0"/>
//                </a>';
//    }
//print '<input type="text" id="'.$pID.'" name="'.$pID.'" value="'.$value.'" onchange="'.$this->onchange.'"/>';
    $html .= $this->renderHint();
    return $html;
  }

  /**
   * modify the date format
   * @param <Date> $date
   * @param  $mask
   * @return <type>
   */
  function getSplitDate($date, $mask){
  $sw1 = false;
  for($i=0; $i<3; $i++){
    $item = substr($mask, $i*2, 1);
    switch($item){
      case 'Y':
        switch($i){
          case 0: $d1 = substr($date, 0, 4); break;
          case 1: $d1 = substr($date, 3, 4); break;
          case 2: $d1 = substr($date, 6, 4); break;
        }
        $sw1 = true;
      break;
      case 'y':
        switch($i){
          case 0: $d1 = substr($date, 0, 2); break;
          case 1: $d1 = substr($date, 3, 2); break;
          case 2: $d1 = substr($date, 6, 2); break;
        }
      break;
      case 'm':
        switch($i){

          case 0: $d2 = substr($date, 0, 2); break;
          case 1: $d2 = ($sw1)? substr($date, 5, 2): substr($date, 3, 2); break;
          case 2: $d2 = ($sw1)? substr($date, 8, 2): substr($date, 5, 2); break;
        }
      break;
      case 'd':
        switch($i){
          case 0: $d3 = substr($date, 0, 2); break;
          case 1: $d3 = ($sw1)? substr($date, 5, 2): substr($date, 3, 2); break;
          case 2: $d3 = ($sw1)? substr($date, 8, 2): substr($date, 5, 2); break;
        }
      break;
    }
  }
  return Array(isset($d1)?$d1:'', isset($d2)?$d2:'', isset($d3)?$d3:'');
  }
}

 
/**
 * @package gulliver.system
 * AVOID TO ENTER HERE : EXPERIMENTAL !!!
 * by Caleeli.
*/
class XmlForm_Field_Xmlform extends XmlForm_Field {
  var $xmlfile   = '';
  var $initRows  = 1;
  var $group     = 0;
  var $addRow    = true;
  var $deleteRow = false;
  var $editRow   = false;
  var $sql       = '';
  //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
  var $fillType  = 0;
  var $fields    = array ();
  var $scriptURL;
  var $id        = '';

  /**
   * Function XmlForm_Field_Xmlform
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string xmlnode
   * @param string language
   * @param string home
   * @return string
   */
  function XmlForm_Field_Xmlform($xmlnode, $language, $home) {
    parent::XmlForm_Field ( $xmlnode, $language );
    $this->parseFile ( $home, $language );
  }

  /**
   * Function parseFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string home
   * @param string language
   * @return string
   */
  function parseFile($home, $language) {
    $this->xmlform       = new XmlForm ( );
    $this->xmlform->home = $home;
    $this->xmlform->parseFile ( $this->xmlfile . '.xml', $language, false );
    $this->fields    = $this->xmlform->fields;
    $this->scriptURL = $this->xmlform->scriptURL;
    $this->id = $this->xmlform->id;
    unset ( $this->xmlform );
  }

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @return string
   */
  function render($values) {
    $html = '';
    foreach ( $this->fields as $f => $v ) {
      $html .= $v->render ( '' );
    }
    $this->id = $this->owner->id . $this->name;
    $tpl = new xmlformTemplate ( $this, PATH_CORE . 'templates/xmlform.html' );
    $this->values = $values;
    //$this->rows=count(reset($values));
    $tpl->template = $tpl->printTemplate ( $this );
    //In the header

    $oHeadPublisher = & headPublisher::getSingleton ();
    $oHeadPublisher->addScriptFile ( $this->scriptURL );
    $oHeadPublisher->addScriptCode ( $tpl->printJavaScript ( $this ) );
    return $tpl->printObject ( $this );
  }
}

/**
 * Class XmlForm
 * Main Class
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class XmlForm
{
  var $tree;
  var $id                = '';
  var $name              = '';
  var $language;
  /* @attribute string version 0.xxx = Previous to pre-open source
  */
  var $version           = '0.3';
  var $fields            = array ();
  var $title             = '';
  var $home              = '';
  var $parsedFile        = '';
  var $type              = 'xmlform';
  var $fileName          = '';
  var $scriptFile        = '';
  var $scriptURL         = '';
  /* Special propose attributes*/
  var $sql;
  var $sqlConnection;
  /*Attributes for the xmlform template*/
  var $width             = 600;
  var $height            = "100%";
  var $border            = 1;
  var $mode              = '';
  // var $labelWidth = 140;
  // var $labelWidth        = 180;
  var $labelWidth =  "40%";
  var $onsubmit          = '';
  var $requiredFields    = array ();
  var $fieldContentWidth = 450;

  /**
   * Function xmlformTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @param string templateFile
   * @return string
   */
  function parseFile($filename, $language, $forceParse)
  {
    $this->language = $language;
    $filenameInitial=$filename;
    $filename = $this->home . $filename;

    //if the xmlform file doesn't exists, then try with the plugins folders
      if ( !is_file ( $filename ) ) {
        $aux = explode ( PATH_SEP, $filenameInitial );
        //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
        if(count($aux) > 2){//Subfolders
          $filename=array_pop($aux);
          $aux0=implode(PATH_SEP,$aux);
          $aux=array();
          $aux[0]=$aux0;
          $aux[1]=$filename;
        }
        if ( count($aux) == 2 && defined ( 'G_PLUGIN_CLASS' ) ) {
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $response=$oPluginRegistry->isRegisteredFolder($aux[0]) ) {
            if($response!==true){
              $sPath = PATH_PLUGINS.$response.PATH_SEP;
            }else{
              $sPath = PATH_PLUGINS;
            }
            $filename=$sPath.$aux[0].PATH_SEP.$aux[1];
          }
        }
      }

    $this->fileName = $filename;
    $parsedFile = dirname ( $filename ) . PATH_SEP . basename ( $filename, 'xml' ) . $language;

    $parsedFilePath = defined ( 'PATH_C' ) ? ( defined('SYS_SYS') ? PATH_C . 'ws' . PATH_SEP . SYS_SYS . PATH_SEP: PATH_C ) : PATH_DATA;
    $parsedFilePath .= 'xmlform/' . substr ( $parsedFile, strlen ( $this->home ) );

    $this->parsedFile = $parsedFilePath;
    //Note that scriptFile must be public URL.
    $realPath = substr ( realpath ( $this->fileName ), strlen ( realpath ( $this->home ) ), - 4 );
    if (substr ( $realPath, 0, 1 ) != PATH_SEP)
      $realPath = PATH_SEP . $realPath;
    $this->scriptURL = '/jsform' . $realPath . '.js';
    $this->scriptFile = substr ( (defined ( 'PATH_C' ) ? PATH_C : PATH_DATA) . 'xmlform/', 0, - 1 ) . substr ( $this->scriptURL, 7 );
    $this->id = G::createUID ( '', substr ( $this->fileName, strlen ( $this->home ) ) );
    $this->scriptURL = str_replace ( '\\', '/', $this->scriptURL );

    $newVersion = false;
    if ($forceParse || ((! file_exists ( $this->parsedFile )) || (filemtime ( $filename ) > filemtime ( $this->parsedFile )) || (filemtime ( __FILE__ ) > filemtime ( $this->parsedFile ))) || (! file_exists ( $this->scriptFile )) || (filemtime ( $filename ) > filemtime ( $this->scriptFile ))) {
      $this->tree = new Xml_Document ( );
      $this->tree->parseXmlFile ( $filename );
      //$this->tree->unsetParent();
      if (! is_object ( $this->tree->children [0] ))
        throw new Exception ( 'Failure loading root node.' );
      $this->tree = &$this->tree->children [0]->toTree ();
      //ERROR CODE [1] : Failed to read the xml document
      if (! isset ( $this->tree ))
        return 1;
      $xmlNode = & $this->tree->children;

      //Set the form's attributes
      $myAttributes = get_class_vars ( get_class ( $this ) );
      foreach ( $myAttributes as $k => $v )
        $myAttributes [$k] = strtolower ( $k );
      foreach ( $this->tree->attributes as $k => $v ) {
        $key = array_search ( strtolower ( $k ), $myAttributes );
        if (($key !== FALSE) && (strtolower ( $k ) !== 'fields') && (strtolower ( $k ) !== 'values'))
          $this->{$key} = $v;
      }
      //Reeplace non valid characters in xmlform name with "_"
      $this->name = preg_replace ( '/\W/', '_', $this->name );
      //Create fields

      foreach ( $xmlNode as $k => $v ) {
        if (($xmlNode [$k]->type !== 'cdata') && isset ( $xmlNode [$k]->attributes ['type'] )) {
          if (class_exists ( 'XmlForm_Field_' . $xmlNode [$k]->attributes ['type'] )) {
            $x = '$field = new XmlForm_Field_' . $xmlNode [$k]->attributes ['type'] . '( $xmlNode[$k], $language, $this->home, $this);';


            eval ( $x );
          } else
            $field = new XmlForm_Field ( $xmlNode [$k], $language, $this->home, $this );

          $field->language = $this->language;
          $this->fields [$field->name] = $field;
        }
			
        if (isset($xmlNode [$k]->attributes ['required'] ) || isset($xmlNode [$k]->attributes ['validate'] )) {
          // the fields or xml nodes with a required attribute are put in an array that is passed to the view file
          $isEditMode = isset($xmlNode[$k]->attributes['mode']) && $xmlNode[$k]->attributes['mode'] == 'view' ? false: true;
          
          if ($isEditMode && $this->mode != 'view') {
          
            $validateValue = "";
            if(isset($xmlNode[$k]->attributes['validate'])) {            
              $validateValue = $xmlNode[$k]->attributes['validate'];
            }
            $requiredValue = "0";
            if(isset($xmlNode[$k]->attributes['required'])) {
              $requiredValue = $xmlNode[$k]->attributes['required'] == 1 ? '1': '0';
            }          
            
            $this->requiredFields [] = array (
              'name' => $field->name,
              'type' => $xmlNode [$k]->attributes ['type'],
              'label' => addslashes(trim ( $field->label )),                
              'validate' => $validateValue,
              'required' => $requiredValue
            );          
          }

        }        
      }

      $oJSON = new Services_JSON ( );
      $this->objectRequiredFields =  str_replace('"', "%27", str_replace("'", "%39", $oJSON->encode ( $this->requiredFields )) );

      //Load the default values
      //$this->setDefaultValues();
      //Save the cache file
      if (! is_dir ( dirname ( $this->parsedFile ) ))
        G::mk_dir ( dirname ( $this->parsedFile ) );
      $f = fopen ( $this->parsedFile, 'w+' );
      //ERROR CODE [2] : Failed to open cache file
      if ($f === FALSE)
        return 2;
      fwrite ( $f, "<?php\n" );
      /*  fwrite ($f, '$this = unserialize( \'' .
                  addcslashes( serialize ( $this ), '\\\'' ) . '\' );' . "\n" );*/
      foreach ( $this as $key => $value ) {
        //cho $key .'<br/>';
        switch ($key) {
          case 'home' :
          case 'fileName' :
          case 'parsedFile' :
          case 'scriptFile' :
          case 'scriptURL' :
            break;
          default :
            switch (true) {
              case is_string ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=\'' . addcslashes ( $this->{$key}, '\\\'' ) . '\'' . ";\n" );
                break;
              case is_bool ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=' . (($this->{$key}) ? 'true;' : 'false') . ";\n" );
                break;
              case is_null ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=NULL' . ";\n" );
                break;
              case is_float ( $this->{$key} ) :
              case is_int ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=' . $this->{$key} . ";\n" );
                break;
              default :
                fwrite ( $f, '$this->' . $key . ' = unserialize( \'' . addcslashes ( serialize ( $this->{$key} ), '\\\'' ) . '\' );' . "\n" );
            }
        }
      }
      fwrite ( $f, "?>" );
      fclose ( $f );
      $newVersion = true;
    } //if $forceParse
    //Loads the parsedFile.
    require ($this->parsedFile);
    $this->fileName = $filename;
    $this->parsedFile = $parsedFile;

    //RECREATE LA JS file
    //Note: Template defined with publisher doesn't affect the .js file
    //created at this point.
    if ($newVersion) {
      $template = PATH_CORE . 'templates/' . $this->type . '.html';
      //If the type is not the correct template name, use xmlform.html
      //if (!file_exists($template)) $template = PATH_CORE . 'templates/xmlform.html';
      if (($template !== '') && (file_exists ( $template ))) {
        if (! is_dir ( dirname ( $this->scriptFile ) ))
           G::mk_dir ( dirname ( $this->scriptFile ) );
        $f = fopen ( $this->scriptFile, 'w' );
        $o = new xmlformTemplate ( $this, $template );
        $scriptContent = $o->printJSFile ( $this );
        unset ( $o );
        fwrite ( $f, $scriptContent );
        fclose ( $f );
      }
    }
    return 0;
  }

  /**
   * Generic function to set values for the current object.
   * @param $newValues
   * @return void
   */
  function setValues($newValues = array())
  {
    foreach ( $this->fields as $k => $v ) {
      if (array_key_exists ( $k, $newValues ))
        $this->values [$k] = $newValues [$k];
      }
    foreach ( $this->fields as $k => $v ) {
      if(is_object ($this->fields[$k]) && get_class($this->fields[$k])!='__PHP_Incomplete_Class'){
        $this->fields [$k]->owner = & $this;
      }

    }
  }

  /**
   * Generic function to print the current object.
   * @param $template
   * @param &$scriptContent
   * @return string
   */
  function render($template, &$scriptContent)
  {
    $o = new xmlformTemplate ( $this, $template );
    if (is_array ( reset ( $this->values ) ))
      $this->rows = count ( reset ( $this->values ) );
    $o->template = $o->printTemplate ( $this );
    $scriptContent = $o->printJavaScript ( $this );
    return $o->printObject ( $this );
  }

  /**
   * Clone the current object
   * @return Object
   */
  function cloneObject()
  {
    return unserialize ( serialize ( $this ) );
  }
}

/**
 * Class xmlformTemplate
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class xmlformTemplate extends Smarty
{
  var $template;
  var $templateFile;

  /**
   * Function xmlformTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @param string templateFile
   * @return string
   */
  function xmlformTemplate(&$form, $templateFile)
  {
    $this->template_dir = PATH_XMLFORM;
    $this->compile_dir = PATH_SMARTY_C;
    $this->cache_dir = PATH_SMARTY_CACHE;
    $this->config_dir = PATH_THIRDPARTY . 'smarty/configs';
    $this->caching = false;

    // register the resource name "db"
    $this->templateFile = $templateFile;
  }

  /**
   * Function printTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @param string target
   * @return string
   */
  function printTemplate(&$form, $target = 'smarty')
  {
    if (strcasecmp ( $target, 'smarty' ) === 0)
      $varPrefix = '$';
    if (strcasecmp ( $target, 'templatePower' ) === 0)
      $varPrefix = '';

    $ft = new StdClass ( );
    foreach ( $form as $name => $value ) {
      if (($name !== 'fields') && ($value !== ''))
        $ft->{$name} = '{$form_' . $name . '}';
      if ($name === 'cols')
        $ft->{$name} = $value;
      if ($name === 'owner')
        $ft->owner = & $form->owner;
      if ($name === 'deleteRow')
        $ft->deleteRow = $form->deleteRow;
      if ($name === 'addRow')
        $ft->addRow = $form->addRow;
      if ($name === 'editRow')
        $ft->editRow = $form->editRow;
    }
    if (! isset ( $ft->action )) {
      $ft->action = '{$form_action}';
    }
    $hasRequiredFields = false;

    foreach ( $form->fields as $k => $v ) {
      $ft->fields [$k] = $v->cloneObject ();
      $ft->fields [$k]->label = '{' . $varPrefix . $k . '}';

      if ($form->type === 'grid') {
        if (strcasecmp ( $target, 'smarty' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form.' . $k . '[row]}';
        if (strcasecmp ( $target, 'templatePower' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form[' . $k . '][row]}';
      } 
      else {
        if (strcasecmp ( $target, 'smarty' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form.' . $k . '}';
        if (strcasecmp ( $target, 'templatePower' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form[' . $k . ']}';
      }

      $hasRequiredFields = $hasRequiredFields | (isset ( $v->required ) && ($v->required == '1') && ($v->mode == 'edit'));

      if ($v->type == 'xmlmenu') {
        $menu = $v;
      }
    }

    if (isset($menu)) {
      if (isset($menu->owner->values['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'])) {
        $prevStep_url = $menu->owner->values['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'];
        
        $this->assign('prevStep_url', $prevStep_url);
        $this->assign('prevStep_label', G::loadTranslation('ID_BACK'));
      }
    }

    $this->assign ( 'hasRequiredFields', $hasRequiredFields );
    $this->assign ( 'form', $ft );
    $this->assign ( 'printTemplate', true );
    $this->assign ( 'printJSFile', false );
    $this->assign ( 'printJavaScript', false );
    //$this->assign ( 'dynaformSetFocus', "try {literal}{{/literal} dynaformSetFocus();}catch(e){literal}{{/literal}}" );
    return $this->fetch ( $this->templateFile );
  }

  /**
   * Function printJavaScript
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @return string
   */
  function printJavaScript(&$form)
  {
    $this->assign ( 'form', $form );
    $this->assign ( 'printTemplate', false );
    $this->assign ( 'printJSFile', false );
    $this->assign ( 'printJavaScript', true );
    return $this->fetch ( $this->templateFile );
  }

  /**
   * Function printJSFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string form
   * @return string
   */
  function printJSFile(&$form)
  {
    $this->assign ( 'form', $form );
    $this->assign ( 'printTemplate', false );
    $this->assign ( 'printJSFile', true );
    $this->assign ( 'printJavaScript', false );
    return $this->fetch ( $this->templateFile );
  }

  /**
   * Function getFields
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @return string
   */
  function getFields(&$form, $therow = -1)
  {
    $result = array ();
    foreach ( $form->fields as $k => $v ) {
      if ($form->mode != '') { #@ last modification: erik
        $v->mode = $form->mode; #@
      } #@
      //if (isset($form->fields[$k]->sql)) $form->fields[$k]->executeSQL( $form );
      $value = (isset ( $form->values [$k] )) ? $form->values [$k] : NULL;
      $result [$k] = G::replaceDataField ( $form->fields [$k]->label, $form->values );
      if (! is_array ( $value )) {
        if ($form->type == 'grid') {
          $aAux = array ();
          $index = ($therow >count ( $form->owner->values [$form->name] ))? $therow : count($form->owner->values [$form->name] );
          for($i = 0; $i < $index; $i ++) {
            $aAux [] = '';
          }
          $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $aAux, $form );
        } else {
          $result ['form'] [$k] = $form->fields [$k]->render ( $value, $form );
        }
      } else {
        /*if (isset ( $form->owner )) {

          if (count ( $value ) < count ( $form->owner->values [$form->name] )) {
            $i = count ( $value );
            $j = count ( $form->owner->values [$form->name] );

            for($i; $i < $j; $i ++) {
              $value [] = '';
            }
          }
        }*/

        if ($v->type == 'grid') {
          $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form, $therow );
        } else {
          if ($v->type == 'dropdown') {
            $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form, false, $therow );
          } else {
            $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form );
          }
        }
      }
    }
    foreach ( $form as $name => $value ) {
      if ($name !== 'fields')
        $result ['form_' . $name] = $value;
    }
    return $result;
  }
  
  /**
   * Function printObject
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string form
   * @return string
   */
  function printObject(&$form, $therow = -1)
  {
    //to do: generate the template for templatePower.
    //DONE: The template was generated in printTemplate, to use it
    // is necesary to load the file with templatePower and send the array
    //result
    $this->register_resource ( 'mem', array (array (&$this, '_get_template' ), array ($this, '_get_timestamp' ), array ($this, '_get_secure' ), array ($this, '_get_trusted' ) ) );
    $result = $this->getFields ( $form, $therow );

    $this->assign ( array ('PATH_TPL' => PATH_TPL ) );
    $this->assign ( $result );
    if( defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' ){
        switch( $form->type ){
          case 'toolbar':

                $form->align = 'right';

            break;
        }
    }

    $this->assign ( array ('_form' => $form ) );
    //'mem:defaultTemplate'.$form->name obtains the template generated for the
    //current "form" object, then this resource y saved by Smarty in the
    //cache_dir. To avoiding troubles when two forms with the same id are being
    //drawed in a same page with different templates, add an . rand(1,1000)
    //to the resource name. This is because the process of creating templates
    //(with the method "printTemplate") and painting takes less than 1 second
    //so the new template resource generally will had the same timestamp.
    $output = $this->fetch ( 'mem:defaultTemplate' . $form->name );
    return $output;
  }

  /**
   * Smarty plugin
   * -------------------------------------------------------------
   * Type:     resource
   * Name:     mem
   * Purpose:  Fetches templates from this object
   * -------------------------------------------------------------
   */
  function _get_template($tpl_name, &$tpl_source, &$smarty_obj)
  {
    $tpl_source = $this->template;
    return true;
  }

  /**
   * Function _get_timestamp
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string tpl_name
   * @param string tpl_timestamp
   * @param string smarty_obj
   * @return string
   */
  function _get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
  {
    //NOTE: +1 prevents to load the smarty cache instead of this resource
    $tpl_timestamp = time () + 1;
    return true;
  }

  /**
   * Function _get_secure
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string tpl_name
   * @param string smarty_obj
   * @return string
   */
  function _get_secure($tpl_name, &$smarty_obj)
  {
    // assume all templates are secure
    return true;
  }

  /**
   * Function _get_trusted
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string tpl_name
   * @param string smarty_obj
   * @return string
   */
  function _get_trusted($tpl_name, &$smarty_obj)
  {
    // not used for templates
  }

}

/**
 * @package gulliver.system
*/

class XmlForm_Field_Image extends XmlForm_Field
{
  var $file         = '';
  var $home         = 'public_html';
  var $withoutLabel = false;

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param string values
   * @return string
   */
  function render( $value, $owner = null )
  {
    $url = G::replaceDataField($this->file, $owner->values);
    if ($this->home === "methods") $url = G::encryptlink( SYS_URI . $url );
    if ($this->home === "public_html") $url ='/' . $url ;
    return '<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'" '.
    (($this->style)?'style="'.$this->style.'"':'')
    .' alt ="'.htmlentities($value,ENT_QUOTES,'utf-8').'"/>';
  }
}
  //mask function to php
  function masktophp ($mask){
    $tmp = str_replace("%", "", $mask);
    if(preg_match('/M/',$tmp)) {
      $tmp = str_replace("M", "i", $tmp);
    }
    if(preg_match('/b/',$tmp)) {
      $tmp = str_replace("b", "M", $tmp);
    }
    if(preg_match('/B/',$tmp)) {
      $tmp = str_replace("B", "F", $tmp);
    }
    if(preg_match('/S/',$tmp)) {
      $tmp = str_replace("S", "s", $tmp);
    }
    $value = date($tmp);
    return $value;
  }
