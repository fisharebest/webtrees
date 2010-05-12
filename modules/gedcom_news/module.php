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

class gedcom_news_WT_Module extends WT_Module implements WT_Module_Block {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('GEDCOM News');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('The GEDCOM News block shows the visitor news releases or articles posted by an admin user.<br /><br />The News block is a good place to announce a significant database update, a family reunion, or the birth of a child.');
	}

	// Implement class WT_Module_Block
	public function getBlock($block_id) {
		global $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION, $ctype, $THEME_DIR;

		switch (safe_GET('action')) {
		case 'deletenews':
			$news_id=safe_GET('news_id');
			if ($news_id) {
				deleteNews($news_id);
			}
			break;
		}

		if (isset($_REQUEST['gedcom_news_archive'])) {
			$limit='nolimit';
			$flag=0;
		} else {
			$flag=get_block_setting($block_id, 'flag');
			if ($flag==0) {
				$limit='nolimit';
			} else {
				$limit=get_block_setting($block_id, 'limit');
			}
		}

		$usernews = getUserNews(WT_GEDCOM);

		$id=$this->getName().$block_id;
		$title='';
		if ($ctype=="gedcom" && WT_USER_GEDCOM_ADMIN || $ctype=="user" && WT_USER_ID) {
			if ($ctype=="gedcom") {
				$name = WT_GEDCOM;
			} else {
				$name = WT_USER_NAME;
			}
			$title .= "<a href=\"javascript: configure block\" onclick=\"window.open('index_edit.php?action=configure&block_id={$block_id}', '_blank', 'top=50,left=50,width=600,height=350,scrollbars=1,resizable=1'); return false;\">"
			."<img class=\"adminicon\" src=\"{$WT_IMAGE_DIR}/{$WT_IMAGES['admin']['small']}\" width=\"15\" height=\"15\" border=\"0\" alt=\"".i18n::translate('Configure')."\" /></a>\n"
			;
		}
		$title .= i18n::translate('News');
		if(WT_USER_GEDCOM_ADMIN) {
			$title .= help_link('index_gedcom_news_adm');
		} else {
			$title .= help_link('index_gedcom_news');
		}
		$content = "";
		if(count($usernews) == 0)
		{
			$content .= i18n::translate('No News articles have been submitted.').'<br />';
		}
		$c = 0;
		$td = time();
		foreach($usernews as $news)
		{
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
			//		print "<div class=\"person_box\" id=\"{$news['anchor']}\">\n";
			$content .= "<div class=\"news_box\" id=\"{$news['anchor']}\">\n";

			// Look for $GLOBALS substitutions in the News title
			$newsTitle = embed_globals($news['title']);
			$content .= "<span class=\"news_title\">".PrintReady($newsTitle)."</span><br />\n";
			$content .= "<span class=\"news_date\">".format_timestamp($news['date'])."</span><br /><br />\n";

			// Look for $GLOBALS substitutions in the News text
			$newsText = embed_globals($news['text']);
			$trans = get_html_translation_table(HTML_SPECIALCHARS);
			$trans = array_flip($trans);
			$newsText = strtr($newsText, $trans);
			$newsText = nl2br($newsText);
			$content .= PrintReady($newsText)."<br />\n";

			// Print Admin options for this News item
			if(WT_USER_GEDCOM_ADMIN) {
				$content .= "<hr size=\"1\" />"
				."<a href=\"javascript:;\" onclick=\"editnews('".$news['id']."'); return false;\">".i18n::translate('Edit')."</a> | "
				."<a href=\"".encode_url("index.php?action=deletenews&news_id=".$news['id']."&ctype={$ctype}")."\" onclick=\"return confirm('".i18n::translate('Are you sure you want to delete this News entry?')."');\">".i18n::translate('Delete')."</a><br />";
			}
			$content .= "</div>\n";
		}
		$printedAddLink = false;
		if (WT_USER_GEDCOM_ADMIN) {
			$content .= "<a href=\"javascript:;\" onclick=\"addnews('".urlencode(WT_GEDCOM)."'); return false;\">".i18n::translate('Add a News article')."</a>";
			$printedAddLink = true;
		}
		if ($limit=='date' || $limit=='count') {
			if ($printedAddLink) $content .= "&nbsp;&nbsp;|&nbsp;&nbsp;";
			$content .= "<a href=\"".encode_url("index.php?gedcom_news_archive=yes&ctype={$ctype}")."\">".i18n::translate('View archive')."</a>";
			$content .= help_link('gedcom_news_archive').'<br />';
		}

		require $THEME_DIR.'templates/block_main_temp.php';
	}

	// Implement class WT_Module_Block
	public function canLoadAjax() {
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
		global $ctype, $WT_BLOCKS;
		if (empty ($config)) $config = $WT_BLOCKS["print_gedcom_news"]["config"];
		if (!isset ($config["limit"])) $config["limit"] = "nolimit";
		if (!isset ($config["flag"])) $config["flag"] = 0;
		if (!isset($config["cache"])) $config["cache"] = $WT_BLOCKS["print_gedcom_news"]["config"]["cache"];

		// Limit Type
		echo
			'<tr><td class="descriptionbox wrap width33">',
			i18n::translate('Limit display by:'), help_link('gedcom_news_limit'),
			'</td><td class="optionbox"><select name="limit"><option value="nolimit"',
			($config['limit'] == 'nolimit'?' selected="selected"':'').">",
			i18n::translate('No limit')."</option>",
			'<option value="date"'.($config['limit'] == 'date'?' selected="selected"':'').">".i18n::translate('Age of item')."</option>",
			'<option value="count"'.($config['limit'] == 'count'?' selected="selected"':'').">".i18n::translate('Number of items')."</option>",
			'</select></td></tr>';

		// Flag to look for
		echo '<tr><td class="descriptionbox wrap width33">';
		echo i18n::translate('Limit:'), help_link('gedcom_news_flag');
		echo '</td><td class="optionbox"><input type="text" name="flag" size="4" maxlength="4" value="'.$config['flag'].'" /></td></tr>';

		// Cache file life
		if ($ctype=="gedcom") {
			echo '<tr><td class="descriptionbox wrap width33">';
			echo i18n::translate('Cache file life'), help_link('cache_life');
			echo '</td><td class="optionbox">';
			echo '<input type="text" name="cache" size="2" value="', $config['cache'], '" />';
			echo "</td></tr>";
		}
	}
}
