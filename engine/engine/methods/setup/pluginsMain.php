<?php
/**
 * main.php Cases List main processor
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

$oHeadPublisher =& headPublisher::getSingleton(); 
  
  $oHeadPublisher->addExtJsScript('setup/pluginsMain', false );    //adding a javascript file .js
  //$oHeadPublisher->addContent('setup/main'); //adding a html file  .html.
  
  $translations = G::getTranslations(Array(
    'ID_CONFIGURE', 'ID_STATUS', 'ID_DELETE', 'ID_IMPORT', 'ID_SELECT',
    'ID_STATUS', 'ID_ACTIVATE', 'ID_DEACTIVATE',
    'ID_SELECT', 'ID_NO_SELECTION_WARNING', 'ID_MSG_REMOVE_PLUGIN',
    'ID_TITLE', 'ID_VERSION', 'ID_STATUS', 'ID_TITLE', 'ID_VERSION', 'ID_DESCRIPTION', 
    'ID_STATUS', 'ID_PLUGIN_CANT_DELETE', 'ID_XPDL_IMPORT', 'ID_DISABLE', 'ID_ENABLE', 'ID_CONFIRM'
  ));
  $oHeadPublisher->assign('TRANSLATIONS', $translations);
  G::RenderPage('publish', 'extJs');


  
