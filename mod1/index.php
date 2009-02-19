<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Sven Wappler <typo3@wapplersystems.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:ws_stats/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.

require_once('../wsstats/main.php');

class  tx_wsstats_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $conf;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ws_stats']);

		parent::init();
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
                    'function' => Array (
                            '1' => $LANG->getLL('function1'),
                            '2' => $LANG->getLL('function2'),
                            '3' => $LANG->getLL('function3'),
		)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		require_once '../wsstats/wsstats.php';


		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id) || (($BE_USER->user['uid'] && !$this->id))) {

			$this->doc = t3lib_div::makeInstance('noDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->doc->JScode = '';

			$this->doc->JScode .= '<link rel="stylesheet" type="text/css" href="style.css" />';
			$this->doc->JScode .= '<!--[if IE]><link rel="stylesheet" type="text/css" href="iehacks.css" /><![endif]--> ';
			$this->doc->JScode .= '<script src="../res/protochart/prototype.js"></script>';
			$this->doc->JScode .= '<!--[if IE]><script language="javascript" type="text/javascript" src="../res/protochart/excanvas.js"></script><![endif]--> ';
			$this->doc->JScode .= '<script language="javascript" type="text/javascript" src="../res/protochart/protochart.js"></script>';
				
			$wsstats = new wsstats();
			$wsstats->init($this->conf);

			$content = $wsstats->menu();
			$content .= $wsstats->main();
				
			$this->doc->JScode .= $wsstats->getPageheaderJS();  // bloed, aber umgeht deprecated call-by-reference

			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->header('Visitor tracking');

			$this->content .= $content;
				
			$this->content .= $this->doc->endPage();

		} else {
			// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	function printContent() {
		echo $this->content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ws_stats/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ws_stats/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_wsstats_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>