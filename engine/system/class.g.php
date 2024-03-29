<?php
/**
 * class.g.php
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
 * @package gulliver.system
 */

class G
{

  /**
   * is_https
   * @return void
   */
  function is_https()
  {
    if(isset($_SERVER['HTTPS']))
    {
      if($_SERVER['HTTPS']=='on')
        return true;
      else
        return false;
    }
    else
      return false;
  }
  
  /**
   * Fill array values (recursive)
   * @author maborak <maborak@maborak.com>
   * @access public
   * @param  Array $arr
   * @param  Void  $value
   * @param  Boolean $recursive
   * @return Array
   */
  function array_fill_value($arr = Array(),$value = '',$recursive = false)
  {
    if(is_array($arr)) {
      foreach($arr as $key=>$val) {
        if(is_array($arr[$key])) {
          $arr[$key] = ($recursive===true)?G::array_fill_value($arr[$key],$value,true):$val;
        }
        else {
          $arr[$key] = $value;
        }
      }
    }
    else {
      $arr = Array();
    }
    return $arr;
  }
  
  /**
   * Generate Password Random
   * @author maborak <maborak@maborak.com>
   * @access public
   * @param  Int
   * @return String
   */
  function generate_password($length = 8)
  {
    $password = "";
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";
    $i        = 0;
    while($i<$length) {
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      if(!strstr($password, $char)) {
        $password .= $char;
        $i++;
      }
    }
    return $password;
  }
  
  /**
   * Array concat
   * array_concat(ArrayToConcat,ArrayOriginal);
   * @author maborak <maborak@maborak.com>
   * @access public
   * @param  Array
   * @return Array
   */
  function array_concat()
  {
    $nums = func_num_args();
    $vars = func_get_args();
    $ret  = Array();
    for($i = 0;$i < $nums; $i++)
    {
      if(is_array($vars[$i])) {
        foreach($vars[$i] as $key=>$value) {
          $ret[$key] = $value;
        }
      }
    }
    return $ret;
  }

  /**
   * Compare Variables
   * var_compare(value,[var1,var2,varN]);
   * @author maborak <maborak@maborak.com>
   * @access public
   * @param  void $value
   * @param  void $var1-N
   * @return Boolean
   */
  function var_compare($value=true,$varN)
  {
    $nums = func_num_args();
    if($nums<2){return true;}
    $vars = func_get_args();
    $ret  = Array();
    for($i=1;$i<$nums;$i++) {
      if($vars[$i]!==$value) {
        return false;
      }
    }
    return true;
  }
  /**
   * Emulate variable selector
   * @author maborak <maborak@maborak.com>
   * @access public
   * @param  void
   * @return void
   */
  function var_probe()
  {
    //return (!$variable)?
    $nums = func_num_args();
    $vars = func_get_args();
    for($i=0;$i<$nums;$i++) {
      if($vars[$i]) {
        return $vars[$i];
      }
    }
    return 1;
  }

  /**
   * Get the current version of gulliver classes
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return string
   */
  function &getVersion(  )
  {
    //majorVersion.minorVersion-SvnRevision
    return '3.0-1';
  }

  /**
   * getIpAddress
   * @return string $ip
   */
  function getIpAddress ()
  {
    if (getenv('HTTP_CLIENT_IP')) {
      $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif(getenv('HTTP_X_FORWARDED_FOR')) {
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    else {
      $ip = getenv('REMOTE_ADDR');
    }
    return $ip;
  }
  
  /**
   * getMacAddress
   * @return string $mac
   */
  function getMacAddress()
  {
    if ( strstr ( getenv ( 'OS' ), 'Windows' ) ) {
      $ipconfig = `ipconfig /all`;
      preg_match('/[\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}/i',$ipconfig,$mac);
    } else {
      $ifconfig = `/sbin/ifconfig`;
      preg_match('/[\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}/i',$ifconfig,$mac);
    }
    return isset($mac[0])? $mac[0]:'00:00:00:00:00:00';
  }

  /**
   * microtime_float
   * @return array_sum(explode(' ',microtime()))
   */
  /*public static*/ function microtime_float() {
    return array_sum(explode(' ',microtime()));
  }
  /* custom error functions */

  /**
   * &setFatalErrorHandler
   *
   * @param  string $newFatalErrorHandler default value null
   *
   * @return boolean true
   */
  /*public static*/
  function &setFatalErrorHandler( $newFatalErrorHandler = null )
     {
     if ( isset ( $newFatalErrorHandler ) ) {
       set_error_handler( $newFatalErrorHandler );
     }
     else {
       ob_start( array ( 'G', 'fatalErrorHandler' ) );
     }
     return true;
     }

  /**
   * setErrorHandler
   * @param  string setErrorHandler
   * @param  object $newCustomErrorHandler
   *
   * @return boolean true
   */
  /*public static*/
  function setErrorHandler( $newCustomErrorHandler = null )
    {
    if ( isset ( $newCustomErrorHandler ) ) {
      set_error_handler( $newCustomErrorHandler );
    }
    else {
      set_error_handler( array("G", "customErrorHandler"));
    }
    return true;
    }

  /**
   * fatalErrorHandler
   *
   * @param  string $buffer
   *
   * @return string $errorBox or $buffer
   */
  /*public static*/ function fatalErrorHandler($buffer) {
    // The ereg function has been DEPRECATED as of PHP 5.3.0.
    // if (ereg("(error</b>:)(.+)(<br)", $buffer, $regs) ) {
    if (preg_match("/(error</b>:)(.+)(<br)/", $buffer, $regs) ) {
      $err = preg_replace("/<.*?>/","",$regs[2]);
      G::customErrorLog('FATAL', $err,  '', 0, '');
      $ip_addr  = G::getIpAddress();
      $errorBox = "<table cellpadding=1 cellspacing=0 border=0 bgcolor=#808080 width=250><tr><td >" .
                  "<table cellpadding=2 cellspacing=0 border=0 bgcolor=white width=100%>" .
                  "<tr bgcolor=#d04040><td colspan=2 nowrap><font color=#ffffaa><code> ERROR CAUGHT check log file</code></font></td></tr>" .
                  "<tr ><td colspan=2 nowrap><font color=black><code>IP address: $ip_addr</code></font></td></tr> " .
                  "</table></td></tr></table>";
      return $errorBox;
    }
    return $buffer;
  }

  /**
   * customErrorHandler
   *
   * @param  string $errno
   * @param  string $msg
   * @param  string $file
   * @param  string $line
   * @param  string $context
   *
   * @return void
   */
  /*public static*/
  function customErrorHandler ( $errno, $msg, $file, $line, $context) {
    switch ($errno) {
      case E_ERROR:
      case E_USER_ERROR:
            $type = "FATAL";
            G::customErrorLog ($type, $msg, $file, $line);
            G::verboseError ($type, $errno, $msg, $file, $line, $context);
            if (defined ("ERROR_SHOW_SOURCE_CODE") && ERROR_SHOW_SOURCE_CODE)
              G::showErrorSource ($type, $msg, $file, $line, "#c00000");
            die ();
            break;
      case E_WARNING:
      case E_USER_WARNING:
            $type = "WARNING";
            G::customErrorLog ($type, $msg, $file, $line);
            break;
      case E_NOTICE:
      case E_USER_NOTICE:
            $type = "NOTICE";
            if (defined ("ERROR_LOG_NOTICE_ERROR") && ERROR_LOG_NOTICE_ERROR)
            G::customErrorLog ($type, $msg, $file, $line);
            break;
      case E_STRICT:
            $type = "STRICT"; //dont show STRICT Errors
            //if (defined ("ERROR_LOG_NOTICE_ERROR") && ERROR_LOG_NOTICE_ERROR)
            //  G::customErrorLog ($type, $msg, $file, $line);
            break;
      default:
            $type = "ERROR ($errno)";
            G::customErrorLog ($type, $msg, $file, $line);
            break;
    }
  
    if (defined ("ERROR_SHOW_SOURCE_CODE") && ERROR_SHOW_SOURCE_CODE && $errno <> E_STRICT  )
    G::showErrorSource ($type, $msg, $file, $line);
  }
  
  /**
   * Function showErrorSource
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string type
   * @parameter string msg
   * @parameter string file
   * @parameter string line
   * @return string
   */
  function showErrorSource($type, $msg, $file, $line)
  {
    global $__src_array;
    $line_offset = 3;

    if (! isset ($__src_array[$file]))
      $__src_array[$file] = @file ($file);

    if (!$__src_array[$file])
      return;

    if ($line - $line_offset < 1)
      $start = 1;
    else
      $start = $line - $line_offset;

    if ($line + $line_offset > count ($__src_array[$file]))
      $end = count ($__src_array[$file]);
    else
      $end = $line + $line_offset;

    print "<table cellpadding=1 cellspacing=0 border=0 bgcolor=#808080 width=80%><tr><td >";
    print "<table cellpadding=2 cellspacing=0 border=0 bgcolor=white width=100%>";
    print "<tr bgcolor=#d04040>
          <td colspan=2 nowrap><font color=#ffffaa><code> $type: $msg</code></font></td></tr>
          <tr >
          <td colspan=2 nowrap><font color=gray>File: $file</font></td></tr>
          ";
    for ($i = $start; $i <= $end; $i++) {
      $str  = @highlight_string ("<?php" . $__src_array[$file][$i-1] . "?>", TRUE);

      $pos1 = strpos ($str,"&lt;?");
      $pos2 = strrpos ($str,"?&gt;");

      $str  = substr ($str, 0, $pos1) .
      substr ($str, $pos1+5, $pos2-($pos1+5)) .
      substr ($str, $pos2+5);

      ($i == $line) ? $bgcolor = "bgcolor=#ffccaa" : $bgcolor = "bgcolor=#ffffff";
      print "<tr><td bgcolor=#d0d0d0 width=15 align=right><code>$i</code></td>
            <td $bgcolor>$str</td></tr>";
    }

    print "</table></td></tr></table><p>";
  }

  /**
   * customErrorLog
   *
   * @param  string $type
   * @param  string $msg
   * @param  string $file
   * @param  string $line
   *
   * @return void
   */
  /*public static*/
  function customErrorLog ($type, $msg, $file, $line)
    {
    global $HTTP_X_FORWARDED_FOR, $REMOTE_ADDR, $HTTP_USER_AGENT, $REQUEST_URI;

    $ip_addr = G::getIpAddress();

    if (defined ('APPLICATION_CODE'))
      $name = APPLICATION_CODE;
    else
      $name = "php";

    if ( $file != '')
      $msg .= " in $file:$line ";

    $date            = date ( 'Y-m-d H:i:s');
    $REQUEST_URI     = getenv ( 'REQUEST_URI' );
    $HTTP_USER_AGENT = getenv ( 'HTTP_USER_AGENT' );
    error_log ("[$date] [$ip_addr] [$name] $type: $msg [$HTTP_USER_AGENT] URI: $REQUEST_URI", 0);
    }

  /**
   * verboseError
   *
   * @param  string $type
   * @param  string $errno
   * @param  string $msg
   * @param  string $file
   * @param  string $line
   * @param  string $context
   *
   * @return void
   */
  /*public static*/ function verboseError ($type, $errno, $msg, $file, $line, $context) {
    global $SERVER_ADMIN;
  
    print "<h1>Error!</h1>";
    print "An error occurred while executing this script. Please
          contact the <a href=mailto:$SERVER_ADMIN>$SERVER_ADMIN</a> to
          report this error.";
    print "<p>";
    print "Here is the information provided by the script:";
    print "<hr><pre>";
    print "Error type: $type (code: $errno)<br>";
    print "Error message: $msg<br>";
    print "Script name and line number of error: $file:$line<br>";
    print "Variable context when error occurred: <br>";
    print_r ($context);
    print "</pre><hr>";
  }

  /*** Encrypt and decrypt functions ****/
  /**
   * Encrypt string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @return string
   */
  function encrypt($string, $key)
    {
    //print $string;
    //    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {
    if (strpos($string, '|', 0) !== false) return $string;
    $result = '';
    for($i = 0; $i<strlen($string); $i++) {
      $char    = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char    = chr(ord($char)+ord($keychar));
      $result .= $char;
     }
    //echo $result . '<br>';
    $result = base64_encode($result);
    $result = str_replace ( '/' , '°' , $result);
    $result = str_replace ( '=' , '' , $result);
    //  }
    // else
    //  $result = $string;

    return $result;
    }

  /**
   * Decrypt string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @return string
   */
  function decrypt($string, $key) {

    //   if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {

    //if (strpos($string, '|', 0) !== false) return $string;
    $result     = '';
    $string     = str_replace ( '°', '/' , $string);
    $string_jhl = explode("?",$string);
    $string     = base64_decode($string);
    $string     = base64_decode($string_jhl[0]);

    for($i=0; $i<strlen($string); $i++) {
      $char     = substr($string, $i, 1);
      $keychar  = substr($key, ($i % strlen($key))-1, 1);
      $char     = chr(ord($char)-ord($keychar));
      $result  .= $char;
    }
    if (!empty($string_jhl[1]))
      $result .= '?' . $string_jhl[1];
    // }
    // else
    // $result = $string;
    return $result;
  }

  /**
   * Look up an IP address direction
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $target
   * @return  void
   */
  function lookup($target)
  {
    //if( eregi("[a-zA-Z]", $target) )
    if( preg_match("[a-zA-Z]", $target) )//Made compatible to PHP 5.3
      $ntarget = gethostbyname($target);
    else
      $ntarget = gethostbyaddr($target);
    return($ntarget);
  }

  /***************  path functions *****************/
  
  function mk_dir( $strPath, $rights = 0777)
  {
    $folder_path = array($strPath);
    $oldumask    = umask(0);
    while(!@is_dir(dirname(end($folder_path)))
          && dirname(end($folder_path)) != '/'
          && dirname(end($folder_path)) != '.'
          && dirname(end($folder_path)) != '')
      array_push($folder_path, dirname(end($folder_path))); //var_dump($folder_path); die;
      
    while($parent_folder_path = array_pop($folder_path))
      if(!@is_dir($parent_folder_path))
        if(!@mkdir($parent_folder_path, $rights))
    //trigger_error ("Can't create folder \"$parent_folder_path\".", E_USER_WARNING);
    umask($oldumask);
  }

  /**
   * rm_dir
   *
   * @param  string $dirName
   *
   * @return void
   */
  function rm_dir($dirName) 
  {
    if (!is_writable($dirName)) {
      return false;
    }

    if (is_dir($dirName)) {
      foreach(glob($dirName . '/*') as $file) {
        if(is_dir($file)) {
          G::rm_dir($file);

          if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            exec('DEL /F /S /Q %' . $dirName . '%', $res);
          else 
            @rmdir($file);
        }
        else {
          @unlink($file);
        }
      }
    }
    else {
      @unlink($dirName);
    }
  }

  /**
   * verify path
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string  $strPath      path
   * @param  boolean $createPath   if true this function will create the path
   * @return boolean
   */
  function verifyPath( $strPath , $createPath = false )
  {
    $folder_path = strstr($strPath, '.') ? dirname($strPath) : $strPath;

    if ( file_exists($strPath ) || @is_dir( $strPath )) {
      return true;
    }
    else {
      if ( $createPath ) {
        //TODO:: Define Environment constants: Devel (0777), Production (0770), ...
        G::mk_dir ( $strPath , 0777 );
      }
      else
      return false;
    }
    return false;
  }

  /**
   * Expand the path using the path constants
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strPath
   * @return string
   */
  function expandPath( $strPath = '' )
  {
    $res = "";
    $res = PATH_CORE;
    if( $strPath != "" )
    {
      $res .= $strPath . "/";
    }
    return $res;
  }

  /**
   * Load Gulliver Classes
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadSystem( $strClass )
  {
    require_once( PATH_GULLIVER . 'class.' . $strClass . '.php' );
  }

  function LoadSystemExist($strClass)
  {
    if (file_exists (PATH_GULLIVER . 'class.' . $strClass . '.php') )
      return true;
    else
      return false;
  }

  /**
   * Render Page
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  object $objContent
   * @param  string $strTemplate
   * @param  string $strSkin
   * @return void
   */
  function RenderPage( $strTemplate = "default", $strSkin = SYS_SKIN , $objContent = NULL, $layout='')
  {
    global $G_CONTENT;
    global $G_TEMPLATE;
    global $G_SKIN;
    global $G_PUBLISH;

    $G_CONTENT  = $objContent;
    $G_TEMPLATE = $strTemplate;
    $G_SKIN = $strSkin;

    try {
      $file = G::ExpandPath( "skinEngine" ) . "skinEngine.php";
      include $file;

      $skinEngine = new SkinEngine($G_TEMPLATE, $G_SKIN, $G_CONTENT);
      $skinEngine->setLayout($layout);
      $skinEngine->dispatch();
    }
    catch ( Exception $e ) {
      $aMessage['MESSAGE'] = $e->getMessage();
      global $G_PUBLISH;
      global $G_MAIN_MENU;
      global $G_SUB_MENU;
      $G_MAIN_MENU = '';
      $G_SUB_MENU  = '';
      //$G_PUBLISH          = new Publisher;

      //remove the login.js script
      global $oHeadPublisher;
      if ( count ( $G_PUBLISH->Parts ) == 1 )
      array_shift ( $G_PUBLISH->Parts );
      $leimnudInitString = $oHeadPublisher->leimnudInitString;
      //restart the oHeadPublisher
      $oHeadPublisher->clearScripts();
      //add the missing components, and go on.
      $oHeadPublisher->leimnudInitString = $leimnudInitString;
      $oHeadPublisher->addScriptFile("/js/maborak/core/maborak.js");

      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', null, $aMessage );
      G::LoadSkin( 'green' );
      die;
    }
  }

  /**
   * Load a skin
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strSkinName
   * @return void
   */
  function LoadSkin( $strSkinName )
  {
    //print $strSkinName;
    //now, we are using the skin, a skin is a file in engine/skin directory
    $file = G::ExpandPath( "skins" ) . $strSkinName. ".php";
    //G::pr($file);
    if (file_exists ($file) ) {
      require_once( $file );
      return;
    }
    else {
      if (file_exists ( PATH_HTML . 'errors/error703.php') ) {
        header ( 'location: /errors/error703.php' );
        die;
      }
      else   {
        $text = "The Skin $file does not exist, please review the Skin Definition";
        throw ( new Exception ( $text)  );
      }
    }

  }

  /**
   * Include javascript files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strInclude
   * @return void
   */
  function LoadInclude( $strInclude )
  {
    $incfile = G::ExpandPath( "includes" ) . 'inc.' . $strInclude . '.php';
    if ( !file_exists( $incfile )) {
      $incfile = PATH_GULLIVER_HOME . 'includes' . PATH_SEP . 'inc.' . $strInclude . '.php';
    }

    if ( file_exists( $incfile )) {
      require_once( $incfile  );
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Include all model files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strInclude
   * @return void
   */
  function LoadAllModelClasses( )
  {
    $baseDir = PATH_CORE . 'classes' . PATH_SEP . 'model';
    if ($handle = opendir( $baseDir  )) {
      while ( false !== ($file = readdir($handle))) {
        if ( strpos($file, '.php',1) && !strpos($file, 'Peer.php',1) ) {
          require_once ( $baseDir . PATH_SEP . $file );
        }
      }
    }
  }

  /**
   * Include all model plugin files
   *
   * LoadAllPluginModelClasses
   * @author Hugo Loza <hugo@colosa.com>
   * @access public
   * @return void
   */
  function LoadAllPluginModelClasses(){
    //Get the current Include path, where the plugins directories should be
    if ( !defined('PATH_SEPARATOR') ) {
      define('PATH_SEPARATOR', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? ';' : ':');
    }
    $path=explode(PATH_SEPARATOR,get_include_path());


    foreach($path as $possiblePath){
      if(strstr($possiblePath,"plugins")){
        $baseDir = $possiblePath . 'classes' . PATH_SEP . 'model';
        if(file_exists($baseDir)){
          if ($handle = opendir( $baseDir  )) {
            while ( false !== ($file = readdir($handle))) {
              if ( strpos($file, '.php',1) && !strpos($file, 'Peer.php',1) ) {
                require_once ( $baseDir . PATH_SEP . $file );
              }
            }
          }
          //Include also the extendGulliverClass that could have some new definitions for fields
          if(file_exists($possiblePath . 'classes' . PATH_SEP.'class.extendGulliver.php')){
            include_once $possiblePath . 'classes' . PATH_SEP.'class.extendGulliver.php';
          }
        }
      }
    }
  }

  /**
   * Load a template
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTemplateName
   * @return void
   */
  function LoadTemplate( $strTemplateName )
  {
    if ( $strTemplateName == '' ) return;
    $temp = $strTemplateName . ".php";
    $file = G::ExpandPath( 'templates' ) . $temp;
    // Check if its a user template
    if ( file_exists($file) ) {
      //require_once( $file );
      include( $file );
    } else {
      // Try to get the global system template
      $file = PATH_TEMPLATE . PATH_SEP . $temp;
      //require_once( $file );
      if ( file_exists($file) )
        include( $file );
    }
  }
  
  /**
   * Function LoadClassRBAC
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string strClass
   * @return string
   */
  function LoadClassRBAC( $strClass )
  {
    $classfile = PATH_RBAC . "class.$strClass"  . '.php';
    require_once( $classfile );
  }
  /**
   * If the class is not defined by the aplication, it
   * attempt to load the class from gulliver.system
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadClass( $strClass )
  {
    $classfile = G::ExpandPath( "classes" ) . 'class.' . $strClass . '.php';
    if (!file_exists( $classfile )) {
      if (file_exists( PATH_GULLIVER . 'class.' . $strClass . '.php' ))
        return require_once( PATH_GULLIVER . 'class.' . $strClass . '.php' );
      else
        return false;
    } else {
      return require_once( $classfile );
    }
  }
  
  /**
   * Loads a Class. If the class is not defined by the aplication, it
   * attempt to load the class from gulliver.system
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadThirdParty( $sPath , $sFile )
  {
    $classfile = PATH_THIRDPARTY . $sPath .'/'. $sFile .
                ( (substr($sFile,0,-4)!=='.php')? '.php': '' );
    return require_once( $classfile );
  }

  /**
   * Encrypt URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $urlLink
   * @return string
   */
  function encryptlink($url)
  {
    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      return urlencode( G::encrypt( $url ,URL_KEY ) );
    else
      return $url;
  }

  /**
   * Parsing the URI
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $urlLink
   * @return string
   */
  function parseURI($uri, $config = array())
  {
    //*** process the $_POST with magic_quotes enabled
    // The magic_quotes_gpc feature has been DEPRECATED as of PHP 5.3.0.
    if (get_magic_quotes_gpc() === 1) {
      $_POST = G::strip_slashes($_POST);
    }

    $aRequestUri = explode('/', $uri );

    if ( substr ( $aRequestUri[1], 0, 3 ) == 'sys' ) {
      define( 'SYS_TEMP', substr ( $aRequestUri[1], 3 ) );
    }
    else {
      define("ENABLE_ENCRYPT", 'yes' );
      define( 'SYS_TEMP', $aRequestUri[1] );

      $plain = '/sys' . SYS_TEMP;

      for ($i = 2 ; $i < count($aRequestUri); $i++ ) {
        $decoded = G::decrypt ( urldecode($aRequestUri[$i]) , URL_KEY );
        if ( $decoded == 'sWì›' ) $decoded = $VARS[$i]; //this is for the string  "../"
        $plain .= '/' . $decoded;
      }
      $_SERVER["REQUEST_URI"] = $plain;
    }

    $CURRENT_PAGE = $_SERVER["REQUEST_URI"];

    $work = explode('?', $CURRENT_PAGE);
    if ( count($work) > 1 )
      define( 'SYS_CURRENT_PARMS', $work[1]);
    else
      define( 'SYS_CURRENT_PARMS', '');
    define( 'SYS_CURRENT_URI'  , $work[0]);

    if (!defined('SYS_CURRENT_PARMS'))
      define('SYS_CURRENT_PARMS', $work[1]);
    $preArray = explode('&', SYS_CURRENT_PARMS);
    $buffer = explode( '.', $work[0] );
    if ( count($buffer) == 1 ) $buffer[1]='';

    //request type
    define('REQUEST_TYPE', ($buffer[1] != "" ?$buffer[1] : 'html'));

    $toparse  = substr($buffer[0], 1, strlen($buffer[0]) - 1);
    $URL = "";
    $URI_VARS = explode('/', $toparse);
    for ( $i=3; $i < count( $URI_VARS) ; $i++)
      $URL .= $URI_VARS[$i].'/';

    $URI_VARS = explode('/', $toparse);

    unset($work);
    unset($buffer);
    unset($toparse);

    array_shift($URI_VARS);

    $SYS_LANG = array_shift($URI_VARS);
    $SYS_SKIN = array_shift($URI_VARS);

    $SYS_COLLECTION = array_shift($URI_VARS);
    $SYS_TARGET     = array_shift($URI_VARS);

    //to enable more than 2 directories...in the methods structure
    $exit = 0;
    while ( count ( $URI_VARS ) > 0 && $exit == 0) {
      $SYS_TARGET .= '/' . array_shift($URI_VARS);
    }
    /* Fix to prevent use uxs skin outside siplified interface, 
     because that skin is not compatible with others interfaces*/
    if ($SYS_SKIN == 'uxs' && $SYS_COLLECTION !== 'home') {
      $SYS_SKIN = 'classic';
    }

    define("SYS_LANG", $SYS_LANG);
    define("SYS_SKIN", $SYS_SKIN);
    define('SYS_COLLECTION', $SYS_COLLECTION);
    define('SYS_TARGET', $SYS_TARGET);

    if ( $SYS_COLLECTION == 'js2' ) {
      print "ERROR"; die;
    }
  }

  function strip_slashes($vVar) {
    if (is_array($vVar)) {
      foreach($vVar as $sKey => $vValue) {
        if (is_array($vValue)) {
          G::strip_slashes($vVar[$sKey]);
        }
        else {
          $vVar[$sKey] = stripslashes($vVar[$sKey]);
        }
      }
    }
    else {
      $vVar = stripslashes($vVar);
    }

    return $vVar;
  }

  /** 
   * function to calculate the time used to render a page  
   */
  function logTimeByPage() 
  {
    if (!defined(PATH_DATA)) {
      return false;
    }
    
    $serverAddr = $_SERVER['SERVER_ADDR'];
    global $startingTime;
    $endTime =  microtime(true);
    $time = $endTime - $startingTime;
    $fpt= fopen ( PATH_DATA . 'log/time.log', 'a' );
    fwrite( $fpt, sprintf ( "%s.%03d %15s %s %5.3f %s\n", date('Y-m-d H:i:s'), $time, getenv('REMOTE_ADDR'), substr($serverAddr,-4), $time, $_SERVER['REQUEST_URI'] ));
    fclose( $fpt);
  }

  /**
   * streaming a big JS file with small js files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @return string
   */
  function streamCSSBigFile( $filename )
  {
      header('Content-Type: text/css');
         
      //First get Skin info
      $filenameParts = explode("-",$filename);
      $skinName      = $filenameParts[0];
      $skinVariant   = "skin";
      
      if(isset($filenameParts[1])) {
        $skinVariant = strtolower($filenameParts[1]);
      }

      if ($skinName == "jscolors") $skinName  = "classic";
      if ($skinName == "xmlcolors") $skinName = "classic";
      if ($skinName=="classic") {
        $configurationFile = G::ExpandPath( "skinEngine" ).'base'.PATH_SEP.'config.xml';
      }
      else {
        $configurationFile = G::ExpandPath( "skinEngine" ) . $skinName . PATH_SEP . 'config.xml';
        
        if (!is_file($configurationFile)) {
          $configurationFile = PATH_CUSTOM_SKINS . $skinName . PATH_SEP . 'config.xml';
        }
      }

      //Read Configuration File
      $xmlConfiguration = file_get_contents ( $configurationFile );
      $xmlConfigurationObj=G::xmlParser($xmlConfiguration);
      $baseSkinDirectory=dirname($configurationFile);
      $directorySize=G::getDirectorySize($baseSkinDirectory);
      $mtime=$directorySize['maxmtime'];
      
      

      //if userAgent (BROWSER) is MSIE we need special headers to avoid MSIE behaivor.
      //$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
      
      $gmt_mtime = gmdate("D, d M Y H:i:s", $mtime ) . " GMT";
      header('Pragma: cache');
      header('ETag: "' . md5 ($mtime . $filename ) . '"' );
      header("Last-Modified: " . $gmt_mtime );
      header('Cache-Control: public');
      header("Expires: " . gmdate("D, d M Y H:i:s", time () + 30*60*60*24 ) . " GMT"); //1 month
      //header("Expires: " . gmdate("D, d M Y H:i:s", time () + 60*60*24 ) . " GMT"); //1 day - tempor
      if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
          if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
              header('HTTP/1.1 304 Not Modified');
              exit();
          }
      }

      if ( isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
          if ( str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5( $mtime . $filename))  {
              header("HTTP/1.1 304 Not Modified");
              exit();
          }
      }

      $outputHeader = "/* Autogenerated CSS file by gulliver framework \n";
      $outputHeader .="   Skin: $filename\n";
      $outputHeader .="   Configuration: $configurationFile\n";
      $mtimeNow = date('U');
      $gmt_mtimeNow = gmdate("D, d M Y H:i:s", $mtimeNow ) . " GMT";
      $outputHeader .="   Date: $gmt_mtimeNow*/\n";
      $output ="";
      //Base files
      switch(strtolower($skinVariant)){
          case "extjs":
              //Base
              $baseCSSPath=PATH_SKIN_ENGINE."base". PATH_SEP."baseCss". PATH_SEP;
              $output .= file_get_contents ( $baseCSSPath . 'ext-all-notheme.css' );
              //$output .= file_get_contents ( $publicExtPath . 'ext-all.css' );

              //Classic Skin
              $extJsSkin = 'xtheme-gray';
              /*$publicExtPath = PATH_SKIN_ENGINE."base". PATH_SEP."css". PATH_SEP;
               $output .= file_get_contents ( $publicExtPath . $extJsSkin . '.css' );
               $output .= file_get_contents ( $publicExtPath . 'pmos-' . $extJsSkin . '.css' );
               */
              //adding the extend css for extjs-pmos
              //TODO: move this files to pmos-xthem..
              //$cssThemeExtensions = glob(PATH_TPL . "*/css/extjs-extend/{$extJsSkin}.css");
              //foreach($cssThemeExtensions as $cssThemeExtensionFile)
              //$helper->addFile($cssThemeExtensionFile);
              //$output .= file_get_contents ( $cssThemeExtensionFile );
              //                $classicCSSPath=PATH_SKIN_ENGINE."base". PATH_SEP."css". PATH_SEP;
              //                $output .= file_get_contents ( $classicCSSPath . 'sprite.css' );
              //$output .= file_get_contents ( $classicCSSPath . 'sprite_ie.css' );


              break;
          default:

              break;

      }



      //Get Browser Info
      $infoBrowser=G::browser_detection( 'full_assoc');
      $browserName=$infoBrowser['browser_working'];
      if(isset($infoBrowser[$browserName.'_data'])){
          if($infoBrowser[$browserName.'_data'][0]!=""){
              $browserName=$infoBrowser[$browserName.'_data'][0];
          }
      }
      //print "<h1>$browserName</h1>";
      //G::pr($infoBrowser);


      //Read Configuration File
      $xmlConfiguration = file_get_contents ( $configurationFile );
      $xmlConfigurationObj=G::xmlParser($xmlConfiguration);

      //G::pr($xmlConfigurationObj->result['skinConfiguration']['__CONTENT__']['cssFiles']['__CONTENT__'][$tempSkin]['__CONTENT__']    );
      $skinFilesArray=$xmlConfigurationObj->result['skinConfiguration']['__CONTENT__']['cssFiles']['__CONTENT__'][$skinVariant]['__CONTENT__']['cssFile'] ;
      //G::pr($skinFilesArray);
      foreach($skinFilesArray as $keyFile => $cssFileInfo){
          //G::pr($cssFileInfo);
          //TODO: Also review browser versions for restrictions
          $enabledBrowsers=explode(",",$cssFileInfo['__ATTRIBUTES__']['enabledBrowsers']);
          $disabledBrowsers=explode(",",$cssFileInfo['__ATTRIBUTES__']['disabledBrowsers']);

          if(((in_array($browserName, $enabledBrowsers))||(in_array('ALL', $enabledBrowsers)))&&(!(in_array($browserName, $disabledBrowsers)))){
              //G::pr($cssFileInfo['__ATTRIBUTES__']['file']);
              $output .= file_get_contents ( $baseSkinDirectory . PATH_SEP.'css'.PATH_SEP.$cssFileInfo['__ATTRIBUTES__']['file'] );
          }
      }
      
      //Remove comments..
      $regex = array(
"`^([\t\s]+)`ism"=>'',
"`^\/\*(.+?)\*\/`ism"=>"",
"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
);
$output = preg_replace(array_keys($regex),$regex,$output);
$output = $outputHeader.$output;
      
      return $output;
  }

  /**
   * streaming a big JS file with small js files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @param  boolean $download
   * @param  string $downloadFileName
   * @return string
   */
  function streamJSTranslationFile( $filename, $locale = 'en' )
  {
    header('Content-Type: text/javascript');

  	if (!G::LoadTranslationObject($locale)) {
      header('Cache-Control: no-cache');
      header('Pragma: no-cache');
      return;
    }

    global $translation;

    //if userAgent (BROWSER) is MSIE we need special headers to avoid MSIE behaivor.
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ( file_exists($filename) )
      $mtime = filemtime($filename);
    else
      $mtime = date('U');
    $gmt_mtime = gmdate("D, d M Y H:i:s", $mtime ) . " GMT";
    header('Pragma: cache');
    header('ETag: "' . md5 ($mtime . $filename ) . '"' );
    header("Last-Modified: " . $gmt_mtime );
    header('Cache-Control: public');
    header("Expires: " . gmdate("D, d M Y H:i:s", time () + 30*60*60*24 ) . " GMT"); //1 month
    if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
      if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
        header('HTTP/1.1 304 Not Modified');
        exit();
      }
    }

    if ( isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
      if ( str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5( $mtime . $filename))  {
        header("HTTP/1.1 304 Not Modified");
        exit();
      }
    }
    
    return JSMin::minify ( 'var TRANSLATIONS = ' . G::json_encode($translation) . ';' );
  }

