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

require_once('wsstats/main.php');

class tx_wsstats_tsfehook {
  var $extkey = "ws_stats";
  var $conf = null;

  /**
   *
   * @param
   * @access public
   * @return void
   */
  function main($param) {
    global $TSFE;

    $this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ws_stats']);

    if ($param['pObj']->beUserLogin == 1) return;

    //t3lib_div::devLog(var_export($param, true),$this->extkey);

    $this->track();
  }


  /**
   *
   *
   * @param
   * @access public
   * @return void
   */
  function track() {
    global $TYPO3_DB,$TSFE;
    $data = array();
    $pageId = $TSFE->id;

    if (isset($_COOKIE['be_typo_user']) && $this->conf['disableBEUserLogging']) return;

    $urlRequested = t3lib_div::getIndpEnv('REQUEST_URI');

    $exclude = $this->conf['exclude'];

    /*$search = array("*",";");
     $replace = array("","");
     $exclude = str_replace( $exclude);
     */
    $referrer = t3lib_div::getIndpEnv('HTTP_REFERER');
    $referrerData = parse_url($referrer);
    $ip = t3lib_div::getIndpEnv('REMOTE_ADDR');
    $language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $agent = t3lib_div::getIndpEnv('HTTP_USER_AGENT');
    $search_phrase = "";
    list($browser,$os) = $this->getBrowser($agent);
    list($hostname,$ip) = $this->getHostnameAndIP();

    $bot = $this->isBot($hostname,$agent,$ip);
    if ($bot && !$this->conf['logBots']) return;


    $language = $this->getLocale($language,$hostname,$referrer);



    $se = $this->parseReferrer($referrer);

    $searchengine = $se['searchengine'];
    $searchpage = 0;
    if ($se['phrase'] != '') {
      if (stristr($searchengine,"images")) {
        // ATTENTION Position retrieved by referer in Google Images is
        // the Position number of image NOT the number of items in the page like web search
        $searchpage = $se['page'];
        $searchcountry = explode(".", $se['domain']);
      } else {
        $searchpage = (($se['page']/10)+1);
        $searchcountry = explode(".", $se['domain']);
      }
      if ($searchcountry[3] != '' ) {
        $searchengine .= " ".strtoupper($searchcountry[3]);
      } elseif ($searchcountry[2] != '') {
        $searchengine .= " ".strtoupper($searchcountry[2]);
      }
    }

    // trackercookie
    $cookie = "";
    if (!$bot) {
      if (isset($_COOKIE['tc'])) {
        $cookie = $_COOKIE['tc'];
      } else {
        $cookie = $this->generateRandomString(40);
        setcookie("tc",$cookie,time()+60*60*24*2*365,"/");
      }
    }


    $wsstats_id = md5(($ip).($agent).date('Ymd'));

    $data['urlrequested'] = $GLOBALS['TYPO3_DB']->quoteStr($urlRequested,'tx_wsstats_tracking');
    $data['ip'] = $ip;
    $data['wsstats_id'] = $wsstats_id;
    $data['timestamp'] = $this->getTime();
    $data['language'] = $GLOBALS['TYPO3_DB']->quoteStr($language,'tx_wsstats_tracking');
    $data['referrer'] = $GLOBALS['TYPO3_DB']->quoteStr($referrer,'tx_wsstats_tracking');
    $data['hostname'] = $hostname;
    $data['agent'] = $GLOBALS['TYPO3_DB']->quoteStr($agent,'tx_wsstats_tracking');
    $data['searchphrase'] = $GLOBALS['TYPO3_DB']->quoteStr($se['phrase'],'tx_wsstats_tracking');
    $data['searchpage '] = $searchpage;
    $data['os '] = $os;
    $data['browser '] = $GLOBALS['TYPO3_DB']->quoteStr($browser,'tx_wsstats_tracking');
    $data['searchengine '] = $searchengine;
    $data['cookiekey '] = $GLOBALS['TYPO3_DB']->quoteStr($cookie,'tx_wsstats_tracking');
    $data['bot '] = $bot ? "1" : "0";
    $data['feed '] = "";
    $data['fe_user '] = "";

    if (intval($GLOBALS['TSFE']->fe_user->user['uid'])) $data['fe_user'] = intval($GLOBALS['TSFE']->fe_user->user['uid']);

    $TYPO3_DB->exec_INSERTquery('tx_wsstats_tracking',$data);
  }


  function getTime() {
    return $GLOBALS['SIM_EXEC_TIME'];
  }

