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

class wsstats {

  var $extkey = "ws_stats";
  var $table_name = "tx_wsstats_tracking";
  var $t3url = "";
  var $conf;
  var $pageheaderjs = "";

  function init($conf) {
    $this->conf = $conf;
    $this->t3url = $_SERVER['SERVER_NAME'];
  }

  function menu() {
    
    global $LANG;
    
    $mods = Array('visitors' => $LANG->getLL('visitor_details'),'maintenance' => $LANG->getLL('maintenance'),'about' => $LANG->getLL('about'));

    $cm = '';
    if (!empty($_GET['mod'])) $cm = $_GET['mod'];
    $c = '<ul class="mainmenu">';
    foreach ($mods as $k => $v) {
      $c .= '<li><a href="'.$this->file.($k?'?mod='.$k:'').'"'.($cm==$k?' class="act"':'').'>'.$v.'</a></li>';
    }
    $c .= '</ul>';
    return $c;
  }

  function doAction() {

    switch ($_GET['action']) {
      case "deleterecord":
        $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->table_name,'wsstats_id='. $GLOBALS['TYPO3_DB']->fullQuoteStr(t3lib_div::_GP('wsstatsid'),$this->table_name).'');
        break;
      case "deletebotrecords":

        $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->table_name,'bot=1');

