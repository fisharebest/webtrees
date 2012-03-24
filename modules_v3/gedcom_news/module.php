<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Create tables, if not already present
try {
	WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'gedcom_news/db_schema/', 'NB_SCHEMA_VERSION', 2);
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
		return /* I18N: Description of the "GEDCOM News" module */ WT_I18N::translate('Family news and site announcements.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $ctype;

		switch (safe_GET('action')) {
		case 'deletenews':
			$news_id=safe_GET('news_id');
			if ($news_id) {
				deleteNews($news_id);
			}
			break;
		}
		$block=get_block_setting($block_id, 'block', true);

		if (isset($_REQUEST['gedcom_news_archive'])) {
			$limit='nolimit';
			$flag=0;
		} else {
			$flag=get_block_setting($block_id, 'flag', 0);
			if ($flag==0) {
				$limit='nolimit';
			} else {
				$limit=get_block_setting($block_id, 'limit', 'nolimit');
			}
		}
		if ($cfg) {
			foreach (array('limit', 'flag') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name=$cfg[$name];
				}
			}
		}
		$usernews = getGedcomNews(WT_GED_ID);

		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		if ($ctype=='gedcom' && WT_USER_GEDCOM_ADMIN || $ctype=='user' && WT_USER_ID) {
			$title='<i class="icon-admin" title="'.WT_I18N::translate('Configure').'" onclick="modalDialog(\'block_edit.php?block_id='.$block_id.'\', \''.$this->getTitle().'\');"></i>';
		} else {
			$title='';
		}
		$title.=$this->getTitle();

		$content = '';
		if (count($usernews)==0) {
			$content .= WT_I18N::translate('No News articles have been submitted.').'<br>';
		}
		$c = 0;
		$td = time();
		foreach ($usernews as $news) {
			if ($limit=='count') {
				if ($c >= $flag) {
					break;
				}
				$c++;
			}
			if ($limit=='date') {
				if (floor(($td - $news['date']) / 86400) > $flag) {
					break;
				}
			}
			$content .= "<div class=\"news_box\" id=\"article{$news['id']}\">";
			$content .= "<div class=\"news_title\">".htmlspecialchars($news['title']).'</div>';
			$content .= "<div class=\"news_date\">".format_timestamp($news['date']).'</div>';
			if ($news["text"]==strip_tags($news["text"])) {
				// No HTML?
				$news["text"]=nl2br($news["text"]);
			}
			$content .= $news["text"];
			// Print Admin options for this News item
			if (WT_USER_GEDCOM_ADMIN) {
				$content .= '<hr>'
				."<a href=\"#\" onclick=\"window.open('editnews.php?news_id='+".$news['id'].", '_blank', indx_window_specs); return false;\">".WT_I18N::translate('Edit')."</a> | "
				."<a href=\"index.php?action=deletenews&amp;news_id=".$news['id']."&amp;ctype={$ctype}\" onclick=\"return confirm('".WT_I18N::translate('Are you sure you want to delete this News entry?')."');\">".WT_I18N::translate('Delete')."</a><br>";
			}
			$content .= "</div>";
		}
		$printedAddLink = false;
		if (WT_USER_GEDCOM_ADMIN) {
			$content .= "<a href=\"#\" onclick=\"window.open('editnews.php?gedcom_id='+WT_GED_ID, '_blank', indx_window_specs); return false;\">".WT_I18N::translate('Add a News article')."</a>";
			$printedAddLink = true;
		}
		if ($limit=='date' || $limit=='count') {
			if ($printedAddLink) $content .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
			$content .= "<a href=\"index.php?gedcom_news_archive=yes&amp;ctype={$ctype}\">".WT_I18N::translate('View archive')."</a>";
			$content .= help_link('gedcom_news_archive').'<br>';
		}

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
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
		if (safe_POST_bool('save')) {
			set_block_setting($block_id, 'limit', safe_POST('limit'));
			set_block_setting($block_id, 'flag',  safe_POST('flag'));
			echo WT_JS_START, 'window.opener.location.href=window.opener.location.href;window.close();', WT_JS_END;
			exit;
		}

		require_once WT_ROOT.'includes/functions/functions_edit.php';


		// Limit Type
		$limit=get_block_setting($block_id, 'limit', 'nolimit');
		echo
			'<tr><td class="descriptionbox wrap width33">',
			WT_I18N::translate('Limit display by:'), help_link('gedcom_news_limit'),
			'</td><td class="optionbox"><select name="limit"><option value="nolimit"',
			($limit == 'nolimit'?' selected="selected"':'').">",
			WT_I18N::translate('No limit')."</option>",
			'<option value="date"'.($limit == 'date'?' selected="selected"':'').">".WT_I18N::translate('Age of item')."</option>",
			'<option value="count"'.($limit == 'count'?' selected="selected"':'').">".WT_I18N::translate('Number of items')."</option>",
			'</select></td></tr>';

		// Flag to look for
		$flag=get_block_setting($block_id, 'flag', 0);
		echo '<tr><td class="descriptionbox wrap width33">';
		echo WT_I18N::translate('Limit:'), help_link('gedcom_news_flag');
		echo '</td><td class="optionbox"><input type="text" name="flag" size="4" maxlength="4" value="'.$flag.'"></td></tr>';
	}
}