  /**
   * streaming a file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @param  boolean $download
   * @param  string $downloadFileName
   * @return string
   */
  function streamFile( $file, $download = false, $downloadFileName = '' )
  {
    require_once (PATH_THIRDPARTY . 'jsmin/jsmin.php');
    $folderarray = explode ( '/', $file );
    $typearray = explode ( '.', basename( $file) );
    $typefile  = $typearray[ count($typearray) -1 ];
    $filename  = $file;
    
    //trick to generate the translation.language.js file , merging two files and then minified the content.
    if ( strtolower ($typefile ) == 'js' && $typearray[0] == 'translation' ) {
    	$output = G::streamJSTranslationFile ($filename, $typearray[1]);
      print $output;
      return;
    }

    //trick to generate the big css file for ext style .
    if ( strtolower ($typefile ) == 'css' && $folderarray[count($folderarray)-2] == 'css' ) {
    	$output = G::streamCSSBigFile( $typearray[0] );
      print $output;
      return;
    }

    if ( file_exists ( $filename ) ) {
      switch ( strtolower ($typefile ) ) {
        case 'swf' :
          G::sendHeaders ( $filename , 'application/x-shockwave-flash', $download, $downloadFileName ); break;
        case 'js' :
          G::sendHeaders ( $filename , 'text/javascript', $download, $downloadFileName ); break;
        case 'htm' :
        case 'html' :
          G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
        case 'htc' :
          G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName ); break;
        case 'json' :
          G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName ); break;
        case 'gif' :
          G::sendHeaders ( $filename , 'image/gif', $download, $downloadFileName ); break;
        case 'png' :
          G::sendHeaders ( $filename , 'image/png', $download, $downloadFileName ); break;
        case 'jpg' :
          G::sendHeaders ( $filename , 'image/jpg', $download, $downloadFileName ); break;
        case 'css' :
          G::sendHeaders ( $filename , 'text/css', $download, $downloadFileName ); break;
        case 'css' :
          G::sendHeaders ( $filename , 'text/css', $download, $downloadFileName ); break;
        case 'xml' :
          G::sendHeaders ( $filename , 'text/xml', $download, $downloadFileName ); break;
        case 'txt' :
          G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
        case 'doc' :
        case 'pdf' :
        case 'pm'  :
        case 'po'  :
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
        case 'php' :
          if ($download) {
            G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName );
          }
          else {
            require_once( $filename  );
            return;
          }
          break;
        case 'tar':
          G::sendHeaders ( $filename , 'application/x-tar', $download, $downloadFileName ); break;
        default :
          //throw new Exception ( "Unknown type of file '$file'. " );
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
          break;
      }
    }
    else {
      if( strpos($file, 'gulliver') !== false ){
        list($path, $filename) = explode('gulliver', $file);
      }
      
      $_SESSION['phpFileNotFound'] = $file;
      G::header("location: /errors/error404.php?l=".$_SERVER['REQUEST_URI']);
    }

    switch ( strtolower($typefile ) ) {
      case "js" :
        $paths  = explode ( '/', $filename);
        $jsName = $paths[ count ($paths) -1 ];
        $output = '';
        $pathJs = PATH_GULLIVER_HOME . PATH_SEP . 'js' . PATH_SEP;
        switch ( $jsName ) {
          case 'draw2d.js' :
            $cachePath = PATH_C . 'ExtJs' . PATH_SEP;
            $checksum = G::getCheckSum(array(
              $pathJs . 'ext/wz_jsgraphics.js',
              $pathJs . 'ext/mootools.js',
              $pathJs . 'ext/moocanvas.js'
            ));
            
            $cf = $cachePath . "ext-draw2d-cache.$checksum.js"; 
            $cfStored = G::getCacheFileNameByPattern($cachePath, 'ext-draw2d-cache.*.js');
            //error_log("draw2d.js ".$checksum ."==". $cfStored['checksum']);
            if(is_file($cfStored['filename']) && $checksum == $cfStored['checksum']) {
              $output = file_get_contents($cf);
            } else {
              if (is_file($cfStored['filename']))
                @unlink($cfStored['filename']);
              
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/wz_jsgraphics.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/mootools.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/moocanvas.js' ) );
              $output .= file_get_contents ($pathJs . 'ext/draw2d.js'); //already minified
              file_put_contents($cf, $output);
              //error_log("draw2d.js writting ".$cf);
            }
            break;
          case 'ext-all.js' :
            $cachePath = PATH_C . 'ExtJs' . PATH_SEP;
            $checksum = G::getCheckSum(array(
              $pathJs . 'ext/pmos-common.js',
              $pathJs . 'ext/ux/miframe.js',
              $pathJs . 'ext/ux.locationbar/Ext.ux.LocationBar.js',
              $pathJs . 'ext/ux.statusbar/ext-statusbar.js',
              $pathJs . 'ext/ux.treefilterx/Ext.ux.tree.TreeFilterX.js'
            ));
            
            $cfStored = G::getCacheFileNameByPattern($cachePath, 'ext-all-cache.*.js');
            $cf = PATH_C . 'ExtJs' . PATH_SEP . "ext-all-cache.$checksum.js";
            if(is_file($cfStored['filename']) && $checksum == $cfStored['checksum']) {
              $output = file_get_contents($cf);
            } else {
              if (is_file($cfStored['filename']))
                @unlink($cfStored['filename']);
              
              $output .= file_get_contents ( $pathJs . 'ext/ext-all.js' ); //already minified
              $output .= file_get_contents ( $pathJs . 'ext/ux/ux-all.js' ); //already minified
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/pmos-common.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/ux/miframe.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/ux.locationbar/Ext.ux.LocationBar.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/ux.statusbar/ext-statusbar.js' ) );
              $output .= JSMin::minify ( file_get_contents ( $pathJs . 'ext/ux.treefilterx/Ext.ux.tree.TreeFilterX.js' ) );
              
              file_put_contents($cf, $output);
            }
            break;

          case 'maborak.js' :
            $oHeadPublisher =& headPublisher::getSingleton();
            foreach ( $oHeadPublisher->maborakFiles as $fileJS ) {
              //$output .= JSMin::minify ( file_get_contents ( $fileJS ) );
              $output .= G::trimSourceCodeFile ($fileJS );
            }
            break;
          case 'maborak.loader.js':
            $oHeadPublisher =& headPublisher::getSingleton();
            foreach ( $oHeadPublisher->maborakLoaderFiles as $fileJS ) {
              $output .= JSMin::minify ( file_get_contents ( $fileJS ) );
              //$output .= G::trimSourceCodeFile ($fileJS );
            }
            break;
          default :
            $output = file_get_contents ( $filename ) ;
            //$output = JSMin::minify ( file_get_contents ( $filename ) );
            //$output = G::trimSourceCodeFile ($filename );
        }
        print $output;
        break;
      case 'css' :
        //$output = JSMin::minify ( file_get_contents ( $filename) );
        print G::trimSourceCodeFile ($filename );
        break;
      default :
        @readfile($filename);
    }
  }

  /**
   * trimSourceCodeFile
   *
   * @param  string $filename
   *
   * @return string $output
   */
  function trimSourceCodeFile ( $filename ) {
    $handle    = fopen ($filename, "r");
    $lastChar  = '';
    $firstChar = '';
    $content   = '';
    $line      = '';

    //no optimizing code
    if ($handle) {
      while (!feof($handle)) {
        //$line = trim( fgets($handle, 16096) ) . "\n" ;
        $line = fgets($handle, 16096);
        $content .= $line;
      }
      fclose($handle);
    }
    return $content;
    //end NO optimizing code
    //begin optimizing code
    /*
     if ($handle) {
     while (!feof($handle)) {
     $lastChar = ( strlen ( $line ) > 5 ) ? $line[strlen($line)-1] : '';

     $line = trim( fgets($handle, 16096) ) ;
     if ( substr ($line,0,2 ) == '//' )  $line = '';
     $firstChar = ( strlen ( $line ) > 6 ) ? strtolower($line[0]) : '';
     if ( ord( $firstChar ) > 96 && ord($firstChar) < 122 && $lastChar == ';')
     $content .= '';
     else
     $content .= "\n";
     //          $content .= '('.$firstChar . $lastChar . ord( $firstChar ).'-'. ord( $lastChar ) . ")\n";

     $content .= $line;
     }
     fclose($handle);
     }
     */
    //end optimizing code

    $index  = 0;
    $output = '';
    while ( $index < strlen ($content) ) {
      $car = $content[$index];
      $index++;
      if ( $car == '/' && isset($content[$index]) && $content[$index] == '*' ) {
        $endComment = false;
        $index ++;
        while ( $endComment == false && $index < strlen ($content) ) {
          if ($content[$index] == '*' && isset($content[$index+1]) && $content[$index+1] == '/' ) {
            $endComment = true; $index ++;
          }
          $index ++;
        }
        $car = '';
      }
      $output .= $car;
    }
    return $output;
  }

  /**
   * sendHeaders
   *
   * @param  string  $filename
   * @param  string  $contentType default value ''
   * @param  boolean $download default value false
   * @param  string  $downloadFileName default value ''
   *
   * @return void
   */
  function sendHeaders ( $filename , $contentType = '', $download = false, $downloadFileName = '' )
  {
    if ($download) {
      if ($downloadFileName == '') {
        $aAux = explode('/', $filename);
        $downloadFileName = $aAux[count($aAux) - 1];
      }
      header('Content-Disposition: attachment; filename="' . $downloadFileName . '"');
    }
    header('Content-Type: ' . $contentType);

    //if userAgent (BROWSER) is MSIE we need special headers to avoid MSIE behaivor.
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ( preg_match("/msie/i", $userAgent)) {
    //if ( ereg("msie", $userAgent)) {
      header('Pragma: cache');

      $mtime = filemtime($filename);
      $gmt_mtime = gmdate("D, d M Y H:i:s", $mtime ) . " GMT";
      header('ETag: "' . md5 ($mtime . $filename ) . '"' );
      header("Last-Modified: " . $gmt_mtime );
      header('Cache-Control: public');
      header("Expires: " . gmdate("D, d M Y H:i:s", time () + 60*10 ) . " GMT"); //ten minutes
      return;
    }
    
    if (!$download) {

      header('Pragma: cache');

      if ( file_exists($filename) )
        $mtime = filemtime($filename);
      else
        $mtime = date('U');
      $gmt_mtime = gmdate("D, d M Y H:i:s", $mtime ) . " GMT";
      header('ETag: "' . md5 ($mtime . $filename ) . '"' );
      header("Last-Modified: " . $gmt_mtime );
      header('Cache-Control: public');
      header("Expires: " . gmdate("D, d M Y H:i:s", time () + 90*60*60*24 ) . " GMT");
      if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
          header('HTTP/1.1 304 Not Modified');
          exit();
        }
      }

      if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        if ( str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5( $mtime . $filename))  {
          header("HTTP/1.1 304 Not Modified");
          exit();
        }
      }
    }
  }

  /**
   * Transform a public URL into a local path.
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string $url
   * @param  string $corvertionTable
   * @param  string $realPath = local path
   * @return boolean
   */
  function virtualURI( $url , $convertionTable , &$realPath )
  {
    foreach($convertionTable as $urlPattern => $localPath ) {
      //      $urlPattern = addcslashes( $urlPattern , '/');
      $urlPattern = addcslashes( $urlPattern , './');
      $urlPattern = '/^' . str_replace(
      array('*','?'),
      array('.*','.?'),
      $urlPattern) . '$/';
      if (preg_match($urlPattern , $url, $match)) {
        if ($localPath === FALSE) {
          $realPath = $url;
          return false;
        }
        if ( $localPath != 'jsMethod' )
          $realPath = $localPath . $match[1];
        else
          $realPath = $localPath;
        return true;
      }
    }
    $realPath = $url;
    return false;
  }

  /**
   * Create an encrypted unique identifier based on $id and the selected scope id.
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string $scope
   * @param  string $id
   * @return string
   */
  function createUID( $scope, $id )
  {
    $e = $scope . $id;
    $e=G::encrypt( $e , URL_KEY );
    $e=str_replace(array('+','/','='),array('__','_','___'),base64_encode($e));
    return $e;
  }
  
  /**
   * (Create an encrypted unique identificator based on $id and the selected scope id.) ^-1
   * getUIDName
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string $id
   * @param  string $scope
   * @return string
   */
  function getUIDName( $uid , $scope = '' )
  {
    $e=str_replace(array('=','+','/'),array('___','__','_'),$uid);
    $e=base64_decode($e);
    $e=G::decrypt( $e , URL_KEY );
    $e=substr( $e , strlen($scope) );
    return $e;
  }
  
  /* formatNumber
   *
   * @author David Callizaya <calidavidx21@yahoo.com.ar>
   * @param  int/string $num
   * @return string number
   */
  function formatNumber($num, $language='latin')
  {
    switch($language)
    {
      default:
        $snum=$num;
    }
    return $snum;
  }
  
  /* Returns a date formatted according to the given format string
   * @author David Callizaya <calidavidx21@hotmail.com>
   * @param string $format     The format of the outputted date string
   * @param string $datetime   Date in the format YYYY-MM-DD HH:MM:SS
   */
  function formatDate($datetime, $format='Y-m-d', $lang='')
  {
    if ($lang==='') $lang=defined(SYS_LANG)?SYS_LANG:'en';
    $aux     = explode (' ', $datetime);  //para dividir la fecha del dia
    $date    = explode ('-', isset ( $aux[0] ) ? $aux[0] : '00-00-00' );   //para obtener los dias, el mes, y el año.
    $time    = explode (':', isset ( $aux[1] ) ? $aux[1] : '00:00:00' );   //para obtener las horas, minutos, segundos.
    $date[0] = (int)((isset($date[0]))?$date[0]:'0');
    $date[1] = (int)((isset($date[1]))?$date[1]:'0');
    $date[2] = (int)((isset($date[2]))?$date[2]:'0');
    $time[0] = (int)((isset($time[0]))?$time[0]:'0');
    $time[1] = (int)((isset($time[1]))?$time[1]:'0');
    $time[2] = (int)((isset($time[2]))?$time[2]:'0');
    // Spanish months
    $ARR_MONTHS['es'] = array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    // English months
    $ARR_MONTHS['en'] = array("January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");
    
    
    // Spanish days
    $ARR_WEEKDAYS['es'] = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
    // English days
    $ARR_WEEKDAYS['en'] = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    
    

    if ($lang=='fa')
      $number='persian';
    else
      $number='latin';
    $d = '0'.$date[2];$d=G::formatNumber(substr($d,strlen($d)-2,2),$number);
    $j = G::formatNumber($date[2],$number);
    $F = isset ( $ARR_MONTHS[$lang][$date[1]-1] ) ? $ARR_MONTHS[$lang][$date[1]-1] : '';
    $m = '0'.$date[1];$m=G::formatNumber(substr($m,strlen($m)-2,2),$number);
    $n = G::formatNumber($date[1],$number);
    $y = G::formatNumber(substr($date[0],strlen($date[0])-2,2),$number);
    $Y = '0000'.$date[0];$Y=G::formatNumber(substr($Y,strlen($Y)-4,4),$number);
    $g = ($time[0] % 12);if ($g===0)$g=12;
    $G = $time[0];
    $h = '0'.$g;$h=G::formatNumber(substr($h,strlen($h)-2,2),$number);
    $H = '0'.$G;$H=G::formatNumber(substr($H,strlen($H)-2,2),$number);
    $i = '0'.$time[1];$i=G::formatNumber(substr($i,strlen($i)-2,2),$number);
    $s = '0'.$time[2];$s=G::formatNumber(substr($s,strlen($s)-2,2),$number);
    $names=array('d','j','F','m','n','y','Y','g','G','h','H','i','s');
    $values=array($d, $j, $F, $m, $n, $y, $Y, $g, $G, $h, $H, $i, $s);
    $_formatedDate = str_replace( $names, $values, $format );
    return $_formatedDate;
  }

  /**
   * getformatedDate
   *
   * @param  date   $date
   * @param  string $format default value 'yyyy-mm-dd',
   * @param  string $lang default value ''
   *
   * @return string $ret
   */
  function getformatedDate($date, $format = 'yyyy-mm-dd', $lang = '')
  {
    /********************************************************************************************************
     * if the year is 2008 and the format is yy  then -> 08
     * if the year is 2008 and the format is yyyy  then -> 2008
     *
     * if the month is 05 and the format is mm  then -> 05
     * if the month is 05 and the format is m and the month is less than 10 then -> 5 else digit normal
     * if the month is 05 and the format is MM or M then -> May
     *
     * if the day is 5 and the format is dd  then -> 05
     * if the day is 5 and the format is d and the day is less than 10 then -> 5 else digit normal
     * if the day is 5 and the format is DD or D then -> five
     *********************************************************************************************************/

    //scape the literal
    switch($lang) {
      case 'es':
        $format = str_replace(' de ', '[of]', $format);
        break;
    }

    //first we must formatted the string
    $format  = str_replace('yyyy', '{YEAR}', $format);
    $format  = str_replace('yy', '{year}', $format);

    $format  = str_replace('mm', '{YONTH}', $format);
    $format  = str_replace('m', '{month}', $format);
    $format  = str_replace('M', '{XONTH}', $format);

    $format  = str_replace('dd', '{DAY}', $format);
    $format  = str_replace('d', '{day}', $format);

    $format  = str_replace('h', '{h}', $format);
    $format  = str_replace('i', '{i}', $format);
    $format  = str_replace('s', '{s}', $format);


    if ($lang==='') $lang=defined(SYS_LANG)?SYS_LANG:'en';

    $aux     = explode (' ', $date);  //para dividir la fecha del dia
    $date    = explode ('-', isset ( $aux[0] ) ? $aux[0] : '00-00-00' );   //para obtener los dias, el mes, y el año.
    $time    = explode (':', isset ( $aux[1] ) ? $aux[1] : '00:00:00' );   //para obtener las horas, minutos, segundos.

    $year    = (int)((isset($date[0]))?$date[0]:'0'); //year
    $month   = (int)((isset($date[1]))?$date[1]:'0'); //month
    $day     = (int)((isset($date[2]))?$date[2]:'0'); //day

    $h = isset($time[0])? $time[0]: '00'; //hour
    $i = isset($time[1])? $time[1]: '00'; //minute
    $s = isset($time[2])? $time[2]: '00'; //second

    $MONTHS  = Array();
    for($i=1; $i<=12; $i++){
      $MONTHS[$i] =   G::LoadTranslation("ID_MONTH_$i", $lang);
    }

    $d  = (int)$day;
    $dd = G::complete_field($day, 2, 1);

    //missing D

    $M      = $MONTHS[$month];
    $m      = (int)$month;
    $mm     = G::complete_field($month, 2, 1);

    $yy     = substr($year,strlen($year)-2,2);
    $yyyy   = $year;

    $names  = array('{day}', '{DAY}', '{month}', '{YONTH}', '{XONTH}', '{year}', '{YEAR}', '{h}', '{i}', '{s}');
    $values = array($d, $dd, $m, $mm, $M, $yy, $yyyy, $h, $i, $s);

    $ret    = str_replace( $names, $values, $format );

    //recovering the original literal
    switch($lang){
      case 'es':
        $ret = str_replace('[of]', ' de ', $ret);
        break;
    }

    return $ret;
  }

  /**
   *  By <erik@colosa.com>
   *  Here's a little wrapper for array_diff - I found myself needing
   *  to iterate through the edited array, and I didn't need to original keys for anything.
   */
  function arrayDiff($array1, $array2) {
    // This wrapper for array_diff rekeys the array returned
    $valid_array = array_diff($array1,$array2);

    // reinstantiate $array1 variable
    $array1 = array();

    // loop through the validated array and move elements to $array1
    // this is necessary because the array_diff function returns arrays that retain their original keys
    foreach ($valid_array as $valid){
      $array1[] = $valid;
    }
    return $array1;
  }

  /**
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @name complete_field($string, $lenght, $type={1:number/2:string/3:float})
   */
  function complete_field($campo, $long, $tipo)
  {
    $campo = trim($campo);
    switch($tipo)
    {
      case 1: //number
        $long = $long-strlen($campo);
        for($i=1; $i<=$long; $i++) {
          $campo = "0".$campo;
        }
        break;

      case 2: //string
        $long = $long-strlen($campo);
        for($i=1; $i<=$long; $i++) {
          $campo = " ".$campo;
        }
        break;

      case 3: //float
        if($campo!="0") {
          $vals = explode(".",$long);
          $ints = $vals[0];

          $decs = $vals[1];

          $valscampo = explode(".",$campo);

          $intscampo = $valscampo[0];
          $decscampo = $valscampo[1];

          $ints = $ints - strlen($intscampo);

          for($i=1; $i<=$ints; $i++) {
            $intscampo = "0".$intscampo;
          }

          //los decimales pueden ser 0 uno o dos
          $decs = $decs - strlen($decscampo);
          for($i=1; $i<=$decs; $i++) {
            $decscampo = $decscampo."0";
          }

          $campo = $intscampo.".".$decscampo;
        } else {
          $vals  = explode(".",$long);
          $ints  = $vals[0];
          $decs  = $vals[1];

          $campo = "";
          for($i=1; $i<=$ints; $i++) {
            $campo = "0".$campo;
          }
          $campod = "";
          for($i=1; $i<=$decs; $i++) {
            $campod = "0".$campod;
          }

          $campo = $campo.".".$campod;
        }
        break;
    }
    return $campo;
  }

  /* Escapes special characters in a string for use in a SQL statement
   * @author David Callizaya <calidavidx21@hotmail.com>
   * @param string $sqlString  The string to be escaped
   * @param string $DBEngine   Target DBMS
   */
  function sqlEscape( $sqlString, $DBEngine = DB_ADAPTER )
  {
    $DBEngine = DB_ADAPTER;
    switch($DBEngine){
      case 'mysql':
        $con = Propel::getConnection('workflow') ;
        return mysql_real_escape_string(stripslashes($sqlString), $con->getResource() );
      case 'myxml':
        $sqlString = str_replace('"', '""', $sqlString);
        return str_replace("'", "''", $sqlString);
        //return str_replace(array('"',"'"),array('""',"''"),stripslashes($sqlString));
      default:
        return addslashes(stripslashes($sqlString));
    }
  }
  
  /**
   * Function MySQLSintaxis
   * @access public
   * @return Boolean
   **/
  function MySQLSintaxis()
  {
    $DBEngine = DB_ADAPTER;
    switch($DBEngine){
      case 'mysql' :
        return TRUE;
        break;
      case 'mssql' :
      default:
        return FALSE;
        break;
    }
  }
  /* Returns a sql string with @@parameters replaced with its values defined
   * in array $result using the next notation:
   * NOTATION:
   *     @@  Quoted parameter acording to the SYSTEM's Database
   *     @Q  Double quoted parameter \\  \"
   *     @q  Single quoted parameter \\  \'
   *     @%  URL string
   *     @#  Non-quoted parameter
   *     @!  Evaluate string : Replace the parameters in value and then in the sql string
   *     @fn()  Evaluate string with the function "fn"
   * @author David Callizaya <calidavidx21@hotmail.com>
   */
  function replaceDataField( $sqlString, $result, $DBEngine = 'mysql' )
  {
    if (!is_array($result)) {
      $result = array();
    }
    $result      = $result + G::getSystemConstants();
    $__textoEval = "";
    $u           = 0;
    //$count=preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
    $count = preg_match_all('/\@(?:([\@\%\#\=\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
    if ($count) {
      for($r=0;$r<$count;$r++) {
        if (!isset($result[$match[2][$r][0]]))
          $result[$match[2][$r][0]] = '';
        if (!is_array($result[$match[2][$r][0]])) {
          $__textoEval.=substr($sqlString,$u,$match[0][$r][1]-$u);
          $u = $match[0][$r][1]+strlen($match[0][$r][0]);
          //Mysql quotes scape
          if (($match[1][$r][0]=='@')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.="\"". G::sqlEscape($result[$match[2][$r][0]],$DBEngine) ."\"";continue;
          }
          //URL encode
          if (($match[1][$r][0]=='%')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.=urlencode($result[$match[2][$r][0]]);continue;
          }
          //Double quoted parameter
          if (($match[1][$r][0]=='Q')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.='"'.addcslashes($result[$match[2][$r][0]],'\\"').'"';continue;
          }
          //Single quoted parameter
          if (($match[1][$r][0]=='q')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.="'".addcslashes($result[$match[2][$r][0]],'\\\'')."'";continue;
          }
          //Substring (Sub replaceDataField)
          if (($match[1][$r][0]=='!')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.=G::replaceDataField($result[$match[2][$r][0]],$result);continue;
          }
          //Call function
          if (($match[1][$r][0]==='')&&($match[2][$r][0]==='')&&($match[3][$r][0]!=='')) {
            eval('$strAux = ' . $match[3][$r][0] . '(\'' . addcslashes(G::replaceDataField(stripslashes($match[4][$r][0]),$result),'\\\'') . '\');');
            
            if ($match[3][$r][0] == "G::LoadTranslation") {
              $arraySearch  = array("'");
              $arrayReplace = array("\\'");
            
              $strAux = str_replace($arraySearch, $arrayReplace, $strAux);
            }
            
            $__textoEval .= $strAux; continue;
          }
          //Non-quoted
          if (($match[1][$r][0]=='#')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.=G::replaceDataField($result[$match[2][$r][0]],$result);continue;
          }
          //Non-quoted =
          if (($match[1][$r][0]=='=')&&(isset($result[$match[2][$r][0]]))) {
            $__textoEval.=G::replaceDataField($result[$match[2][$r][0]],$result);continue;
          }
        }
      }
    }
    $__textoEval.=substr($sqlString,$u);
    return $__textoEval;
  }

  /* Load strings from a XMLFile.
   * @author David Callizaya <davidsantos@colosa.com>
   * @parameter $languageFile An xml language file.
   * @parameter $languageId   (es|en|...).
   * @parameter $forceParse   Force to read and parse the xml file.
   */
  function loadLanguageFile ( $filename , $languageId = '', $forceParse = false )
  {
    global $arrayXmlMessages;
    if ($languageId==='') $languageId = defined('SYS_LANG') ? SYS_LANG : 'en';
    $languageFile = basename( $filename , '.xml' );
    $cacheFile = substr( $filename , 0 ,-3 ) . $languageId;
    if (($forceParse) || (!file_exists($cacheFile)) ||
        ( filemtime($filename) > filemtime($cacheFile))
    //|| ( filemtime(__FILE__) > filemtime($cacheFile))
    ) {
      $languageDocument = new Xml_document();
      $languageDocument->parseXmlFile( $filename );
      if (!is_array($arrayXmlMessages)) $arrayXmlMessages = array();
      $arrayXmlMessages[ $languageFile ] = array();
      for($r=0 ; $r < sizeof($languageDocument->children[0]->children) ; $r++ ) {
        $n = $languageDocument->children[0]->children[$r]->findNode($languageId);
        if ($n) {
          $k = $languageDocument->children[0]->children[$r]->name;
          $arrayXmlMessages[ $languageFile ][ $k ] = $n->value;
        }
      }
      $f = fopen( $cacheFile , 'w');
      fwrite( $f , "<?php\n" );
      fwrite( $f , '$arrayXmlMessages[\'' . $languageFile . '\']=' . 'unserialize(\'' .
              addcslashes( serialize ( $arrayXmlMessages[ $languageFile ] ), '\\\'' ) .
                  "');\n");
      fwrite( $f , "?>" );
      fclose( $f );
    } else {
      require( $cacheFile );
    }
  }
  /* Funcion auxiliar Temporal:
   *   Registra en la base de datos los labels xml usados en el sistema
   * @author David Callizaya <calidavidx21@hotmail.com>
   */
  function registerLabel( $id , $label )
  {
    return 1;
    $dbc = new DBConnection();
    $ses = new DBSession($dbc);
    $ses->Execute(G::replaceDataField(
      'REPLACE INTO `TRANSLATION` (`TRN_CATEGORY`, `TRN_ID`, `TRN_LANG`, `TRN_VALUE`) VALUES
      ("LABEL", @@ID, "'.SYS_LANG.'", @@LABEL);',array('ID'=>$id,'LABEL'=>($label !== null ? $label : ''))));
  }
  /**
   * Function LoadMenuXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @return string
   */
  function LoadMenuXml( $msgID )
  {
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages['menus']))
    G::loadLanguageFile( G::ExpandPath('content') . 'languages/menus.xml' );
    G::registerLabel($msgID,$arrayXmlMessages['menus'][$msgID]);
    return $arrayXmlMessages['menus'][$msgID];
  }
  /**
   * Function SendMessageXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string strType
   * @parameter string file
   * @return string
   */
  function SendMessageXml( $msgID, $strType , $file="labels")
  {
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages[$file]))
    G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    G::registerLabel($msgID,$arrayXmlMessages[$file][$msgID]);
    $_SESSION['G_MESSAGE'] = nl2br ($arrayXmlMessages[$file][$msgID]);
  }
  
  /**
   * SendTemporalMessage
   *
   * @param  string  $msgID
   * @param  string  $strType
   * @param  string  $sType default value 'LABEL'
   * @param  date    $time default value null
   * @param  integer $width default value null
   * @param  string  $customLabels default value null
   *
   * @return void
   */
  function SendTemporalMessage($msgID, $strType, $sType='LABEL', $time=null, $width=null, $customLabels= null)
  {
    if( isset($width) ){
      $_SESSION['G_MESSAGE_WIDTH'] = $width;
    }
    if( isset($time) ){
      $_SESSION['G_MESSAGE_TIME'] = $time;
    }
    switch(strtolower($sType)){
      case 'label':
      case 'labels':
        $_SESSION['G_MESSAGE_TYPE'] = $strType;
        $_SESSION['G_MESSAGE'] = nl2br(G::LoadTranslation($msgID));
        break;
      case 'string':
        $_SESSION['G_MESSAGE_TYPE'] = $strType;
        $_SESSION['G_MESSAGE'] = nl2br($msgID);
        break;
    }
    if ( $customLabels != null ) {
      $message = $_SESSION['G_MESSAGE'];
      foreach ( $customLabels as $key=>$val ) {
        $message = str_replace ( '{' . nl2br($key) . '}' , nl2br($val), $message );
      }
      $_SESSION['G_MESSAGE'] = $message;
    }
  }

  /**
   * SendMessage
   *
   * @param  string $msgID
   * @param  string $strType
   * @param  string $file default value "labels"
   *
   * @return void
   */
  function SendMessage( $msgID, $strType , $file="labels")
  {
    global $arrayXmlMessages;
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    $_SESSION['G_MESSAGE']      = nl2br (G::LoadTranslation($msgID));
  }

  /**
   * SendMessageText
   * Just put the $text in the message text
   *
   * @param  string $text
   * @param  string $strType
   *
   * @return void
   */
  function SendMessageText( $text, $strType)
  {
    global $arrayXmlMessages;
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    $_SESSION['G_MESSAGE']      = nl2br ( $text );
  }

  /**
   * Render message from XML file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @return void
   */
  function LoadMessage( $msgID, $file = "messages" )
  {
    global $_SESSION;
    global $arrayXmlMessages;

    if ( !is_array ($arrayXmlMessages) )
    $arrayXmlMessages = G::LoadArrayFile( G::ExpandPath( 'content' ) . $file . "." . SYS_LANG );

    $aux = $arrayXmlMessages[$msgID];
    $msg = "";
    for ($i = 0; $i < strlen($aux); $i++) {
      if ( $aux[$i] == "$") {
        $token = ""; $i++;
        while ($i < strlen ($aux) && $aux[$i]!=" " && $aux[$i]!="."  && $aux[$i]!="'" && $aux[$i]!='"')
          $token.= $aux[$i++];
        eval ( "\$msg.= \$_SESSION['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return $msg;
  }
  /**
   * Function LoadXmlLabel
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string file
   * @parameter string msgID
   * @return string
   */
  function LoadXmlLabel( $msgID , $file = 'labels' )
  {
    return 'xxxxxx';
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages[$file]))
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    G::registerLabel($msgID,$arrayXmlMessages[$file][$msgID]);
    return $arrayXmlMessages[$file][$msgID];
  }
  /**
   * Function LoadMessageXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string file
   * @return string
   */
  function LoadMessageXml( $msgID , $file ='labels' )
  {
    global $arrayXmlMessages;
    if ( !isset($arrayXmlMessages[$file]) )
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    if ( isset($arrayXmlMessages[$file][$msgID]) ) {
      G::registerLabel( $msgID, $arrayXmlMessages[$file][$msgID] );
      return $arrayXmlMessages[$file][$msgID];
    }
    else {
      G::registerLabel($msgID,'');
      return NULL;
    }
  }
  /**
   * Function LoadTranslationObject
   * It generates a global Translation variable that will be used in all the system. Per script
   * @author Hugo Loza. <hugo@colosa.com>
   * @access public
   * @parameter string lang
   * @return void
   */
  function LoadTranslationObject($lang = SYS_LANG){
    $defaultTranslations = Array();
    $foreignTranslations = Array();
    
    //if the default translations table doesn't exist we can't proceed
    if( ! is_file(PATH_LANGUAGECONT . 'translation.en') )
      return NULL;
    
    //load the translations table
    require_once ( PATH_LANGUAGECONT . 'translation.en' );
    $defaultTranslations = $translation;
    
    //if some foreign language was requested and its translation file exists
    if( $lang != 'en' && file_exists(PATH_LANGUAGECONT . 'translation.' . $lang) ){
      require_once ( PATH_LANGUAGECONT . 'translation.' . $lang ); //load the foreign translations table
      $foreignTranslations = $translation;
    }
    
    global $translation;
    if( defined("SHOW_UNTRANSLATED_AS_TAG") && SHOW_UNTRANSLATED_AS_TAG != 0 )
      $translation = $foreignTranslations;
    else
      $translation = array_merge($defaultTranslations, $foreignTranslations);
    
    return true;
  }
  
  /**
   * Function LoadTranslation
   * @author Aldo Mauricio Veliz Valenzuela. <mauricio@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string file
   * @parameter array data // erik: associative array within data input to replace for formatted string i.e "any messsage {replaced_label} that contains a replace label"
   * @return string
   */
  function LoadTranslation($msgID, $lang = SYS_LANG, $data = null)
  {
    global $translation;

    // if the second parameter $lang is an array does mean it was especified to use as data
    if (is_array($lang)) {
      $data = $lang;
      $lang = SYS_LANG;
    }
    
    if ( isset ( $translation[$msgID] ) ){
      $translationString = preg_replace("[\n|\r|\n\r]", ' ', $translation[$msgID]);
    
      if( isset($data) && is_array($data) ) {
        foreach($data as $label=>$value) {
          $translationString = str_replace('{'.$label.'}', $value, $translationString);
        }
      }
      
      return $translationString;
    } else {
      if( defined("UNTRANSLATED_MARK") ) {
        $untranslatedMark = strip_tags(UNTRANSLATED_MARK);
      } else {
        $untranslatedMark = "**";
      }
      return $untranslatedMark . $msgID . $untranslatedMark;
    }

  }

  /**
   * Function getTranslations
   * @author Erik Amaru O. <erik@colosa.com>
   * @access public
   * @parameter array msgIDs
   * @parameter string file
   * @return string
   */
  function getTranslations($msgIDs , $lang = SYS_LANG)
  {
    if ( ! is_array($msgIDs) ) return null;

    $translations = Array();
    foreach( $msgIDs as $mID ) {
      $translations[$mID] = self::LoadTranslation($mID , $lang);
    }
    
    return $translations;
  }
  /**
   * Load an array File Content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @return void
   */
  function LoadArrayFile( $strFile = '' )
  {
    $res = NULL;
    if ( $strFile != '' )
    {
      $src = file( $strFile );
      if( is_array( $src ) )
      {
        foreach( $src as $key => $val )
        {
          $res[$key] = trim( $val );
        }
      }
    }
    unset( $src );
    return $res;
  }

  /**
   * Expand an uri based in the current URI
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $methodPage the method directory and the page
   * @return the expanded uri, later, will encryt the uri...
   */
  function expandUri ( $methodPage ) {
    $uri    = explode ( '/', getenv ( 'REQUEST_URI' ) );
    $sw     = 0;
    $newUri = '';
    if ( !defined ( 'SYS_SKIN' ) ) {
      for ( $i = 0; $i < count( $uri) ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    else {
      for ( $i =0; $i < 4 ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    $newUri .= $methodPage;
    return $newUri;
  }

  /**
   * Forces login for generic applications
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $userid
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function genericForceLogin( $permission , $urlNoAccess, $urlLogin = 'login/login' )  {
    global $RBAC;

    //the session is expired, go to login page,
    //the login page is login/login.html
    if ( ! isset ( $_SESSION ) ) {
      header ( 'location: ' . G::expandUri ( $urlLogin ) );
      die ();
    }

    //$permission is an array, we'll verify all permission to allow access.
    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;

    $sw = 0;
    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
    }

    //you don't have access to this page
    if ($sw == 0) {
      header ( 'location: ' . G::expandUri ( $urlNoAccess ) );
      die;
    }
  }
  
  /**
   * capitalize
   *
   * @param  string $string
   *
   * @return string $string
   */
  function capitalize($string)
  {
    $capitalized = '';
    $singleWords = preg_split( "/\W+/m" , $string );
    for($r=0; $r < sizeof($singleWords) ; $r++ ) {
      @$string = substr($string , 0 , $singleWords[$r][1]) .
      strtoupper( substr($singleWords[$r][0], 0,1) ) .
      strtolower( substr($singleWords[$r][0], 1) ) .
      substr( $string , $singleWords[$r][1] + strlen($singleWords[$r][0]) );
    }
    return $string;
  }

  /**
   * toUpper
   *
   * @param  string $sText
   *
   * @return string strtoupper($sText)
   */
  function toUpper($sText)
  {
    return strtoupper($sText);
  }
  
  /**
   * toLower
   *
   * @param  string $sText
   * @return string strtolower($sText)
   */
  function toLower($sText)
  {
    return strtolower($sText);
  }
  
  /**
   * http_build_query
   *
   * @param  string $formdata,
   * @param  string $numeric_prefix default value null,
   * @param  string $key default value null
   *
   * @return array  $res
   */
  function http_build_query( $formdata, $numeric_prefix = null, $key = null )
  {
    $res = array();
    foreach ((array)$formdata as $k=>$v) {
      $tmp_key = rawurlencode(is_int($k) ? $numeric_prefix.$k : $k);
      if ($key) $tmp_key = $key.'['.$tmp_key.']';
      if ( is_array($v) || is_object($v) ) {
        $res[] = G::http_build_query($v, null /* or $numeric_prefix if you want to add numeric_prefix to all indexes in array*/, $tmp_key);
      } else {
        $res[] = $tmp_key."=".rawurlencode($v);
      }
      /*
       If you want, you can write this as one string:
       $res[] = ( ( is_array($v) || is_object($v) ) ? G::http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
       */
    }
    $separator = ini_get('arg_separator.output');
    return implode($separator, $res);
  }
  /**
   * Redirect URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $parameter
   * @return string
   */
  function header( $parameter ) {
    if ( defined ('ENABLE_ENCRYPT' ) && (ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9) == 'location:')) {
      $url = G::encryptUrl ( substr( $parameter, 10) , URL_KEY );
      header ( 'location:' . $url );
    }
    else
      header ( $parameter );
    return ;
  }

  /**
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function forceLogin( $permission = "", $urlNoAccess = "" )  {
    global $RBAC;

    if ( isset(  $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] == '' ) {
      $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
      $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
      $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
      $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
      $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
      $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
      die;
      header ("location: $direction");
      die;
      return;
    }

    $Connection     = new DBConnection;
    $ses            = new DBSession($Connection);
    $stQry          = "SELECT LOG_STATUS FROM LOGIN WHERE LOG_SID = '" . session_id() . "'";
    $dset           = $ses->Execute  ( $stQry );
    $row            = $dset->read();
    $sessionPc      = defined ( 'SESSION_PC' ) ? SESSION_PC  : '' ;
    $sessionBrowser = defined ( 'SESSION_BROWSER' ) ? SESSION_BROWSER  : '' ;
    if (($sessionPc == "1" ) or ( $sessionBrowser == "1"))
    if($row['LOG_STATUS'] == 'X'){
      $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
      $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
      $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
      $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
      $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
      $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
      G::SendMessageXml ('ID_CLOSE_SESSION', "warning");
      header ("location: $direction");
      die;
      return;
    }

    if ( defined( 'SIN_COMPATIBILIDAD_RBAC')  and SIN_COMPATIBILIDAD_RBAC == 1 )
      return;

    if ( $permission == "" ) {
      return;
    }

    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;


    $sw = 0;
    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
      //print " $aux[$i]  $res $sw <br>";
    }

    if ($sw == 0 && $urlNoAccess != "") {
      $aux        = explode ( '/', $urlNoAccess );
      $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"/sys".SYS_LANG);
      $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
      $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
      $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[0]) , URL_KEY ):$aux[0]);
      $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[1]) , URL_KEY ):$aux[1]);
      //header ("location: /$sys/$lang/$skin/$login/$loginhtml");
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      die;
    }


    if ($sw == 0) {
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      //header ( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/noViewPage.html" );
      die;
    }
  }
  /**
   * Add slashes to a string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $val_old
   * @return string
   */
  function add_slashes($val_old) {

    if (!is_string ($val_old)) $val_old ="$val_old";

    $tamano_cadena   = strlen ($val_old);
    $contador_cadena = 0;
    $new_val         = "";

    for ($contador_cadena=0; $contador_cadena< $tamano_cadena; $contador_cadena ++)
    {
      $car  = $val_old[$contador_cadena];

      if ( $car != chr(34) && $car != chr(39) && $car != chr(92))
      {
        $new_val .= $car;
      }
      else
      {
        if ($car2 != chr (92) )
        {
          //print " xmlvar: $new_val -- $car -- $car2 <br>";
          $new_val .= chr(92) . $car;
        }
        else
        $new_val .= $car;
      }
    }
    return $new_val;
  }
  /**
   * Upload a file and then copy to path+ nameToSave
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @param  string $nameToSave
   * @param  integer $permission
   * @return void
   */
  function uploadFile($file, $path ,$nameToSave, $permission = 0666)
  {
    try {
      if ($file == '') {
        throw new Exception('The filename is empty!');
      }
      if (filesize($file) > ((((ini_get('upload_max_filesize') + 0)) * 1024) * 1024)) {
        throw new Exception('The size of upload file exceeds the allowed by the server!');
      }
      $oldumask = umask(0);
      if (!is_dir($path)) {
        G::verifyPath($path, true);
      }
      move_uploaded_file($file , $path . "/" . $nameToSave);
      chmod($path . "/" . $nameToSave , $permission);
      umask($oldumask);
    }
    catch (Exception $oException) {
      throw $oException;
    }
  }

  /**
   * resizeImage
   *
   * @param  string $path,
   * @param  string $resWidth
   * @param  string $resHeight
   * @param  string $saveTo default value null
   *
   * @return void
   */
  function resizeImage($path, $resWidth, $resHeight, $saveTo=null)
  {
    $imageInfo = @getimagesize($path);

    if (!$imageInfo)
      throw new Exception("Could not get image information");

    list($width, $height) = $imageInfo;
    $percentHeight        = $resHeight / $height;
    $percentWidth         = $resWidth / $width;
    $percent              = ($percentWidth < $percentHeight) ? $percentWidth : $percentHeight;
    $resWidth             = $width * $percent;
    $resHeight            = $height * $percent;

    // Resample
    $image_p = imagecreatetruecolor($resWidth, $resHeight);
    imagealphablending($image_p, false);
    imagesavealpha($image_p, true);

    $background = imagecolorallocate($image_p, 0, 0, 0);
    ImageColorTransparent($image_p, $background); // make the new temp image all transparent

    //Assume 3 channels if we can't find that information
    if (!array_key_exists("channels", $imageInfo))
      $imageInfo["channels"] = 3;
    $memoryNeeded = Round( ($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] + Pow(2, 16)) * 1.95) / (1024*1024);
    if ( $memoryNeeded < 80 ) $memoryNeeded = 80;
    ini_set('memory_limit', intval($memoryNeeded) . 'M');

    $functions = array(
      IMAGETYPE_GIF => array('imagecreatefromgif', 'imagegif'),
      IMAGETYPE_JPEG => array('imagecreatefromjpeg', 'imagejpeg'),
      IMAGETYPE_PNG => array('imagecreatefrompng', 'imagepng'),
    );

    if (!array_key_exists($imageInfo[2], $functions))
      throw new Exception("Image format not supported");

    list($inputFn, $outputFn) = $functions[$imageInfo[2]];

    $image = $inputFn($path);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $resWidth, $resHeight, $width, $height);
    $outputFn($image_p, $saveTo);

    chmod($saveTo, 0666);
  }

  /**
   * Merge 2 arrays
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return array
   */
  function array_merges() {
    $array  = array();
    $arrays =& func_get_args();
    foreach ($arrays as $array_i) {
      if (is_array($array_i)) {
        G::array_merge_2($array, $array_i);
      }
    }
    return $array;
  }

  /**
   * Merge 2 arrays
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $array
   * @param  string $array_i
   * @return array
   */
  function array_merge_2(&$array, &$array_i) {
    foreach ($array_i as $k => $v) {
      if (is_array($v)) {
        if (!isset($array[$k])) {
          $array[$k] = array();
        }
        G::array_merge_2($array[$k], $v);
      } else {
        if (isset($array[$k]) && is_array($array[$k])) {
          $array[$k][0] = $v;
        } else {
          if (isset($array) && !is_array($array)) {
            $temp     = $array;
            $array    = array();
            $array[0] = $temp;
          }
          $array[$k]  = $v;
        }
      }
    }
  }

  /**
   * Generate random number
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return int
   */
  function generateUniqueID() {
    do {
      $sUID = str_replace('.', '0', uniqid(rand(0, 999999999), true));
    } while (strlen($sUID) != 32);
    return $sUID;
    //return strtoupper(substr(uniqid(rand(0, 9), false),0,14));
  }


  /**
   * Generate a numeric or alphanumeric code
   *
   * @author Julio Cesar Laura Avenda𭞼juliocesar@colosa.com>
   * @access public
   * @return string
   */
  function generateCode($iDigits = 4, $sType = 'NUMERIC') {
    if (($iDigits < 4) || ($iDigits > 50)) {
      $iDigits = 4;
    }
    if (($sType != 'NUMERIC') && ($sType != 'ALPHA') && ($sType != 'ALPHANUMERIC')) {
      $sType = 'NUMERIC';
    }
    $aValidCharacters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
      'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
      'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
      'U', 'V', 'W', 'X', 'Y', 'Z');
    switch ($sType) {
      case 'NUMERIC':
        $iMin = 0;
        $iMax = 9;
        break;
      case 'ALPHA':
        $iMin = 10;
        $iMax = 35;
        break;
      case 'ALPHANUMERIC':
        $iMin = 0;
        $iMax = 35;
        break;
    }
    $sCode = '';
    for ($i = 0; $i < $iDigits; $i++) {
      $sCode .= $aValidCharacters[rand($iMin, $iMax)];
    }
    return $sCode;
  }

  /**
   * Verify if the input string is a valid UID
   *
   * @author David Callizaya <davidsantos@colosa.com>
   * @access public
   * @return int
   */
  function verifyUniqueID( $uid ) {
    return (bool) preg_match('/^[0-9A-Za-z]{14,}/',$uid);
  }

  /**
   * is_utf8
   *
   * @param  string $string
   *
   * @return string utf8_encode()
   */
  function is_utf8($string)
  {
    if (is_array($string))
    {
      $enc = implode('', $string);
      return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }
    else
    {
      return (utf8_encode(utf8_decode($string)) == $string);
    }
  }


  /**
   * Return date in Y-m-d format
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return void
   */
  function CurDate($sFormat = '')
  {
    $sFormat = ( $sFormat != '' )? $sFormat: 'Y-m-d H:i:s';
    return date($sFormat);
  }

  /*
   * Return the System defined constants and Application variables
   *   Constants: SYS_*
   *   Sessions : USER_* , URS_*
   */
  function getSystemConstants($params = null)
  {
    $t1 = G::microtime_float();
    $sysCon = array();
    if (defined("SYS_LANG")) $sysCon["SYS_LANG"] = SYS_LANG;
    if (defined("SYS_SKIN")) $sysCon["SYS_SKIN"] = SYS_SKIN;
    if (defined("SYS_SYS"))  $sysCon["SYS_SYS"]  = SYS_SYS;

    $sysCon["APPLICATION"]  = (isset($_SESSION["APPLICATION"]))?  $_SESSION["APPLICATION"]  : "";
    $sysCon["PROCESS"]      = (isset($_SESSION["PROCESS"]))?      $_SESSION["PROCESS"]      : "";
    $sysCon["TASK"]         = (isset($_SESSION["TASK"]))?         $_SESSION["TASK"]         : "";
    $sysCon["INDEX"]        = (isset($_SESSION["INDEX"]))?        $_SESSION["INDEX"]        : "";
    $sysCon["USER_LOGGED"]  = (isset($_SESSION["USER_LOGGED"]))?  $_SESSION["USER_LOGGED"]  : "";
    $sysCon["USR_USERNAME"] = (isset($_SESSION["USR_USERNAME"]))? $_SESSION["USR_USERNAME"] : "";
    
    //###############################################################################################
    // Added for compatibility betweek aplication called from web Entry that uses just WS functions
    //###############################################################################################
    
    if ($params != null) {
      switch ($params->option) {
        case "STORED SESSION":
          if (isset($params->SID)) {
            G::LoadClass("sessions");
            $oSessions = new Sessions($params->SID);
            $sysCon = array_merge($sysCon, $oSessions->getGlobals());
          }
          break;
      }
      
      if (isset($params->appData) && is_array($params->appData)) {
        $sysCon["APPLICATION"]  = $params->appData["APPLICATION"];
        $sysCon["PROCESS"]      = $params->appData["PROCESS"];
        $sysCon["TASK"]         = $params->appData["TASK"];
        $sysCon["INDEX"]        = $params->appData["INDEX"];
        $sysCon["USER_LOGGED"]  = $params->appData["USER_LOGGED"];
        $sysCon["USR_USERNAME"] = $params->appData["USR_USERNAME"];
      }
    }
    
    return $sysCon;
  }


  /*
   * Return the Friendly Title for a string, capitalize every word and remove spaces
   *   param : text string
   */
  function capitalizeWords( $text )
  {
    /*$result = '';
     $space = true;
     for ( $i = 0; $i < strlen ( $text); $i++ ) {
     $car = strtolower ( $text[$i] );
     if ( strpos( "abcdefghijklmnopqrstuvwxyz1234567890", $car ) !== false ) {
     if ($space ) $car = strtoupper ( $car );
     $result .= $car;
     $space  = false;
     }
     else
     $space = true;
     }
     return $result;*/
    if (function_exists('mb_detect_encoding')) {
      if (strtoupper(mb_detect_encoding($text)) == 'UTF-8') {
        $text = utf8_encode($text);
      }
    }
    if(function_exists('mb_ucwords')) {
      return mb_ucwords($text);
    }
    else {
      return mb_convert_case($text, MB_CASE_TITLE, "UTF-8");
    }
  }

  /**
   * unhtmlentities
   *
   * @param  string $string
   *
   * @return string substring
   */
  function unhtmlentities ($string)
  {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    foreach($trans_tbl as $k => $v)
    {
      $ttr[$v] = utf8_encode($k);
    }
    return strtr ($string, $ttr);
  }

  /*************************************** init **********************************************
   * Xml parse collection functions
   * Returns a associative array within the xml structure and data
   *
   * @author Erik Amaru Ortiz <erik@colosa.com>
   */
  function xmlParser(&$string) {
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parse_into_struct($parser, $string, $vals, $index);

    $mnary = array();
    $ary   =&$mnary;
    foreach ($vals as $r) {
      $t=$r['tag'];
      if ($r['type']=='open') {
        if (isset($ary[$t])) {
          if (isset($ary[$t][0]))
            $ary[$t][]=array();
          else
            $ary[$t]=array($ary[$t], array());
          $cv=&$ary[$t][count($ary[$t])-1];
        } else $cv=&$ary[$t];
        if (isset($r['attributes'])) {
          foreach ($r['attributes'] as $k=>$v) $cv['__ATTRIBUTES__'][$k]=$v;
        }
        // note by gustavo cruz gustavo[at]colosa[dot]com
        // minor adjustments to validate if an open node have a value attribute.
        // for example a dropdown has many childs, but also can have a value attribute.
        if (isset($r['value']) && trim($r['value'])!=''){
          $cv['__VALUE__'] = $r['value'];
        }
        // end added code
        $cv['__CONTENT__']       = array();
        $cv['__CONTENT__']['_p'] =&$ary;
        $ary                     =&$cv['__CONTENT__'];

      } elseif ($r['type']=='complete') {
        if (isset($ary[$t])) { // same as open
          if (isset($ary[$t][0])) $ary[$t][]=array();
          else $ary[$t]=array($ary[$t], array());
          $cv=&$ary[$t][count($ary[$t])-1];
        } else $cv=&$ary[$t];
        if (isset($r['attributes'])) {
          foreach ($r['attributes'] as $k=>$v) $cv['__ATTRIBUTES__'][$k]=$v;
        }
        $cv['__VALUE__']=(isset($r['value']) ? $r['value'] : '');

      } elseif ($r['type']=='close') {
        $ary=&$ary['_p'];
      }
    }

    self::_del_p($mnary);

    $obj_resp->code    = xml_get_error_code($parser);
    $obj_resp->message = xml_error_string($obj_resp->code);
    $obj_resp->result  = $mnary;
    xml_parser_free($parser);

    return $obj_resp;
  }

  /**
   * _del_p
   *
   * @param  string &$ary
   *
   * @return void
   */
  // _Internal: Remove recursion in result array
  function _del_p(&$ary) {
    foreach ($ary as $k=>$v) {
      if ($k==='_p') unset($ary[$k]);
      elseif (is_array($ary[$k])) self::_del_p($ary[$k]);
    }
  }

  /**
   * ary2xml
   *
   * Array to XML
   *
   * @param  string $cary
   * @param  string $d=0
   * @param  string $forcetag default value ''
   *
   * @return void
   */
  // Array to XML
  function ary2xml($cary, $d=0, $forcetag='') {
    $res = array();
    foreach ($cary as $tag=>$r) {
      if (isset($r[0])) {
        $res[]=self::ary2xml($r, $d, $tag);
      } else {
        if ($forcetag) $tag=$forcetag;
        $sp    = str_repeat("\t", $d);
        $res[] = "$sp<$tag";
        if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
        $res[] = ">".((isset($r['_c'])) ? "\n" : '');
        if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);
        elseif (isset($r['_v'])) $res[]=$r['_v'];
        $res[] = (isset($r['_c']) ? $sp : '')."</$tag>\n";
      }

    }
    return implode('', $res);
  }

  /**
   * ins2ary
   *
   * Insert element into array
   *
   * @param  string &$ary
   * @param  string $element
   * @param  string $pos
   *
   * @return void
   */
  // Insert element into array
  function ins2ary(&$ary, $element, $pos)
  {
    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
    $ary=array_merge($ar1, array_slice($ary, $pos));
  }

  /*
   * Xml parse collection functions
   *************************************** end **********************************************/


  /**
   * evalJScript
   *
   * @param  string $c
   *
   * @return void
   */
  function evalJScript($c){
    print("<script language=\"javascript\">{$c}</script>");
  }


  /**
   *  Inflects a string with accented characters and other characteres not suitable for file names, by defaul replace with undescore
   *
   *  @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gamil.com>
   *  @param (string) string to convert
   *  @param (string) character for replace
   *  @param (array) additional characteres map
   *
   */
  function inflect($string, $replacement = '_', $map = array()) {
    if (is_array($replacement)) {
      $map = $replacement;
      $replacement = '_';
    }

    $quotedReplacement = preg_quote($replacement, '/');

    $default = array(
                '/à|á|å|â/' => 'a',
                '/è|é|ê|ẽ|ë/' => 'e',
                '/ì|í|î/' => 'i',
                '/ò|ó|ô|ø/' => 'o',
                '/ù|ú|ů|û/' => 'u',
                '/ç/' => 'c',
                '/ñ/' => 'n',
                '/ä|æ/' => 'ae',
                '/ö/' => 'oe',
                '/ü/' => 'ue',
                '/Ä/' => 'Ae',
                '/Ü/' => 'Ue',
                '/Ö/' => 'Oe',
                '/ß/' => 'ss',
                '/\.|\,|\:|\-|\\|\//' =>  " ",
                '/\\s+/' => $replacement
              );

    $map = array_merge($default, $map);
    return preg_replace(array_keys($map), array_values($map), $string);
  }

  /**
   * pr
   *
   * @param  string $var
   *
   * @return void
   */
  function pr($var)
  {
    print("<pre>");
    print_r($var);
    print("</pre>");
  }

  /**
   * dump
   *
   * @param  string $var
   *
   * @return void
   */
  function dump($var){
    print("<pre>");
    var_dump($var);
    print("</pre>");
  }

  /**
   * stripCDATA
   *
   * @param  string $string
   *
   * @return string str_replace
   */
  function stripCDATA($string){
    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
    return str_replace($matches[0], $matches[1], $string);
  }
  
  /**
   * Get the temporal directory path on differents O.S.  i.e. /temp -> linux, C:/Temp -> win
   * @author <erik@colosa.com>
   */
  function sys_get_temp_dir() {
    if ( !function_exists('sys_get_temp_dir') ){
      // Based on http://www.phpit.net/
      // article/creating-zip-tar-archives-dynamically-php/2/
      // Try to get from environment variable
      if ( !empty($_ENV['TMP']) ){
        return realpath( $_ENV['TMP'] );
      } else if ( !empty($_ENV['TMPDIR']) ){
        return realpath( $_ENV['TMPDIR'] );
      } else if ( !empty($_ENV['TEMP']) ){
        return realpath( $_ENV['TEMP'] );
      } else {// Detect by creating a temporary file
        // Try to use system's temporary directory
        // as random name shouldn't exist
        $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
        if ( $temp_file ){
          $temp_dir = realpath( dirname($temp_file) );
          unlink( $temp_file );
          return $temp_dir;
        } else {
          return FALSE;
        }
      }
    } else {
      return sys_get_temp_dir();
    }
  }
  
  /**
   * Get the content of a compose pmos web service response
   * Returns an array when has a valid reponse, if the response is invalid returns an object containing a status_code and message properties.
   *
   * @author <erik@colosa.com>
   */
  function PMWSCompositeResponse($oResp, $prop) {
    $Resp = new stdClass();
    
    if( is_object($oResp) && isset($oResp->{$prop}) ){
      $list = $oResp->{$prop};
      
      if( is_object($list) ){
        $aList[0] = $list;
      } else {
        $aList = $list;
      }
      
      $result = true;
      if( is_array($aList) ){
        foreach($aList as $item){
          if( !isset($item->guid) ){
            $result = false;
            break;
          }
        }
      } else {
        $Resp->status_code = -1;
        $Resp->message = "Bad respose type for ({$prop})";
      }
      
      if( $result ){
        //verifing if the response has a composite response into a guid value of the first row.
        $tmp = explode(' ', trim($aList[0]->guid));
        if( sizeof($tmp) >= 2 ){ //the guid can't has a space, so this should be a ws response
          $Resp->status_code = $tmp[0];
          $Resp->message = substr($aList[0]->guid, strpos($aList[0]->guid, ' ') + 1);
        } else {
          return $aList;
        }
           
      } else {
        $Resp->status_code = -2;
        $Resp->message = "Bad respose, the response has not a uniform struct.";
      }
    } else if( is_object($oResp) ){
      return Array();
    } else {
      $Resp->status_code = -1;
      $Resp->message = "1 Bad respose type for ({$prop})";
    }
    return $Resp;
  }
    
  /**
   * Validate and emai address in complete forms,
   *
   * @author Erik A.O. <erik@gmail.com, aortiz.erik@gmail.com>
   * i.e. if the param. is 'erik a.o. <erik@colosa.com>'
   *      -> returns a object within $o->email => erik@colosa.com and $o->name => erik A.O. in other case returns false
   *
   */
  function emailAddress($sEmail){
    $o = new stdClass();
    if( strpos($sEmail, '<') !== false ) {
      preg_match('/([\"\w@\.-_\s]*\s*)?(<(\w+[\.-]?\w+]*@\w+([\.-]?\w+)*\.\w{2,3})+>)/', $sEmail, $matches);
      g::pr($matches);
      if( isset($matches[1]) && $matches[3]){
        $o->email = $matches[3];
        $o->name = $matches[1];
        return $o;
      }
      return false;
    } else {
      preg_match('/\w+[\.-]?\w+]*@\w+([\.-]?\w+)*\.\w{2,3}+/', $sEmail, $matches);
      if( isset($matches[0]) ){
        $o->email = $matches[0];
        $o->name = '';
        return $o;
      }
      return false;
    }
  }
  
  /**
   * JSON encode
   *
   * @author Erik A.O. <erik@gmail.com, aortiz.erik@gmail.com>
   */
  function json_encode($Json){
    if( function_exists('json_encode') ){
      return json_encode($Json);
    } else {
      G::LoadThirdParty('pear/json', 'class.json');
      $oJSON = new Services_JSON();
      return $oJSON->encode($Json);
    }
  }
  
  /**
   * JSON decode
   *
   * @author Erik A.O. <erik@gmail.com, aortiz.erik@gmail.com>
   */
  function json_decode($Json){
    if( function_exists('json_decode') ){
      return json_decode($Json);
    } else {
      G::LoadThirdParty('pear/json', 'class.json');
      $oJSON = new Services_JSON();
      return $oJSON->decode($Json);
    }
  }
  
  /**
   * isHttpRequest
   *
   * @return boolean true or false
   */
  function isHttpRequest(){
    if( isset($_SERVER['SERVER_SOFTWARE']) && strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false ){
      return true;
    }
    return false;
  }
  
  /**
   * Send a mail using phpmailer
   * this method use the global smtp server connection stored on Configuration table
   * this information is retrieved by the PMFunction getEmailConfiguration()
   *  
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @param string $from address that is sending the email
   * @param string $fromName name of sender 
   * @param mixed $address the possibles values are:
   *        string
   *        array('email1', 'some name <email2>')
   *        array('to'=>array('email1', 'some name <email2>'), 'cc'=>array(...), 'bcc'=>array(...))
   * @param string $subject contains the email subject
   * @param string $body contains the email body (text plain or html) 
   * @return mixed boolean or string : if the email was sent successfully returns true, otherwise returns a string within error message
   */
  function sendMail($from, $fromName, $address, $subject, $body)
  {
   // require_once "classes/class.pmFunctions.php";
    G::LoadClass("pmFunctions");
    G::LoadThirdParty('phpmailer', 'class.phpmailer');
    $setup = getEmailConfiguration();
    
    if (count($setup) == 0 || !isset($setup['MESS_ENGINE']) || !isset($setup['MESS_SERVER']) 
        || !isset($setup['MESS_ENABLED']) || !isset($setup['MESS_RAUTH']) || $setup['MESS_SERVER'] == '') {
      return G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_CONFIGURED');
    }
    
    if (!$setup['MESS_ENABLED']) {
      return G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_ENABLED');
    }

    $passwd    = $setup['MESS_PASSWORD'];
    $passwdDec = G::decrypt($passwd,'EMAILENCRYPT');
    if (strpos( $passwdDec, 'hash:' ) !== false) {
      list($hash, $pass) = explode(":", $passwdDec);
      $setup['MESS_PASSWORD'] = $pass;
    }
    $mail = new PHPMailer(true);
    $mail->From = $from != '' && $from ? $from : $setup['MESS_ACCOUNT'];
    $mail->FromName = $fromName;
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->IsHTML (true);
    $mail->IsSMTP();
    $mail->Host = $setup['MESS_SERVER'];    
    $mail->Port = $setup['MESS_PORT'];
    $mail->SMTPAuth = isset($setup['MESS_RAUTH']) && $setup['MESS_RAUTH'] ? true : false;
    $mail->Username = $setup['MESS_ACCOUNT'];
    $mail->Password = $setup['MESS_PASSWORD'];
    $mail->SMTPSecure = $setup['SMTPSecure'];

    $emailAddressList = G::envelopEmailAddresses($address);
    
    foreach ($emailAddressList['to'] as $emails) {
      $mail->AddAddress($emails[0], $emails[1]);
    }
    foreach ($emailAddressList['cc'] as $emails) {
      $mail->AddCC($emails[0], $emails[1]);
    }
    foreach ($emailAddressList['bcc'] as $emails) {
       $mail->AddBCC($emails[0], $emails[1]);
    }
    
    return $mail->Send() ? true : $mail->ErrorInfo;
  }
  
  /**
   * Envelope a emails collection from a string or array
   * @author Erik Amaru Ortiz <erik@colosa.com> 
   * @param mixed $address the possibles values are:
   *        string
   *        array('email1', 'some name <email2>')
   *        array('to'=>array('email1', 'some name <email2>'), 'cc'=>array(...), 'bcc'=>array(...))
   * @return array contains: 
   *                 array(
   *                    'to' => array('email@host.com', 'some name or empty string', array('email@host.com', '..'), ...),
   *                    'cc' => array('email@host.com', 'some name or empty string', ...),
   *                    'bcc' => array('email@host.com', 'some name or empty string', ...)
   *                 )
   */
  function envelopEmailAddresses($address)
  {
    $emailAddressList = array();
    $emailAddressList['to'] = array();
    $emailAddressList['cc'] = array();
    $emailAddressList['bcc'] = array();
    $ereg = '/([\"\w\W\s]*\s*)?(<([\w\-\.]+@[\.-\w]+\.\w{2,3})+>)/';
    
    if (!is_array($address)) {
      if (preg_match($ereg, $address, $match)) 
        $emailAddressList['to'][] = array($match[3], $match[1]);
      else
        $emailAddressList['to'][] = array($address, '');
    } 
    else {
      foreach ($address as $type => $emails) {
        if (!is_array($emails)) {
          if (preg_match($ereg, $emails, $match))
            $emailAddressList['to'][] = array($match[3], $match[1]);
          else
            $emailAddressList['to'][] = array($emails, '');
        }
        else {
          switch ($type) {
            case 'cc':
              foreach ($emails as $email) {
                if (preg_match($ereg, $email, $match))
                  $emailAddressList['cc'][] = array($match[3], $match[1]);
                else
                  $emailAddressList['cc'][] = array($email, '');
              }
              break;
            case 'bcc':
              foreach ($emails as $email) {
                if (preg_match($ereg, $email, $match))
                  $emailAddressList['bcc'][] = array($match[3], $match[1]);
                else
                  $emailAddressList['bcc'][] = array($email, '');
              }
              break;
            case 'to':
            default:
              foreach ($emails as $email) {
                if (preg_match($ereg, $email, $match))
                  $emailAddressList['to'][] = array($match[3], $match[1]);
                else
                  $emailAddressList['to'][] = array($email, '');
              }
              break;
          }
        }
      }
    }
    
    return $emailAddressList;
  }
    
  /**
   * Get the type of a variable
   * Returns the type of the PHP variable var.
   *
   * @author Erik A. Ortiz. <erik@colosa.com>
   * @return (string) type of variable
   */
  public function gettype($var) {
      switch ($var) {
        case is_null($var):
          $type='NULL';
          break;
           
        case is_bool($var):
          $type='boolean';
          break;

        case is_float($var):
          $type='double';
          break;

        case is_int($var):
          $type='integer';
          break;

        case is_string($var):
          $type='string';
          break;

        case is_array($var):
          $type='array';
          break;

        case is_object($var):
          $type='object';
          break;

        case is_resource($var):
          $type='resource';
          break;

        default:
          $type='unknown type';
          break;
      }

      return $type;
    }

  function removeComments($buffer)
  {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
  }

  function getMemoryUsage(){
    $size = memory_get_usage(true);
    $unit=array('B','Kb','Mb','Gb','Tb','Pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  function getFormatUserList($format, $aUserInfo){
  	
   	switch($format){
     case '@firstName @lastName':
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $format);
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $infoUser);
     break;
     case '@firstName @lastName (@userName)':
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $format);
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $infoUser);
     $infoUser = str_replace('@userName', $aUserInfo['USR_USERNAME'], $infoUser);
     break;
     case '@userName':
     $infoUser = str_replace('@userName', $aUserInfo['USR_USERNAME'], $format);
     break;
     case '@userName (@firstName @lastName)':
     $infoUser = str_replace('@userName', $aUserInfo['USR_USERNAME'], $format);
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $infoUser);
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $infoUser);
     break;
     case '@lastName @firstName':
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $format);
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $infoUser);
     break;
     case '@lastName, @firstName':
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $format);
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $infoUser);
     break;
     case '@lastName, @firstName (@userName)':
     $infoUser = str_replace('@lastName', $aUserInfo['USR_LASTNAME'], $format);
     $infoUser = str_replace('@firstName', $aUserInfo['USR_FIRSTNAME'], $infoUser);
     $infoUser = str_replace('@userName', $aUserInfo['USR_USERNAME'], $infoUser);
     break;
     default :
     $infoUser = str_replace('@userName', $aUserInfo['USR_USERNAME'], '@userName');
     break;
     }
  	return $infoUser;
  }

  function getModel($model){
    require_once "classes/model/$model.php";
    return new $model();
  }
  
  /**
   * Recursive Is writeable function
   *
   * @author Erik Amaru Ortiz <erik@colosa.com>
   *
   * @param $path path to scan recursively the write permission
   * @param $pattern pattern to filter some especified files
   * @return <boolean> if the $path, assuming that is a directory -> all files in it are writeables or not
   */
  function is_rwritable($path, $pattern='*')
  {
    $files = G::rglob($pattern, 0, $path);
    foreach ($files as $file) {
      if( ! is_writable($file) )
        return false;
    }
    return true;
  }
  
  /**
   * Recursive version of glob php standard function
   *
   * @author Erik Amaru Ortiz <erik@colosa.com>
   *
   * @param $path path to scan recursively the write permission
   * @param $flags to notive glob function
   * @param $pattern pattern to filter some especified files
   * @return <array> array containing the recursive glob results
   */
  function rglob($pattern='*', $flags = 0, $path='')
  {
    $paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files = glob($path.$pattern, $flags);
    foreach ($paths as $path) {
      $files = array_merge($files, G::rglob($pattern, $flags, $path));
    }
    return $files;
  }