        break;
    }

  }

  function main() {
    $content = '';

    $this->doAction();

    if (empty($_GET['mod'])) {
      $content .= $this->page_visitordetails();
    } else {
      switch ($_GET['mod']) {
        case 'visitors': $content .= $this->page_visitordetails(); break;
        case 'maintenance': $content .= $this->page_maintenance(); break;
        case 'about': $content .= $this->page_about(); break;
        case 'cookie': $content .= $this->page_cookie(); break;
        default: $content .= $this->page_visitordetails();
      }
    }
    return $content;
  }

  function page_visitordetails() {
    
    global $LANG;
    
    $content = '';

    $cookies = isset($_COOKIE['wsstats']) ? $_COOKIE['wsstats'] : array();

    $params = isset($_GET['wsstats']) ? $_GET['wsstats'] : array();
    // parse request params

    $range = isset($params['range']) ? intval($params['range']) : (isset($cookies['range']) ? $cookies['range'] : 1);
    $items = isset($params['limit']) ? intval($params['limit']) : 10;
    $page = isset($params['page']) ? intval($params['page']) : 1;
    if (isset($params['page'])) unset($params['page']);
    $limit = ($page > 1) ? (($page-1)*$items).",$items" : $items;
    $search = isset($params['search']) ? $params['search'] : "";
    $sortby = null;
    $visitortype = isset($params['visitortype']) ? $params['visitortype'] : (isset($cookies['visitortype']) ? $cookies['visitortype'] : "all");

    setcookie ("wsstats[visitortype]", $visitortype);
    setcookie ("wsstats[range]", $range);

    $to_date = $this->getTime();
    $to_date = mktime(date("G"), 0, 0, date("m")  , date("d"), date("Y"));
    $to_date += 60*60; // +1 hour
    $from_date = strtotime('-'.$range.' day', $to_date);

    $where = "timestamp BETWEEN ".$from_date." AND ".$to_date."";
    $where .= (($visitortype == "all") ? "" : (($visitortype == "human") ? " AND bot = 0" : " AND bot = 1"));

    $acitems = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(DISTINCT wsstats_id) as num_items",$this->table_name,$where);
    $num_items = $acitems[0]['num_items'];

    $apages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(id) as pages",$this->table_name,$where,"","timestamp DESC");
    $num_pages = $apages[0]['pages'];

    $this->addPageheaderJS($params);

    $content .= $this->createMenubar($range,$sortby,$params,$visitortype);

    $content .= $this->createChart($where,$from_date,$to_date,$range);

    $content .= $this->getSummary($num_items,$num_pages);


    $main = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("wsstats_id, ip, hostname, cookiekey, agent",$this->table_name,$where,"wsstats_id","timestamp DESC",$limit);


    if ($num_items > 0) {
      $p = new pagination();
      $p->items($num_items);
      $p->limit($items);
      $p->currentPage($page);
      $p->target("index.php?mod=visitors&".$this->paramsToString($params));
      $p->calculate();
      $p->adjacents(5);
    }


    //# Show Page numbers/Links...
    if ($num_items >= 10) {
      $content .= '<div id="pag" align="center">'.$p->get().'</div><br />';
    }
    if ($num_items > 0) {
      foreach ($main as $rk) {

        $content .= $this->createListItem($rk,$params);
      }
    } else {
      $content .= '<div class="norecords"><p>'.$LANG->getLL('norecords').'</p></div>';
    }



    $content .= '<br />';
    if ($num_items >= 10) $content .= $p->get();
    $content .= '<br />';

    return $content;
  }

  function page_cookie() {
  
    global $LANG;
     
    $key = $_GET['key'];

    $content = '<h2 style="margin-bottom: 2em;">'.$LANG->getLL('visits_for_cookie').' "'.$key.'"</h2>';
    
    $content = '<ul id="menubar"><li><a class="button" href="?mod=cookie&key='.$key.'">'.$LANG->getLL('refresh').'</a></li></ul>';
    
    $main = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("DISTINCT wsstats_id, ip, hostname, cookiekey, agent",$this->table_name,"cookiekey = ".$GLOBALS['TYPO3_DB']->fullQuoteStr($key,$this->table_name)."","","timestamp DESC");

    $params = isset($_GET['wsstats']) ? $_GET['wsstats'] : array();

    foreach ($main as $rk) {

      $content .= $this->createListItem($rk,$params);
    }

    return $content;
  }

  function createListItem($rk,$params) {
    
    global $LANG;
    
    $content = '';

    $max_char_len = 70;

    $reqs = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("id, timestamp, urlrequested, referrer, searchphrase, searchpage, os, browser, language, agent, searchengine, bot, feed, fe_user",$this->table_name,"wsstats_id = '".$rk['wsstats_id']."'","","timestamp ASC");

    if (strlen($rk['cookiekey']) > 0) {
      $a_recurs = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(DISTINCT wsstats_id) as num_recurs",$this->table_name,"cookiekey = \"".$rk['cookiekey']."\"");

      $recurs = $a_recurs[0]['num_recurs'];
    } else $recurs = 0;

    $firstreq = $reqs[0];
    $lastreq = $reqs[count($reqs)-1];

    $timestampF = $firstreq['timestamp'];
    $dateF = date("d M Y", $timestampF);
    $datetimeF = date('Y-m-d H:i:s', $timestampF);
    $timeF = date("H:i:s", $timestampF);
    $period = $lastreq['timestamp'] - $firstreq['timestamp'];

    //$ip = @explode(",", $reqs->ip);
    $ip_proxy = strpos($rk['ip'],",");
    //if proxy, get 2nd ip...
    if ($ip_proxy !== false) {
      $ip = substr($rk['ip'],(int)$ip_proxy+1);
    } else {
      $ip = $rk['ip'];
    }

    //Visitor Record - detail listing
    if ($firstreq['referrer'] != '') {
      if (!eregi($this->t3url, $firstreq['referrer']) || $firstreq['searchengine'] != "") {
        if (!eregi($this->t3url, $firstreq['referrer']) && $firstreq['searchengine'] == "") {
          $referer = '<a href="'.$firstreq['referrer'].'" target="_BLANK"><span style="font-weight: bold;">'.stringShortener($firstreq['referrer'], round($max_char_len*.8,0)).'</span></a>';
        } else {
          $referer = '<a href="'.$firstreq['referrer'].'" target="_BLANK">'.stringShortener($firstreq['referrer'], round($max_char_len*.9,0)).'</a>';
        }
      } else {
        $referer = $LANG->getLL('from_your_website');
      }
    } else {
      $referer = $LANG->getLL('direct_hit');
    }
    $hostname = ($rk['hostname'] != "") ? $rk['hostname'] : $LANG->getLL('unknown');


    $content .= '<div class="record" id="delID'.$rk['wsstats_id'].'">
    <div class="sum-nav">

        <p class="delbut">';
    $content .= '<a href="?mod=visitors&action=deleterecord&wsstatsid='.$rk['wsstats_id'].'&'.$this->paramsToString($params).'" class="deleteID" id="'.$rk['wsstats_id'] .'" style="text-decoration:none;">
        <img src="../res/cross.png" alt="delete" title="'.$LANG->getLL('delete').'" /></a>';

    $content .= '</p>
		<div style="float: left">
          <span class="sum-box"><a href="http://www.geoip.co.uk/?IP='.$ip.'" target="_blank">'.$ip.'</a></span>
          <span class="sum-date">'.$datetimeF.'</span>
        </div>
        <div class="sum-det"><span class="det1"><a href="'.htmlspecialchars(html_entity_decode($firstreq['urlrequested'])).'" target="_blank">'.stringShortener(urlencode(html_entity_decode(($firstreq['urlrequested']))), round($max_char_len*.8,0)).'</a></span><br />
          <span class="det2"><strong>'.$LANG->getLL('referer').'</strong>'.$referer.'<br /><strong>'.$LANG->getLL('hostname').'</strong> '.$hostname.'';

    if ($recurs > 1) {
        $visited_string = sprintf($LANG->getLL('visited'), $recurs);
        $content .= ', <a href="?mod=cookie&key='.$rk['cookiekey'].'">'.$visited_string.'</a>';
    }

    $content .= '</span>
		</div>
		</div>
		<div class="information">';

    // Referer is search engine
    if ($firstreq['searchengine'] != "") {
      if (eregi("images", $firstreq['searchengine'])) {
        $class = 'images';
        $page = (number_format(($firstreq['searchpage'] / 19), 0) * 18);
        $Apagenum = explode(".", number_format(($firstreq['searchpage'] / 19), 1));
        $pagenum = ($Apagenum[0] + 1);
        $url = parse_url($firstreq['referrer']);
        $ref = $url['scheme']."://".$url['host']."/images?q=".eregi_replace(" ", "+", $firstreq['searchphrase'])."&start=".$page;
      } else {
        $class = '';
        $pagenum = $firstreq['searchpage'];
        $ref = $firstreq['referrer'];
      }

      $content .= '<ul class="searchengine '.$class.'">
		<li>'.$LANG->getLL('search_engine').'<strong>'. $firstreq['searchengine']." (".$LANG->getLL('page').": $pagenum)".'</strong></li>
		<li>'.$LANG->getLL('keywords').'<strong> <a href="'.$ref .'" target="_BLANK">'.stringShortener($firstreq['searchphrase'], round($max_char_len*.52,0)).'</a></strong></li>
		</ul>';
    }


    // User os/browser/language
    if ($firstreq['bot'] == 0 && ($firstreq['os'] != "" || $firstreq['browser'] != "")) {

      $content .= '<ul class="agent"><li><span>';
       
      if (eregi('^[a-z]{2}$',$firstreq['language'], $vars)) {
        $content .= '<img src="../res/flags/'.strtolower($firstreq['language']).'.png" alt="'.strtolower($firstreq['language']).'" title="'.$LANG->getLL('language').': '.strtolower($firstreq['language']).'" />';
      }
      $content .=  $LANG->getLL('os').'<strong>'. $firstreq['os'].'</strong></span></li><li>'.$LANG->getLL('browser').'<strong> '.$firstreq['browser'].'</strong></li>';
       
      $content .= '<li>'.$LANG->getLL('agent').'<img src="../res/info.png" title="'.$firstreq['agent'].'" /></li>';
      if (count($reqs) > 1) {
        $content .= '<li>'.$LANG->getLL('minutes_per_page').'<b>'. (round($period/60) > 0 ? ( round( ($period / 60) / count($reqs) * 100) / 100 ) : "0") . '</b></li>';
        $content .= '<li>'.$LANG->getLL('period').'<b>'.time_left_to_string($period).'</b></li>';
      }
      $content .= '</ul>';
    }

    if ($firstreq['bot'] == 1) {

      $content .= '<ul class="bot"><li><span>';
       
      $content .= '<li>'.$firstreq['agent'].'</li>';
       
      $content .= '</ul>';
    }


    if (count($reqs) > 1) {
      $content .= '<div class="navi'. $firstreq['id'].'">
                  <ul class="url">';

      array_shift($reqs);
      foreach ($reqs as $reqrc) {
        $content .= $this->createOtherView($reqrc);
      }

      $content .= '</ul></div>';
    }
    $content .= '</div>
