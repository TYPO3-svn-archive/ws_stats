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

if (!class_exists('pagination')) {
	class pagination{
		/*
		 Script Name: *Digg Style Paginator Class
		 Script URI: http://www.mis-algoritmos.com/2007/05/27/digg-style-pagination-class/
		 Description: Class in PHP that allows to use a pagination like a digg or sabrosus style.
		 Script Version: 0.3.2
		 Author: Victor De la Rocha
		 Author URI: http://www.mis-algoritmos.com
		 */
		/*Default values*/
		var $total_pages;
		var $limit;
		var $target;
		var $page;
		var $adjacents;
		var $showCounter;
		var $className;
		var $parameterName;
		var $urlF ;

		/*Buttons next and previous*/
		var $nextT;
		var $nextI;
		var $prevT;
		var $prevI;

		/*****/
		var $calculate;

		#Total items
		function items($value){$this->total_pages = intval($value);}

		#how many items to show per page
		function limit($value){$this->limit = intval($value);}

		#Page to sent the page value
		function target($value){$this->target = $value;}

		#Current page
		function currentPage($value){$this->page = intval($value);}

		#How many adjacent pages should be shown on each side of the current page?
		function adjacents($value){$this->adjacents = intval($value);}

		#show counter?
		function showCounter($value=""){$this->showCounter=($value===true)?true:false;}

		#to change the class name of the pagination div
		function changeClass($value=""){$this->className=$value;}

		function nextLabel($value){$this->nextT = $value;}
		function nextIcon($value){$this->nextI = $value;}
		function prevLabel($value){$this->prevT = $value;}
		function prevIcon($value){$this->prevI = $value;}

		#to change the class name of the pagination div
		function parameterName($value=""){$this->parameterName=$value;}

		#to change urlFriendly
		function urlFriendly($value="%"){
			if(eregi('^ *$',$value)){
				$this->urlF=false;
				return false;
			}
			$this->urlF=$value;
		}

		var $pagination;

		function pagination(){
			/*Set Default values*/
			$this->total_pages = null;
			$this->limit = null;
			$this->target = "";
			$this->page = 1;
			$this->adjacents = 2;
			$this->showCounter = false;
			$this->className = "pagination";
			$this->parameterName = "wsstats[page]";
			$this->urlF = false;//urlFriendly

			/*Buttons next and previous*/
			$this->nextT = "Next";
			$this->nextI = "&#187;"; //&#9658;
			$this->prevT = "Previous";
			$this->prevI = "&#171;"; //&#9668;

			$this->calculate = false;
		}
		function get(){
			if(!$this->calculate)
			if($this->calculate())
			return "<div class=\"$this->className\">$this->pagination</div>";
		}
		function get_pagenum_link($id){
			if(strpos($this->target,'?')===false)
			if($this->urlF)
			return str_replace($this->urlF,$id,$this->target);
			else
			return "$this->target?$this->parameterName=$id";
			else
			return "$this->target&$this->parameterName=$id";
		}

		function calculate(){
			$this->pagination = "";
			$this->calculate == true;
			$error = false;
			if($this->urlF and $this->urlF != '%' and strpos($this->target,$this->urlF)===false){
				//Es necesario especificar el comodin para sustituir
				echo 'Especificaste un wildcard para sustituir, pero no existe en el target<br />';
				$error = true;
			}elseif($this->urlF and $this->urlF == '%' and strpos($this->target,$this->urlF)===false){
				echo 'Es necesario especificar en el target el comodin';
				$error = true;
			}
			if($this->total_pages == null){
				echo __("It is necessary to specify the","wassup")." <strong>".__("number of pages","wassup")."</strong> (\$class->items(1000))<br />";
				$error = true;
			}
			if($this->limit == null){
				echo __("It is necessary to specify the","wassup")." <strong>".__("limit of items","wassup")."</strong> ".__("to show per page","wassup")." (\$class->limit(10))<br />";
				$error = true;
			}
			if($error)return false;

			$n = trim($this->nextT.' '.$this->nextI);
			$p = trim($this->prevI.' '.$this->prevT);

			/* Setup vars for query. */
			if($this->page)
			$start = ($this->page - 1) * $this->limit;             //first item to display on this page
			else
			$start = 0;                                //if no page var is given, set start to 0

			/* Setup page vars for display. */
			if ($this->page == 0) $this->page = 1;                    //if no page var is given, default to 1.
			$prev = $this->page - 1;                            //previous page is page - 1
			$next = $this->page + 1;                            //next page is page + 1
			$lastpage = ceil($this->total_pages/$this->limit);        //lastpage is = total pages / items per page, rounded up.
			$lpm1 = $lastpage - 1;                        //last page minus 1

			/*
			 Now we apply our rules and draw the pagination object.
			 We're actually saving the code to a variable in case we want to draw it more than once.
			 */

			if($lastpage > 1){
				//anterior button
				if($this->page > 1)
				$this->pagination .= "<a href=\"".$this->get_pagenum_link($prev)."\">$p</a>";
				else
				$this->pagination .= "<span class=\"disabled\">$p</span>";
				//pages
				if ($lastpage < 7 + ($this->adjacents * 2)){//not enough pages to bother breaking it up
					for ($counter = 1; $counter <= $lastpage; $counter++){
						if ($counter == $this->page)
						$this->pagination .= "<span class=\"current\">$counter</span>";
						else
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
					}
				}
				elseif($lastpage > 5 + ($this->adjacents * 2)){//enough pages to hide some
					//close to beginning; only hide later pages
					if($this->page < 1 + ($this->adjacents * 2)){
						for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++){
							if ($counter == $this->page)
							$this->pagination .= "<span class=\"current\">$counter</span>";
							else
							$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
						}
						$this->pagination .= "...";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
					}
					//in middle; hide some front and some back
					elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)){
						$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
						$this->pagination .= "...";
						for ($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
						if ($counter == $this->page)
						$this->pagination .= "<span class=\"current\">$counter</span>";
						else
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
						$this->pagination .= "...";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($lpm1)."\">$lpm1</a>";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($lastpage)."\">$lastpage</a>";
					}
					//close to end; only hide early pages
					else{
						$this->pagination .= "<a href=\"".$this->get_pagenum_link(1)."\">1</a>";
						$this->pagination .= "<a href=\"".$this->get_pagenum_link(2)."\">2</a>";
						$this->pagination .= "...";
						for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
						if ($counter == $this->page)
						$this->pagination .= "<span class=\"current\">$counter</span>";
						else
						$this->pagination .= "<a href=\"".$this->get_pagenum_link($counter)."\">$counter</a>";
					}
				}
				//siguiente button
				if ($this->page < $counter - 1)
				$this->pagination .= "<a href=\"".$this->get_pagenum_link($next)."\">$n</a>";
				else
				$this->pagination .= "<span class=\"disabled\">$n</span>";
				if($this->showCounter)$this->pagination .= "<div class=\"pagination_data\">($this->total_pages ".__("Pages","wassup").")</div>";
			}

			return true;
		}
	} //end class pagination
} //end if !class_exists('pagination')

