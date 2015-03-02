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
 * Class LoggedInUsersModule
 */
class LoggedInUsersModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. (A list of users who are online now) */ I18N::translate('Who is online');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Who is online” module */ I18N::translate('A list of users and visitors who are currently online.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
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
			$content .= I18N::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $anonymous, $anonymous);
			if ($count_logged_in) {
				$content .= '&nbsp;|&nbsp;';
			}
		}
		if ($count_logged_in) {
			$content .= I18N::plural('%d logged-in user', '%d logged-in users', $count_logged_in, $count_logged_in);
		}
		$content .= '</div>';
		$content .= '<div class="logged_in_list">';
		if (Auth::check()) {
			foreach ($logged_in as $user) {
				$content .= '<div class="logged_in_name">';
				$content .= Filter::escapeHtml($user->getRealName()) . ' - ' . Filter::escapeHtml($user->getUserName());
				if (Auth::id() != $user->getUserId() && $user->getPreference('contactmethod') != 'none') {
					$content .= ' <a class="icon-email" href="#" onclick="return message(\'' . Filter::escapeHtml($user->getUserName()) . '\', \'\', \'' . Filter::escapeHtml(get_query_url()) . '\');" title="' . I18N::translate('Send a message') . '"></a>';
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

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
	}
}
