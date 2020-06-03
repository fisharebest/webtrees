<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function assert;

/**
 * Controller for the administration pages
 */
class AdminController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /** @var TreeService */
    private $tree_service;

    /**
     * TreesMenuModule constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function treePrivacyEdit(ServerRequestInterface $request): ResponseInterface
    {
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function treePrivacyUpdate(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();

        $delete_default_resn_id = $params['delete'] ?? [];

        DB::table('default_resn')
            ->whereIn('default_resn_id', $delete_default_resn_id)
            ->delete();

        $xrefs     = $params['xref'] ?? [];
        $tag_types = $params['tag_type'] ?? [];
        $resns     = $params['resn'] ?? [];

        foreach ($xrefs as $n => $xref) {
            $tag_type = $tag_types[$n];
            $resn     = $resns[$n];

            // Delete any existing data
            if ($tag_type !== '' && $xref !== '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('tag_type', '=', $tag_type)
                    ->where('xref', '=', $xref)
                    ->delete();
            }

            if ($tag_type !== '' && $xref === '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('tag_type', '=', $tag_type)
                    ->whereNull('xref')
                    ->delete();
            }

            if ($tag_type === '' && $xref !== '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->whereNull('tag_type')
                    ->where('xref', '=', $xref)
                    ->delete();
            }

            // Add (or update) the new data
            if ($tag_type !== '' || $xref !== '') {
                DB::table('default_resn')->insert([
                    'gedcom_id' => $tree->id(),
                    'xref'      => $xref === '' ? null : $xref,
                    'tag_type'  => $tag_type === '' ? null : $tag_type,
                    'resn'      => $resn,
                ]);
            }
        }

        $tree->setPreference('HIDE_LIVE_PEOPLE', $params['HIDE_LIVE_PEOPLE']);
        $tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', $params['KEEP_ALIVE_YEARS_BIRTH']);
        $tree->setPreference('KEEP_ALIVE_YEARS_DEATH', $params['KEEP_ALIVE_YEARS_DEATH']);
        $tree->setPreference('MAX_ALIVE_AGE', $params['MAX_ALIVE_AGE']);
        $tree->setPreference('REQUIRE_AUTHENTICATION', $params['REQUIRE_AUTHENTICATION']);
        $tree->setPreference('SHOW_DEAD_PEOPLE', $params['SHOW_DEAD_PEOPLE']);
        $tree->setPreference('SHOW_LIVING_NAMES', $params['SHOW_LIVING_NAMES']);
        $tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $params['SHOW_PRIVATE_RELATIONSHIPS']);

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title()), 'success'));

        // Coming soon...
        $all_trees = $params['all_trees'] ?? '';
        $new_trees = $params['new_trees'] ?? '';

        if ($all_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->title())), 'success');
        }
        if ($new_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->title())), 'success');
        }

        return redirect(route('manage-trees', ['tree' => $tree->name()]));
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
                    $row->record = Factory::gedcomRecord()->make($row->xref, $tree);
                }

                if ($row->tag_type) {
                    $row->tag_label = GedcomTag::getLabel($row->tag_type);
                } else {
                    $row->tag_label = '';
                }

                return $row;
            })
            ->sort(static function (stdClass $x, stdClass $y): int {
                return I18N::strcasecmp($x->tag_label, $y->tag_label);
            })
            ->all();
    }

    /**
     * Generate a list of potential problems with the server.
     *
     * @param Tree $tree
     *
     * @return string[]
     */
    private function tagsForPrivacy(Tree $tree): array
    {
        $tags = array_unique(array_merge(
            explode(',', $tree->getPreference('INDI_FACTS_ADD')),
            explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('FAM_FACTS_ADD')),
            explode(',', $tree->getPreference('FAM_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('NOTE_FACTS_ADD')),
            explode(',', $tree->getPreference('NOTE_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('SOUR_FACTS_ADD')),
            explode(',', $tree->getPreference('SOUR_FACTS_UNIQUE')),
            explode(',', $tree->getPreference('REPO_FACTS_ADD')),
            explode(',', $tree->getPreference('REPO_FACTS_UNIQUE')),
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

        uasort($all_tags, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return array_merge(
            ['' => I18N::translate('All facts and events')],
            $all_tags
        );
    }
}
