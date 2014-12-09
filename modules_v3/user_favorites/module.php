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

// The "user favorites" module is almost identical to the "gedcom favorites" module
require_once WT_ROOT . WT_MODULES_DIR . 'gedcom_favorites/module.php';

/**
 * Class user_favorites_WT_Module
 */
class user_favorites_WT_Module extends gedcom_favorites_WT_Module {
	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Favorites” module */ WT_I18N::translate('Display and manage a user’s favorite pages.');
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
	 * Get the favorites for a user (for the current family tree)
	 *
	 * @param integer $user_id
	 *
	 * @return string[][]
	 */
	public static function getFavorites($user_id) {
		self::updateSchema(); // make sure the favorites table has been created

		return
			WT_DB::prepare(
				"SELECT SQL_CACHE favorite_id AS id, user_id, gedcom_id, xref AS gid, favorite_type AS type, title AS title, note AS note, url AS url".
				" FROM `##favorite` WHERE user_id=? AND gedcom_id=?")
			->execute(array($user_id, WT_GED_ID))
			->fetchAll(PDO::FETCH_ASSOC);
	}

	/** {@inheritdoc} */
	public function modAction($modAction) {
		switch($modAction) {
		case 'menu-add-favorite':
			// Process the "add to user favorites" menu item on indi/fam/etc. pages
			$record = WT_GedcomRecord::getInstance(WT_Filter::post('xref', WT_REGEX_XREF));
			if (Auth::check() && $record->canShowName()) {
				self::addFavorite(array(
					'user_id'   => Auth::id(),
					'gedcom_id' => $record->getGedcomId(),
					'gid'       => $record->getXref(),
					'type'      => $record::RECORD_TYPE,
					'url'       => null,
					'note'      => null,
					'title'     => null,
				));
				WT_FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ WT_I18N::translate('“%s” has been added to your favorites.', $record->getFullName()));
			}
			break;
		}
	}
}