function browser_detection( $which_test, $test_excludes='', $external_ua_string='' )
{
	/*
	uncomment the global variable declaration if you want the variables to be available on
	a global level throughout your php page, make sure that php is configured to support
	the use of globals first!
	Use of globals should be avoided however, and they are not necessary with this script
	/*
	/*
	global $a_full_assoc_data, $a_mobile_data, $a_moz_data, $a_webkit_data, $b_dom_browser, $b_repeat, $b_safe_browser, $browser_name, $browser_number, $browser_math_number, $browser_user_agent, $browser_working, $ie_version, $mobile_test, $moz_number, $moz_rv, $moz_rv_full, $moz_release_date, $moz_type, $os_number, $os_type, $true_ie_number, $ua_type, $webkit_type, $webkit_type_number;
	*/
	G::script_time(); // set script timer to start timing

	static $a_full_assoc_data, $a_mobile_data, $a_moz_data, $a_webkit_data, $b_dom_browser, $b_repeat, $b_safe_browser, $browser_name, $browser_number, $browser_math_number, $browser_user_agent, $browser_working, $ie_version, $mobile_test, $moz_number, $moz_rv, $moz_rv_full, $moz_release_date, $moz_type, $os_number, $os_type, $true_ie_number, $ua_type, $webkit_type, $webkit_type_number;
	
	// switch off the optimization for external ua string testing.
	if ( $external_ua_string )
	{
		$b_repeat = false;
	}
	
	/*
	this makes the test only run once no matter how many times you call it since
	all the variables are filled on the first run through, it's only a matter of
	returning the the right ones
	*/
	if ( !$b_repeat )
	{
		//initialize all variables with default values to prevent error
		$a_browser_math_number = '';
		$a_full_assoc_data = '';
		$a_full_data = '';
		$a_mobile_data = '';
		$a_moz_data = '';
		$a_os_data = '';
		$a_unhandled_browser = '';
		$a_webkit_data = '';
		$b_dom_browser = false;
		$b_os_test = true;
		$b_mobile_test = true;
		$b_safe_browser = false;
		$b_success = false;// boolean for if browser found in main test
		$browser_math_number = '';
		$browser_temp = '';
		$browser_working = '';
		$browser_number = '';
		$ie_version = '';
		$mobile_test = '';
		$moz_release_date = '';
		$moz_rv = '';
		$moz_rv_full = '';
		$moz_type = '';
		$moz_number = '';
		$os_number = '';
		$os_type = '';
		$run_time = '';
		$true_ie_number = '';
		$ua_type = 'bot';// default to bot since you never know with bots
		$webkit_type = '';
		$webkit_type_number = '';

		// set the excludes if required
		if ( $test_excludes )
		{
			switch ( $test_excludes )
			{
				case '1':
					$b_os_test = false;
					break;
				case '2':
					$b_mobile_test = false;
					break;
				case '3':
					$b_os_test = false;
					$b_mobile_test = false;
					break;
				default:
					die( 'Error: bad $test_excludes parameter 2 used: ' . $test_excludes );
					break;
			}
		}

		/*
		make navigator user agent string lower case to make sure all versions get caught
		isset protects against blank user agent failure. tolower also lets the script use
		strstr instead of stristr, which drops overhead slightly.
		*/
		if ( $external_ua_string )
		{
			$browser_user_agent = strtolower( $external_ua_string );
		}
		elseif ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
		{
			$browser_user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		}
		else
		{
			$browser_user_agent = '';
		}

		/*
		pack the browser type array, in this order
		the order is important, because opera must be tested first, then omniweb [which has safari
		data in string], same for konqueror, then safari, then gecko, since safari navigator user
		agent id's with 'gecko' in string.
		Note that $b_dom_browser is set for all  modern dom browsers, this gives you a default to use.

		array[0] = id string for useragent, array[1] is if dom capable, array[2] is working name
		for browser, array[3] identifies navigator useragent type

		Note: all browser strings are in lower case to match the strtolower output, this avoids
		possible detection errors

		Note: These are the navigator user agent types:
		bro - modern, css supporting browser.
		bbro - basic browser, text only, table only, defective css implementation
		bot - search type spider
		dow - known download agent
		lib - standard http libraries
		mobile - handheld or mobile browser, set using $mobile_test
		*/
		// known browsers, list will be updated routinely, check back now and then
		$a_browser_types = array(
			array( 'opera', true, 'op', 'bro' ),
			array( 'msie', true, 'ie', 'bro' ),
			// webkit before gecko because some webkit ua strings say: like gecko
			array( 'webkit', true, 'webkit', 'bro' ),
			// konq will be using webkit soon
			array( 'konqueror', true, 'konq', 'bro' ),
			// covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
			array( 'gecko', true, 'moz', 'bro' ),
			array( 'netpositive', false, 'netp', 'bbro' ),// beos browser
			array( 'lynx', false, 'lynx', 'bbro' ), // command line browser
			array( 'elinks ', false, 'elinks', 'bbro' ), // new version of links
			array( 'elinks', false, 'elinks', 'bbro' ), // alternate id for it
			array( 'links2', false, 'links2', 'bbro' ), // alternate links version
			array( 'links ', false, 'links', 'bbro' ), // old name for links
			array( 'links', false, 'links', 'bbro' ), // alternate id for it
			array( 'w3m', false, 'w3m', 'bbro' ), // open source browser, more features than lynx/links
			array( 'webtv', false, 'webtv', 'bbro' ),// junk ms webtv
			array( 'amaya', false, 'amaya', 'bbro' ),// w3c browser
			array( 'dillo', false, 'dillo', 'bbro' ),// linux browser, basic table support
			array( 'ibrowse', false, 'ibrowse', 'bbro' ),// amiga browser
			array( 'icab', false, 'icab', 'bro' ),// mac browser
			array( 'crazy browser', true, 'ie', 'bro' ),// uses ie rendering engine
	
			// search engine spider bots:
			array( 'bingbot', false, 'bing', 'bot' ),// bing
			array( 'exabot', false, 'exabot', 'bot' ),// exabot
			array( 'googlebot', false, 'google', 'bot' ),// google
			array( 'google web preview', false, 'googlewp', 'bot' ),// google preview
			array( 'mediapartners-google', false, 'adsense', 'bot' ),// google adsense
			array( 'yahoo-verticalcrawler', false, 'yahoo', 'bot' ),// old yahoo bot
			array( 'yahoo! slurp', false, 'yahoo', 'bot' ), // new yahoo bot
			array( 'yahoo-mm', false, 'yahoomm', 'bot' ), // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
			array( 'inktomi', false, 'inktomi', 'bot' ), // inktomi bot
			array( 'slurp', false, 'inktomi', 'bot' ), // inktomi bot
			array( 'fast-webcrawler', false, 'fast', 'bot' ),// Fast AllTheWeb
			array( 'msnbot', false, 'msn', 'bot' ),// msn search
			array( 'ask jeeves', false, 'ask', 'bot' ), //jeeves/teoma
			array( 'teoma', false, 'ask', 'bot' ),//jeeves teoma
			array( 'scooter', false, 'scooter', 'bot' ),// altavista
			array( 'openbot', false, 'openbot', 'bot' ),// openbot, from taiwan
			array( 'ia_archiver', false, 'ia_archiver', 'bot' ),// ia archiver
			array( 'zyborg', false, 'looksmart', 'bot' ),// looksmart
			array( 'almaden', false, 'ibm', 'bot' ),// ibm almaden web crawler
			array( 'baiduspider', false, 'baidu', 'bot' ),// Baiduspider asian search spider
			array( 'psbot', false, 'psbot', 'bot' ),// psbot image crawler
			array( 'gigabot', false, 'gigabot', 'bot' ),// gigabot crawler
			array( 'naverbot', false, 'naverbot', 'bot' ),// naverbot crawler, bad bot, block
			array( 'surveybot', false, 'surveybot', 'bot' ),//
			array( 'boitho.com-dc', false, 'boitho', 'bot' ),//norwegian search engine
			array( 'objectssearch', false, 'objectsearch', 'bot' ),// open source search engine
			array( 'answerbus', false, 'answerbus', 'bot' ),// http://www.answerbus.com/, web questions
			array( 'sohu-search', false, 'sohu', 'bot' ),// chinese media company, search component
			array( 'iltrovatore-setaccio', false, 'il-set', 'bot' ),
	
			// various http utility libaries
			array( 'w3c_validator', false, 'w3c', 'lib' ), // uses libperl, make first
			array( 'wdg_validator', false, 'wdg', 'lib' ), //
			array( 'libwww-perl', false, 'libwww-perl', 'lib' ),
			array( 'jakarta commons-httpclient', false, 'jakarta', 'lib' ),
			array( 'python-urllib', false, 'python-urllib', 'lib' ),
	
			// download apps
			array( 'getright', false, 'getright', 'dow' ),
			array( 'wget', false, 'wget', 'dow' ),// open source downloader, obeys robots.txt
	
			// netscape 4 and earlier tests, put last so spiders don't get caught
			array( 'mozilla/4.', false, 'ns', 'bbro' ),
			array( 'mozilla/3.', false, 'ns', 'bbro' ),
			array( 'mozilla/2.', false, 'ns', 'bbro' )
		);
	
		//array( '', false ); // browser array template

		/*
		moz types array
		note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
		rv comes last in case it is plain old mozilla. firefox/netscape/seamonkey need to be later
		Thanks to: http://www.zytrax.com/tech/web/firefox-history.html
		*/
		$a_moz_types = array( 'bonecho', 'camino', 'epiphany', 'firebird', 'flock', 'galeon', 'iceape', 'icecat', 'k-meleon', 'minimo', 'multizilla', 'phoenix', 'songbird', 'swiftfox', 'seamonkey', 'shiretoko', 'iceweasel', 'firefox', 'minefield', 'netscape6', 'netscape', 'rv' );

		/*
		webkit types, this is going to expand over time as webkit browsers spread
		konqueror is probably going to move to webkit, so this is preparing for that
		It will now default to khtml. gtklauncher is the temp id for epiphany, might
		change. Defaults to applewebkit, and will all show the webkit number.
		*/
		$a_webkit_types = array( 'arora', 'chrome', 'epiphany', 'gtklauncher', 'konqueror', 'midori', 'omniweb', 'safari', 'uzbl', 'applewebkit', 'webkit' );

		/*
		run through the browser_types array, break if you hit a match, if no match, assume old browser
		or non dom browser, assigns false value to $b_success.
		*/
		$i_count = count( $a_browser_types );
		for ( $i = 0; $i < $i_count; $i++ )
		{
			//unpacks browser array, assigns to variables, need to not assign til found in string
			$browser_temp = $a_browser_types[$i][0];// text string to id browser from array

			if ( strstr( $browser_user_agent, $browser_temp ) )
			{
				/*
				it defaults to true, will become false below if needed
				this keeps it easier to keep track of what is safe, only
				explicit false assignment will make it false.
				*/
				$b_safe_browser = true;
				$browser_name = $browser_temp;// text string to id browser from array

				// assign values based on match of user agent string
				$b_dom_browser = $a_browser_types[$i][1];// hardcoded dom support from array
				$browser_working = $a_browser_types[$i][2];// working name for browser
				$ua_type = $a_browser_types[$i][3];// sets whether bot or browser

				switch ( $browser_working )
				{
					// this is modified quite a bit, now will return proper netscape version number
					// check your implementation to make sure it works
					case 'ns':
						$b_safe_browser = false;
						$browser_number = G::get_item_version( $browser_user_agent, 'mozilla' );
						break;
					case 'moz':
						/*
						note: The 'rv' test is not absolute since the rv number is very different on
						different versions, for example Galean doesn't use the same rv version as Mozilla,
						neither do later Netscapes, like 7.x. For more on this, read the full mozilla
						numbering conventions here: http://www.mozilla.org/releases/cvstags.html
						*/
						// this will return alpha and beta version numbers, if present
						$moz_rv_full = G::get_item_version( $browser_user_agent, 'rv' );
						// this slices them back off for math comparisons
						$moz_rv = substr( $moz_rv_full, 0, 3 );

						// this is to pull out specific mozilla versions, firebird, netscape etc..
						$j_count = count( $a_moz_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_moz_types[$j] ) )
							{
								$moz_type = $a_moz_types[$j];
								$moz_number = G::get_item_version( $browser_user_agent, $moz_type );
								break;
							}
						}
						/*
						this is necesary to protect against false id'ed moz'es and new moz'es.
						this corrects for galeon, or any other moz browser without an rv number
						*/
						if ( !$moz_rv )
						{
							// you can use this if you are running php >= 4.2
							if ( function_exists( 'floatval' ) )
							{
								$moz_rv = floatval( $moz_number );
							}
							else
							{
								$moz_rv = substr( $moz_number, 0, 3 );
							}
							$moz_rv_full = $moz_number;
						}
						// this corrects the version name in case it went to the default 'rv' for the test
						if ( $moz_type == 'rv' )
						{
							$moz_type = 'mozilla';
						}

						//the moz version will be taken from the rv number, see notes above for rv problems
						$browser_number = $moz_rv;
						// gets the actual release date, necessary if you need to do functionality tests
						G::get_set_count( 'set', 0 );
						$moz_release_date = G::get_item_version( $browser_user_agent, 'gecko/' );
						/*
						Test for mozilla 0.9.x / netscape 6.x
						test your javascript/CSS to see if it works in these mozilla releases, if it
						does, just default it to: $b_safe_browser = true;
						*/
						if ( ( $moz_release_date < 20020400 ) || ( $moz_rv < 1 ) )
						{
							$b_safe_browser = false;
						}
						break;
					case 'ie':
						/*
						note we're adding in the trident/ search to return only first instance in case
						of msie 8, and we're triggering the  break last condition in the test, as well
						as the test for a second search string, trident/
						*/
						$browser_number = G::get_item_version( $browser_user_agent, $browser_name, true, 'trident/' );
						// construct the proper real number if it's in compat mode and msie 8.0/9.0
						if ( strstr( $browser_number, '7.' ) && strstr( $browser_user_agent, 'trident/5' ) )
						{
							// note that 7.0 becomes 9 when adding 1, but if it's 7.1 it will be 9.1
							$true_ie_number = $browser_number + 2;
						}
						elseif ( strstr( $browser_number, '7.' ) && strstr( $browser_user_agent, 'trident/4' ) )
						{
							// note that 7.0 becomes 8 when adding 1, but if it's 7.1 it will be 8.1
							$true_ie_number = $browser_number + 1;
						}
						// the 9 series is finally standards compatible, html 5 etc, so worth a new id
						if ( $browser_number >= 9 )
						{
							$ie_version = 'ie9x';
						}
						// 7/8 were not yet quite to standards levels but getting there
						elseif ( $browser_number >= 7 )
						{
							$ie_version = 'ie7x';
						}
						// then test for IE 5x mac, that's the most problematic IE out there
						elseif ( strstr( $browser_user_agent, 'mac') )
						{
							$ie_version = 'ieMac';
						}
						// ie 5/6 are both very weak in standards compliance
						elseif ( $browser_number >= 5 )
						{
							$ie_version = 'ie5x';
						}
						elseif ( ( $browser_number > 3 ) && ( $browser_number < 5 ) )
						{
							$b_dom_browser = false;
							$ie_version = 'ie4';
							// this depends on what you're using the script for, make sure this fits your needs
							$b_safe_browser = true;
						}
						else
						{
							$ie_version = 'old';
							$b_dom_browser = false;
							$b_safe_browser = false;
						}
						break;
					case 'op':
						$browser_number = G::get_item_version( $browser_user_agent, $browser_name );
						// opera is leaving version at 9.80 (or xx) for 10.x - see this for explanation
						// http://dev.opera.com/articles/view/opera-ua-string-changes/
						if ( strstr( $browser_number, '9.' ) && strstr( $browser_user_agent, 'version/' ) )
						{
							G::get_set_count( 'set', 0 );
							$browser_number = G::get_item_version( $browser_user_agent, 'version/' );
						}
						
						if ( $browser_number < 5 )// opera 4 wasn't very useable.
						{
							$b_safe_browser = false;
						}
						break;
					/*
					note: webkit returns always the webkit version number, not the specific user
					agent version, ie, webkit 583, not chrome 0.3
					*/
					case 'webkit':
						// note that this is the Webkit version number
						$browser_number = G::get_item_version( $browser_user_agent, $browser_name );
						// this is to pull out specific webkit versions, safari, google-chrome etc..
						$j_count = count( $a_webkit_types );
						for ( $j = 0; $j < $j_count; $j++ )
						{
							if ( strstr( $browser_user_agent, $a_webkit_types[$j] ) )
							{
								$webkit_type = $a_webkit_types[$j];
								/*
								and this is the webkit type version number, like: chrome 1.2
								if omni web, we want the count 2, not default 1
								*/
								if ( $webkit_type == 'omniweb' )
								{
									G::get_set_count( 'set', 2 );
								}
								$webkit_type_number = G::get_item_version( $browser_user_agent, $webkit_type );
								// epiphany hack
								if ( $a_webkit_types[$j] == 'gtklauncher' )
								{
									$browser_name = 'epiphany';
								}
								else
								{
									$browser_name = $a_webkit_types[$j];
								}
								break;
							}
						}
						break;
					default:
						$browser_number = G::get_item_version( $browser_user_agent, $browser_name );
						break;
				}
				// the browser was id'ed
				$b_success = true;
				break;
			}
		}
		
		//assigns defaults if the browser was not found in the loop test
		if ( !$b_success )
		{
			/*
			this will return the first part of the browser string if the above id's failed
			usually the first part of the browser string has the navigator useragent name/version in it.
			This will usually correctly id the browser and the browser number if it didn't get
			caught by the above routine.
			If you want a '' to do a if browser == '' type test, just comment out all lines below
			except for the last line, and uncomment the last line. If you want undefined values,
			the browser_name is '', you can always test for that
			*/
			// delete this part if you want an unknown browser returned
			$browser_name = substr( $browser_user_agent, 0, strcspn( $browser_user_agent , '();') );
			// this extracts just the browser name from the string, if something usable was found
			if ( $browser_name && preg_match( '/[^0-9][a-z]*-*\ *[a-z]*\ *[a-z]*/', $browser_name, $a_unhandled_browser ) )
			{
				$browser_name = $a_unhandled_browser[0];
				
				if ( $browser_name == 'blackberry' )
				{
					G::get_set_count( 'set', 0 );
				}
				$browser_number = G::get_item_version( $browser_user_agent, $browser_name );
			}
			else
			{
				$browser_name = 'NA';
				$browser_number = 'NA';
			}

			// then uncomment this part
			//$browser_name = '';//deletes the last array item in case the browser was not a match
		}
		// get os data, mac os x test requires browser/version information, this is a change from older scripts
		if ( $b_os_test )
		{
			$a_os_data = G::get_os_data( $browser_user_agent, $browser_working, $browser_number );
			$os_type = $a_os_data[0];// os name, abbreviated
			$os_number = $a_os_data[1];// os number or version if available
		}
		/*
		this ends the run through once if clause, set the boolean
		to true so the function won't retest everything
		*/
		$b_repeat = true;
		/*
		pulls out primary version number from more complex string, like 7.5a,
		use this for numeric version comparison
		*/
		if ( $browser_number && preg_match( '/[0-9]*\.*[0-9]*/', $browser_number, $a_browser_math_number ) )
		{
			$browser_math_number = $a_browser_math_number[0];
			//print_r($a_browser_math_number);
		}
		if ( $b_mobile_test )
		{
			$mobile_test = G::check_is_mobile( $browser_user_agent );
			if ( $mobile_test )
			{
				$a_mobile_data = G::get_mobile_data( $browser_user_agent );
				$ua_type = 'mobile';
			}
		}
	}
	//$browser_number = $_SERVER["REMOTE_ADDR"];
	/*
	This is where you return values based on what parameter you used to call the function
	$which_test is the passed parameter in the initial browser_detection('os') for example returns
	the os version only.
	
	Update deprecated parameter names to new names
	*/
	switch ( $which_test )
	{
		case 'math_number':
			$which_test = 'browser_math_number';
			break;
		case 'number':
			$which_test = 'browser_number';
			break;
		case 'browser':
			$which_test = 'browser_working';
			break;
		case 'moz_version':
			$which_test = 'moz_data';
			break;
		case 'true_msie_version':
			$which_test = 'true_ie_number';
			break;
		case 'type':
			$which_test = 'ua_type';
			break;
		case 'webkit_version':
			$which_test = 'webkit_data';
			break;
	}
	/*
	assemble these first so they can be included in full return data, using static variables
	Note that there's no need to keep repacking these every time the script is called
	*/
	if ( !$a_moz_data )
	{
		$a_moz_data = array( $moz_type, $moz_number, $moz_rv, $moz_rv_full, $moz_release_date );
	}
	if ( !$a_webkit_data )
	{
		$a_webkit_data = array( $webkit_type, $webkit_type_number, $browser_number );
	}
	$run_time = G::script_time();
	// then pack the primary data array
	if ( !$a_full_assoc_data )
	{
		$a_full_assoc_data = array(
			'browser_working' => $browser_working,
			'browser_number' => $browser_number,
			'ie_version' => $ie_version,
			'dom' => $b_dom_browser,
			'safe' => $b_safe_browser,
			'os' => $os_type,
			'os_number' => $os_number,
			'browser_name' => $browser_name,
			'ua_type' => $ua_type,
			'browser_math_number' => $browser_math_number,
			'moz_data' => $a_moz_data,
			'webkit_data' => $a_webkit_data,
			'mobile_test' => $mobile_test,
			'mobile_data' => $a_mobile_data,
			'true_ie_number' => $true_ie_number,
			'run_time' => $run_time
		);
	}
	// return parameters, either full data arrays, or by associative array index key
	switch ( $which_test )
	{
		// returns all relevant browser information in an array with standard numberic indexes
		case 'full':
			$a_full_data = array(
				$browser_working,
				$browser_number,
				$ie_version,
				$b_dom_browser,
				$b_safe_browser,
				$os_type,
				$os_number,
				$browser_name,
				$ua_type,
				$browser_math_number,
				$a_moz_data,
				$a_webkit_data,
				$mobile_test,
				$a_mobile_data,
				$true_ie_number,
				$run_time
			);
			// print_r( $a_full_data );
			return $a_full_data;
			break;
		// returns all relevant browser information in an associative array
		case 'full_assoc':
			return $a_full_assoc_data;
			break;
		default:
			# check to see if the data is available, otherwise it's user typo of unsupported option
			if ( isset( $a_full_assoc_data[$which_test] ) )
			{
				return $a_full_assoc_data[$which_test];
			}
			else
			{
				die( "You passed the browser detector an unsupported option for parameter 1: " . $which_test );
			}
			break;
	}
}

