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

use PDO;

/**
 * Class UserFavoritesModule
 *
 * The "user favorites" module is almost identical to the "family tree favorites" module
 */
class UserFavoritesModule extends FamilyTreeFavoritesModule {
	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Favorites” module */ I18N::translate('Display and manage a user’s favorite pages.');
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
			Database::prepare(
				"SELECT SQL_CACHE favorite_id AS id, user_id, gedcom_id, xref AS gid, favorite_type AS type, title AS title, note AS note, url AS url" .
				" FROM `##favorite` WHERE user_id=? AND gedcom_id=?")
			->execute(array($user_id, WT_GED_ID))
			->fetchAll(PDO::FETCH_ASSOC);
	}

	/** {@inheritdoc} */
	public function modAction($modAction) {
		switch ($modAction) {
		case 'menu-add-favorite':
			// Process the "add to user favorites" menu item on indi/fam/etc. pages
			$record = GedcomRecord::getInstance(Filter::post('xref', WT_REGEX_XREF));
			if (Auth::check() && $record->canShowName()) {
				self::addFavorite(array(
					'user_id'   => Auth::id(),
					'gedcom_id' => $record->getTree()->getTreeId(),
					'gid'       => $record->getXref(),
					'type'      => $record::RECORD_TYPE,
					'url'       => null,
					'note'      => null,
					'title'     => null,
				));
				FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ I18N::translate('“%s” has been added to your favorites.', $record->getFullName()));
			}
			break;
		}
	}
}
