<?php
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
use WT\User;

/**
 * Class logged_in_WT_Module
 */
class logged_in_WT_Module extends WT_Module implements WT_Module_Block {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module. (A list of users who are online now) */ WT_I18N::translate('Who is online');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Who is online” module */ WT_I18N::translate('A list of users and visitors who are currently online.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template=true, $cfg=null) {
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
			$content .= WT_I18N::plural('%d anonymous logged-in user', '%d anonymous logged-in users', $anonymous, $anonymous);
			if ($count_logged_in) {
				$content .=  '&nbsp;|&nbsp;';
			}
		}
		if ($count_logged_in) {
			$content .= WT_I18N::plural('%d logged-in user', '%d logged-in users', $count_logged_in, $count_logged_in);
		}
		$content .= '</div>';
		$content .= '<div class="logged_in_list">';
		if (Auth::check()) {
			foreach ($logged_in as $user) {
				$content .= '<div class="logged_in_name">';
				$content .= WT_Filter::escapeHtml($user->getRealName()) . ' - ' . WT_Filter::escapeHtml($user->getUserName());
				if (Auth::id() != $user->getUserId() && $user->getPreference('contactmethod') != 'none') {
					$content .= ' <a class="icon-email" href="#" onclick="return message(\'' . WT_Filter::escapeJs($user->getUserName()) . '\', \'\', \'' . WT_Filter::escapeJs(get_query_url()) . '\');" title="' . WT_I18N::translate('Send a message').'"></a>';
				}
				$content .= '</div>';
			}
		}
		$content .= '</div>';

		if ($anonymous === 0 && $count_logged_in === 0) {
			return '';
		}

		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
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