// gets which os from the browser string
function get_os_data ( $pv_browser_string, $pv_browser_name, $pv_version_number  )
{
	// initialize variables
	$os_working_type = '';
	$os_working_number = '';
	/*
	packs the os array. Use this order since some navigator user agents will put 'macintosh'
	in the navigator user agent string which would make the nt test register true
	*/
	$a_mac = array( 'intel mac', 'ppc mac', 'mac68k' );// this is not used currently
	// same logic, check in order to catch the os's in order, last is always default item
	$a_unix_types = array( 'dragonfly', 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' );
	// only sometimes will you get a linux distro to id itself...
	$a_linux_distros = array( 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'jolicloud', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' );
	$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
	// note, order of os very important in os array, you will get failed ids if changed
	$a_os_types = array( 'android', 'blackberry', 'iphone', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros );
	
	//os tester
	$i_count = count( $a_os_types );
	for ( $i = 0; $i < $i_count; $i++ )
	{
		// unpacks os array, assigns to variable $a_os_working
		$os_working_data = $a_os_types[$i];
		/*
		assign os to global os variable, os flag true on success
		!strstr($pv_browser_string, "linux" ) corrects a linux detection bug
		*/
		if ( !is_array( $os_working_data ) && strstr( $pv_browser_string, $os_working_data ) && !strstr( $pv_browser_string, "linux" ) )
		{
			$os_working_type = $os_working_data;
			
			switch ( $os_working_type )
			{
				// most windows now uses: NT X.Y syntax
				case 'nt':
					if ( strstr( $pv_browser_string, 'nt 6.1' ) )// windows 7
					{
						$os_working_number = 6.1;
					}
					elseif ( strstr( $pv_browser_string, 'nt 6.0' ) )// windows vista/server 2008
					{
						$os_working_number = 6.0;
					}
					elseif ( strstr( $pv_browser_string, 'nt 5.2' ) )// windows server 2003
					{
						$os_working_number = 5.2;
					}
					elseif ( strstr( $pv_browser_string, 'nt 5.1' ) || strstr( $pv_browser_string, 'xp' ) )// windows xp
					{
						$os_working_number = 5.1;//
					}
					elseif ( strstr( $pv_browser_string, 'nt 5' ) || strstr( $pv_browser_string, '2000' ) )// windows 2000
					{
						$os_working_number = 5.0;
					}
					elseif ( strstr( $pv_browser_string, 'nt 4' ) )// nt 4
					{
						$os_working_number = 4;
					}
					elseif ( strstr( $pv_browser_string, 'nt 3' ) )// nt 4
					{
						$os_working_number = 3;
					}
					break;
				case 'win':
					if ( strstr( $pv_browser_string, 'vista' ) )// windows vista, for opera ID
					{
						$os_working_number = 6.0;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, 'xp' ) )// windows xp, for opera ID
					{
						$os_working_number = 5.1;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, '2003' ) )// windows server 2003, for opera ID
					{
						$os_working_number = 5.2;
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, 'windows ce' ) )// windows CE
					{
						$os_working_number = 'ce';
						$os_working_type = 'nt';
					}
					elseif ( strstr( $pv_browser_string, '95' ) )
					{
						$os_working_number = '95';
					}
					elseif ( ( strstr( $pv_browser_string, '9x 4.9' ) ) || ( strstr( $pv_browser_string, ' me' ) ) )
					{
						$os_working_number = 'me';
					}
					elseif ( strstr( $pv_browser_string, '98' ) )
					{
						$os_working_number = '98';
					}
					elseif ( strstr( $pv_browser_string, '2000' ) )// windows 2000, for opera ID
					{
						$os_working_number = 5.0;
						$os_working_type = 'nt';
					}
					break;
				case 'mac':
					if ( strstr( $pv_browser_string, 'os x' ) )
					{
						// if it doesn't have a version number, it is os x;
						if ( strstr( $pv_browser_string, 'os x ' ) )
						{
							// numbers are like: 10_2.4, others 10.2.4
							$os_working_number = str_replace( '_', '.', G::get_item_version( $pv_browser_string, 'os x' ) );
						}
						else
						{
							$os_working_number = 10;
						}
					}
					/*
					this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3
					are only made for os x
					*/
					elseif ( ( $pv_browser_name == 'saf' ) || ( $pv_browser_name == 'cam' ) ||
						( ( $pv_browser_name == 'moz' ) && ( $pv_version_number >= 1.3 ) ) ||
						( ( $pv_browser_name == 'ie' ) && ( $pv_version_number >= 5.2 ) ) )
					{
						$os_working_number = 10;
					}
					break;
				case 'iphone':
					$os_working_number = 10;
					break;
				default:
					break;
			}
			break;
		}
		/*
		check that it's an array, check it's the second to last item
		in the main os array, the unix one that is
		*/
		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 2 ) ) )
		{
			$j_count = count($os_working_data);
			for ($j = 0; $j < $j_count; $j++)
			{
				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
				{
					$os_working_type = 'unix'; //if the os is in the unix array, it's unix, obviously...
					$os_working_number = ( $os_working_data[$j] != 'unix' ) ? $os_working_data[$j] : '';// assign sub unix version from the unix array
					break;
				}
			}
		}
		/*
		check that it's an array, check it's the last item
		in the main os array, the linux one that is
		*/
		elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 1 ) ) )
		{
			$j_count = count($os_working_data);
			for ($j = 0; $j < $j_count; $j++)
			{
				if ( strstr( $pv_browser_string, $os_working_data[$j] ) )
				{
					$os_working_type = 'lin';
					// assign linux distro from the linux array, there's a default
					//search for 'lin', if it's that, set version to ''
					$os_working_number = ( $os_working_data[$j] != 'linux' ) ? $os_working_data[$j] : '';
					break;
				}
			}
		}
	}

	// pack the os data array for return to main function
	$a_os_data = array( $os_working_type, $os_working_number );

	return $a_os_data;
}

