<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function array_merge;
use function array_unique;
use function assert;
use function e;
use function explode;
use function uasort;

/**
 * Edit the tree privacy.
 */
class TreePrivacyPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private TreeService $tree_service;

    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $title                = e($tree->name()) . ' — ' . I18N::translate('Privacy');
        $all_tags             = $this->tagsForPrivacy($tree);
        $privacy_constants    = $this->privacyConstants();
        $privacy_restrictions = $this->privacyRestrictions($tree);

        return $this->viewResponse('admin/trees-privacy', [
            'all_tags'             => $all_tags,
            'count_trees'          => $this->tree_service->all()->count(),
            'privacy_constants'    => $privacy_constants,
            'privacy_restrictions' => $privacy_restrictions,
            'title'                => $title,
            'tree'                 => $tree,
        ]);
    }

    /**
     * Names of our privacy levels
     *
     * @return array<string,string>
     */
    private function privacyConstants(): array
    {
        return [
            'none'         => I18N::translate('Show to visitors'),
            'privacy'      => I18N::translate('Show to members'),
            'confidential' => I18N::translate('Show to managers'),
            'hidden'       => I18N::translate('Hide from everyone'),
        ];
    }

    /**
     * The current privacy restrictions for a tree.
     *
     * @param Tree $tree
     *
     * @return array<string,string>
     */
    private function privacyRestrictions(Tree $tree): array
    {
        return DB::table('default_resn')
            ->where('gedcom_id', '=', $tree->id())
            ->get()
            ->map(static function (stdClass $row) use ($tree): stdClass {
                $row->record = null;
                $row->label  = '';

                if ($row->xref !== null) {
                    $row->record = Registry::gedcomRecordFactory()->make($row->xref, $tree);
                }

                if ($row->tag_type) {
                    $row->tag_label = GedcomTag::getLabel($row->tag_type);
                } else {
                    $row->tag_label = '';
                }

                return $row;
            })
            ->sort(static function (stdClass $x, stdClass $y): int {
                return I18N::comparator()($x->tag_label, $y->tag_label);
            })
            ->all();
    }

    /**
     * Generate a list of potential problems with the server.
     *
     * @param Tree $tree
     *
     * @return array<string>
     */
    private function tagsForPrivacy(Tree $tree): array
    {
        $tags = array_unique(array_merge(
            explode(',', $tree->getPreference('INDI_FACTS_ADD')),
            explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('FAM_FACTS_ADD')),
            explode(',', $tree->getPreference('FAM_FACTS_UNIQUE')),
            [
                'SOUR',
                'REPO',
                'OBJE',
                '_PRIM',
                'NOTE',
                'SUBM',
                'SUBN',
                '_UID',
                'CHAN',
            ]
        ));

        $all_tags = [];

        foreach ($tags as $tag) {
            if ($tag) {
                $all_tags[$tag] = GedcomTag::getLabel($tag);
            }
        }

        uasort($all_tags, I18N::comparator());

        return array_merge(
            ['' => I18N::translate('All facts and events')],
            $all_tags
        );
    }
}
