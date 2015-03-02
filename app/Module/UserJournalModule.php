<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

/**
 * Class UserJournalModule
 */
class UserJournalModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('Journal');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Journal” module */ I18N::translate('A private area to record notes or keep a journal.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		global $ctype;

		switch (Filter::get('action')) {
		case 'deletenews':
			$news_id = Filter::getInteger('news_id');
			if ($news_id) {
				Database::prepare("DELETE FROM `##news` WHERE news_id = ?")->execute(array($news_id));
			}
			break;
		}
		$block = get_block_setting($block_id, 'block', '1');
		if ($cfg) {
			foreach (array('block') as $name) {
				if (array_key_exists($name, $cfg)) {
					$$name = $cfg[$name];
				}
			}
		}
		$usernews = Database::prepare(
			"SELECT SQL_CACHE news_id, user_id, gedcom_id, UNIX_TIMESTAMP(updated) AS updated, subject, body FROM `##news` WHERE user_id = ? ORDER BY updated DESC"
		)->execute(array(Auth::id()))->fetchAll();

		$id    = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		$title = '';
		$title .= $this->getTitle();
		$content = '';
		if (!$usernews) {
			$content .= I18N::translate('You have not created any journal items.');
		}
		foreach ($usernews as $news) {
			$content .= '<div class="journal_box">';
			$content .= '<div class="news_title">' . $news->subject . '</div>';
			$content .= '<div class="news_date">' . format_timestamp($news->updated) . '</div>';
			if ($news->body == strip_tags($news->body)) {
				// No HTML?
				$news->body = nl2br($news->body, false);
			}
			$content .= $news->body . '<br><br>';
			$content .= '<a href="#" onclick="window.open(\'editnews.php?news_id=\'+' . $news->news_id . ', \'_blank\', indx_window_specs); return false;">' . I18N::translate('Edit') . '</a> | ';
			$content .= '<a href="index.php?action=deletenews&amp;news_id=' . $news->news_id . '&amp;ctype=' . $ctype . '" onclick="return confirm(\'' . I18N::translate('Are you sure you want to delete this journal entry?') . "');\">" . I18N::translate('Delete') . '</a><br>';
			$content .= "</div><br>";
		}
		$content .= '<br><a href="#" onclick="window.open(\'editnews.php?user_id=' . Auth::id() . '\', \'_blank\', indx_window_specs); return false;">' . I18N::translate('Add a new journal entry') . '</a>';

		if ($template) {
			if ($block) {
				$class .= ' small_inner_block';
			}
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
		return true;
	}

	/** {@inheritdoc} */
	public function isGedcomBlock() {
		return false;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
	}
}
