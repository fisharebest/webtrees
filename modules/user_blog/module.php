<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
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
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';

class user_blog_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('User Journal');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The User Journal block lets the user keep notes or a journal online.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $ctype, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $THEME_DIR;

		switch (safe_GET('action')) {
		case 'deletenews':
			$news_id=safe_GET('news_id');
			if ($news_id) {
				deleteNews($news_id);
			}
			break;
		}

		$usernews = getUserNews(WT_USER_ID);

		$id=$this->getName().$block_id;
		$title=i18n::translate('My Journal').help_link('mygedview_myjournal');
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
			$news["text"] = nl2br($news["text"], embed_globals($news["text"]));
			$content .= PrintReady($news["text"])."<br /><br />";
			$content .= "<a href=\"javascript:;\" onclick=\"editnews('$key'); return false;\">".i18n::translate('Edit')."</a> | ";
			$content .= "<a href=\"".encode_url("index.php?action=deletenews&news_id={$key}&ctype={$ctype}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this Journal entry?')."');\">".i18n::translate('Delete')."</a><br />";
			$content .= "</div><br />";
		}
		if (WT_USER_ID) {
			$content .= "<br /><a href=\"javascript:;\" onclick=\"addnews('".WT_USER_ID."'); return false;\">".i18n::translate('Add a new Journal entry')."</a>";
		}

		$block=get_block_setting($block_id, 'block', true);
		if ($block) {
			require $THEME_DIR.'templates/block_small_temp.php';
		} else {
			require $THEME_DIR.'templates/block_main_temp.php';
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}
