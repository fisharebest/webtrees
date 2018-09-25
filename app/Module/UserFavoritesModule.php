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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserFavoritesModule
 */
class UserFavoritesModule extends AbstractModule implements ModuleBlockInterface
{
    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Favorites');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Favorites” module */
        return I18N::translate('Display and manage a user’s favorite pages.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        $content = view('modules/user_favorites/favorites', [
            'block_id'  => $block_id,
            'favorites' => $this->getFavorites($tree, Auth::user()),
            'tree'      => $tree,
        ]);

        if ($template) {
            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $this->getTitle(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more comples ones
     * can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return false;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isGedcomBlock(): bool
    {
        return false;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
    }

    /**
     * Get the favorites for a user
     *
     * @param Tree $tree
     * @param User $user
     *
     * @return stdClass[]
     */
    public function getFavorites(Tree $tree, User $user): array
    {
        $favorites = Database::prepare(
            "SELECT favorite_id, user_id, gedcom_id, xref, favorite_type, title, note, url" .
            " FROM `##favorite` WHERE gedcom_id = :tree_id AND user_id = :user_id"
        )->execute([
            'tree_id' => $tree->getTreeId(),
            'user_id' => $user->getUserId(),
        ])->fetchAll();

        foreach ($favorites as $favorite) {
            $favorite->record = GedcomRecord::getInstance($favorite->xref, $tree);
        }

        return $favorites;
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function postAddFavoriteAction(Request $request, Tree $tree, User $user): RedirectResponse
    {
        $note  = $request->get('note', '');
        $title = $request->get('title', '');
        $url   = $request->get('url', '');
        $xref  = $request->get('xref', '');

        if (Auth::check()) {
            if ($url !== '') {
                $this->addUrlFavorite($tree, $user, $url, $title ?: $url, $note);
            } else {
                $this->addRecordFavorite($tree, $user, $xref, $note);
            }
        }

        $url = route('user-page', ['ged' => $tree->getName()]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     * @param User    $user
     *
     * @return RedirectResponse
     */
    public function postDeleteFavoriteAction(Request $request, Tree $tree, User $user): RedirectResponse
    {
        $favorite_id = (int) $request->get('favorite_id');

        if (Auth::check()) {
            Database::prepare(
                "DELETE FROM `##favorite` WHERE favorite_id = :favorite_id AND user_id = :user_id"
            )->execute([
                'favorite_id' => $favorite_id,
                'user_id'     => $user->getUserId(),
            ]);
        }

        $url = route('user-page', ['ged' => $tree->getName()]);

        return new RedirectResponse($url);
    }

    /**
     * @param Tree   $tree
     * @param User   $user
     * @param string $url
     * @param string $title
     * @param string $note
     */
    private function addUrlFavorite(Tree $tree, User $user, string $url, string $title, string $note)
    {
        $favorite = Database::prepare(
            "SELECT * FROM `##favorite` WHERE gedcom_id = :gedcom_id AND user_id = :user_id AND url = :url"
        )->execute([
            'gedcom_id' => $tree->getTreeId(),
            'user_id'   => $user->getUserId(),
            'url'       => $url,
        ])->fetchOneRow();

        if ($favorite === null) {
            Database::prepare(
                "INSERT INTO `##favorite` (gedcom_id, user_id, url, note, title) VALUES (:gedcom_id, :user_id, :url, :note, :title)"
            )->execute([
                'gedcom_id' => $tree->getTreeId(),
                'user_id'   => $user->getUserId(),
                'url'       => $url,
                'note'      => $note,
                'title'     => $title,
            ]);
        } else {
            Database::prepare(
                "UPDATE `##favorite` SET note = :note, title = :title WHERE favorite_id = :favorite_id"
            )->execute([
                'note'        => $note,
                'title'       => $title,
                'favorite_id' => $favorite->favorite_id,
            ]);
        }
    }

    /**
     * @param Tree   $tree
     * @param User   $user
     * @param string $xref
     * @param string $note
     */
    private function addRecordFavorite(Tree $tree, User $user, string $xref, string $note)
    {
        $favorite = Database::prepare(
            "SELECT * FROM `##favorite` WHERE gedcom_id = :gedcom_id AND user_id = :user_id AND xref = :xref"
        )->execute([
            'gedcom_id' => $tree->getTreeId(),
            'user_id'   => $user->getUserId(),
            'xref'      => $xref,
        ])->fetchOneRow();

        if ($favorite === null) {
            Database::prepare(
                "INSERT INTO `##favorite` (gedcom_id, user_id, xref, note) VALUES (:gedcom_id, :user_id, :xref, :note)"
            )->execute([
                'gedcom_id' => $tree->getTreeId(),
                'user_id'   => $user->getUserId(),
                'xref'      => $xref,
                'note'      => $note,
            ]);
        } else {
            Database::prepare(
                "UPDATE `##favorite` SET note = :note WHERE favorite_id = :favorite_id"
            )->execute([
                'note'        => $note,
                'favorite_id' => $favorite->favorite_id,
            ]);
        }
    }
}