  function getBrowser($agent="") {
    $browsercap = array();
    $browscapbrowser = "";
    $browser = "";
    $os = "";
    //check PHP browscap data for browser and platform, when available
    if (ini_get("browscap") != "" ) {
      $browsercap = get_browser($agent,true);
      if (!empty($browsercap['platform'])) {
        if (stristr($browsercap['platform'],"unknown") === false) {
          $os = $browsercap['platform'];
          if (!empty($browsercap['browser'])) {
            $browser = $browsercap['browser'];
          } else {
            $browser = $browsercap['parent'];
          }
          if (!empty($browsercap['version'])) {
            $browser = $browser." ".$browsercap['version'];
          }
        } }
        //reject generic browscap browsers (ex: mozilla, default)
        if (preg_match('/^(mozilla|default|unknown)/i',$browser) > 0) {
          $browscapbrowser = $browser;	//save just in case
          $browser = "";
        }
    }
    $os = trim($os);
    $browser = trim($browser);

    //use Detector class when browscap is missing or browser is unknown
    if ($os == "" || $browser == "") {
      $dip = new Detector("", $agent);
      $browser =  trim($dip->browser." ".$dip->browser_version);
      $os = trim($dip->os." ".$dip->os_version);

      //use saved browscap data, if Detector had no results
      if (!empty($browscapbrowser) && ($browser == "" || $browser == "N/A")) {
        if ($os != "" && $os != "N/A") {
          $browser = $browscapbrowser;
        }
      }
    }
    return array($browser,$os);
  }


  function getLocale($language="",$hostname="",$referrer="") {
    //#use country code for language, if it exists in hostname
    if (!empty($hostname) && preg_match("/\.[a-zA-Z]{2}$/", $hostname) > 0) {
      $country = strtolower(substr($hostname,-2));
      if ($country == "uk") { $country = "gb"; } //change UK to GB for consistent language codes
      $language = $country;
    } elseif (strlen($language) >2) {
      $langarray = @explode("-", $language);
      $langarray = @explode(",", count($langarray) > 1 ? $langarray[1] : $langarray[0]);
      list($language) = @explode(";", strtolower($langarray[0]));
    }
    //#check referrer search string for language/locale code, if any
    if ((empty($language) || $language == "us" || $language == "en") && !empty($referrer)) {
      $country = $language;
      // google referrer syntax: google.com[.country],hl=language
      if (preg_match('/\.google(\.com)?\.(com|([a-z]{2}))?\/.*[&?]hl\=(\w{2})\-?(\w{2})?/',$referrer,$matches)>0) {
        if (!empty($matches[5])) {
          $country = strtolower($matches[5]);
        } elseif (!empty($matches[3])) {
          $country = strtolower($matches[3]);
        } elseif (!empty($matches[4])) {
          $country = strtolower($matches[4]);
        }
      }
      $language = $country;
    }
    //default to "US" if language==en (english)
    if ($language == "en") {
      $language = "us";
    }
    return $language;
  }

