<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TBE_MODULES['txwsstats'] = '';

if (TYPO3_MODE == 'BE')	{

	t3lib_extMgm::addModule('txwsstats','','top',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	t3lib_extMgm::addModule('txwsstats','txwsstatsM1','bottom',t3lib_extMgm::extPath($_EXTKEY).'mod1/');


}

?>