<?php
    if (!defined ('TYPO3_MODE')) die ('Access denied.');



    //$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['isOutputting'][] = 'EXT:ws_stats/class.tx_wsstats_tsfehook.php:tx_wsstats_tsfehook->main';
    
    $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preBeUser']['dd'] = 'EXT:ws_stats/class.tx_wsstats_tsfehook.php:tx_wsstats_tsfehook->main';
    
    //$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc']['a'] = 'EXT:ws_stats/class.tx_wsstats_tsfehook.php:tx_wsstats_tsfehook->main';

    //$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['determineId-PostProc']['a'] = 'EXT:ws_stats/class.tx_wsstats_tsfehook.php:tx_wsstats_tsfehook->main';

    
?>