<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FamilyTreeFavoritesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Favorites');
    }

    public function description(): string
    {
        /* I18N: Description of the “Favorites” module */
        return I18N::translate('Display and manage a family tree’s favorite pages.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $content = view('modules/favorites/favorites', [
            'block_id'    => $block_id,
            'can_edit'    => Auth::isManager($tree),
            'favorites'   => $this->getFavorites($tree),
            'module_name' => $this->name(),
            'tree'        => $tree,
        ]);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $this->title(),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     * Simple blocks are faster in-line, more complex ones can be loaded later.
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
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Get the favorites for a family tree
     *
     * @param Tree $tree
     *
     * @return array<int,object{
     *      favorite_id:string,
     *      favorite_type:string,
     *      url:string|null,
     *      note:string|null,
     *      title:string|null,
     *      record:GedcomRecord|null
     *  }>
     */
    public function getFavorites(Tree $tree): array
    {
        return DB::table('favorite')
            ->where('gedcom_id', '=', $tree->id())
            ->whereNull('user_id')
            ->select(['favorite_id', 'xref', 'favorite_type', 'url', 'title', 'note'])
            ->get()
            ->map(static function (object $row) use ($tree): object {
                if ($row->xref !== null) {
                    $row->record = Registry::gedcomRecordFactory()->make($row->xref, $tree);

                    if ($row->record instanceof GedcomRecord && !$row->record->canShowName()) {
                        $row->record = null;
                        $row->note   = null;
                    }
                } else {
                    $row->record = null;
                }

                return $row;
            })
            ->all();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAddFavoriteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $user   = Validator::attributes($request)->user();
        $note   = Validator::parsedBody($request)->string('note');
        $title  = Validator::parsedBody($request)->string('title');
        $url    = Validator::parsedBody($request)->string('url');
        $type   = Validator::parsedBody($request)->string('type');
        $xref   = Validator::parsedBody($request)->string($type . '-xref', '');
        $record = $this->getRecordForType($type, $xref, $tree);

        if (Auth::isManager($tree, $user)) {
            if ($type === 'url' && $url !== '') {
                $this->addUrlFavorite($tree, $url, $title ?: $url, $note);
            }

            if ($record instanceof GedcomRecord && $record->canShow()) {
                $this->addRecordFavorite($tree, $record, $note);
            }
        }

        $url = route(TreePage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postDeleteFavoriteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $user        = Validator::attributes($request)->user();
        $favorite_id = Validator::queryParams($request)->integer('favorite_id');

        if (Auth::isManager($tree, $user)) {
            DB::table('favorite')
                ->where('favorite_id', '=', $favorite_id)
                ->whereNull('user_id')
                ->delete();
        }

        $url = route(TreePage::class, ['tree' => $tree->name()]);

        return redirect($url);
    }

    /**
     * @param Tree   $tree
     * @param string $url
     * @param string $title
     * @param string $note
     *
     * @return void
     */
    private function addUrlFavorite(Tree $tree, string $url, string $title, string $note): void
    {
        DB::table('favorite')->updateOrInsert([
            'gedcom_id' => $tree->id(),
            'user_id'   => null,
            'url'       => $url,
        ], [
            'favorite_type' => 'URL',
            'note'          => $note,
            'title'         => $title,
        ]);
    }

    /**
     * @param Tree         $tree
     * @param GedcomRecord $record
     * @param string       $note
     *
     * @return void
     */
    private function addRecordFavorite(Tree $tree, GedcomRecord $record, string $note): void
    {
        DB::table('favorite')->updateOrInsert([
            'gedcom_id' => $tree->id(),
            'user_id'   => null,
            'xref'      => $record->xref(),
        ], [
            'favorite_type' => $record->tag(),
            'note'          => $note,
        ]);
    }

    private function getRecordForType(string $type, string $xref, Tree $tree): GedcomRecord|null
    {
        switch ($type) {
            case 'indi':
                return Registry::individualFactory()->make($xref, $tree);

            case 'fam':
                return Registry::familyFactory()->make($xref, $tree);

            case 'sour':
                return Registry::sourceFactory()->make($xref, $tree);

            case 'repo':
                return Registry::repositoryFactory()->make($xref, $tree);

            case 'obje':
                return Registry::mediaFactory()->make($xref, $tree);

            default:
                return null;
        }
    }
}