</div>';

    return $content;
  }


  function createOtherView($cd) {
    
    global $LANG;
    
    $content = '';
    $max_char_len = 70;

    $time2 = date("H:i:s", $cd['timestamp']);
    $char_len = round($max_char_len*.92,0);

    $content .= '<li class="navi'.$cd['id'].'"><span class="indent-li-nav">'.$time2.' -> ';
    $content .= '<a href="'.htmlspecialchars(html_entity_decode($cd['urlrequested'])).'" target="_BLANK">';
    $content .= stringShortener(urlencode(html_entity_decode($cd['urlrequested'])), $char_len).'</a></span></li>'."\n";

    return $content;
  }

  function getTime() {
    return date("U");
  }

  function createMenubar($range,$sortby,$params,$visitortype) {
  
    global $LANG;
    
    $s = '<ul id="menubar">';
    $s .= '<li><label for="range">'.$LANG->getLL('range').'</label>';
    $s .= '<select onchange="window.location.href=this.options[this.selectedIndex].value;" name="range">';
    $aranges = array("1" => $LANG->getLL('day'),"7" => $LANG->getLL('week'),"30" => $LANG->getLL('month'));
    foreach ($aranges as $key => $val) $s .= '<option value="?mod=visitors&'.$this->paramsToString($params,"range",$key).'" '.(($range == $key) ? 'selected="selected"' : '' ).'>'.$val.'</option>';
    $s .= '</select></li>';

    /*
     $s .= '<li><label for="sortby">Sort by:</label>';
     $s .= '<select onchange="window.location.href=this.options[this.selectedIndex].value;" name="sortby">';
     $aranges = array("lastactive" => "Last active user","incomming" => "Last incomming user");
     foreach ($aranges as $key => $val) $s .= '<option value="?mod=visitors&'.$this->paramsToString($params,"sortby",$key).'" '.(($sortby == $key) ? 'selected="selected"' : '' ).'>'.$val.'</option>';
     $s .= '</select></li>';
     */

    $s .= '<li><label for="visitortype">'.$LANG->getLL('type').'</label>';
    $s .= '<select onchange="window.location.href=this.options[this.selectedIndex].value;" name="visitortype">';
    $aranges = array("all" => $LANG->getLL('all'),"human" => $LANG->getLL('human'),"bot" => $LANG->getLL('bot'));
    foreach ($aranges as $key => $val) $s .= '<option value="?mod=visitors&'.$this->paramsToString($params,"visitortype",$key).'" '.(($visitortype == $key) ? 'selected="selected"' : '' ).'>'.$val.'</option>';
    $s .= '</select></li>';

    $s .= '<li><a class="button" href="?mod=visitors&'.$this->paramsToString($params).'">'.$LANG->getLL('refresh').'</a></li>';


    $s .= '</ul>';
    return $s;
  }

  function createChart($where,$from_date,$to_date,$range) {
  
    global $LANG;

    $data1 = $data2 = $ticks = "";
    $max_y = 0;

    switch ($range) {
      case 1:
        $hours = array();
        $ticks = "";
        for ($i = 24; $i >= 0; $i--) {
          $hours[($to_date-($i*60*60))-(60*30)] = 0;
          $ticks .= "[".($to_date-($i*60*60)).",\"".date("H",($to_date-($i*60*60)))."\"],";
        }
        $ticks = substr($ticks, 0, -1);
        //print_r($hours);
        $reqs = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(DISTINCT wsstats_id) as items, COUNT(wsstats_id) as pages, timestamp as thedate, DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%H') as hour",$this->table_name,$where,"DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%H')","timestamp DESC");

        $visitors = $pages = $hours;
        //print_r($reqs);
        foreach ($reqs as $req) {
           
          $h = mktime(intval(date("G",$req['thedate']))+1, 0, 0, intval(date("n",$req['thedate'])), intval(date("j",$req['thedate'])), intval(date("Y",$req['thedate'])))-(60*30);
          //echo $h."-".$req['items']."-".$req['pages']." ".date("H:i:s",$req['thedate'])."\n";
          $visitors[$h] = $req['items'];
          $pages[$h] = $req['pages'];
          $max_y = ($req['pages'] > $max_y) ? $req['pages'] : $max_y;
        }


        foreach ($visitors as $hour => $visitor) {
          $data1 .= '['.$hour.','.$visitor.'],';
          $data2 .= '['.$hour.','.$pages[$hour].'],';
        }
        $data1 = substr($data1, 0, -1);
        $data2 = substr($data2, 0, -1);
        //$to_date += 60*60;

        break;
      case 7:
        $nextday = mktime(0, 0, 0, intval(date("m")) , intval(date("j")), intval(date("Y")));
        $nextday += 60*60*24;
        $days = array();
        $ticks = "";
        for ($i = 8; $i >= 0; $i--) {
          $days[($nextday-($i*60*60*24))-(60*60*12)] = 0;
          $ticks .= "[".($nextday-($i*60*60*24)).",\"".date("d.m",($nextday-($i*60*60*24)))."\"],";
        }
        $ticks = substr($ticks, 0, -1);

        $reqs = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(DISTINCT wsstats_id) as items, COUNT(wsstats_id) as pages, timestamp as thedate, DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%e') as day",$this->table_name,$where,"DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%d')","timestamp DESC");


        $visitors = $pages = $days;
        foreach ($reqs as $req) {
          $h = mktime(0, 0, 0, date("m",$req['thedate'])  , date("d",$req['thedate']), date("Y",$req['thedate']))+(60*60*12);
          $visitors[$h] = $req['items'];
          $pages[$h] = $req['pages'];
          $max_y = ($req['pages'] > $max_y) ? $req['pages'] : $max_y;
        }

        foreach ($visitors as $hour => $visitor) {
          $data1 .= '['.$hour.','.$visitor.'],';
          $data2 .= '['.$hour.','.$pages[$hour].'],';
        }
        $data1 = substr($data1, 0, -1);
        $data2 = substr($data2, 0, -1);
        $to_date += 60*60*24;

        break;

      case 30:
        $nextday = mktime(0, 0, 0, intval(date("m")) , intval(date("j")), intval(date("Y")));
        $nextday += 60*60*24;
        $days = array();
        $ticks = "";
        for ($i = 31; $i >= 0; $i--) {
          $days[($nextday-($i*60*60*24))-(60*60*12)] = 0;
          if ($i % 3 == 0) {
            $ticks .= "[".($nextday-($i*60*60*24)).",\"".date("d.m",($nextday-($i*60*60*24)))."\"],";
          } else {
            $ticks .= "[".($nextday-($i*60*60*24)).",\" \"],";
          }
        }
        $ticks = substr($ticks, 0, -1);

        $reqs = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows("COUNT(DISTINCT wsstats_id) as items, COUNT(wsstats_id) as pages, timestamp as thedate, DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%e') as day",$this->table_name,$where,"DATE_FORMAT(FROM_UNIXTIME(CAST((timestamp) AS UNSIGNED)), '%d')","timestamp DESC");


        $visitors = $pages = $days;
        foreach ($reqs as $req) {
          $h = mktime(0, 0, 0, date("m",$req['thedate'])  , date("d",$req['thedate']), date("Y",$req['thedate']))+(60*60*12);
          $visitors[$h] = $req['items'];
          $pages[$h] = $req['pages'];
          $max_y = ($req['pages'] > $max_y) ? $req['pages'] : $max_y;
        }

        foreach ($visitors as $hour => $visitor) {
          $data1 .= '['.$hour.','.$visitor.'],';
          $data2 .= '['.$hour.','.$pages[$hour].'],';
        }
        $data1 = substr($data1, 0, -1);
        $data2 = substr($data2, 0, -1);
        $to_date += 60*60*24;

        break;
    }
    $max_y += ceil($max_y*0.05);

    $chart = "<script>
		Event.observe(window, 'load', function() {

		new Proto.Chart($('chart'),
			[
				{data: [".$data2."], label: '".$LANG->getLL('pages')."'},
				{data: [".$data1."], label: '".$LANG->getLL('visitors')."'}
			],{
				xaxis: { ticks: [".$ticks."], min: ".$from_date." , max: ".$to_date." },
				yaxis: {min: 0, max: ".$max_y."},
				legend: {show: true},
				//points: { show: true },
				//bars: { show: true, lineWidth: 10000 },
				lines: {show: true, fill: true}
			});
		});
		</script>";
    $this->pageheaderjs .= $chart;

    return '<div id="chart" style="width:700px;height:250px"></div>';
  }

  function getSummary($visitors, $pages) {
    
    global $LANG;
    
    $s = "";

    $s .= '<div id="summary" class="clearfix"><div class="fl visitors"><span>'.$LANG->getLL('visitors').': '.$visitors.'</span></div><div class="fl pages"><span>'.$LANG->getLL('pages').': '.$pages.'</span></div><div class="fl"><span>'.$LANG->getLL('auto-refresh_1').'&nbsp;</span><span id="countdown"></span><span>&nbsp;'.$LANG->getLL('auto-refresh_2').'</span></div></div>';

    return $s;
  }

  function page_maintenance() {
    
    global $LANG;
    
    $content = '';
    $content .= '<h1>'.$LANG->getLL('table_usage').'</h1><p>';

    $res = mysql_query("SHOW TABLE STATUS LIKE '".$this->table_name."'",$GLOBALS['TYPO3_DB']->link);

    $output = array();
    while($tempRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
      $output[] = $tempRow;
    }

    foreach ($output as $fstatus) {
      $data_lenght = $fstatus['Data_length'];
      $data_rows = $fstatus['Rows'];
      $table_engine = (isset($fstatus['Engine'])? $fstatus['Engine']: $LANG->getLL('unknown'));
    }
    
    $tusage = number_format(($data_lenght/1024/1024), 2, ",", " ");
    $content .= $tusage.$LANG->getLL('mb').'<br />';
    $content .= $LANG->getLL('rows').$data_rows."<br /></p>";

    $content .= '<p><br><a href="?mod=maintenance&action=deletebotrecords">'.$LANG->getLL('delete_bot_records').'</a></p>';

    return $content;
  }


  function page_about() {
  
    global $LANG;
  
    $content = '';

    $content .= '<h2>'.$LANG->getLL('authors').'</h2>';
    $content .= "<p><ul><li><img src=\"../res/flags/de.png\" /> Sven Wappler, <a href=\"http://www.wapplersystems.de\" target=\"_blank\">WapplerSystems</a></li>
    	<li><img src=\"../res/flags/fr.png\" /> Fedir Rykhtik, <a href=\"http://www.bleuroy.com/\" target=\"_blank\">BleuRoy.com</a></li></ul>
    </p>";

    $content .= '<h2>'.$LANG->getLL('flag_icons').'</h2>';
    $content .= "<p>famfamfam.com</p>";

    $content .= "<hr />";

    return $content;
  }

  function addPageheaderJS($params) {
    ob_start();
    ?>
<script type="text/javascript">
  //<![CDATA[
  function selfRefresh(){
 	location.href="?mod=visitors&<?php echo $this->paramsToString($params) ?>";
  }
  setTimeout("selfRefresh()", <?php echo intval($this->conf['refreshintervall'])*1000 ?>);
  var _countDowncontainer = null;
  var _currentSeconds = 0;
  
  function activate_countdown(container, initialValue) {
  	_countDowncontainer = container;
  	SetCountdownText(initialValue);
  	window.setTimeout("CountDownTick()", 1000);
  }
  function CountDownTick() {
	SetCountdownText(_currentSeconds-1);
  	window.setTimeout("CountDownTick()", 1000);
  }
  function SetCountdownText(seconds) {
  	_currentSeconds = seconds;
  	var strText = AddZero(seconds);
  	if (_countDowncontainer) {
  		_countDowncontainer.innerHTML = strText;
  	}
  }
  function AddZero(num) {
  	return ((num >= "0")&&(num < 10))?"0"+num:num+"";
  }

	Event.observe(window, 'load', function() {
		activate_countdown($('countdown'), <?php echo $this->conf['refreshintervall'] ?>);
	});
  //]]>
</script>
    <?php
    $content = ob_get_contents();
    ob_end_clean();
    $this->pageheaderjs .= $content;
  }

  function paramsToString($params = array(),$overwritekey = null,$overwriteval = null) {
    $s = "";
    $b = false;
    foreach ($params as $key => $val) {
      if (isset($overwritekey) && $key == $overwritekey) {
        $val = $overwriteval;
        $b = true;
      }
      $s .= "wsstats[".$key."]=".$val."&";
    }
    if (isset($overwritekey) && !$b) $s .= "wsstats[".$overwritekey."]=".$overwriteval."&";
    return $s;
  }

  function getPageheaderJS() {
    return $this->pageheaderjs;
  }


}

?>