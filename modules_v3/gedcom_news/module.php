<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Create tables, if not already present
try {
	WT_DB::updateSchema(WT_ROOT . WT_MODULES_DIR . 'gedcom_news/db_schema/', 'NB_SCHEMA_VERSION', 3);
} catch (PDOException $ex) {
	// The schema update scripts should never fail.  If they do, there is no clean recovery.
	die($ex);
}

class gedcom_news_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return /* I18N: Name of a module */ WT_I18N::translate('News');
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the “GEDCOM News” module */ WT_I18N::translate('Family news and site announcements.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype;

		switch (WT_Filter::get('action')) {
		case 'deletenews':
			$news_id = WT_Filter::get('news_id');
			if ($news_id) {
				WT_DB::prepare("DELETE FROM `##news` WHERE news_id = ?")->execute(array($news_id));
			}
			break;
		}
		$block = get_block_setting($block_id, 'block', true);

		if (isset($_REQUEST['gedcom_news_archive'])) {
			$limit = 'nolimit';
			$flag  = 0;
		} else {
			$flag = get_block_setting($block_id, 'flag', 0);
			if ($flag == 0) {
				$limit = 'nolimit';
			} else {
				$limit = get_block_setting($block_id, 'limit', 'nolimit');
			}
		}
		if ($cfg) {
			foreach (array('limit', 'flag') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}
		$usernews = WT_DB::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE gedcom_id=? ORDER BY updated DESC"
		)->execute(array(WT_GED_ID))->fetchAll();

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && WT_USER_GEDCOM_ADMIN || $ctype === 'user' && Auth::check()) {
			$title = '<i class="icon-admin" title="' . WT_I18N::translate('Configure') . '" onclick="modalDialog(\'block_edit.php?block_id=' . $block_id . '\', \'' . $this->getTitle() . '\');"></i>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = '';
		if (count($usernews) == 0) {
			$content .= WT_I18N::translate('No news articles have been submitted.') . '<br>';
		}
		$c = 0;
		foreach ($usernews as $news) {
			if ($limit == 'count') {
				if ($c >= $flag) {
					break;
				}
				$c++;
			}
			if ($limit == 'date') {
				if ((int)((WT_TIMESTAMP - $news->updated) / 86400) > $flag) {
					break;
				}
			}
			$content .= '<div class="news_box" id="article' . $news->news_id . '">';
			$content .= '<div class="news_title">' . WT_Filter::escapeHtml($news->subject) . '</div>';
			$content .= '<div class="news_date">' . format_timestamp($news->updated) . '</div>';
			if ($news->body == strip_tags($news->body)) {
				$news->body = nl2br($news->body, false);
			}
			$content .= $news->body;
			// Print Admin options for this News item
			if (WT_USER_GEDCOM_ADMIN) {
				$content .= '<hr>' . '<a href="#" onclick="window.open(\'editnews.php?news_id=\'+' . $news->news_id . ', \'_blank\', news_window_specs); return false;">' . WT_I18N::translate('Edit') . '</a> | ' . '<a href="index.php?action=deletenews&amp;news_id=' . $news->news_id . '&amp;ctype=' . $ctype .'" onclick="return confirm(\'' . WT_I18N::translate('Are you sure you want to delete this news article?') . "');\">" . WT_I18N::translate('Delete') . '</a><br>';
			}
			$content .= '</div>';
		}
		$printedAddLink = false;
		if (WT_USER_GEDCOM_ADMIN) {
			$content .= "<a href=\"#\" onclick=\"window.open('editnews.php?gedcom_id='+WT_GED_ID, '_blank', news_window_specs); return false;\">" . WT_I18N::translate('Add a news article') . "</a>";
			$printedAddLink = true;
		}
		if ($limit == 'date' || $limit == 'count') {
			if ($printedAddLink) {
				$content .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
			}
			$content .= '<a href="index.php?gedcom_news_archive=yes&amp;ctype=' . $ctype .'">' . WT_I18N::translate('View archive') . "</a>";
			$content .= help_link('gedcom_news_archive') . '<br>';
		}

		if ($template) {
			require WT_THEME_DIR . 'templates/block_main_temp.php';
		} else {
			return $content;
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			set_block_setting($block_id, 'limit', WT_Filter::post('limit'));
			set_block_setting($block_id, 'flag', WT_Filter::post('flag'));
			exit;
		}

		require_once WT_ROOT . 'includes/functions/functions_edit.php';

		// Limit Type
		$limit = get_block_setting($block_id, 'limit', 'nolimit');
		echo
			'<tr><td class="descriptionbox wrap width33">',
			WT_I18N::translate('Limit display by:'), help_link('gedcom_news_limit'),
			'</td><td class="optionbox"><select name="limit"><option value="nolimit"',
			($limit == 'nolimit' ? ' selected="selected"' : '') . ">",
			WT_I18N::translate('No limit') . "</option>",
			'<option value="date"' . ($limit == 'date' ? ' selected="selected"' : '') . ">" . WT_I18N::translate('Age of item') . "</option>",
			'<option value="count"' . ($limit == 'count' ? ' selected="selected"' : '') . ">" . WT_I18N::translate('Number of items') . "</option>",
			'</select></td></tr>';

		// Flag to look for
		$flag = get_block_setting($block_id, 'flag', 0);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Limit:'), help_link('gedcom_news_flag');
		echo '</td><td class="optionbox"><input type="text" name="flag" size="4" maxlength="4" value="' . $flag . '"></td></tr>';
	}
}