/*
Function Info:
function returns browser number, gecko rv number, or gecko release date
function get_item_version( $browser_user_agent, $search_string, $substring_length )
$pv_extra_search='' allows us to set an additional search/exit loop parameter, but we
only want this running when needed
*/
function get_item_version( $pv_browser_user_agent, $pv_search_string, $pv_b_break_last='', $pv_extra_search='' )
{
	// 12 is the longest that will be required, handles release dates: 20020323; 0.8.0+
	$substring_length = 15;
	$start_pos = 0; // set $start_pos to 0 for first iteration
	//initialize browser number, will return '' if not found
	$string_working_number = '';
	/*
	use the passed parameter for $pv_search_string
	start the substring slice right after these moz search strings
	there are some cases of double msie id's, first in string and then with then number
	$start_pos = 0;
	this test covers you for multiple occurrences of string, only with ie though
	with for example google bot you want the first occurance returned, since that's where the
	numbering happens
	*/
	for ( $i = 0; $i < 4; $i++ )
	{
		//start the search after the first string occurrence
		if ( strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) !== false )
		{
			// update start position if position found
			$start_pos = strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) + strlen( $pv_search_string );
			/*
			msie (and maybe other userAgents requires special handling because some apps inject
			a second msie, usually at the beginning, custom modes allow breaking at first instance
			if $pv_b_break_last $pv_extra_search conditions exist. Since we only want this test
			to run if and only if we need it, it's triggered by caller passing these values.
			*/
			if ( !$pv_b_break_last || ( $pv_extra_search && strstr( $pv_browser_user_agent, $pv_extra_search ) ) )
			{
				break;
			}
		}
		else
		{
			break;
		}
	}
	/*
	Handles things like extra omniweb/v456, gecko/, blackberry9700
	also corrects for the omniweb 'v'
	*/
	$start_pos += G::get_set_count( 'get' );
	$string_working_number = substr( $pv_browser_user_agent, $start_pos, $substring_length );

	// Find the space, ;, or parentheses that ends the number
	$string_working_number = substr( $string_working_number, 0, strcspn($string_working_number, ' );/') );

	//make sure the returned value is actually the id number and not a string
	// otherwise return ''
	// strcspn( $string_working_number, '0123456789.') == strlen( $string_working_number)
	//	if ( preg_match("/\\d/", $string_working_number) == 0 )
 	if ( !is_numeric( substr( $string_working_number, 0, 1 ) ) )
	{
		$string_working_number = '';
	}
	//$string_working_number = strrpos( $pv_browser_user_agent, $pv_search_string );
	return $string_working_number;
}

