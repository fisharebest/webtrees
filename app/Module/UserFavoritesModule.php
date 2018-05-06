<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

	/**
	 * Can this block be shown on the user’s home page?
	 *
	 * @return bool
	 */
	public function isUserBlock(): bool {
		return true;
	}

	/**
	 * Can this block be shown on the tree’s home page?
	 *
	 * @return bool
	 */
	public function isGedcomBlock(): bool {
		return false;
	}

	/**
	 * Get the favorites for a user (for the current family tree)
	 *
	 * @param Tree $tree
	 * @param User $user
	 *
	 * @return string[][]
	 */
	public static function getFavorites(Tree $tree, User $user) {
		$favorites =
			Database::prepare(
				"SELECT SQL_CACHE favorite_id, user_id, gedcom_id, xref, favorite_type, title, note, url" .
				" FROM `##favorite` WHERE user_id = :user_id AND gedcom_id = :tree_id")
			->execute([
				'tree_id' => $tree->getTreeId(),
				'user_id' => $user->getUserId(),
			])
			->fetchAll();

		foreach ($favorites as $favorite) {
			$favorite->record = GedcomRecord::getInstance($favorite->xref, $tree);
		}

		return $favorites;
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function postAddFavoriteAction(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$xref = $request->get('xref');

		$record = GedcomRecord::getInstance($xref, $tree);

		if (Auth::check() && $record->canShowName()) {
			self::addFavorite([
				'user_id'   => Auth::id(),
				'gedcom_id' => $record->getTree()->getTreeId(),
				'gid'       => $record->getXref(),
				'type'      => $record::RECORD_TYPE,
				'url'       => null,
				'note'      => null,
				'title'     => null,
			]);

			FlashMessages::addMessage(/* I18N: %s is the name of an individual, source or other record */ I18N::translate('“%s” has been added to your favorites.', $record->getFullName()));
		}

		return new Response;
	}
}
