<?php
/**
 * Outputs an ATOM or RSS feed of information, mostly based on the information available
 * in the index page.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @package webtrees
 * @subpackage RSS
 * @TODO add Basic HTTP authentication to allow RSS aggregators to "log on"
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'rss.php');
require './includes/session.php';

require_once WT_ROOT.'includes/classes/class_feedcreator.php';
require_once WT_ROOT.'includes/functions/functions_rss.php';
require_once WT_ROOT.'includes/index_cache.php';

$feedCacheName = "fullFeed";

/*if (!empty($auth)){
	if($auth=="basic"){
		basicHTTPAuthenticateUser();
	}
}*/

// valid format strings are: RSS0.91, RSS1.0, RSS2.0, MBOX, OPML, ATOM, ATOM1.0, ATOM0.3, HTML, JS
if (empty($rssStyle)){
	if (!empty($RSS_FORMAT)) $rssStyle = $RSS_FORMAT;
	else $rssStyle = "ATOM";	// Unless configured otherwise, default to ATOM
}

if (!isset($_SERVER['QUERY_STRING'])){
	$_SERVER['QUERY_STRING'] = "lang=".WT_LOCALE;
}

$printTodays = false;
$printUpcoming = false;
$printGedcomStats = false;
$printGedcomNews = false;
$printTop10Surnames = false;
$printRecentChanges = false;
$printRandomMedia = false;

if(!empty($module)){
	$feedCacheName = $module . "Feed";
	if($module == "today"){
		$printTodays = true;
	} else if($module == "upcoming"){
		$printUpcoming = true;
	} else if($module == "gedcomStats"){
		$printGedcomStats = true;
	} else if($module == "gedcomNews"){
		$printGedcomNews = true;
	} else if($module == "top10Surnames"){
		$printTop10Surnames = true;
	} else if($module == "recentChanges"){
		$printRecentChanges = true;
	} else if($module == "randomMedia"){
		$printRandomMedia = true;
	}
}

// Build the array to control caching
$cacheControl = array();
$cacheControl[0] = $feedCacheName;
$cacheControl[1] = array("cache"=>1);
if (!empty($module) && $module=="randomMedia") $cacheControl[1]["cache"] = 0;

