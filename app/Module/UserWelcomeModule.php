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
 * Class UserWelcomeModule
 */
class UserWelcomeModule extends Module implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('My page');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “My page” module */ I18N::translate('A greeting message and useful links for a user.');
	}

	/** {@inheritdoc} */
	public function getBlock($block_id, $template = true, $cfg = null) {
		$id = $this->getName() . $block_id;
		$class = $this->getName() . '_block';
		$title = '<span dir="auto">' . /* I18N: A greeting; %s is the user’s name */ I18N::translate('Welcome %s', Auth::user()->getRealNameHtml()) . '</span>';
		$content = '<table><tr>';
		$content .= '<td><a href="edituser.php"><i class="icon-mypage"></i><br>' . I18N::translate('My account') . '</a></td>';

		if (WT_USER_GEDCOM_ID) {
			$content .= '<td><a href="pedigree.php?rootid=' . WT_USER_GEDCOM_ID . '&amp;ged=' . WT_GEDURL . '"><i class="icon-pedigree"></i><br>' . I18N::translate('My pedigree') . '</a></td>';
			$content .= '<td><a href="individual.php?pid=' . WT_USER_GEDCOM_ID . '&amp;ged=' . WT_GEDURL . '"><i class="icon-indis"></i><br>' . I18N::translate('My individual record') . '</a></td>';
		}
		$content .= '</tr></table>';

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
		return false;
	}

	/** {@inheritdoc} */
	public function configureBlock($block_id) {
	}
}
