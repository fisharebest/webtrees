<?php
/**
 * User Blog Block
 *
 * This block allows users to have their own blog
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Blocks
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_USER_BLOG_PHP', '');

$WT_BLOCKS['print_user_news']=array(
	'name'=>i18n::translate('User Journal'),
	'type'=>'user',
	'descr'=>i18n::translate('The User Journal block lets the user keep notes or a journal online.'),
	'canconfig'=>false,
	'config'=>array(
		'cache'=>0
	)
);

/**
 * Prints a user news/journal
 *
 */
function print_user_news($block=true, $config="", $side, $index) {
	global $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $ctype;

	$usernews = getUserNews(WT_USER_ID);

	$id="user_news";
	$title = i18n::translate('My Journal');
	$title .= help_link('mygedview_myjournal');
	$content = "";
	if (count($usernews)==0) {
		$content .= i18n::translate('You have not created any Journal items.').' ';
	}
	foreach($usernews as $key=>$news) {
		$day = date("j", $news["date"]);
		$mon = date("M", $news["date"]);
		$year = date("Y", $news["date"]);
		$content .= "<div class=\"person_box\">";
		$content .= "<span class=\"news_title\">".embed_globals($news["title"])."</span><br />";
		$content .= "<span class=\"news_date\">".format_timestamp($news["date"])."</span><br /><br />";
		$trans = get_html_translation_table(HTML_SPECIALCHARS);
		$trans = array_flip($trans);
		$news["text"] = strtr($news["text"], embed_globals($news["text"]));
		$news["text"] = nl2br($news["text"]);
		$content .= PrintReady($news["text"])."<br /><br />";
		$content .= "<a href=\"javascript:;\" onclick=\"editnews('$key'); return false;\">".i18n::translate('Edit')."</a> | ";
		$content .= "<a href=\"".encode_url("index.php?action=deletenews&news_id={$key}&ctype={$ctype}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this Journal entry?')."');\">".i18n::translate('Delete')."</a><br />";
		$content .= "</div><br />";
	}
	if (WT_USER_ID) {
		$content .= "<br /><a href=\"javascript:;\" onclick=\"addnews('".WT_USER_ID."'); return false;\">".i18n::translate('Add a new Journal entry')."</a>";
	}

	global $THEME_DIR;
	if ($block) {
		require $THEME_DIR.'templates/block_small_temp.php';
	} else {
		require $THEME_DIR.'templates/block_main_temp.php';
	}
}
?>
