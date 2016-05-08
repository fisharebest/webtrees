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
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\User;

/**
 * Class LoggedInUsersModule
 */
class LoggedInUsersModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. (A list of users who are online now) */ I18N::translate('Who is online');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Who is online” module */ I18N::translate('A list of users and visitors who are currently online.');
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
		global $WT_TREE;

		$id        = $this->getName() . $block_id;
		$class     = $this->getName() . '_block';
		$title     = $this->getTitle();
		$anonymous = 0;
		$logged_in = array();
		$content   = '';
		foreach (User::allLoggedIn() as $user) {
			if (Auth::isAdmin() || $user->getPreference('visibleonline')) {
				$logged_in[] = $user;
			} else {
				$anonymous++;
			}
		}
		$count_logged_in = count($logged_in);
		$content .= '<div class="logged_in_count">';
		if ($anonymous) {
			$content .= I18N::plural('%s anonymous signed-in user', '%s anonymous signed-in users', $anonymous, I18N::number($anonymous));
			if ($count_logged_in) {
				$content .= '&nbsp;|&nbsp;';
			}
		}
		if ($count_logged_in) {
			$content .= I18N::plural('%s signed-in user', '%s signed-in users', $count_logged_in, I18N::number($count_logged_in));
		}
		$content .= '</div>';
		$content .= '<div class="logged_in_list">';
		if (Auth::check()) {
			foreach ($logged_in as $user) {
				$individual = Individual::getInstance($WT_TREE->getUserPreference($user, 'gedcomid'), $WT_TREE);

				$content .= '<div class="logged_in_name">';
				if ($individual) {
					$content .= '<a href="' . $individual->getHtmlUrl() . '">' . $user->getRealNameHtml() . '</a>';
				} else {
					$content .= $user->getRealNameHtml();
				}
				$content .= ' - ' . Filter::escapeHtml($user->getUserName());
				if (Auth::id() != $user->getUserId() && $user->getPreference('contactmethod') != 'none') {
					$content .= ' <a class="icon-email" href="#" onclick="return message(\'' . Filter::escapeHtml($user->getUserName()) . '\', \'\', \'' . Filter::escapeHtml(Functions::getQueryUrl()) . '\');" title="' . I18N::translate('Send a message') . '"></a>';
				}
				$content .= '</div>';
			}
		}
		$content .= '</div>';

		if ($anonymous === 0 && $count_logged_in === 0) {
			return '';
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
		return true;
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
	}
}
