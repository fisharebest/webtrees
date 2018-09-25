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
 * Class FamilyTreeFavoritesModule
 */
class FamilyTreeFavoritesModule extends AbstractModule implements ModuleBlockInterface
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
        return I18N::translate('Display and manage a family tree’s favorite pages.');
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
        $content = view('modules/gedcom_favorites/favorites', [
            'block_id'   => $block_id,
            'favorites'  => $this->getFavorites($tree),
            'is_manager' => Auth::isManager($tree),
            'tree'       => $tree,
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
        return false;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isGedcomBlock(): bool
    {
        return true;
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
     * Get favorites for a family tree
     *
     * @param Tree $tree
     *
     * @return stdClass[]
     */
    public function getFavorites(Tree $tree): array
    {
        $favorites = Database::prepare(
            "SELECT favorite_id, user_id, gedcom_id, xref, favorite_type, title, note, url" .
            " FROM `##favorite` WHERE gedcom_id = :tree_id AND user_id IS NULL"
        )->execute([
            'tree_id' => $tree->getTreeId(),
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

        if (Auth::isManager($tree, $user)) {
            if ($url !== '') {
                $this->addUrlFavorite($tree, $url, $title ?: $url, $note);
            } else {
                $this->addRecordFavorite($tree, $xref, $note);
            }
        }

        $url = route('tree-page', ['ged' => $tree->getName()]);

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

        if (Auth::isManager($tree, $user)) {
            Database::prepare(
                "DELETE FROM `##favorite` WHERE favorite_id = :favorite_id AND gedcom_id = :tree_id"
            )->execute([
                'favorite_id' => $favorite_id,
                'tree_id'     => $tree->getTreeId(),
            ]);
        }

        $url = route('tree-page', ['ged' => $tree->getName()]);

        return new RedirectResponse($url);
    }

    /**
     * @param Tree   $tree
     * @param string $url
     * @param string $title
     * @param string $note
     */
    private function addUrlFavorite(Tree $tree, string $url, string $title, string $note)
    {
        $favorite = Database::prepare(
            "SELECT * FROM `##favorite` WHERE gedcom_id = :gedcom_id AND user_id IS NULL AND url = :url"
        )->execute([
            'gedcom_id' => $tree->getTreeId(),
            'url'       => $url,
        ])->fetchOneRow();

        if ($favorite === null) {
            Database::prepare(
                "INSERT INTO `##favorite` (gedcom_id, url, note, title) VALUES (:gedcom_id, :user_id, :url, :note, :title)"
            )->execute([
                'gedcom_id' => $tree->getTreeId(),
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
     * @param string $xref
     * @param string $note
     */
    private function addRecordFavorite(Tree $tree, string $xref, string $note)
    {
        $favorite = Database::prepare(
            "SELECT * FROM `##favorite` WHERE gedcom_id = :gedcom_id AND user_id IS NULL AND xref = :xref"
        )->execute([
            'gedcom_id' => $tree->getTreeId(),
            'xref'      => $xref,
        ])->fetchOneRow();

        if ($favorite === null) {
            Database::prepare(
                "INSERT INTO `##favorite` (gedcom_id, xref, note) VALUES (:gedcom_id, :xref, :note)"
            )->execute([
                'gedcom_id' => $tree->getTreeId(),
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