  function getHostnameAndIP() {

    if (isset($_SERVER["REMOTE_ADDR"])) {
      if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        //in case of multiple forwarding
        list($IP) = explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]);
        $proxy = $_SERVER["REMOTE_ADDR"];
        $hostname = @gethostbyaddr($IP);
        if (empty($hostname) || $hostname == "unknown") {
          $hostname = @gethostbyaddr($proxy);
        }
        if (empty($IP) || $IP == "unknown") {
          $IP = $proxy;
          $ipAddress = $_SERVER["REMOTE_ADDR"];
        } else {
          $ipAddress = $proxy.",".$IP;
        }
      }else{
        list($IP) = explode(",",$_SERVER["REMOTE_ADDR"]);
        $hostname = @gethostbyaddr($IP);
        $ipAddress = $_SERVER["REMOTE_ADDR"];
      }
    }
    if (empty($IP)) $IP = $ipAddress;
    if (empty($hostname)) $hostname = "unknown";

    return array($hostname,$IP);
  }

  function isBot($hostname,$agent,$ip) {

    if ($this->isCrawler($hostname)) return true;

    if (preg_match('/(bot|crawler|spider|java|findlinks|libwww-perl)/i',$agent) > 0) return true;
    
    // Filter search-fake of windows live bot
    $aip = split("\.",$ip);
    if ($aip[0] == "65" && $aip[1] == "55") return true;

    return false;
  }

  function isCrawler($hostname) {
    if ($hostname == "") return false;

    return (preg_match('/(\.|)(googlebot|live|msn|yahoo|technorati|ayell|alexa|bigfinder|scoutjet|netluchs|ask)\.(com|net|org|de)$/i',$hostname) > 0);
  }

  function parseSearchengineAndSearchphrase($referrer = null){
    $key = null;
    $lines = array("Alice|search.alice.it|qs|", "Google|www.google.|as_q|", "Google|www.google.|q|", "Google Groups|groups.google.|q|",
                        "Google Images|images.google.|prev|", "Yahoo|search.yahoo.com|p|", "Google Blog|blogsearch.google.|as_q|", "Google Blog|blogsearch.google.|q|",
                        "Virgilio|search.virgilio.it|qs|","Arianna|arianna.libero.it|query|","Altavista|.altavista.com|q|","Kataweb|kataweb.it|q|",
                        "Il Trovatore|categorie.iltrovatore.it|query|","Il Trovatore|search.iltrovatore.it|q|","2020Search|2020search.c|us|st|pn|1|",
                        "abcsearch.com|abcsearch.com|terms|","100Links|100links.supereva.it|q|","Alexa|alexa.com|q|","Alltheweb|alltheweb.com|q|",
                        "Aol|.aol.|query|","Aol|aolrecherches.aol.fr|query|","Ask|ask.com|ask|","Ask|ask.com|q|","DMOZ|search.dmoz.org|search|",
                        "Dogpile|dogpile.com|q|","Excite|excite.|q|","Godago|.godago.com|keywords|","HotBot|hotbot.*|query|","ixquick|ixquick.com|query|",
                        "Lycos|cerca.lycos.it|query|","Lycos|lycos.|q|","Windows Live|search.live.com|q|mkt|","My Search|mysearch.com|searchfor|",
                        "My Way|mysearch.myway.com|searchfor|","Metacrawler|metacrawler.|q|","Netscape Search|search.netscape.com|query|","MSN|msn.|q|",
                        "Overture|overture.com|Keywords|","Supereva|supereva.it|q|","Teoma|teoma.com|q|","Tiscali|search-dyn.tiscali.|key|","Voil|voila.fr|kw|",
                        "Web|web.de|su|","Clarence|search.clarence.com|q|","Gazzetta|search.gazzetta.it|q|","PagineGialle|paginegialle.it|qs|",
                        "Jumpy|servizi.mediaset.it|searchWord|","ItaliaPuntoNet|italiapuntonet.net|search|","StartNow|search.startnow.|q|","Search|search.it|srctxt|",
                        "Search|search.com|q|", "Good Search|goodsearch.com|Keywords|", "ABC Sok|verden.abcsok.no|q|", "Kvasir|kvasir.no|searchExpr|",
                        "Start.no|start.no|q|", "bluewin.ch|bluewin.ch|query|", "Google Translate|translate.google.|u|","Metager|metager.de|eingabe|","ICQ Search|search.icq.com|q|",
                        "T-Online|suche.t-online.de|q|");
    foreach ($lines as $line_num => $se) {
      list ($name,$url,$key,$lang) = explode("|",$se);
      if (@strpos($referrer,$url) === false) continue;
      // found it!
      $variables = $this->explodeQuery($referrer);
      // The SE is Google Images
      if ($name == "Google Images") {
        $rightkey = array_search_extended($variables, "images");
        $variables = eregi_replace("prev=/images\?q=", "", urldecode($variables[$rightkey]));
        $variables = explode("&",$variables);
        return array("name" => $name, "phrase" => urldecode($variables[0]));
      } else {
        $i = count($variables);
        while ($i--){
          $tab = explode("=",$variables[$i]);
          if ($tab[0] == $key) return array("name" => $name, "phrase" => urldecode($tab[1]));
        }
      }
    }
    return array();
  }

  function parseReferrer($ref = false) {
    $page = 0;

    $se = $this->parseSearchengineAndSearchphrase($ref);
    $searchengine = $se['name'];
    $search_phrase = $se['phrase'];

    // Check against Google, Yahoo, MSN, Ask and others
    if (preg_match("/[&\?](prev|q|p|w|searchfor|as_q|as_epq|s|query)=([^&]+)/i",$ref,$pcs)){
      if (preg_match("/https?:\/\/([^\/]+)\//i",$ref,$SeDomain)){
        $domain = trim(strtolower($SeDomain[1]));
        $SeQuery = $pcs[2];
        if (preg_match("/[&\?](start|b|first|stq)=([0-9]*)/i",$ref,$pcs)){
          $page = intval(trim($pcs[2]));
        }
      }
    }
    /*
     if (!isset($SeQuery)){
     //Check against DogPile
     if (preg_match("/\/search\/web\/([^\/]+)\//i",$SeReferer,$pcs)) {
     if (preg_match("/https?:\/\/([^\/]+)\//i",$SeReferer,$SeDomain)){
     $SeDomain = trim(strtolower($SeDomain[1]));
     $SeQuery = $pcs[1];
     }
     }
     }
     if (!isset($SeQuery)) return false;
     $OldQ = $SeQuery;
     $SeQuery = urldecode($SeQuery);
     while ($SeQuery != $OldQ) {
     $OldQ = $SeQuery;
     $SeQuery = urldecode($SeQuery);
     }*/
    return array(
	        "domain" => $domain,
	        "page" => $page,
			"searchengine" => $searchengine,
			"phrase" => $search_phrase,
    );
  }

  function explodeQuery($url){
    $tab = parse_url($url);
    $host = $tab['host'];
    if (key_exists("query",$tab)){
      $query = $tab["query"];
      return explode("&",$query);
    } else {
      return null;
    }
  }

  function generateRandomString($length = 10, $letters = '1234567890qwertyuiopasdfghjklzxcvbnm')
  {
    $s = '';
    $lettersLength = strlen($letters)-1;
     
    for($i = 0 ; $i < $length ; $i++)
    {
      $s .= $letters[rand(0,$lettersLength)];
    }
     
    return $s;
  }

}


global $TYPO3_CONF_VARS;
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ws_stats/class.tx_wsstats_tsfehook.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ws_stats/class.tx_wsstats_tsfehook.php']);
}

?>