if (!class_exists('Detector')) { 	//in case another app uses this class...
	//
	// Detector class (c) Mohammad Hafiz bin Ismail 2006
	// detect location by ipaddress
	// detect browser type and operating system
	//
	// November 27, 2006
	//
	// by : Mohammad Hafiz bin Ismail (info@mypapit.net)
	//
	// You are allowed to use this work under the terms of
	// Creative Commons Attribution-Share Alike 3.0 License
	//
	// Reference : http://creativecommons.org/licenses/by-sa/3.0/
	//

	class Detector {

		var $town;
		var $state;
		var $country;
		var $Ctimeformatode;
		var $longitude;
		var $latitude;
		var $ipaddress;
		var $txt;

		var $browser;
		var $browser_version;
		var $os_version;
		var $os;
		var $useragent;

		function Detector($ip="", $ua="")
		{
			$apiserver="http://showip.fakap.net/txt/";
			if ($ip != "") {
				if (preg_match('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/',$ip,$matches))
				{
					$this->ipaddress=$ip;
				}

				else { $this->ipaddress = "0.0.0.0"; }

				//uncomment this below if CURL doesnt work

				$this->txt=file_get_contents($apiserver . "$ip");

				$wtf=$this->txt;
				$this->processTxt($wtf);
			}

			$this->useragent=$ua;
			$this->check_os($ua);
			$this->check_browser($ua);
		}

		function processTxt($wtf)

		{
			//	  	$tok = strtok($txt, ',');
			$this->town = strtok($wtf,',');
			$this->state = strtok(',');
			$this->country=strtok(',');
			$this->ccode = strtok(',');
			$this->latitude=strtok(',');
			$this->longitude=strtok(',');
		}

		function check_os($useragent) {

			$os = "N/A"; $version = "";

			if (preg_match("/Windows NT 5.1/",$useragent,$match)) {
				$os = "WinXP"; $version = "";
			} elseif (preg_match("/Windows NT 5.2/",$useragent,$match)) {
				$os = "Win2003"; $version = "";
			} elseif (preg_match("/Windows NT 6.0/",$useragent,$match)) {
				$os = "WinVista"; $version = "";
			} elseif (preg_match("/(?:Windows NT 5.0|Windows 2000)/",$useragent,$match)) {
				$os = "Win2000"; $version = "";
			} elseif (preg_match("/Windows ME/",$useragent,$match)) {
				$os = "WinME"; $version = "";
			} elseif (preg_match("/(?:WinNT|Windows\s?NT)\s?([0-9\.]+)?/",$useragent,$match)) {
				$os = "WinNT"; $version = $match[1];
			} elseif (preg_match("/Mac OS X/",$useragent,$match)) {
				$os = "MacOSX"; $version = "";
			} elseif (preg_match("/(Mac_PowerPC|Macintosh)/",$useragent,$match)) {
				$os = "MacPPC"; $version = "";
			} elseif (preg_match("/(?:Windows95|Windows 95|Win95|Win 95)/",$useragent,$match)) {
				$os = "Win95"; $version = "";
			} elseif (preg_match("/(?:Windows98|Windows 98|Win98|Win 98|Win 9x)/",$useragent,$match)) {
				$os = "Win98"; $version = "";
			} elseif (preg_match("/(?:WindowsCE|Windows CE|WinCE|Win CE)/",$useragent,$match)) {
				$os = "WinCE"; $version = "";
			} elseif (preg_match("/PalmOS/",$useragent,$match)) {
				$os = "PalmOS";
			} elseif (preg_match("/\(PDA(?:.*)\)(.*)Zaurus/",$useragent,$match)) {
				$os = "Sharp Zaurus";
			} elseif (preg_match("/Linux\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "Linux"; $version = $match[1];
			} elseif (preg_match("/NetBSD\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "NetBSD"; $version = $match[1];
			} elseif (preg_match("/OpenBSD\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "OpenBSD"; $version = $match[1];
			} elseif (preg_match("/CYGWIN\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2}\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "CYGWIN"; $version = $match[1];
			} elseif (preg_match("/SunOS\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "SunOS"; $version = $match[1];
			} elseif (preg_match("/IRIX\s*([0-9\.]+)?/",$useragent,$match)) {
				$os = "SGI IRIX"; $version = $match[1];
			} elseif (preg_match("/FreeBSD\s*((?:i[0-9]{3})?\s*(?:[0-9]\.[0-9]{1,2})?\s*(?:i[0-9]{3})?)?/",$useragent,$match)) {
				$os = "FreeBSD"; $version = $match[1];
			} elseif (preg_match("/SymbianOS\/([0-9.]+)/i",$useragent,$match)) {
				$os = "SymbianOS"; $version = $match[1];
			} elseif (preg_match("/Symbian\/([0-9.]+)/i",$useragent,$match)) {
				$os = "Symbian"; $version = $match[1];
			} elseif (preg_match("/PLAYSTATION 3/",$useragent,$match)) {
				$os = "Playstation"; $version = 3;
			}

			$this->os = $os;
			$this->os_version = $version;
		}

		function check_browser($useragent) {

			$browser = "";

			if (preg_match("/^Mozilla(?:.*)compatible;\sMSIE\s(?:.*)Opera\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "Opera";
			} elseif (preg_match("/^Opera\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Opera";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\siCab\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "iCab";
			} elseif (preg_match("/^iCab\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "iCab";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\sMSIE\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "IE";
			} elseif (preg_match("/^(?:.*)compatible;\sMSIE\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "IE";
			} elseif (preg_match("/^Mozilla(?:.*)(?:.*)Safari/",$useragent,$match)) {
				$browser = "Safari";
				//} elseif (preg_match("/^Mozilla(?:.*)\(Windows(?:.*)Safari\/([0-9\.]+)/",$useragent,$match)) {
				//	$browser = "Safari";
			} elseif (preg_match("/^Mozilla(?:.*)\(Macintosh(?:.*)OmniWeb\/v([0-9\.]+)/",$useragent,$match)) {
				$browser = "Omniweb";
			} elseif (preg_match("/^Mozilla(?:.*)\(compatible; Google Desktop/",$useragent,$match)) {
				$browser = "Google Desktop";
			} elseif (preg_match("/^Mozilla(?:.*)\(compatible;\sOmniWeb\/([0-9\.v-]+)/",$useragent,$match)) {
				$browser = "Omniweb";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)(?:Camino|Chimera)\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Camino";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Netscape\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Netscape";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)(?:Fire(?:fox|bird)|Phoenix)\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Firefox";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Minefield\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Minefield";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)Epiphany\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Epiphany";
			} elseif (preg_match("/^Mozilla(?:.*)Galeon\/([0-9\.]+)\s(?:.*)Gecko/",$useragent,$match)) {
				$browser = "Galeon";
			} elseif (preg_match("/^Mozilla(?:.*)Gecko(?:.*?)K-Meleon\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "K-Meleon";
			} elseif (preg_match("/^Mozilla(?:.*)rv:([0-9\.]+)\)\sGecko/",$useragent,$match)) {
				$browser = "Mozilla";
			} elseif (preg_match("/^Mozilla(?:.*)compatible;\sKonqueror\/([0-9\.]+);/",$useragent,$match)) {
				$browser = "Konqueror";
			} elseif (preg_match("/^Mozilla\/(?:[34]\.[0-9]+)(?:.*)AvantGo\s([0-9\.]+)/",$useragent,$match)) {
				$browser = "AvantGo";
			} elseif (preg_match("/^Mozilla(?:.*)NetFront\/([34]\.[0-9]+)/",$useragent,$match)) {
				$browser = "NetFront";
			} elseif (preg_match("/^Mozilla\/([34]\.[0-9]+)/",$useragent,$match)) {
				$browser = "Netscape";
			} elseif (preg_match("/^Liferea\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "Liferea";
			} elseif (preg_match("/^curl\/([0-9\.]+)/",$useragent,$match)) {
				$browser = "curl";
			} elseif (preg_match("/^links\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Links";
			} elseif (preg_match("/^links\s?\(([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Links";
			} elseif (preg_match("/^lynx\/([0-9a-z\.]+)/i",$useragent,$match)) {
				$browser = "Lynx";
			} elseif (preg_match("/^Wget\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Wget";
			} elseif (preg_match("/^Xiino\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Xiino";
			} elseif (preg_match("/^W3C_Validator\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "W3C Validator";
			} elseif (preg_match("/^Jigsaw(?:.*) W3C_CSS_Validator_(?:[A-Z]+)\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "W3C CSS Validator";
			} elseif (preg_match("/^Dillo\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Dillo";
			} elseif (preg_match("/^amaya\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "Amaya";
			} elseif (preg_match("/^DocZilla\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "DocZilla";
			} elseif (preg_match("/^fetch\slibfetch\/([0-9\.]+)/i",$useragent,$match)) {
				$browser = "FreeBSD libfetch";
			} elseif (preg_match("/^Nokia([0-9a-zA-Z\-.]+)\/([0-9\.]+)/i",$useragent,$match)) {
				$browser="Nokia";
			} elseif (preg_match("/^SonyEricsson([0-9a-zA-Z\-.]+)\/([a-zA-Z0-9\.]+)/i",$useragent,$match)) {
				$browser="SonyEricsson";
			}

			//$version = $match[1];
			//restrict version to major and minor version #'s
			preg_match("/^\d+(\.\d+)?/",$match[1],$majorvers);
			$version = $majorvers[0];

			$this->browser = $browser;
			$this->browser_version = $version;
		}
	}
}


/*
 # PHP Calendar (version 2.3), written by Keith Devens
 # http://keithdevens.com/software/php_calendar
 #  see example at http://keithdevens.com/weblog
 # License: http://keithdevens.com/software/license
 */
function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	#remember that mktime will automatically correct if invalid dates are entered
	# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	# this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
	$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

	#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table class="calendar">'."\n".
                '<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		#if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
		$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}

	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
			$calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
			($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
		}
		else $calendar .= "<td>$day</td>";
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}

//Truncate $input string to a length of $max
function stringShortener($input, $max=0, $separator="(...)", $exceedFromEnd=0){
	if(!$input || !is_string($input)){return false;};

	//Replace all %-hex chars with literals and trim the input string of whitespaces
	//   ...because it is shorter and more legible -Helene D. 11/18/07
	$input = trim(rawurldecode($input));

	$inputlen=strlen($input);
	$max=(is_numeric($max))?(integer)$max:$inputlen;
	if($max>=$inputlen){return $input;};
	$separator=($separator)?$separator:"(...)";
	$modulus=(($max%2));
	$halfMax=floor($max/2);
	$begin="";
	if(!$modulus){$begin=substr($input, 0, $halfMax);}
	else{$begin=(!$exceedFromEnd)? substr($input, 0, $halfMax+1) : substr($input, 0, $halfMax);}
	$end="";
	if(!$modulus){$end=substr($input,$inputlen-$halfMax);}
	else{$end=($exceedFromEnd)? substr($input,$inputlen-$halfMax-1) :substr($input,$inputlen-$halfMax);}
	$extracted=substr( $input, strpos($input,$begin)+strlen($begin), $inputlen-$max );
	$outstring = $begin.$separator.$end;
	if (strlen($outstring) >= $inputlen) {  //Because "Fir(...)fox" is longer than "Firefox"
		$outstring = $input;
	}
	//# added WP 2.x function attribute_escape to help make malicious
	//#   code harmless when echoed to screen...
	if (function_exists('attribute_escape')) {
		return attribute_escape($outstring);
	} else {
		return addslashes($outstring);
	}
}

//# Return a value of true if url argument is a root url and false when
//#  url constains a subdirectory path or query parameters...
//#  - Helene D. 2007
function url_rootcheck($urltocheck) {
	$isroot = false;
	//url must begin with 'http://'
	if (strncasecmp($urltocheck,'http://',7) == 0) {
		$isroot = true;
		$urlparts=parse_url($urltocheck);
		if (!empty($urlparts['path']) && $urlparts['path'] != "/") {
			$isroot=false;
		} elseif (!empty($urlparts['query'])) {
			$isroot=false;
		}
	}
	return $isroot;
}

function time_left($time) {
	
	define("TIME_PERIODS_PLURAL_SINGULAR", "weeks:week,years:year,days:day,hours:hour, : ,minutes:minute,seconds:second");
	define("TIME_LEFT_STRING_TPL", " #num# #period#");

	$timeRanges = array('years' => 365*60*60*24,/* 'weeks' => 60*60*24*7, */ 'days' => 60*60*24, 'hours' => 60*60, 'minutes' => 60, 'seconds' => 1);

	$secondsLeft = $time;

	// prepare ranges
	$outRanges = array();
	foreach ($timeRanges as $period => $sec)
        if ($secondsLeft/$sec >= 1) {
          $outRanges[$period] =  floor($secondsLeft/$sec);
          $secondsLeft -= ($outRanges[$period] * $sec);
		}

	// playing with TIME_PERIODS_PLURAL_SINGULAR
	$periodsEx = explode(",", TIME_PERIODS_PLURAL_SINGULAR);
	$periodsAr = array();
	foreach ($periodsEx as $periods) {
		$ex  = explode(":", $periods);
		$periodsAr[$ex[0]] = array('plural' => $ex[0], 'singular' => $ex[1]);
	}

	// string out
	$outString = "";
	$outStringAr = array();
	foreach ($outRanges as $period => $num) {
		$per = $periodsAr[$period]['plural'];
		if ($num == 1)  $per = $periodsAr[$period]['singular'];

		$outString .= $outStringAr[$period] = str_replace(array("#num#", "#period#"), array($num, $per), TIME_LEFT_STRING_TPL);
	}

	return array('timeRanges' => $outRanges, 'leftStringAr' => $outStringAr, 'leftString' => $outString);
}

function time_left_to_string($time) {
	$a = time_left($time);
	$a = $a['timeRanges'];
	$s = "";
	$s .= (isset($a['hours']) ? $a['hours']: "0").":";
	$s .= (isset($a['minutes']) ? $a['minutes']: "0").":";
	$s .= (isset($a['seconds']) ? $a['seconds']: "0");
	
	return $s;
}

function array_search_extended($file,$str_search) {
	foreach($file as $key => $line) {
		if (strpos($line, $str_search)!== FALSE) {
			return $key;
		}
	}
	return false;
}

?>