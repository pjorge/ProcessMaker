<?php
/**
 * class.headPublisher.php
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
 * Class headPublisher
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 */
class headPublisher {
  private static $instance = NULL;
  var $maborakFiles = array ();
  var $maborakLoaderFiles = array ();
  var $scriptFiles = array ();
  var $leimnudLoad = array ();

  /* extJsSkin  init coreLoad flag*/
  var $extJsInit = 'false';

  /* extJsSkin  store the current skin for the ExtJs*/
  var $extJsSkin = '';

  /* extJsScript Array, to store the file to be include  */
  var $extJsScript = array ();

  /* extJsLibrary Array, to store extended ExtJs lybraries  */
  var $extJsLibrary = array ();

  /* extJsContent Array, to store the file to be include in the skin content  */
  var $extJsContent = array ();

  /* extVariable array, to store the variables generated in PHP, and used in JavaScript */
  var $extVariable = array ();

  /* variable array, to store the variables generated in PHP, and used in JavaScript */
  var $vars = array ();

  /* tplVariable array, to store the variables for template power */
  var $tplVariable = array ();

  var $leimnudInitString = '  var leimnud = new maborak();
  leimnud.make({
    zip:true,
    inGulliver:true,
    modules :"dom,abbr,rpc,drag,drop,app,panel,fx,grid,xmlform,validator,dashboard",
    files :""
  });';
  var $headerScript = '
  try{
    leimnud.exec(leimnud.fix.memoryLeak);
    if(leimnud.browser.isIphone){
      leimnud.iphone.make();
    }
  }catch(e){}';
  var $disableHeaderScripts = false;
  var $title = '';

  /**
   * Function headPublisher
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */

