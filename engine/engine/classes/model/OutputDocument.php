<?php
/**
 * OutputDocument.php
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

require_once 'classes/model/om/BaseOutputDocument.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'OUTPUT_DOCUMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class OutputDocument extends BaseOutputDocument {

  /**
   * This value goes in the content table
   * @var string
   */
  protected $out_doc_title = '';

  /**
   * This value goes in the content table
   * @var   string
   */
  protected $out_doc_description = '';

  /**
   * This value goes in the content table
   * @var string
   */
  protected $out_doc_filename = '';

  /**
   * This value goes in the content table
   * @var string
   */
  protected $out_doc_template = '';


  function __construct() { 
      $javaInput  = PATH_C . 'javaBridgePM' . PATH_SEP . 'input'  . PATH_SEP;
      $javaOutput = PATH_C . 'javaBridgePM' . PATH_SEP . 'output' . PATH_SEP;
      G::mk_dir ( $javaInput );
      G::mk_dir ( $javaOutput );  
  }

  public function getByUid($sOutDocUid)
  {
    try {
      $oOutputDocument = OutputDocumentPeer::retrieveByPK( $sOutDocUid );
      if( is_null($oOutputDocument) )
        return false;

      $aFields = $oOutputDocument->toArray(BasePeer::TYPE_FIELDNAME);
      $aFields['OUT_DOC_TITLE']       = $oOutputDocument->getOutDocTitle();
      $aFields['OUT_DOC_DESCRIPTION'] = $oOutputDocument->getOutDocDescription();
      $aFields['OUT_DOC_FILENAME']    = $oOutputDocument->getOutDocFilename();
      $aFields['OUT_DOC_TEMPLATE']    = $oOutputDocument->getOutDocTemplate();
      $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
      return $aFields;
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /*
  * Load the application document registry
  * @param string $sAppDocUid
  * @return variant
  */
  public function load($sOutDocUid)
  {
    try {
      $oOutputDocument = OutputDocumentPeer::retrieveByPK( $sOutDocUid );
      if (!is_null($oOutputDocument))
      {
        $aFields = $oOutputDocument->toArray(BasePeer::TYPE_FIELDNAME);       
        $aFields['OUT_DOC_TITLE']       = $oOutputDocument->getOutDocTitle();
        $aFields['OUT_DOC_DESCRIPTION'] = $oOutputDocument->getOutDocDescription();
        $aFields['OUT_DOC_FILENAME']    = $oOutputDocument->getOutDocFilename();
        $aFields['OUT_DOC_TEMPLATE']    = $oOutputDocument->getOutDocTemplate();
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        return $aFields;
      }
      else {
        throw(new Exception('This row doesn\'t exist!'));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /**
   * Create the application document registry
   * @param array $aData
   * @return string
  **/
  public function create($aData)
  {
    $oConnection = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
    try {
      if ( isset ( $aData['OUT_DOC_UID'] ) && $aData['OUT_DOC_UID']== '' )
        unset ( $aData['OUT_DOC_UID'] );
      if ( !isset ( $aData['OUT_DOC_UID'] ) )
        $aData['OUT_DOC_UID'] = G::generateUniqueID();
      if (!isset($aData['OUT_DOC_GENERATE'])) {
        $aData['OUT_DOC_GENERATE'] = 'BOTH';
      }
      else {
        if ($aData['OUT_DOC_GENERATE'] == '') {
          $aData['OUT_DOC_GENERATE'] = 'BOTH';
        }
      }
      $oOutputDocument = new OutputDocument();
      $oOutputDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
      if ($oOutputDocument->validate()) {
        $oConnection->begin();
        if (isset($aData['OUT_DOC_TITLE'])) {
          $oOutputDocument->setOutDocTitle($aData['OUT_DOC_TITLE']);
        }
        if (isset($aData['OUT_DOC_DESCRIPTION'])) {
          $oOutputDocument->setOutDocDescription($aData['OUT_DOC_DESCRIPTION']);
        }
        $oOutputDocument->setOutDocFilename($aData['OUT_DOC_FILENAME']);
        if (isset($aData['OUT_DOC_TEMPLATE'])) {
          $oOutputDocument->setOutDocTemplate($aData['OUT_DOC_TEMPLATE']);
        }
        $iResult = $oOutputDocument->save();
        $oConnection->commit();
        return $aData['OUT_DOC_UID'];
      }
      else {
        $sMessage = '';
        $aValidationFailures = $oOutputDocument->getValidationFailures();
        foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />'.$sMessage));
      }
    }
    catch (Exception $oError) {
      $oConnection->rollback();
      throw($oError);
    }
  }

  /**
   * Update the application document registry
   * @param array $aData
   * @return string
  **/
  public function update($aData)
  {
    $oConnection = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
    try {
      $oOutputDocument = OutputDocumentPeer::retrieveByPK($aData['OUT_DOC_UID']);
      if (!is_null($oOutputDocument))
      {
        $oOutputDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
        if ($oOutputDocument->validate()) {
          $oConnection->begin();
          if (isset($aData['OUT_DOC_TITLE']))
          {
            $oOutputDocument->setOutDocTitle($aData['OUT_DOC_TITLE']);
          }
          if (isset($aData['OUT_DOC_DESCRIPTION']))
          {
            $oOutputDocument->setOutDocDescription($aData['OUT_DOC_DESCRIPTION']);
          }
          if (isset($aData['OUT_DOC_FILENAME']))
          {
            $oOutputDocument->setOutDocFilename($aData['OUT_DOC_FILENAME']);
          }
          if (isset($aData['OUT_DOC_TEMPLATE']))
          {
            $oOutputDocument->setOutDocTemplate($aData['OUT_DOC_TEMPLATE']);
          }
          $iResult = $oOutputDocument->save();
          $oConnection->commit();
          return $iResult;
        }
        else {
          $sMessage = '';
          $aValidationFailures = $oOutputDocument->getValidationFailures();
          foreach($aValidationFailures as $oValidationFailure) {
            $sMessage .= $oValidationFailure->getMessage() . '<br />';
          }
          throw(new Exception('The registry cannot be updated!<br />'.$sMessage));
        }
      }
      else {
        throw(new Exception('This row doesn\'t exist!'));
      }
    }
    catch (Exception $oError) {
      $oConnection->rollback();
      throw($oError);
    }
  }

  /**
   * Remove the application document registry
   * @param array $aData
   * @return string
  **/
  public function remove($sOutDocUid)
  {
    $oConnection = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
    try {
      $oOutputDocument = OutputDocumentPeer::retrieveByPK($sOutDocUid);
      if (!is_null($oOutputDocument))
      {
        $oConnection->begin();
        Content::removeContent('OUT_DOC_TITLE', '', $oOutputDocument->getOutDocUid());
        Content::removeContent('OUT_DOC_DESCRIPTION', '', $oOutputDocument->getOutDocUid());
        Content::removeContent('OUT_DOC_FILENAME', '', $oOutputDocument->getOutDocUid());
        Content::removeContent('OUT_DOC_TEMPLATE', '', $oOutputDocument->getOutDocUid());
        $iResult = $oOutputDocument->delete();
        $oConnection->commit();
        return $iResult;
      }
      else {
        throw(new Exception('This row doesn\'t exist!'));
      }
    }
    catch (Exception $oError) {
      $oConnection->rollback();
      throw($oError);
    }
  }

  /**
   * Get the [out_doc_title] column value.
   * @return string
   */
  public function getOutDocTitle()
  {
    if ($this->out_doc_title == '') {
      try {
        $this->out_doc_title = Content::load('OUT_DOC_TITLE', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
      }
      catch (Exception $oError) {
        throw($oError);
      }
    }
    return $this->out_doc_title;
  }

  /**
   * Set the [out_doc_title] column value.
   *
   * @param string $sValue new value
   * @return void
   */
  public function setOutDocTitle($sValue)
  {
    if ($sValue !== null && !is_string($sValue)) {
      $sValue = (string)$sValue;
    }
    if ($this->out_doc_title !== $sValue || $sValue === '') {
      try {
        $this->out_doc_title = $sValue;
     
        $iResult = Content::addContent('OUT_DOC_TITLE', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->out_doc_title);
      }
      catch (Exception $oError) {
        $this->out_doc_title = '';
        throw($oError);
      }
    }
  }

  /**
   * Get the [out_doc_comment] column value.
   * @return string
   */
  public function getOutDocDescription()
  {
    if ($this->out_doc_description == '') {
      try {
        $this->out_doc_description = Content::load('OUT_DOC_DESCRIPTION', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
      }
      catch (Exception $oError) {
        throw($oError);
      }
    }
    return $this->out_doc_description;
  }

  /**
   * Set the [out_doc_comment] column value.
   *
   * @param string $sValue new value
   * @return void
   */
  public function setOutDocDescription($sValue)
  {
    if ($sValue !== null && !is_string($sValue)) {
      $sValue = (string)$sValue;
    }
    if ($this->out_doc_description !== $sValue || $sValue === '') {
      try {
        $this->out_doc_description = $sValue;
   
        $iResult = Content::addContent('OUT_DOC_DESCRIPTION', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->out_doc_description);
      }
      catch (Exception $oError) {
        $this->out_doc_description = '';
        throw($oError);
      }
    }
  }

  /**
   * Get the [out_doc_filename] column value.
   * @return string
   */
  public function getOutDocFilename()
  {
    if ($this->out_doc_filename == '') {
      try {
        $this->out_doc_filename = Content::load('OUT_DOC_FILENAME', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
      }
      catch (Exception $oError) {
        throw($oError);
      }
    }
    return $this->out_doc_filename;
  }

  /**
   * Set the [out_doc_filename] column value.
   *
   * @param string $sValue new value
   * @return void
   */
  public function setOutDocFilename($sValue)
  {
    if ($sValue !== null && !is_string($sValue)) {
      $sValue = (string)$sValue;
    }
    if ($this->out_doc_filename !== $sValue || $sValue === '') {
      try {
        $this->out_doc_filename = $sValue;
        $iResult = Content::addContent('OUT_DOC_FILENAME', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->out_doc_filename);
      }
      catch (Exception $oError) {
        $this->out_doc_filename = '';
        throw($oError);
      }
    }
  }

  /**
   * Get the [out_doc_template] column value.
   * @return string
   */
  public function getOutDocTemplate()
  {
    if ($this->out_doc_template == '') {
      try {
        $this->out_doc_template = Content::load('OUT_DOC_TEMPLATE', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
      }
      catch (Exception $oError) {
        throw($oError);
      }
    }
    return $this->out_doc_template;
  }

  /**
   * Set the [out_doc_template] column value.
   *
   * @param string $sValue new value
   * @return void
   */
  public function setOutDocTemplate($sValue)
  {
    if ($sValue !== null && !is_string($sValue)) {
      $sValue = (string)$sValue;
    }
    if ($this->out_doc_template !== $sValue || $sValue === '') {
      try {
        $this->out_doc_template = $sValue;
        $iResult = Content::addContent('OUT_DOC_TEMPLATE', '', $this->getOutDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->out_doc_template);
      }
      catch (Exception $oError) {
        $this->out_doc_template = '';
        throw($oError);
      }
    }
  }

  /*
  * Generate the output document
  * @param string $sUID
  * @param array $aFields
  * @param string $sPath
  * @return variant
  */
  public function generate($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape = false, $sTypeDocToGener = 'BOTH', $aProperties = array()) {
    if (($sUID != '') && is_array($aFields) && ($sPath != '')) {
      $sContent    = G::unhtmlentities($sContent);
      $iAux        = 0;
      $iOcurrences = preg_match_all('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $sContent, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
      if ($iOcurrences) {
        for($i = 0; $i < $iOcurrences; $i++) {
          preg_match_all('/@>' . $aMatch[2][$i][0] . '([\w\W]*)' . '@<' . $aMatch[2][$i][0] . '/', $sContent, $aMatch2, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
          $sGridName       = $aMatch[2][$i][0];
          $sStringToRepeat = $aMatch2[1][0][0];
          if (isset($aFields[$sGridName])) {
            if (is_array($aFields[$sGridName])) {
              $sAux = '';
              foreach ($aFields[$sGridName] as $aRow) {
                foreach ($aRow as $sKey => $vValue) {
                  if (!is_array($vValue)) {
                    $aRow[$sKey] = nl2br($aRow[$sKey]);
                  }
                }
                $sAux .= G::replaceDataField($sStringToRepeat, $aRow);
              }
            }
          }
          $sContent = str_replace('@>' . $sGridName . $sStringToRepeat . '@<' . $sGridName, $sAux, $sContent);
        }
      }
      foreach ($aFields as $sKey => $vValue) {
        if (!is_array($vValue)) {
          $aFields[$sKey] = nl2br($aFields[$sKey]);
        }
      }
      $sContent = G::replaceDataField($sContent, $aFields);
      G::verifyPath($sPath, true);
      /* Start - Create .doc */
      $oFile = fopen($sPath .  $sFilename . '.doc', 'wb');
      $size = array();
      $size["Letter"]         = "216mm  279mm"; 
      $size["Legal"]          = "216mm  357mm"; 
      $size["Executive"]      = "184mm  267mm"; 
      $size["B5"]             = "182mm  257mm"; 
      $size["Folio"]          = "216mm  330mm"; 
      $size["A0Oversize"]     = "882mm  1247mm";
      $size["A0"]             = "841mm  1189mm";
      $size["A1"]             = "594mm  841mm"; 
      $size["A2"]             = "420mm  594mm"; 
      $size["A3"]             = "297mm  420mm"; 
      $size["A4"]             = "210mm  297mm"; 
      $size["A5"]             = "148mm  210mm"; 
      $size["A6"]             = "105mm  148mm"; 
      $size["A7"]             = "74mm   105mm"; 
      $size["A8"]             = "52mm   74mm";  
      $size["A9"]             = "37mm   52mm";  
      $size["A10"]            = "26mm   37mm";  
      $size["Screenshot640"]  = "640mm  480mm"; 
      $size["Screenshot800"]  = "800mm  600mm"; 
      $size["Screenshot1024"] = "1024mm 768mm"; 
      
      $sizeLandscape["Letter"]         = "279mm  216mm";      
      $sizeLandscape["Legal"]          = "357mm  216mm";      
      $sizeLandscape["Executive"]      = "267mm  184mm";      
      $sizeLandscape["B5"]             = "257mm  182mm";      
      $sizeLandscape["Folio"]          = "330mm  216mm";      
      $sizeLandscape["A0Oversize"]     = "1247mm 882mm";      
      $sizeLandscape["A0"]             = "1189mm 841mm";      
      $sizeLandscape["A1"]             = "841mm  594mm";      
      $sizeLandscape["A2"]             = "594mm  420mm";      
      $sizeLandscape["A3"]             = "420mm  297mm";      
      $sizeLandscape["A4"]             = "297mm  210mm";      
      $sizeLandscape["A5"]             = "210mm  148mm";      
      $sizeLandscape["A6"]             = "148mm  105mm";      
      $sizeLandscape["A7"]             = "105mm  74mm";       
      $sizeLandscape["A8"]             = "74mm   52mm";       
      $sizeLandscape["A9"]             = "52mm   37mm";       
      $sizeLandscape["A10"]            = "37mm   26mm";       
      $sizeLandscape["Screenshot640"]  = "480mm  640mm";      
      $sizeLandscape["Screenshot800"]  = "600mm  800mm";      
      $sizeLandscape["Screenshot1024"] = "768mm  1024mm";  	
      
      if(!isset($aProperties['media']))
      	$aProperties['media'] = 'Letter';
      	
      if($sLandscape) 
        $media = $sizeLandscape[$aProperties['media']];
      else 
        $media = $size[$aProperties['media']];
        
      $marginLeft = '15'; 
      if(isset($aProperties['margins']['left']))
        $marginLeft = $aProperties['margins']['left']; 
      	
      $marginRight = '15'; 	
      if(isset($aProperties['margins']['right']))	
        $marginRight = $aProperties['margins']['right'];
      	 
      $marginTop = '15'; 
      if(isset($aProperties['margins']['top']))	
        $marginTop = $aProperties['margins']['top']; 
      
      $marginBottom = '15'; 
      if(isset($aProperties['margins']['bottom']))		
        $marginBottom = $aProperties['margins']['bottom']; 
      
      fwrite($oFile, '<html xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
      <head>
      <meta http-equiv=Content-Type content="text/html; charset=utf-8">
      <meta name=ProgId content=Word.Document>
      <meta name=Generator content="Microsoft Word 9">
      <meta name=Originator content="Microsoft Word 9">
      <!--[if !mso]>
      <style>
      v\:* {behavior:url(#default#VML);}
      o\:* {behavior:url(#default#VML);}
      w\:* {behavior:url(#default#VML);}
      .shape {behavior:url(#default#VML);}
      </style>
      <![endif]-->
      <!--[if gte mso 9]><xml>
       <w:WordDocument>
        <w:View>Print</w:View>
        <w:DoNotHyphenateCaps/>
        <w:PunctuationKerning/>
        <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing>
        <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing>
       </w:WordDocument>
      </xml><![endif]-->
      
      <style>
      <!--
      @page WordSection1
      	{size:'.$media.';
      	margin-left:'.$marginLeft.'mm; 
      	margin-right:'.$marginRight.'mm;
      	margin-bottom:'.$marginBottom.'mm; 
      	margin-top:'.$marginTop.'mm;
      	mso-header-margin:35.4pt;
      	mso-footer-margin:35.4pt;
      	mso-paper-source:0;}
      div.WordSection1
      	{page:WordSection1;}
      -->
      </style>
      </head>
      <body> 
      <div class=WordSection1>');
      fwrite($oFile, $sContent);
      fwrite($oFile, "\n</div></body></html>\n\n");
      fclose($oFile);
      /* End - Create .doc */
      
      if($sTypeDocToGener == 'BOTH' || $sTypeDocToGener == 'PDF'){
       /* Start - Create .pdf */
       $oFile = fopen($sPath .  $sFilename . '.html', 'wb');
       fwrite($oFile, $sContent);
       fclose($oFile);
       
       define('PATH_OUTPUT_FILE_DIRECTORY', PATH_HTML . 'files/' . $_SESSION['APPLICATION'] . '/outdocs/');
       G::verifyPath(PATH_OUTPUT_FILE_DIRECTORY, true);
       require_once(PATH_THIRDPARTY . 'html2ps_pdf/config.inc.php');
           require_once(PATH_THIRDPARTY . 'html2ps_pdf/pipeline.factory.class.php');
           parse_config_file(PATH_THIRDPARTY . 'html2ps_pdf/html2ps.config');
           $GLOBALS['g_config'] = array('cssmedia'                => 'screen',
                                    'media'                   => 'Letter',
                                    'scalepoints'             => false,
                                    'renderimages'            => true,
                                    'renderfields'            => true,
                                    'renderforms'             => false,
                                    'pslevel'                 => 3,
                                    'renderlinks'             => true,
                                    'pagewidth'               => 800,
                                    'landscape'               => $sLandscape,
                                    'method'                  => 'fpdf',
                                    'margins'                 => array('left' => 15, 'right' => 15, 'top' => 15, 'bottom' => 15,),
                                    'encoding'                => '',
                                    'ps2pdf'                  => false,
                                    'compress'                => false,
                                    'output'                  => 2,
                                    'pdfversion'              => '1.3',
                                    'transparency_workaround' => false,
                                    'imagequality_workaround' => false,
                                    'draw_page_border'        => isset($_REQUEST['pageborder']),
                                    'debugbox'                => false,
                                    'html2xhtml'              => true,
                                    'mode'                    => 'html',
                                    'smartpagebreak'          => true);
       $GLOBALS['g_config']= array_merge($GLOBALS['g_config'],$aProperties);                               
       $g_media = Media::predefined($GLOBALS['g_config']['media']);
       $g_media->set_landscape($GLOBALS['g_config']['landscape']);
       $g_media->set_margins($GLOBALS['g_config']['margins']);
       $g_media->set_pixels($GLOBALS['g_config']['pagewidth']);
       
       
       if(isset($GLOBALS['g_config']['pdfSecurity'])){
         if (isset($GLOBALS['g_config']['pdfSecurity']['openPassword']) && $GLOBALS['g_config']['pdfSecurity']['openPassword'] != "") {
    $GLOBALS['g_config']['pdfSecurity']['openPassword'] = G::decrypt($GLOBALS['g_config']['pdfSecurity']['openPassword'], $sUID);
         }
               if (isset($GLOBALS['g_config']['pdfSecurity']['ownerPassword']) && $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] != "") {
    $GLOBALS['g_config']['pdfSecurity']['ownerPassword'] = G::decrypt($GLOBALS['g_config']['pdfSecurity']['ownerPassword'], $sUID);
  }
  $g_media->set_security($GLOBALS['g_config']['pdfSecurity']);
  
        require_once(HTML2PS_DIR . 'pdf.fpdf.encryption.php');
       }
       
       $pipeline = new Pipeline();
       if (extension_loaded('curl'))
       {
         require_once(HTML2PS_DIR . 'fetcher.url.curl.class.php');
         $pipeline->fetchers = array(new FetcherURLCurl());
         if (isset($proxy)) {
           if ($proxy != '')
           {
             $pipeline->fetchers[0]->set_proxy($proxy);
           }
         }
       }
       else
       {
         require_once(HTML2PS_DIR . 'fetcher.url.class.php');
         $pipeline->fetchers[] = new FetcherURL();
       }
       $pipeline->data_filters[] = new DataFilterDoctype();
       $pipeline->data_filters[] = new DataFilterUTF8($GLOBALS['g_config']['encoding']);
       if ($GLOBALS['g_config']['html2xhtml'])
       {
         $pipeline->data_filters[] = new DataFilterHTML2XHTML();
       }
       else
       {
         $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
       }
       $pipeline->parser = new ParserXHTML();
       $pipeline->pre_tree_filters = array();
       $header_html = '';
       $footer_html = '';
       $filter      = new PreTreeFilterHeaderFooter($header_html, $footer_html);
       $pipeline->pre_tree_filters[] = $filter;
       if ($GLOBALS['g_config']['renderfields'])
       {
         $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
       }
       if ($GLOBALS['g_config']['method'] === 'ps')
       {
         $pipeline->layout_engine = new LayoutEnginePS();
       }
       else
       {
         $pipeline->layout_engine = new LayoutEngineDefault();
       }
       $pipeline->post_tree_filters = array();
       if ($GLOBALS['g_config']['pslevel'] == 3)
       {
         $image_encoder = new PSL3ImageEncoderStream();
       }
       else
       {
         $image_encoder = new PSL2ImageEncoderStream();
       }
       switch ($GLOBALS['g_config']['method'])
       {
        case 'fastps':
          if ($GLOBALS['g_config']['pslevel'] == 3)
          {
            $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
          }
          else
          {
            $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
          }
        break;
        case 'pdflib':
          $pipeline->output_driver = new OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
        break;
        case 'fpdf':
          $pipeline->output_driver = new OutputDriverFPDF();
        break;
        case 'png':
          $pipeline->output_driver = new OutputDriverPNG();
        break;
        case 'pcl':
          $pipeline->output_driver = new OutputDriverPCL();
        break;
        default:
          die('Unknown output method');
       }
       if (isset($GLOBALS['g_config']['watermarkhtml'])) {
         $watermark_text = $GLOBALS['g_config']['watermarkhtml'];
       }
       else {
         $watermark_text = '';
       }
       $pipeline->output_driver->set_watermark($watermark_text);
       if ($watermark_text != '')
       {
         $dispatcher =& $pipeline->getDispatcher();
       }
       if ($GLOBALS['g_config']['debugbox'])
       {
         $pipeline->output_driver->set_debug_boxes(true);
       }
       if ($GLOBALS['g_config']['draw_page_border'])
       {
         $pipeline->output_driver->set_show_page_border(true);
       }
       if ($GLOBALS['g_config']['ps2pdf'])
       {
         $pipeline->output_filters[] = new OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
       }
       if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps')
       {
         $pipeline->output_filters[] = new OutputFilterGZip();
       }
       if (!isset($GLOBALS['g_config']['process_mode'])) {
         $GLOBALS['g_config']['process_mode'] = '';
       }
       if ($GLOBALS['g_config']['process_mode'] == 'batch')
       {
         $filename = 'batch';
       }
       else
       {
         $filename = $sFilename;
       }
       switch ($GLOBALS['g_config']['output'])
       {
        case 0:
          $pipeline->destination = new DestinationBrowser($filename);
          break;
        case 1:
          $pipeline->destination = new DestinationDownload($filename);
          break;
        case 2:
          $pipeline->destination = new DestinationFile($filename);
          break;
       }
       copy($sPath . $sFilename . '.html', PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
       $status = $pipeline->process((  (isset($_SERVER['HTTPS']))&&($_SERVER['HTTPS']=='on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/files/' . $_SESSION['APPLICATION'] . '/outdocs/' . $sFilename . '.html', $g_media);
       
       copy(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf', $sPath . $sFilename . '.pdf');
       unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.pdf');
       unlink(PATH_OUTPUT_FILE_DIRECTORY . $sFilename . '.html');
      
      }//end if $sTypeDocToGener
      /* End - Create .pdf */
    }
    else
    {
      return PEAR::raiseError(null,
                              G_ERROR_USER_UID,
                              null,
                              null,
                              'You tried to call to a generate method without send the Output Document UID, fields to use and the file path!',
                              'G_Error',
                              true);
    }
  }


  /**
   * verify if Output row specified in [sUid] exists.
   *
   * @param      string $sUid   the uid of the Prolication
   */

  function OutputExists ( $sUid ) {
    $con = Propel::getConnection(OutputDocumentPeer::DATABASE_NAME);
    try {
      $oObj = OutputDocumentPeer::retrieveByPk( $sUid );
      if (is_object($oObj) && get_class ($oObj) == 'OutputDocument' ) {
        return true;
      }
      else {
        return false;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
} // OutputDocument