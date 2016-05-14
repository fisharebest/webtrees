<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;

/**
 * Class FamilyTreeNewsModule
 */
class FamilyTreeNewsModule extends AbstractModule implements ModuleBlockInterface {
	// How to update the database schema for this module
	const SCHEMA_TARGET_VERSION   = 3;
	const SCHEMA_SETTING_NAME     = 'NB_SCHEMA_VERSION';
	const SCHEMA_MIGRATION_PREFIX = '\Fisharebest\Webtrees\Module\FamilyTreeNews\Schema';

	/**
	 * Create a new module.
	 *
	 * @param string $directory Where is this module installed
	 */
	public function __construct($directory) {
		parent::__construct($directory);

		// Create/update the database tables.
		// NOTE: if we want to set any module-settings, we'll need to move this.
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);
	}

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('News');
	}

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: Description of the “GEDCOM News” module */ I18N::translate('Family news and site announcements.');
	}

	/**
	 * Generate the HTML content of this block.
	 *
	 * @param int      $block_id
	 * @param bool     $template
	 * @param string[] $cfg
	 *
	 * @return string
	 */
	public function getBlock($block_id, $template = true, $cfg = array()) {
		global $ctype, $WT_TREE;

		switch (Filter::get('action')) {
		case 'deletenews':
			$news_id = Filter::get('news_id');
			if ($news_id) {
				Database::prepare("DELETE FROM `##news` WHERE news_id = ?")->execute(array($news_id));
			}
			break;
		}

		if (isset($_REQUEST['gedcom_news_archive'])) {
			$limit = 'nolimit';
			$flag  = '0';
		} else {
			$flag = $this->getBlockSetting($block_id, 'flag', 0);
			if ($flag === '0') {
				$limit = 'nolimit';
			} else {
				$limit = $this->getBlockSetting($block_id, 'limit', 'nolimit');
			}
		}
		foreach (array('limit', 'flag') as $name) {
			if (array_key_exists($name, $cfg)) {
				$$name = $cfg[$name];
			}
		}
		$usernews = Database::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE gedcom_id=? ORDER BY updated DESC"
		)->execute(array($WT_TREE->getTreeId()))->fetchAll();

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		if ($ctype === 'gedcom' && Auth::isManager($WT_TREE) || $ctype === 'user' && Auth::check()) {
			$title = '<a class="icon-admin" title="' . I18N::translate('Configure') . '" href="block_edit.php?block_id=' . $block_id . '&amp;ged=' . $WT_TREE->getNameHtml() . '&amp;ctype=' . $ctype . '"></a>';
		} else {
			$title = '';
		}
		$title .= $this->getTitle();

		$content = '';
		if (count($usernews) == 0) {
			$content .= I18N::translate('No news articles have been submitted.') . '<br>';
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
				if ((int) ((WT_TIMESTAMP - $news->updated) / 86400) > $flag) {
					break;
				}
			}
			$content .= '<div class="news_box" id="article' . $news->news_id . '">';
			$content .= '<div class="news_title">' . Filter::escapeHtml($news->subject) . '</div>';
			$content .= '<div class="news_date">' . FunctionsDate::formatTimestamp($news->updated) . '</div>';
			if ($news->body == strip_tags($news->body)) {
				$news->body = nl2br($news->body, false);
			}
			$content .= $news->body;
			// Print Admin options for this News item
			if (Auth::isManager($WT_TREE)) {
				$content .= '<hr>' . '<a href="#" onclick="window.open(\'editnews.php?news_id=\'+' . $news->news_id . ', \'_blank\', news_window_specs); return false;">' . I18N::translate('Edit') . '</a> | ' . '<a href="index.php?action=deletenews&amp;news_id=' . $news->news_id . '&amp;ctype=' . $ctype . '&amp;ged=' . $WT_TREE->getNameHtml() . '" onclick="return confirm(\'' . I18N::translate('Are you sure you want to delete “%s”?', Filter::escapeHtml($news->subject)) . "');\">" . I18N::translate('Delete') . '</a><br>';
			}
			$content .= '</div>';
		}
		$printedAddLink = false;
		if (Auth::isManager($WT_TREE)) {
			$content .= "<a href=\"#\" onclick=\"window.open('editnews.php?gedcom_id=" . $WT_TREE->getTreeId() . "', '_blank', news_window_specs); return false;\">" . I18N::translate('Add a news article') . "</a>";
			$printedAddLink = true;
		}
		if ($limit == 'date' || $limit == 'count') {
			if ($printedAddLink) {
				$content .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
			}
			$content .= '<a href="index.php?gedcom_news_archive=yes&amp;ctype=' . $ctype . '&amp;ged=' . $WT_TREE->getNameHtml() . '">' . I18N::translate('View the archive') . "</a>";
			$content .= FunctionsPrint::helpLink('gedcom_news_archive') . '<br>';
		}

		if ($template) {
			return Theme::theme()->formatBlock($id, $title, $class, $content);
		} else {
			return $content;
		}
	}

	/** {@inheritdoc} */
	public function loadAjax() {
		return false;
	}

	/** {@inheritdoc} */
	public function isUserBlock() {
		return false;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return true;
	}

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
		if (Filter::postBool('save') && Filter::checkCsrf()) {
			$this->setBlockSetting($block_id, 'limit', Filter::post('limit'));
			$this->setBlockSetting($block_id, 'flag', Filter::post('flag'));
		}

		$limit = $this->getBlockSetting($block_id, 'limit', 'nolimit');
		$flag  = $this->getBlockSetting($block_id, 'flag', 0);

		echo
			'<tr><td class="descriptionbox wrap width33">',
			/* I18N: Limit display by [age/number] */ I18N::translate('Limit display by'),
			'</td><td class="optionbox"><select name="limit"><option value="nolimit" ',
			($limit == 'nolimit' ? 'selected' : '') . ">",
			I18N::translate('No limit') . "</option>",
			'<option value="date" ' . ($limit == 'date' ? 'selected' : '') . ">" . I18N::translate('Age of item') . "</option>",
			'<option value="count" ' . ($limit == 'count' ? 'selected' : '') . ">" . I18N::translate('Number of items') . "</option>",
			'</select></td></tr>';

		echo '<tr><td class="descriptionbox wrap width33">';
		echo I18N::translate('Limit');
		echo '</td><td class="optionbox"><input type="text" name="flag" size="4" maxlength="4" value="' . $flag . '"></td></tr>';
	}
}