if(!loadCachedBlock($cacheControl, $rssStyle)){
	$author=getUserFullName($CONTACT_EMAIL);

	$feed = new UniversalFeedCreator();
	$feed->generator = WT_WEBTREES_URL;
	$feed->title = get_gedcom_setting(WT_GED_ID, 'title');
	$feed->language = WT_LOCALE;
	$feed->descriptionHtmlSyndicated = true;
	//$feed->descriptionTruncSize = 500; // does not make sense to truncate HTML since it will result in unpredictable output
	$feed->link = $SERVER_URL;
	$syndURL = $SERVER_URL."rss.php?".$_SERVER['QUERY_STRING'];
	$syndURL = str_replace("&", "&amp;", $syndURL);
	$feed->syndicationURL = $syndURL;

	$feedDesc =  i18n::translate('News and links from the %s site', $feed->title);
	$feed->description = $feedDesc;
	$feed->copyright = $author . " (c) " . date("Y");
	$feed->category="genealogy";

	$image = new FeedImage();
	$image->title = i18n::translate('Feed created by webtrees');
	$image->url = $SERVER_URL."images/gedview.gif";
	$image->link = WT_WEBTREES_URL;
	$image->description = i18n::translate('Feed created by webtrees');
	$image->descriptionHtmlSyndicated = true;
	//$feed->descriptionTruncSize = 500; // does not make sense to truncate HTML since it will result in unpredictable output
	$feed->image = $image;



	if($ENABLE_RSS) {
		// determine if to show parts of feed based on their exsistance in the blocks on index.php
		$blocks=  getBlocks(WT_GEDCOM);
		$main = $blocks["main"];

		if(empty($module)) {
			if (count($main)==0) {
				$printGedcomStats = true;
				$printGedcomNews = true;
			} else {
				foreach($main as $mname => $value){
					$WT_BLOCKS[$value[0]]['config'] = $value[1]; //set the config needed by functions_rss
					if($value[0] == "print_todays_events"){
						$printTodays = true;
					} else if($value[0] == "print_upcoming_events"){
						$printUpcoming = true;
					} else if($value[0] == "print_gedcom_stats"){
						$printGedcomStats = true;
					} else if($value[0] == "print_gedcom_news"){
						$printGedcomNews = true;
					} else if($value[0] == "print_block_name_top10"){
						$printTop10Surnames = true;
					} else if($value[0] == "print_recent_changes"){
						$printRecentChanges = true;
					} else if($value[0] == "print_random_media"){
						$printRandomMedia = true;
					}
				}
			}
			$right = $blocks["right"];
			if (count($right)==0) {
				$printTodays = true;
			} else {
				foreach($right as $mname => $value){
					$WT_BLOCKS[$value[0]]['config'] = $value[1]; //set the config needed by functions_rss
					if($value[0] == "print_todays_events"){
						$printTodays = true;
					} else if($value[0] == "print_upcoming_events"){
						$printUpcoming = true;
					} else if($value[0] == "print_gedcom_stats"){
						$printGedcomStats = true;
					} else if($value[0] == "print_gedcom_news"){
						$printGedcomNews = true;
					} else if($value[0] == "print_block_name_top10"){
						$printTop10Surnames = true;
					} else if($value[0] == "print_recent_changes"){
						$printRecentChanges = true;
					} else if($value[0] == "print_random_media"){
						$printRandomMedia = true;
					}
				}
			}
		}

		if($printTodays){
			$todaysEvents = getTodaysEvents();
			if (! empty($todaysEvents[2])) {
				$item = new FeedItem();
				$item->title = $todaysEvents[0];
				$item->link = $SERVER_URL. "calendar.php?action=today";
				$item->description = $todaysEvents[2];
				$item->descriptionHtmlSyndicated = true;
				$item->date = $todaysEvents[1];
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$feed->addItem($item);
			}
		}

		if($printUpcoming){
			$upcomingEvent = getUpcomingEvents();
			if (! empty($upcomingEvent[2])) {
				$item = new FeedItem();
				$item->title = $upcomingEvent[0];
				$item->link = $SERVER_URL. "calendar.php?action=calendar";
				$item->description = $upcomingEvent[2];
				$item->descriptionHtmlSyndicated = true;
				$item->date = $upcomingEvent[1];
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$feed->addItem($item);
			}
		}

		if($printGedcomStats){
			$gedcomStats = getGedcomStats();
			if (! empty($gedcomStats[2])) {
				$item = new FeedItem();
				$item->title = $gedcomStats[0];
				$item->link = $SERVER_URL. "index.php?ctype=gedcom#gedcom_stats";
				$item->description = $gedcomStats[2];
				$item->descriptionHtmlSyndicated = true;
				if (! empty($gedcomStats[1])) {
					$item->date = $gedcomStats[1];
				}
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$feed->addItem($item);
			}
		}

		if($printTop10Surnames){
			$top10 = getTop10Surnames();
			if (! empty($top10[2])) {
				$item = new FeedItem();
				$item->title = $top10[0];
				$item->link = $SERVER_URL. "indilist.php";
				$item->description = $top10[2];
				$item->descriptionHtmlSyndicated = true;
				if (! empty($top10[1])) {
					$item->date = $top10[1];
				}
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$feed->addItem($item);
			}
		}

		if($printGedcomNews){
			$gedcomNews = getGedcomNews();

			$numElements = count($gedcomNews); //number of news items
			for($i=0; $i < $numElements; $i++) {
				$newsItem = $gedcomNews[$i];
				if (! empty($newsItem[1])) {
					$item = new FeedItem();
					$item->title = $newsItem[0];
					$item->link = $SERVER_URL . "index.php?ctype=gedcom#" . $newsItem[3];
					$item->description = $newsItem[2];
					$item->descriptionHtmlSyndicated = true;
					$item->date = $newsItem[1];
					$item->source = $SERVER_URL ;
					$item->author = $author;
					$item->authorURL = $feed->link;
					$item->category="genealogy";
					$feed->addItem($item);
				}
			}
		}

		if($printRecentChanges){
			$recentChanges= getRecentChanges();
			if (! empty($recentChanges[2])) {
				$item = new FeedItem();
				$item->title = $recentChanges[0];
				$item->link = $SERVER_URL. "index.php?ctype=gedcom#recent_changes";
				$item->description = $recentChanges[2];
				$item->descriptionHtmlSyndicated = true;

				if (! empty($recentChanges[1])) {
					$item->date = $recentChanges[1];
				}
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$feed->addItem($item);
			}
		}

		if($printRandomMedia){
			$randomMedia= getRandomMedia();
			if (! empty($randomMedia[2])) {
				$item = new FeedItem();
				$item->title = $randomMedia[0];
				$item->link = $SERVER_URL. "medialist.php";
				$item->description = $randomMedia[2];
				$item->descriptionHtmlSyndicated = true;

				if (! empty($randomMedia[1])) {
					$item->date = $randomMedia[1];
				}
				$item->source = $SERVER_URL;
				$item->author = $author;
				$item->authorURL = $feed->link;
				$item->category = i18n::translate('genealogy');
				$item->enclosure = new EnclosureItem();
				$item->enclosure->url = $SERVER_URL . $randomMedia[3];
				$item->enclosure->type = $randomMedia[4];
				$item->enclosure->length = $randomMedia[5];
				$item->enclosure->title = $randomMedia[6];

				$feed->addItem($item);
			}
		}
	} else {
		$item = new FeedItem();
		$item->title = i18n::translate('Feed not available');
		$item->link = $SERVER_URL. "index.php";
		$item->description = i18n::translate('There is no RSS feed available for this webtrees site');
		$item->date = time();
		$item->source = $SERVER_URL;
		$item->author = $author;
		$item->authorURL = $feed->link;
		$item->category = i18n::translate('genealogy');
		$feed->addItem($item);
	}

	//$feed->outputFeed($rssStyle);

	ob_start();
	$feed->outputFeed($rssStyle);
	$content = ob_get_contents();
	saveCachedBlock($cacheControl, $rssStyle, $content);
	ob_end_flush();
}

?>