function get_set_count( $pv_type, $pv_value='' )
{
	static $slice_increment;
	$return_value = '';
	switch ( $pv_type )
	{
		case 'get':
			// set if unset, ie, first use. note that empty and isset are not good tests here
			if ( is_null( $slice_increment ) )
			{
				$slice_increment = 1;
			}
			$return_value = $slice_increment;
			$slice_increment = 1; // reset to default
			return $return_value;
			break;
		case 'set':
			$slice_increment = $pv_value;
			break;
	}
}

/*
Special ID notes:
Novarra-Vision is a Content Transformation Server (CTS)
*/
function check_is_mobile( $pv_browser_user_agent )
{
	$mobile_working_test = '';
	/*
	these will search for basic mobile hints, this should catch most of them, first check
	known hand held device os, then check device names, then mobile browser names
	This list is almost the same but not exactly as the 4 arrays in function below
	*/
	$a_mobile_search = array(
	/*
	Make sure to use only data here that always will be a mobile, so this list is not
	identical to the list of get_mobile_data
	*/
	// os
	'android', 'epoc', 'linux armv', 'palmos', 'palmsource', 'windows ce', 'windows phone os', 'symbianos', 'symbian os', 'symbian', 'webos',
	// devices - ipod before iphone or fails
	'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lge ', 'lge-', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zune', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_', 'htc ', 'sec-', 'sie-m', 'sie-s', 'spv ', 'vodaphone', 'smartphone', 'armv', 'midp', 'mobilephone',
	// browsers
	'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino',
	// services - astel out of business
	'astel',  'docomo',  'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone'
	);

	// then do basic mobile type search, this uses data from: get_mobile_data()
	$j_count = count( $a_mobile_search );
	for ($j = 0; $j < $j_count; $j++)
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_search[$j] ) )
		{
			$mobile_working_test = $a_mobile_search[$j];
			break;
		}
	}
	
	return $mobile_working_test;
}

