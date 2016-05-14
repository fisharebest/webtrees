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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Theme;

/**
 * Class UserWelcomeModule
 */
class UserWelcomeModule extends AbstractModule implements ModuleBlockInterface {
	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */ I18N::translate('My page');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “My page” module */ I18N::translate('A greeting message and useful links for a user.');
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

		$id      = $this->getName() . $block_id;
		$class   = $this->getName() . '_block';
		$title   = '<span dir="auto">' . /* I18N: A greeting; %s is the user’s name */ I18N::translate('Welcome %s', Auth::user()->getRealNameHtml()) . '</span>';
		$content = '<table><tr>';
		$content .= '<td><a href="edituser.php"><i class="icon-mypage"></i><br>' . I18N::translate('My account') . '</a></td>';

		$gedcomid = $WT_TREE->getUserPreference(Auth::user(), 'gedcomid');

		if ($gedcomid) {
			if (Module::isActiveChart($WT_TREE, 'pedigree_chart')) {
				$content .= '<td><a href="pedigree.php?rootid=' . $gedcomid . '&amp;ged=' . $WT_TREE->getNameUrl() . '"><i class="icon-pedigree"></i><br>' . I18N::translate('My pedigree') . '</a></td>';
			}
			$content .= '<td><a href="individual.php?pid=' . $gedcomid . '&amp;ged=' . $WT_TREE->getNameUrl() . '"><i class="icon-indis"></i><br>' . I18N::translate('My individual record') . '</a></td>';
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

	/**
	 * An HTML form to edit block settings
	 *
	 * @param int $block_id
	 */
	public function configureBlock($block_id) {
	}
}