  private function __construct() {
    $this->addScriptFile ( "/js/maborak/core/maborak.js" );
  }

  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new headPublisher ( );
    }
    return self::$instance;
  }

  /**
   * Function setTitle
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string url
   * @parameter string LoadType
   * @return string
   */
  function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Function addMaborakFile
   * @access public
   * @parameter string filename
   * @parameter string loader;   false -> maborak files, true maborak.loader
   * @return string
   */
  function addMaborakFile($filename, $loader = false) {
    if ($loader)
      $this->maborakLoaderFiles [] = $filename;
    else
      $this->maborakFiles [] = $filename;
  }

  /**
   * Function addScriptFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string url
   * @parameter string LoadType
   * @return string
   */
  function addScriptFile($url, $LoadType = 1) {
    if ($LoadType == 1)
      $this->scriptFiles [$url] = $url;
    if ($LoadType == 2)
      $this->leimnudLoad [$url] = $url;
  }

  /**
   * Function addInstanceModule
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string instance
   * @parameter string module
   * @return string
   */

  function addInstanceModule($instance, $module) {
    $this->headerScript .= "leimnud.Package.Load('" . $module . "',{Instance:" . $instance . ",Type:'module'});\n";
  }

  /**
   * Function addClassModule
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string class
   * @parameter string module
   * @return string
   */
  function addClassModule($class, $module) {
    $this->headerScript .= "leimnud.Package.Load('" . $module . "',{Class:" . $class . ",Type:'module'});\n";
  }

  /**
   * Function addScriptCode
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string script
   * @return string
   */
  function addScriptCode($script) {
    $this->headerScript .= $script;
  }

  /**
   * Function printHeader
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function printHeader() {
    $jslabel = 'labels/en.js';
    if (defined ( 'SYS_LANG' )) {
      $jslabel = 'labels' . PATH_SEP . SYS_LANG . '.js';
      if (! file_exists ( PATH_CORE . 'js' . PATH_SEP . $jslabel ))
        $jslabel = 'labels/en.js';
    }
    if (file_exists ( PATH_CORE . 'js' . PATH_SEP . $jslabel )) {
      $this->addScriptFile ( '/jscore/' . $jslabel, 1 );
    }
    if ($this->disableHeaderScripts)
      return '';

    // available js-calendar languages array
    $availableJsCalendarLang = array('ca', 'cn', 'cz', 'de', 'en', 'es', 'fr', 'it', 'jp', 'nl', 'pl', 'pt', 'ro', 'ru', 'sv');

    // get the system language without locale
    $sysLang = explode('-', SYS_LANG);
    $sysLang = $sysLang[0];

    // verify if the requested lang by the system is supported by js-calendar library, if not set english by default
    $sysLang = in_array($sysLang, $availableJsCalendarLang) ? $sysLang : 'en';

    $this->addScriptFile ( "/js/widgets/js-calendar/unicode-letter.js" );
    $this->addScriptFile ( "/js/widgets/js-calendar/lang/".$sysLang.".js" );
    $this->addScriptFile ( "/js/widgets/js-calendar/lang/en.js" );

    $head = '';
    $head .= '<TITLE>' . $this->title . "</TITLE>\n";
    foreach ( $this->scriptFiles as $file )
      $head .= "<script type='text/javascript' src='" . $file . "'></script>\n";
    $head .= "<script type='text/javascript'>\n";
    $head .= $this->leimnudInitString;
    foreach ( $this->leimnudLoad as $file )
      $head .= "  leimnud.Package.Load(false, {Type: 'file', Path: '" . $file . "', Absolute : true});\n";
    $head .= $this->headerScript;
    $head .= "</script>\n";
    return $head;
  }

  /**
   * Function printRawHeader
   * Its prupose is to load el HEADs initialization javascript
   * into a single SCRIPT tag, it is usefull when it is needed
   * to load a page by leimnud floating panel of by another ajax
   * method. (See also RAW skin)
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function printRawHeader() {
    $jslabel = '/jscore/labels/en.js';
    if (defined ( 'SYS_LANG' )) {
      $jslabel1 = 'labels' . PATH_SEP . SYS_LANG . '.js';
      if (! file_exists ( PATH_CORE . 'js' . PATH_SEP . $jslabel1 ))
        $jslabel = '/jscore/labels/en.js';
    }
    $head = '';
    //$head .= "<script language='javascript'>\n";
    foreach ( $this->scriptFiles as $file ) {
      if (($file != "/js/maborak/core/maborak.js") && ($file != $jslabel)) {
        $head .= "  eval(ajax_function('" . $file . "','',''));\n";
      }
    }
    foreach ( $this->leimnudLoad as $file )
      $head .= "  eval(ajax_function('" . $file . "','',''));\n";
      //Adapts the add events on load to simple javascript sentences.
    $this->headerScript = preg_replace ( '/\s*leimnud.event.add\s*\(\s*window\s*,\s*(?:\'|")load(?:\'|")\s*,\s*function\(\)\{(.+)\}\s*\)\s*;?/', '$1', $this->headerScript );
    $head .= $this->headerScript;
    //$head .= "</script>\n";
    return $head;
  }

  /**
   * Function clearScripts
   * Its prupose is to clear all the scripts of the header.
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function clearScripts() {
    $this->scriptFiles = array ();
    $this->leimnudLoad = array ();
    $this->leimnudInitString = '';
    $this->headerScript = '';
  }

  /**
   * Function includeExtJs
   * with this function we are using the ExtJs library, this library is not compatible with
   * previous libraries, for that reason oHeadPublisher will clear previous libraries like maborak
   * we need to check if we need the language file
   * this function returns the header needed to render a page using ExtJs
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function includeExtJs() {
    $this->clearScripts ();
    $head = '';
    $head .= "  <script type='text/javascript' src='/js/ext/ext-base.js'></script>\n";
    $head .= "  <script type='text/javascript' src='/js/ext/ext-all.js'></script>\n";

    // enabled for particular use
    $head .= $this->getExtJsLibraries();

    // $head .= "  <script type='text/javascript' src='/js/ext/draw2d.js'></script>\n";
    $head .= "  <script type='text/javascript' src='/js/ext/translation.".SYS_LANG.".js'></script>\n";

    if (! isset ( $this->extJsSkin ) || $this->extJsSkin == '') {
      $this->extJsSkin = 'xtheme-gray';
      //$this->extJsSkin = 'gtheme';
    }

    //$head .= $this->getExtJsStylesheets();
    $head .= $this->getExtJsScripts();
    $head .= $this->getExtJsVariablesScript();

    return $head;
  }

  function getExtJsStylesheets($skinName){
    $script = "  <link rel='stylesheet' type='text/css' href='/css/$skinName.css' />\n";
    $script .= "  <script type='text/javascript' src='/js/ext/translation.en.js'></script>\n";
/*
    $script .= "  <link rel='stylesheet' type='text/css' href='/skins/ext/ext-all-notheme.css' />\n";
    $script .= "  <link rel='stylesheet' type='text/css' href='/skins/ext/" . $this->extJsSkin.".css' />\n";

    // <!-- DEPRECATED, this will be removed in a future - the three next lines
    if (file_exists ( PATH_HTML . 'skins' . PATH_SEP . 'ext' . PATH_SEP . 'pmos-' . $this->extJsSkin . '.css' )) {
      $script .= "  <link rel='stylesheet' type='text/css' href='/skins/ext/pmos-" . $this->extJsSkin . ".css' />\n";
    }
    //DEPRECATED, this will be removed in a future -->

    //new interactive css decorator
    $script .= "  <link rel='stylesheet' type='text/css' href='/gulliver/loader?t=extjs-cssExtended&s=".$this->extJsSkin."' />\n";
    $script .= "  <link rel='stylesheet' type='text/css' href='/images/icons_silk/sprite.css' />\n";
*/
    // Load external/plugin css
    // NOTE is necesary to move this to decorator server
    if(class_exists('PMPluginRegistry')){
      $oPluginRegistry = & PMPluginRegistry::getSingleton ();
      $registeredCss=$oPluginRegistry->getRegisteredCss();
      foreach($registeredCss as $cssFile){
        $script .= "  <link rel='stylesheet' type='text/css' href='" . $cssFile->sCssFile . ".css' />\n";
      }
    }

    return $script;
  }

  function getExtJsScripts(){
    $script = '';
    if (isset ( $this->extJsScript ) && is_array ( $this->extJsScript )) {
      foreach ( $this->extJsScript as $key => $file ) {
        $script .= "  <script type='text/javascript' src='" . $file . ".js'></script>\n";
      }
    }
    return $script;
  }

  function getExtJsVariablesScript(){
    $script = '';

    if (count ( $this->extVariable ) > 0) {
      $script = "<script language='javascript'>\n";
      foreach ( $this->extVariable as $key => $val ) {
        $name = $val ['name'];
        $value = $val ['value'];
        $variablesValues = G::json_encode($value);
        $variablesValues = $this->stripCodeQuotes($variablesValues);
//        var_dump($variablesValues);
//        echo "<br>";
        $script .= "  var $name = ".$variablesValues.";\n";
        /*
        if ($val ['type'] == 'number')
          $script .= "  var $name = $value;\n";
        else
          $script .= "  var $name = '$value';\n";
        */
      }
      $script .= "</script>\n";
    }

    return $script;
  }

  function getExtJsLibraries()
  {
    $script = '';
    if (isset ( $this->extJsLibrary ) && is_array ( $this->extJsLibrary )) {
      foreach ( $this->extJsLibrary as $file ) {
        $script .= "  <script type='text/javascript' src='/js/ext/" . $file . ".js'></script>\n";
      }
    }
    return $script;
  }

  /**
   * add a ExtJS extended library
   *
   * @author Erik A. Ortiz <erik@colosa.com>
   * @access public
   * @param (String) http js path library
   * @return none
   */
  function usingExtJs($library) {
    if (! is_string ( $library )) {
      throw new Exception ( 'headPublisher::usingExt->ERROR - the parameter should be a js path string' );
    }
    array_push ( $this->extJsLibrary, $library );
  }

  /**
   * Function setExtSkin
   * with this function we are using the ExtJs library, this library is not compatible with
   * previous libraries, for that reason oHeadPublisher will clear previous libraries like maborak
   * we need to check if we need the language file
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function setExtSkin($skin) {
    $this->extJsSkin = $skin;
  }

  /**
   * Function addExtJsScript
   * adding a javascript file  .js
   * add a js file in the extension Javascript Array,
   * later, when we use the includeExtJs function, all the files in this array will be included in the output
   * if the second argument is true, the file will not be minified, this is useful for debug purposes.
   *
   * Feature added - <erik@colosa.com>
   * - Hook to find javascript registered from plugins and load them
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @author Erik Amaru Ortiz <erik@colosa.com>
   * @access public
   * @return string
   */
  function addExtJsScript($filename, $debug = false, $isExternal=false) {

    $sPath = PATH_TPL;
    //if the template  file doesn't exists, then try with the plugins folders


    if (! is_file ( $sPath . $filename . ".js" )) {
      $aux = explode ( PATH_SEP, $filename );
      //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
      if (count ( $aux ) == 2 && defined ( 'G_PLUGIN_CLASS' )) {
        $oPluginRegistry = & PMPluginRegistry::getSingleton ();
        if ($oPluginRegistry->isRegisteredFolder ( $aux [0] )) {
          $sPath = PATH_PLUGINS;
        }
      }
    }

    if (!$isExternal)
      $jsFilename = $sPath . $filename . '.js';
    else
      $jsFilename = $filename . '.js';

    if (! file_exists ( $jsFilename )) {
      return;
    }

    $mtime = filemtime ( $jsFilename );
    G::mk_dir ( PATH_C . 'ExtJs' );
    if ($debug) {
      $cacheName = str_replace ( '/', '_', $filename );
      $cacheFilename = PATH_C . 'ExtJs' . PATH_SEP . $cacheName . '.js';
      file_put_contents ( $cacheFilename, file_get_contents ( $jsFilename ) );
    }
    else {
      $cacheName = md5 ( $mtime . $jsFilename );
      $cacheFilename = PATH_C . 'ExtJs' . PATH_SEP . $cacheName . '.js';

      if (! file_exists ( $cacheFilename )) {
        require_once (PATH_THIRDPARTY . 'jsmin/jsmin.php');
        $content = JSMin::minify ( file_get_contents ( $jsFilename ) );
        file_put_contents ( $cacheFilename, $content );
      }
    }


    $this->extJsScript [] = '/extjs/' . $cacheName;

    //hook for registered javascripts from plugins
    if ( class_exists( 'PMPluginRegistry' ) ) {
      $oPluginRegistry = & PMPluginRegistry::getSingleton();
      $pluginJavascripts = $oPluginRegistry->getRegisteredJavascriptBy($filename);
    }
    else
      $pluginJavascripts = array();

    if (count($pluginJavascripts) > 0) {
      if ($debug) {
        foreach ($pluginJavascripts as $pluginJsFile) {
          $jsPluginCacheName = '';
          if (substr($pluginJsFile, -3) != '.js') {
            $pluginJsFile .= '.js';
          }

          if (file_exists(PATH_PLUGINS . $pluginJsFile)) {
            $jsPluginCacheName = str_replace ( '/', '_', str_replace('.js', '', $pluginJsFile) );
            $cacheFilename = PATH_C . 'ExtJs' . PATH_SEP . $jsPluginCacheName.".js";
            file_put_contents ( $cacheFilename, file_get_contents ( PATH_PLUGINS . $pluginJsFile ) );
          }
          if ($jsPluginCacheName != '') {
            $this->extJsScript [] = '/extjs/' . $jsPluginCacheName;
          }
        }
      }
      else {
        foreach ($pluginJavascripts as $pluginJsFile) {
          $jsPluginCacheName = '';
          if (substr($pluginJsFile, -3) !== '.js') {
            $pluginJsFile .= '.js';
          }
          if (file_exists(PATH_PLUGINS . $pluginJsFile)) {
            $mtime = filemtime ( PATH_PLUGINS . $pluginJsFile );
            $jsPluginCacheName = md5 ( $mtime . $pluginJsFile );
            $cacheFilename = PATH_C . 'ExtJs' . PATH_SEP . $jsPluginCacheName . '.js';

            if (! file_exists ( $cacheFilename )) {
              require_once (PATH_THIRDPARTY . 'jsmin/jsmin.php');
              $content = JSMin::minify ( file_get_contents ( PATH_PLUGINS . $pluginJsFile ) );
              file_put_contents ( $cacheFilename, $content );
            }
          }
          if ($jsPluginCacheName != '') {
            $this->extJsScript [] = '/extjs/' . $jsPluginCacheName;
          }
        }
      }
    }
    //end hook for registered javascripts from plugins

  }

  /**
   * Function AddContent
   * adding a html file  .html.
   * the main idea for this function, is to be a replacement to homonymous function in Publisher class.
   * with this function you are adding Content to the output, the class HeadPublisher will maintain a list of
   * files to render in the body of the output page
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function AddContent($templateHtml) {
    $this->extJsContent [] = $templateHtml;
  }

  function getContent() {
    return $this->extJsContent;
  }

  /**
   * Function assign
   * assign a STRING value to a JS variable
   * use this function to send from PHP variables to be used in JavaScript
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function Assign($variable, $value) {
    $this->extVariable [] = array ('name' => $variable, 'value' => $value, 'type' => 'string' );
  }

  function AssignVar($name, $value) {
    $this->vars [$name] = $value;
  }

  function getVars() {
    return $this->vars;
  }

  /**
   * Function assignNumber
   * assign a Number value to a JS variable
   * use this function to send from PHP variables to be used in JavaScript
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function AssignNumber($variable, $value) {
    $this->extVariable [] = array ('name' => $variable, 'value' => $value, 'type' => 'number' );
  }
  /**
   * Function renderExtJs
   * this function returns the content rendered using ExtJs
   * extJsContent have an array, and we iterate this array to draw the content
   *
   * @author Fernando Ontiveros <fernando@colosa.com>
   * @access public
   * @return string
   */
  function renderExtJs() {
    $body = '';
    if (isset ( $this->extJsContent ) && is_array ( $this->extJsContent )) {
      foreach ( $this->extJsContent as $key => $file ) {
        $sPath = PATH_TPL;
        //if the template  file doesn't exists, then try with the plugins folders
        if (! is_file ( $sPath . $file . ".html" )) {
          $aux = explode ( PATH_SEP, $file );
          //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
          if (count ( $aux ) == 2 && defined ( 'G_PLUGIN_CLASS' )) {
            $oPluginRegistry = & PMPluginRegistry::getSingleton ();
            if ($oPluginRegistry->isRegisteredFolder ( $aux [0] )) {
              $sPath = PATH_PLUGINS;
            }
          }
        }

        $template = new TemplatePower ( $sPath . $file . '.html' );
        $template->prepare ();

        foreach ($this->getVars() as $k => $v) {
          $template->assign($k, $v);
        }

        $body .= $template->getOutputContent ();
      }
    }
    return $body;
  }

  function stripCodeQuotes($sJson){
    $fields = array( "editor", "renderer" );
    foreach ($fields as $field) {
      $pattern = '/"('.$field.')":"[a-zA-Z.()]*"/';
//      echo $pattern."<br>";
      preg_match ($pattern,$sJson,$matches);
//      var_dump ($matches);
//      echo "<br>";
      if (!empty($matches)){
        $rendererMatch = $matches[0];
        $replaceBy = explode(":", $matches[0]);
        $replaceBy[1] = str_replace('"','',$replaceBy[1]);
        $tmpString = implode(":",$replaceBy);
        $sJson = str_replace($rendererMatch, $tmpString, $sJson);
//        var_dump ($sJson);
//        echo "<br>";
      }
    }
    return $sJson;
  }

  /**
   * Function disableHeaderScripts
   * this function sets disableHeaderScripts to true
   * to avoid print scripts into the header
   *
   * @author Enrique Ponce de Leom <enrique@colosa.com>
   * @access public
   * @return string
   */
  function disableHeaderScripts(){
    $this->disableHeaderScripts = true;
  }
}