/*
thanks to this page: http://www.zytrax.com/tech/web/mobile_ids.html
for data used here
*/
function get_mobile_data( $pv_browser_user_agent )
{
	$mobile_browser = '';
	$mobile_browser_number = '';
	$mobile_device = '';
	$mobile_device_number = '';
	$mobile_os = ''; // will usually be null, sorry
	$mobile_os_number = '';
	$mobile_server = '';
	$mobile_server_number = '';
	
	// browsers, show it as a handheld, but is not the os
	$a_mobile_browser = array( 'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino' );
	/*
	This goes from easiest to detect to hardest, so don't use this for output unless you
	clean it up more is my advice.
	Special Notes: do not include milestone in general mobile type test above, it's too generic
	*/
	$a_mobile_device = array( 'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'htc_dream', 'htc espresso', 'htc hero', 'htc halo', 'htc huangshan', 'htc legend', 'htc liberty', 'htc paradise', 'htc supersonic', 'htc tattoo', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zunehd', 'zune', 'j-phone', 'milestone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_',  'htc ', 'lge ', 'lge-', 'sec-', 'sie-m', 'sie-s', 'spv ', 'smartphone', 'armv', 'midp', 'mobilephone' );
	/*
	note: linux alone can't be searched for, and almost all linux devices are armv types
	ipad 'cpu os' is how the real os number is handled
	*/
	$a_mobile_os = array( 'android', 'epoc', 'cpu os', 'iphone os', 'palmos', 'palmsource', 'windows phone os', 'windows ce', 'symbianos', 'symbian os', 'symbian', 'webos', 'linux armv'  );
	
	// sometimes there is just no other id for the unit that the CTS type service/server
	$a_mobile_server = array( 'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone' );

	$k_count = count( $a_mobile_browser );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_browser[$k] ) )
		{
			$mobile_browser = $a_mobile_browser[$k];
			// this may or may not work, highly unreliable because mobile ua strings are random
			$mobile_browser_number = G::get_item_version( $pv_browser_user_agent, $mobile_browser );
			break;
		}
	}
	$k_count = count( $a_mobile_device );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_device[$k] ) )
		{
			$mobile_device = trim ( $a_mobile_device[$k], '-_' ); // but not space trims yet
			if ( $mobile_device == 'blackberry' )
			{
				G::get_set_count( 'set', 0 );
			}
			$mobile_device_number = G::get_item_version( $pv_browser_user_agent, $mobile_device );
			$mobile_device = trim( $mobile_device ); // some of the id search strings have white space
			break;
		}
	}
	$k_count = count( $a_mobile_os );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_os[$k] ) )
		{
			$mobile_os = $a_mobile_os[$k];
			// this may or may not work, highly unreliable
			$mobile_os_number = str_replace( '_', '.', G::get_item_version( $pv_browser_user_agent, $mobile_os ) );
			break;
		}
	}
	$k_count = count( $a_mobile_server );
	for ( $k = 0; $k < $k_count; $k++ )
	{
		if ( strstr( $pv_browser_user_agent, $a_mobile_server[$k] ) )
		{
			$mobile_server = $a_mobile_server[$k];
			// this may or may not work, highly unreliable
			$mobile_server_number = G::get_item_version( $pv_browser_user_agent, $mobile_server );
			break;
		}
	}
	// just for cases where we know it's a mobile device already
	if ( !$mobile_os && ( $mobile_browser || $mobile_device || $mobile_server ) && strstr( $pv_browser_user_agent, 'linux' ) )
	{
		$mobile_os = 'linux';
		$mobile_os_number = G::get_item_version( $pv_browser_user_agent, 'linux' );
	}

	$a_mobile_data = array( $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number, $mobile_device_number );
	return $a_mobile_data;
}

  function getBrowser()
  {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?P<browser>' . join('|', $known) . ')[/ ]+(?P<version>[0-9.|a-zA-Z.]*)#';
    @preg_match_all($pattern, $u_agent, $matches);
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => strtolower($ub),
        'longName'  => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
  }



