<?php
/**
* Various functions used to generate the webtrees RSS feed.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @version $Id$
* @package webtrees
* @subpackage RSS
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_RSS_PHP', '');

require_once WT_ROOT.'includes/functions/functions_print_lists.php';
require_once WT_ROOT.'includes/classes/class_stats.php';

$time = client_time();
$day = date("j", $time);
$month = date("M", $time);
$year = date("Y", $time);

/**
* Returns an ISO8601 formatted date used for the RSS feed
*
* @param $time the time in the UNIX time format (milliseconds since Jan 1, 1970)
* @return SO8601 formatted date in the format of 2005-07-06T20:52:16+00:00
*/
function iso8601_date($time) {
	$tzd = date('O',$time);
	$tzd = $tzd[0].str_pad((int)($tzd/100), 2, "0", STR_PAD_LEFT).':'.str_pad((int)($tzd % 100), 2, "0", STR_PAD_LEFT);
	$date = date('Y-m-d\TH:i:s', $time) . $tzd;
	return $date;
}

/**
* Returns the upcoming events array used for the RSS feed.
* Uses configuration set for the blocks. If not configured, it will default to events in the
* next 30 days, all events for living & and not living people
*
* @return the array with upcoming events data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data
*/
function getUpcomingEvents() {
	global $month, $year, $day, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;
	global $DAYS_TO_SHOW_LIMIT, $SERVER_URL;

	$dataArray[0] = i18n::translate('Upcoming Events');
	$dataArray[1] = time();

	if (empty($config)) $config = $WT_BLOCKS["print_upcoming_events"]["config"];
	if (!isset($DAYS_TO_SHOW_LIMIT)) $DAYS_TO_SHOW_LIMIT = 30;
	if (isset($config["days"])) $daysprint = $config["days"];
	else $daysprint = 30;
	if (isset($config["filter"])) $filter = $config["filter"];  // "living" or "all"
	else $filter = "all";
	if (isset($config["onlyBDM"])) $onlyBDM = $config["onlyBDM"];  // "yes" or "no"
	else $onlyBDM = "no";

	if ($daysprint < 1) $daysprint = 1;
	if ($daysprint > $DAYS_TO_SHOW_LIMIT) $daysprint = $DAYS_TO_SHOW_LIMIT;  // valid: 1 to limit

	$startjd=client_jd()+1;
	$endjd=client_jd()+$daysprint;

	$daytext=print_events_list($startjd, $endjd, $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', true);
	$daytext = str_replace(array("<br />", "<ul></ul>", " </a>"), array(" ", "", "</a>"), $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b><span>');
	$dataArray[2]  = $daytext;
	return $dataArray;
}


/**
* Returns the today's events array used for the RSS feed
*
* @return the array with todays events data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data
*/
function getTodaysEvents() {
	global $month, $year, $day, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $WT_BLOCKS;
	global $SERVER_URL;
	global $DAYS_TO_SHOW_LIMIT;

	$dataArray[0] = i18n::translate('On This Day ...');
	$dataArray[1] = time();

	if (empty($config)) $config = $WT_BLOCKS["print_todays_events"]["config"];
	if (isset($config["filter"])) $filter = $config["filter"];  // "living" or "all"
	else $filter = "all";
	if (isset($config["onlyBDM"])) $onlyBDM = $config["onlyBDM"];  // "yes" or "no"
	else $onlyBDM = "no";

	$daytext=print_events_list(client_jd(), client_jd(), $onlyBDM=='yes'?'BIRT MARR DEAT':'', $filter=='living', true);
	$daytext = str_replace(array("<br />", "<ul></ul>", " </a>"), array(" ", "", "</a>"), $daytext);
	$daytext = strip_tags($daytext, '<a><ul><li><b><span>');
	$dataArray[2]  = $daytext;
	return $dataArray;
}

/**
* Returns the GEDCOM stats.
*
* @return the array with GEDCOM stats data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data
* @TODO does not print the family with most children due to the embedded html in that function.
*/
function getGedcomStats() {
	global $day, $month, $year, $WT_BLOCKS, $ALLOW_CHANGE_GEDCOM, $ctype, $COMMON_NAMES_THRESHOLD, $SERVER_URL, $RTLOrd;

	if (empty($config)) $config = $WT_BLOCKS["print_gedcom_stats"]["config"];
	if (!isset($config['stat_indi'])) $config = $WT_BLOCKS["print_gedcom_stats"]["config"];

	$data = "";
	$dataArray[0] = i18n::translate('GEDCOM Statistics') . " - " . get_gedcom_setting(WT_GED_ID, 'title');

	$head = find_gedcom_record("HEAD", WT_GED_ID);
	$ct=preg_match("/1 SOUR (.*)/", $head, $match);
	if ($ct>0) {
		$softrec = get_sub_record(1, "1 SOUR", $head);
		$tt= preg_match("/2 NAME (.*)/", $softrec, $tmatch);
		if ($tt>0) $title = trim($tmatch[1]);
		else $title = trim($match[1]);
		if (!empty($title)) {
			$tt = preg_match("/2 VERS (.*)/", $softrec, $tmatch);
			if ($tt>0) $version = trim($tmatch[1]);
			else $version="";
			$data .= strip_tags(i18n::translate('This GEDCOM was created using %s %s', $title, $version));
		}
	}
	$ct=preg_match("/1 DATE (.+)/", $head, $match);
	if ($ct>0) {
		$date = trim($match[1]);
		$dataArray[1] = strtotime($date);

		$date=new GedcomDate($date);
		if (empty($title)){
			$data .= i18n::translate('This GEDCOM was created on <b>%s</b>', $date->Display(false));
		} else {
			$data .= i18n::translate(' on <b>%s</b>', $date->Display(false));
		}
	}

	$stats=new stats(WT_GEDCOM, $SERVER_URL);

	$data .= " <br />";
	if (!isset($config["stat_indi"]) || $config["stat_indi"]=="yes"){
		$data .= $stats->totalIndividuals()." - " .i18n::translate('Individuals')."<br />";
	}
	if (!isset($config["stat_fam"]) || $config["stat_fam"]=="yes"){
		$data .= $stats->totalFamilies()." - ".i18n::translate('Families')."<br />";
	}
	if (!isset($config["stat_sour"]) || $config["stat_sour"]=="yes"){
		$data .= $stats->totalSources()." - ".i18n::translate('Sources')."<br /> ";
	}
	if (!isset($config["stat_other"]) || $config["stat_other"]=="yes"){
		$data .= $stats->totalOtherRecords()." - ".i18n::translate('Other records')."<br />";
	}
	if (!isset($config["stat_first_birth"]) || $config["stat_first_birth"]=="yes") {
		$data .= i18n::translate('Earliest birth year')." - ".$stats->firstBirthYear()."<br />";
	}
	if (!isset($config["stat_last_birth"]) || $config["stat_last_birth"]=="yes") {
		$data .= i18n::translate('Latest birth year')." - ".$stats->lastBirthYear()."<br />";
	}
	if (!isset($config["stat_long_life"]) || $config["stat_long_life"]=="yes") {
		$data .= i18n::translate('Person who lived the longest')." - ".$stats->LongestLifeAge()."<br />";
	}
	if (!isset($config["stat_avg_life"]) || $config["stat_avg_life"]=="yes") {
		$data .= i18n::translate('Average age at death')." - ".$stats->averageLifespan()."<br />";
	}
	if (!isset($config["stat_most_chil"]) || $config["stat_most_chil"]=="yes") {
		$data .= i18n::translate('Family with the most children') . $stats->largestFamilySize().' - '.$stats->largestFamily()."<br />";
	}
	if (!isset($config["stat_avg_chil"]) || $config["stat_avg_chil"]=="yes") {
		$data .= i18n::translate('Average number of children per family')." - ".$stats->averageChildren()."<br />";
	}
	if (!isset($config["show_common_surnames"]) || $config["show_common_surnames"]=="yes") {
		$data .="<b>".i18n::translate('Most Common Surnames')."</b><br />".$stats->commonSurnames();
	}

	$data = strip_tags($data, '<a><br><b>');
	$dataArray[2] = $data;
	return $dataArray;
}

/**
* Returns the gedcom news for the RSS feed
*
* @return array of GEDCOM news arrays. Each GEDCOM news array contains $itemArray[0] = title, $itemArray[1] = date,
* $itemArray[2] = data, $itemArray[3] = anchor (so that the link will load the proper part of the PGV page)
* @TODO prepend relative URL's in news items with $SERVER_URL
*/
function getGedcomNews() {
	global $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $ctype, $SERVER_URL;

	$usernews = getUserNews(WT_GEDCOM);

	$dataArray = array();
	foreach($usernews as $key=>$news) {

		$day = date("j", $news["date"]);
		$mon = date("M", $news["date"]);
		$year = date("Y", $news["date"]);
		$data = "";

		// Look for $GLOBALS substitutions in the News title
		$newsTitle = embed_globals($news["title"]);
		$itemArray[0] = $newsTitle;

		$itemArray[1] = iso8601_date($news["date"]);

		// Look for $GLOBALS substitutions in the News text
		$newsText = embed_globals($news["text"]);
		$trans = get_html_translation_table(HTML_SPECIALCHARS);
		$trans = array_flip($trans);
		$newsText = strtr($newsText, $trans);
		$newsText = nl2br($newsText);
		$data .= $newsText;
		$itemArray[2] = $data;
		$itemArray[3] = $news["anchor"];
		$dataArray[] = $itemArray;

	}
	return $dataArray;
}

/**
* Returns the top 10 surnames
*
* @return the array with the top 10 surname data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data
* @TODO Possibly turn list into a <ul> list
*/
function getTop10Surnames() {
	global $SERVER_URL, $TEXT_DIRECTION;
	global $COMMON_NAMES_ADD, $COMMON_NAMES_REMOVE, $COMMON_NAMES_THRESHOLD, $WT_BLOCKS, $ctype, $WT_IMAGES, $WT_IMAGE_DIR;

	$data = "";
	$dataArray = array();


	function top_surname_sort($a, $b) {
		return $b["match"] - $a["match"];
	}

	if (empty($config)) $config = $WT_BLOCKS["print_block_name_top10"]["config"];

	if (isset($config["num"])) $numName = $config["num"];
	else $numName = 10;

	$dataArray[0] = str_replace("10", $numName, i18n::translate('Top 10 Surnames'));
	$dataArray[1] = time();

	$surnames = get_common_surnames($numName);

	// Sort the list and save for future reference
	uasort($surnames, "top_surname_sort");

	if (count($surnames)>0) {
		$i=0;
		foreach($surnames as $indexval => $surname) {
			$data .= "<a href=\"".encode_url("{$SERVER_URL}indilist.php?surname={$surname['name']}")."\">".PrintReady($surname["name"])."</a> ";
			if ($TEXT_DIRECTION=="rtl") $data .= getRLM() . "[" . getRLM() .$surname["match"].getRLM() . "]" . getRLM() . "<br />";
			else $data .= "[".$surname["match"]."]<br />";
			$i++;
			if ($i>=$numName) break;
		}
	}
	$dataArray[2] = $data;
	return $dataArray;
}

/**
* Returns the recent changes list for the RSS feed
*
* @return the array with recent changes data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data
* @TODO merge many changes from recent changes block
* @TODO use date of most recent change instead of curent time
*/
function getRecentChanges() {
	global $month, $year, $day, $HIDE_LIVE_PEOPLE, $SHOW_ID_NUMBERS, $ctype, $TEXT_DIRECTION;
	global $WT_IMAGE_DIR, $WT_IMAGES, $ASC, $IGNORE_FACTS, $IGNORE_YEAR, $LAST_QUERY, $WT_BLOCKS, $SHOW_SOURCES;
	global $objectlist, $SERVER_URL;

	if ($ctype=="user") $filter = "living";
	else $filter = "all";

	if (empty($config)) $config = $WT_BLOCKS["print_recent_changes"]["config"];
	$configDays = 30;
	if(isset($config["days"]) && $config["days"] > 0) $configDays = $config["days"];
	if (isset($config["hide_empty"])) $HideEmpty = $config["hide_empty"];
	else $HideEmpty = "no";

	$dataArray[0] = i18n::translate('Recent Changes');
	$dataArray[1] = time();//FIXME - get most recent change time

	$recentText = "<ul>";

	$action = "today";
	$found_facts = array();
	$changes=get_recent_changes(client_jd()-$configDays);

	if (count($changes)>0) {
		$found_facts = array();
		foreach($changes as $gid) {
			$gedrec = find_gedcom_record($gid, WT_GED_ID);
			if (empty($gedrec)) $gedrec = find_updated_record($gid, WT_GED_ID);

			if (!empty($gedrec)) {
				$type = "INDI";
				$match = array();
				$ct = preg_match('/0 @'.WT_REGEX_XREF.'@ ('.WT_REGEX_TAG.')/', $gedrec, $match);
				if ($ct>0) $type = $match[1];
				$disp = true;
				switch($type) {
					case 'INDI':
						if (($filter=="living")&&(is_dead($gedrec)==1)) {
							$disp = false;
						} elseif ($HIDE_LIVE_PEOPLE) {
							$disp = displayDetailsById($gid);
						}
						break;
					case 'FAM':
						if ($filter=="living") {
							$parents = find_parents_in_record($gedrec);
							$husb=Person::getInstance($parents['HUSB']);
							$wife=Person::getInstance($parents['HUSB']);
							if ($husb->isDead()) {
								$disp = false;
							} elseif ($HIDE_LIVE_PEOPLE) {
								$disp = $husb->canDisplayDetails();
							}
							if ($disp) {
								if ($wife->isDead()) {
									$disp = false;
								} elseif ($HIDE_LIVE_PEOPLE) {
									$disp = $wife->canDisplayDetails();
								}
							}
						} else {
							if ($HIDE_LIVE_PEOPLE) $disp = displayDetailsById($gid, "FAM");
						}
						break;
					default:
						$disp = displayDetailsById($gid, $type);
						break;
				}
				if ($disp) {
					$factrec = get_sub_record(1, "1 CHAN", $gedrec);
					$found_facts[$gid] = array($gid, $factrec, $type);
				}
			}
		}
	}

// Start output
	if (count($found_facts)==0 and $HideEmpty=="yes") return false;
// Print block content
	if (count($found_facts)==0) {
		echo i18n::translate('<b>There have been no changes within the last %s days.</b>', $configDays);
	} else {
		echo i18n::translate('<b>Changes made within the last %s days</b>', $configDays);
		foreach($found_facts as $gid=>$factarr) {
			$record=GedcomRecord::getInstance($gid);
			if ($record && $record->canDisplayDetails()) {
				$recentText.='<li>';
				$recentText.='<a href="'.encode_url($record->getAbsoluteLinkUrl()).'"><b>'.PrintReady($record->getFullName()).'</b>';
				if ($SHOW_ID_NUMBERS) {
					$recentText .= ' '.WT_LPARENS.$gid.WT_RPARENS;
				}
				$recentText.='</a> '.i18n::translate('CHAN').' - '.$record->LastChangeTimestamp(false).'</li>';
			}
		}
	}
	$recentText.='</ul>';
	$recentText = strip_tags($recentText, '<a><ul><li><b><span>');
	$dataArray[2] = $recentText;
	return $dataArray;
}

/**
* Returns a random media for the RSS feed
*
* @return the array with random media data. the format is $dataArray[0] = title, $dataArray[1] = date,
* $dataArray[2] = data, $dataArray[3] = file path, $dataArray[4] = mime type,
* $dataArray[5] = file size, $dataArray[5] = media title
*/
function getRandomMedia() {
	global $foundlist, $MULTI_MEDIA, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES;
	global $MEDIA_EXTERNAL, $MEDIA_DIRECTORY, $SHOW_SOURCES;
	global $MEDIATYPE, $THUMBNAIL_WIDTH, $USE_MEDIA_VIEWER;
	global $WT_BLOCKS, $ctype, $action;
	global $WT_IMAGE_DIR, $WT_IMAGES;
	if (empty($config)) $config = $WT_BLOCKS["print_random_media"]["config"];
	if (isset($config["filter"])) $filter = $config["filter"];  // indi, event, or all
	else $filter = "all";

	$dataArray[0] = i18n::translate('Random Picture');
	$dataArray[1] = time();//FIXME - get most recent change time

	$randomMedia = "";


	if (!$MULTI_MEDIA) return;
	$medialist = array();
	$foundlist = array();

	$medialist = get_medialist(false, '', true, true);
	$ct = count($medialist);
	if ($ct>0) {
		$i=0;
		$disp = false;
		//-- try up to 40 times to get a media to display
		while($i<40) {
			$error = false;
			$value = array_rand($medialist);
			$links = $medialist[$value]["LINKS"];
			$disp = ($medialist[$value]["EXISTS"]>0) && $medialist[$value]["LINKED"] && $medialist[$value]["CHANGE"]!="delete" ;
			$disp &= displayDetailsById($value["XREF"], "OBJE");
			$disp &= !FactViewRestricted($value["XREF"], $value["GEDCOM"]);

			$isExternal = isFileExternal($medialist[$value]["FILE"]);

			if (!$isExternal) $disp &= ($medialist[$value]["THUMBEXISTS"]>0);

			// Filter according to format and type  (Default: unless configured otherwise, don't filter)
			if (!empty($medialist[$value]["FORM"]) && isset($config["filter_".$medialist[$value]["FORM"]]) && $config["filter_".$medialist[$value]["FORM"]]!="yes") $disp = false;
			if (!empty($medialist[$value]["TYPE"]) && isset($config["filter_".$medialist[$value]["TYPE"]]) && $config["filter_".$medialist[$value]["TYPE"]]!="yes") $disp = false;

			if ($disp && count($links) != 0){
				foreach($links as $key=>$type) {
					$gedrec = find_gedcom_record($key, WT_GED_ID);
					$disp &= !empty($gedrec);
					//-- source privacy is now available through the display details by id method
					// $disp &= $type!="SOUR";
					$disp &= displayDetailsById($key, $type);
				}
				if ($disp && $filter!="all") {
					// Apply filter criteria
					$ct = preg_match("/0 (@.*@) OBJE/", $medialist[$value]["GEDCOM"], $match);
					$objectID = $match[1];
					$ct2 = preg_match("/(\d) OBJE {$objectID}/", $gedrec, $match2);
					if ($ct2>0) {
						$objectRefLevel = $match2[1];
						if ($filter=="indi" && $objectRefLevel!="1") $disp = false;
						if ($filter=="event" && $objectRefLevel=="1") $disp = false;
					}
					else $disp = false;
				}
			}
			//-- leave the loop if we find an image that works
			if ($disp) {
				break;
			}
			//-- otherwise remove the private media item from the list
			else {
				unset($medialist[$value]);
			}
			//-- if there are no more media items, then try to get some more
			if (count($medialist)==0) $medialist = get_medialist(false, '', true, true);
			$i++;
		}
		if (!$disp) return false;

		$imgsize = findImageSize($medialist[$value]["FILE"]);
		$imgwidth = $imgsize[0]+40;
		$imgheight = $imgsize[1]+150;

		$mediaid = $medialist[$value]["XREF"];
		$randomMedia .= "<a href=\"".encode_url("mediaviewer.php?mid={$mediaid}")."\">";
		$mediaTitle = "";
		if (!empty($medialist[$value]["TITL"])) {
			$mediaTitle = PrintReady($medialist[$value]["TITL"]);
		} else {
			$mediaTitle = basename($medialist[$value]["FILE"]);
		}
		//if ($block) {
			$randomMedia .= "<img src=\"".$medialist[$value]["THUMB"]."\" border=\"0\" class=\"thumbnail\"";
			if ($isExternal) $randomMedia .=  " width=\"".$THUMBNAIL_WIDTH."\"";
			$randomMedia .= " alt=\"" . $mediaTitle . "\" title=\"" . $mediaTitle . "\" />";
		/*} else {
			print "<img src=\"".$medialist[$value]["FILE"]."\" border=\"0\" class=\"thumbnail\" ";
			$imgsize = findImageSize($medialist[$value]["FILE"]);
			if ($imgsize[0] > 175) print "width=\"175\" ";
			print " alt=\"" . $mediaTitle . "\" title=\"" . $mediaTitle . "\" />";
		}*/
		$randomMedia .= "</a>\n";
		$randomMedia .= "<br />";
		$randomMedia .= "<a href=\"".encode_url("mediaviewer.php?mid={$mediaid}")."\">";
		$randomMedia .= "<b>". $mediaTitle ."</b>";
		$randomMedia .= "</a>";

		$dataArray[2] = $randomMedia;
		$dataArray[3] = $medialist[$value]["FILE"];
		$dataArray[4] = image_type_to_mime_type($imgsize[2]);
		if ($dataArray[4] == false){
			$dataArray[4] ="";
			$parts = pathinfo($filename);
			if (isset ($parts["extension"])) {
				$ext = strtolower($parts["extension"]);
			} else {
				$ext="";
			}
			if($ext == "pdf"){
				$dataArray[4] = "application/pdf";
			}
		}
		$dataArray[5] = @filesize($medialist[$value]["FILE"]);
		$dataArray[6] = $mediaTitle;
		//$dataArray[7] = $medialist[$value]["XREF"];
	}
	return $dataArray;
}


?>