// track total script execution time
function script_time()
{
	static $script_time;
	$elapsed_time = '';
	/*
	note that microtime(true) requires php 5 or greater for microtime(true)
	*/
	if ( sprintf("%01.1f", phpversion() ) >= 5 ) {
		if ( is_null( $script_time) ) {
			$script_time = microtime(true);
		}
		else {
			// note: (string)$var is same as strval($var)
			// $elapsed_time = (string)( microtime(true) - $script_time );
			$elapsed_time = ( microtime(true) - $script_time );
			$elapsed_time = sprintf("%01.8f", $elapsed_time );
			$script_time = NULL; // can't unset a static variable
			return $elapsed_time;
		}
	}
}
function getDirectorySize($path,$maxmtime=0)
{
  $totalsize = 0;
  $totalcount = 0;
  $dircount = 0;
  if ($handle = opendir ($path))
  {
    while (false !== ($file = readdir($handle)))
    {
      $nextpath = $path . '/' . $file;
      if ($file != '.' && $file != '..' && !is_link ($nextpath) && $file != '.svn')
      {
        if (is_dir ($nextpath))
        {
          $dircount++;
          $result = G::getDirectorySize($nextpath,$maxmtime);
          $totalsize += $result['size'];
          $totalcount += $result['count'];
          $dircount += $result['dircount'];
          $maxmtime=$result['maxmtime']>$maxmtime?$result['maxmtime']:$maxmtime;
        }
        elseif (is_file ($nextpath))
        {
          $totalsize += filesize ($nextpath);
          $totalcount++;
          
          
          $mtime = filemtime($nextpath);
          if($mtime>$maxmtime) $maxmtime=$mtime;
          
          
        }
      }
    }
  }
  closedir ($handle);
  $total['size'] = $totalsize;
  $total['count'] = $totalcount;
  $total['dircount'] = $dircount;
  $total['maxmtime'] = $maxmtime;
   
  return $total;
}

  /**
   * Get checksum from multiple files
   * @author erik amaru ortiz <erik@colosa.com> 
   */
  function getCacheFileNameByPattern($path, $pattern) 
  {
    if ($file = glob($path . $pattern))
      preg_match('/[a-f0-9]{32}/', $file[0], $match);
    else 
      $file[0] = '';
    return array('filename'=>$file[0], 'checksum'=>(isset($match[0])? $match[0]: ''));
  }


  /**
   * Get checksum from multiple files
   * @author erik amaru ortiz <erik@colosa.com> 
   */
  function getCheckSum($files) 
  { 
    G::LoadClass('system');
    $key = System::getVersion();
    
    if (!is_array($files)) {
      $tmp = $files;
      $files = array();
      $files[0] = $tmp;
    }
    
    $checkSum = '';
    foreach ($files as $file) {
      if (is_file($file))
        $checkSum .= md5_file($file);
    }
    return md5($checkSum.$key);
  }
  
  /**
   * parse_ini_string
     Define parse_ini_string if it doesn't exist.
     Does accept lines starting with ; as comments
     Does not accept comments after values
  */
  function parse_ini_string($string){
    if( function_exists('parse_ini_string') ) {
      return parse_ini_string($string);
    } 
    else {
      $array = Array();
      $lines = explode("\n", $string );
       
      foreach( $lines as $line ) {
        $statement = preg_match( "/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $line, $match );
        if( $statement ) {
          $key    = $match[ 'key' ];
          $value  = $match[ 'value' ];
               
          //Remove quote
          if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ) {
            $value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
          }
               
          $array[ $key ] = $value;
        }
      }
      return $array;
    }
  }

  /**
   * disableEnableINIvariable
     disable or enable a variable in ini file, this is useful for editing the env.ini file
     automatically get the value, and change to inverse value,  I mean from true to false and viceversa
  */
  function disableEnableINIvariable( $inifile, $variable ) {
    $enabled = 'false';
    if ( file_exists($inifile ) ) {
      $fp = fopen( $inifile, 'r' );
      $line = fgets($fp);
      $found = false;
      $buffer = null;
      
      while ( !feof($fp) ) {
       	$config = G::parse_ini_string($line);
        if ( isset($config[$variable] )) {
          $enabled = $config[$variable];
         	$buffer .= sprintf("%s = %d \n", $variable, 1- $enabled );
         	$found = true;
        }
        else {
         	$buffer .= trim($line) . "\n";
        }  
        $line = fgets($fp);
      }
      fclose($fp);
      if ( !$found ) $buffer .= sprintf("\n%s = 1 \n", $variable );
          
      @file_put_contents( $inifile, $buffer);
    }
    else {
      $contents = file_put_contents($inifile, sprintf("\n%s = 1\n", $variable));
    }
  }
		
  /**
   * set a variable in ini file
  */
  function setINIvariable( $inifile, $variable, $value ) {
    if ( file_exists($inifile ) ) {
      $fp = fopen( $inifile, 'r' );
      $line = fgets($fp);
      $found = false;
      $buffer = null;
      
      while ( !feof($fp) ) {
       	$config = G::parse_ini_string($line);
        if ( isset($config[$variable] )) {
          $enabled = $config[$variable];
         	$buffer .= sprintf("%s = %s \n", $variable, $value );
         	$found = true;
        }
        else {
         	$buffer .= trim($line) . "\n";
        }  
        $line = fgets($fp);
      }
      fclose($fp);
      if ( !$found ) $buffer .= sprintf("\n%s = %s \n", $variable, $value );
          
      file_put_contents( $inifile, $buffer);
    }
    else {
      $contents = file_put_contents($inifile, sprintf("\n%s = $s\n", $variable, $value));
    }
  }

  function write_php_ini($file, $array)
  {
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    file_put_contents($file, implode("\r\n", $res));
  }


  /**
   * verify if all files & directories passed by param. are writable
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @param $resources array a list of files to verify write access
   */
  function verifyWriteAccess($resources)
  {
    $noWritable = array();
    foreach ($resources as $i => $resource) {
      if (!is_writable($resource)) {
        $noWritable[] = $resource;
      }
    }

    if (count($noWritable) > 0) {
      $e = new Exception("Write access not allowed for ProcessMaker resources");
      $e->files = $noWritable;
      throw $e;
    }
  }

  /**
   * render a smarty template
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @param $template string containing the template filename on /gulliver/templates/ directory
   * @param $data associative array containig the template data
   */
  function renderTemplate($template, $data=array())
  {
    if (!defined('PATH_THIRDPARTY')) {
      throw new Exception('System constant (PATH_THIRDPARTY) is not defined!');
    }

    require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php'; 
    $fInfo = pathinfo($template);

    $tplExists = true;

    // file has absolute path
    if (substr($template, 0, 1) != PATH_SEP) {
      $template = PATH_TEMPLATE . $template;
    }

    // fix for template that have dot in its name but is not a valid extension
    if (isset($fInfo['extension']) && ($fInfo['extension'] != 'tpl' || $fInfo['extension'] != 'html')) {
      unset($fInfo['extension']);
    }

    if (!isset($fInfo['extension'])) {
      if (file_exists($template . '.tpl')) {
        $template .= '.tpl';
      }
      else if (file_exists($template . '.html')) {
        $template .= '.html';
      }
      else {
        $tplExists = false;
      }
    }
    else {
      if (!file_exists($template)) {
        $tplExists = false;
      }
    }

    if (!$tplExists) {
      throw new Exception("Template: $template, doesn't exist!");
    }

    $smarty = new Smarty();
    $smarty->compile_dir = G::sys_get_temp_dir();
    $smarty->cache_dir   = G::sys_get_temp_dir();
    $smarty->config_dir  = PATH_THIRDPARTY . 'smarty/configs';

    $smarty->template_dir  = PATH_TEMPLATE;
    $smarty->force_compile = true;

    foreach ($data as $key => $value) {
      $smarty->assign($key, $value);
    }

    $smarty->display($template);
  }

  /**
   * parse a smarty template and return teh result as string
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @param $template string containing the template filename on /gulliver/templates/ directory
   * @param $data associative array containig the template data
   * @return $content string containing the parsed template content
   */
  function parseTemplate($template, $data=array())
  {
    $content = '';
    
    ob_start();
    G::renderTemplate($template, $data);
    $content = ob_get_contents();
    ob_get_clean();

    return $content;
  }

  /**
   * Update a ini file passing a array values, this function don't remove the original comments
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @licence GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
   *
   * @param $file string containing the ini file to update
   * @param $array associative array containing the config data
   */
  function update_php_ini($file, $array)
  {
    $iniLines = array();
    $iniContent = array();
    
    if (file_exists($file) && !is_writable($file)) {
      throw new Exception("File $file, is not writable.");
    }
    
    if (file_exists($file)) {
      $iniContent = file($file);
    }

    foreach ($iniContent as $line) {
      $line = trim($line);
      $lineParts = explode(';', $line);
      $setting = G::parse_ini_string($lineParts[0]);

      if (is_array($setting) && count($setting) > 0) {
        list($key, ) = array_keys($setting);

        if (isset($array[$key])) {          
          $value = $array[$key];
          $line = "$key = ".(is_numeric($value) ? $value : '"'.$value.'"');
          $line .= isset($lineParts[1]) ? ' ;' . $lineParts[1] : '';
          unset($array[$key]);
          
          $lastComment = array_pop($iniLines);
          if (strpos($lastComment, "Setting $key") === false) {
            $iniLines[] = $lastComment; 
          }

          $iniLines[] = ";Setting $key - Updated by System on " . date('D d M, Y H:i:s');
        }
      }
      $iniLines[] = $line; 
    }

    // inserting new values
    foreach ($array as $key => $value) {
      $line = "$key = ".(is_numeric($value) ? $value : '"'.$value.'"');
      $iniLines[] = '';
      $iniLines[] = ";Setting $key - Created by System on " . date('D d M, Y H:i:s');
      $iniLines[] = $line;
    }

    $content = implode("\r\n", $iniLines);

    if (@file_put_contents($file, $content) === false) {
      throw new Exception("G::update_php_ini() -> can't update file: $file");
    }
  }

  /**
   * recursive file & directories write permission detect
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @licence GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
   *
   * @param $path string of directory or file to verify recursively
   * @param $noWritableFiles (alternative) array passed by reference to store all no-writable files
   * @return bool true if all files inside a directory path are writable, false in another case
   */
  function is_writable_r($path, &$noWritableFiles = array()) 
  { 
    if (is_writable($path)){
      if (!is_dir($path))
        return true;

      $list = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .'*');
      
      $sw = true;
      foreach ($list as $f) {
        if (!G::is_writable_r($f, $noWritableFiles)) {
          $sw = false;
        }
      }

      return $sw;
    }
    else {
      if (!in_array($path, $noWritableFiles))
        $noWritableFiles[] = $path; 
      
      return false;
    }
  }
};

/**
 * eprint
 *
 * @param  string $s default value ''
 * @param  string $c default value null
 *
 * @return void
 */
function eprint($s = "", $c = null){
  if( G::isHttpRequest() ){
    if(isset($c)){
      echo "<pre style='color:$c'>$s</pre>";
    } else
      echo "<pre>$s</pre>";
  } else {
    if(isset($c)){
      switch($c){
        case 'green':
          printf("\033[0;35;32m$s\033[0m");
          return;
        case 'red':
          printf("\033[0;35;31m$s\033[0m");
          return;
        case 'blue':
          printf("\033[0;35;34m$s\033[0m");
          return;
        default: print "$s";
      }
    } else
      print "$s";
  }
}

/**
 * println
 *
 * @param  string $s
 *
 * @return eprintln($s)
 */
function println($s){
  return eprintln($s);
}

/**
 * eprintln
 *
 * @param  string $s
 * @param  string $c
 *
 * @return void
 */
function eprintln($s="", $c=null){
  if( G::isHttpRequest() ){
    if(isset($c)){
      echo "<pre style='color:$c'>$s</pre>";
    } else
      echo "<pre>$s</pre>";
  } else {
    if(isset($c) && (PHP_OS != 'WINNT')){
      switch($c){
        case 'green':
          printf("\033[0;35;32m$s\033[0m\n");
          return;
        case 'red':
          printf("\033[0;35;31m$s\033[0m\n");
          return;
        case 'blue':
          printf("\033[0;35;34m$s\033[0m\n");
          return;
      }
    }
    print "$s\n";
  }
  
  
}

function __($msgID , $lang = SYS_LANG, $data = null)
{
  return G::LoadTranslation($msgID, $lang, $data);
}
