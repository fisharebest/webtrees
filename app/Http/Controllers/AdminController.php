<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Controller for the administration pages
 */
class AdminController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/administration';

    /**
     * Merge two genealogy records.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function mergeRecords(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $title = I18N::translate('Merge records') . ' — ' . e($tree->title());

        $xref1 = $request->get('xref1', '');
        $xref2 = $request->get('xref2', '');

        $record1 = GedcomRecord::getInstance($xref1, $tree);
        $record2 = GedcomRecord::getInstance($xref2, $tree);

        if ($xref1 !== '' && $record1 === null) {
            $xref1 = '';
        }

        if ($xref2 !== '' && $record2 === null) {
            $xref2 = '';
        }

        if ($record1 === $record2) {
            $xref2 = '';
        }

        if ($record1 !== null && $record2 && $record1::RECORD_TYPE !== $record2::RECORD_TYPE) {
            $xref2 = '';
        }

        if ($xref1 === '' || $xref2 === '') {
            return $this->viewResponse('admin/merge-records-step-1', [
                'individual1' => $record1 instanceof Individual ? $record1 : null,
                'individual2' => $record2 instanceof Individual ? $record2 : null,
                'family1'     => $record1 instanceof Family ? $record1 : null,
                'family2'     => $record2 instanceof Family ? $record2 : null,
                'source1'     => $record1 instanceof Source ? $record1 : null,
                'source2'     => $record2 instanceof Source ? $record2 : null,
                'repository1' => $record1 instanceof Repository ? $record1 : null,
                'repository2' => $record2 instanceof Repository ? $record2 : null,
                'media1'      => $record1 instanceof Media ? $record1 : null,
                'media2'      => $record2 instanceof Media ? $record2 : null,
                'note1'       => $record1 instanceof Note ? $record1 : null,
                'note2'       => $record2 instanceof Note ? $record2 : null,
                'title'       => $title,
            ]);
        }

        // Facts found both records
        $facts = [];
        // Facts found in only one record
        $facts1 = [];
        $facts2 = [];

        foreach ($record1->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts1[$fact->id()] = $fact;
            }
        }

        foreach ($record2->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts2[$fact->id()] = $fact;
            }
        }

        foreach ($facts1 as $id1 => $fact1) {
            foreach ($facts2 as $id2 => $fact2) {
                if ($fact1->id() === $fact2->id()) {
                    $facts[] = $fact1;
                    unset($facts1[$id1]);
                    unset($facts2[$id2]);
                }
            }
        }

        return $this->viewResponse('admin/merge-records-step-2', [
            'facts'   => $facts,
            'facts1'  => $facts1,
            'facts2'  => $facts2,
            'record1' => $record1,
            'record2' => $record2,
            'title'   => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function mergeRecordsAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref1 = $request->get('xref1', '');
        $xref2 = $request->get('xref2', '');
        $keep1 = $request->get('keep1', []);
        $keep2 = $request->get('keep2', []);

        // Merge record2 into record1
        $record1 = GedcomRecord::getInstance($xref1, $tree);
        $record2 = GedcomRecord::getInstance($xref2, $tree);

        // Facts found both records
        $facts = [];
        // Facts found in only one record
        $facts1 = [];
        $facts2 = [];

        foreach ($record1->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts1[$fact->id()] = $fact;
            }
        }

        foreach ($record2->facts() as $fact) {
            if (!$fact->isPendingDeletion() && $fact->getTag() !== 'CHAN') {
                $facts2[$fact->id()] = $fact;
            }
        }

        // If we are not auto-accepting, then we can show a link to the pending deletion
        if (Auth::user()->getPreference('auto_accept')) {
            $record2_name = $record2->fullName();
        } else {
            $record2_name = '<a class="alert-link" href="' . e($record2->url()) . '">' . $record2->fullName() . '</a>';
        }

        // Update records that link to the one we will be removing.
        $linking_records = $record2->linkingRecords();

        foreach ($linking_records as $record) {
            if (!$record->isPendingDeletion()) {
                /* I18N: The placeholders are the names of individuals, sources, etc. */
                FlashMessages::addMessage(I18N::translate(
                    'The link from “%1$s” to “%2$s” has been updated.',
                    '<a class="alert-link" href="' . e($record->url()) . '">' . $record->fullName() . '</a>',
                    $record2_name
                ), 'info');
                $gedcom = str_replace('@' . $xref2 . '@', '@' . $xref1 . '@', $record->gedcom());
                $gedcom = preg_replace(
                    '/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
                    '$2',
                    $gedcom
                );
                $record->updateRecord($gedcom, true);
            }
        }

        // Update any linked user-accounts
        DB::table('user_gedcom_setting')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('setting_name', ['gedcomid', 'rootid'])
            ->where('setting_value', '=', $xref2)
            ->update(['setting_value' => $xref1]);

        // Merge hit counters
        $hits = DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('page_parameter', [$xref1, $xref2])
            ->groupBy('page_name')
            ->pluck(DB::raw('SUM(page_count)'), 'page_name');

        foreach ($hits as $page_name => $page_count) {
            DB::table('hit_counter')
                ->where('gedcom_id', '=', $tree->id())
                ->where('page_name', '=', $page_name)
                ->update(['page_count' => $page_count]);
        }

        DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->where('page_parameter', '=', $xref2)
            ->delete();

        $gedcom = '0 @' . $record1->xref() . '@ ' . $record1::RECORD_TYPE;
        foreach ($facts as $fact_id => $fact) {
            $gedcom .= "\n" . $fact->gedcom();
        }
        foreach ($facts1 as $fact_id => $fact) {
            if (in_array($fact_id, $keep1)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }
        foreach ($facts2 as $fact_id => $fact) {
            if (in_array($fact_id, $keep2)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }

        DB::table('favorite')
            ->where('gedcom_id', '=', $tree->id())
            ->where('xref', '=', $xref2)
            ->update(['xref' => $xref1]);

        $record1->updateRecord($gedcom, true);
        $record2->deleteRecord();

        /* I18N: Records are individuals, sources, etc. */
        FlashMessages::addMessage(I18N::translate(
            'The records “%1$s” and “%2$s” have been merged.',
            '<a class="alert-link" href="' . e($record1->url()) . '">' . $record1->fullName() . '</a>',
            $record2_name
        ), 'success');

        return redirect(route('merge-records', ['ged' => $tree->name()]));
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function treePrivacyEdit(Tree $tree): ResponseInterface
    {
        $title                = e($tree->name()) . ' — ' . I18N::translate('Privacy');
        $all_tags             = $this->tagsForPrivacy($tree);
        $privacy_constants    = $this->privacyConstants();
        $privacy_restrictions = $this->privacyRestrictions($tree);

        return $this->viewResponse('admin/trees-privacy', [
            'all_tags'             => $all_tags,
            'count_trees'          => count(Tree::getAll()),
            'privacy_constants'    => $privacy_constants,
            'privacy_restrictions' => $privacy_restrictions,
            'title'                => $title,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function treePrivacyUpdate(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $delete_default_resn_id = (array) $request->get('delete');

        DB::table('default_resn')
            ->whereIn('default_resn_id', $delete_default_resn_id)
            ->delete();

        $xrefs     = (array) $request->get('xref');
        $tag_types = (array) $request->get('tag_type');
        $resns     = (array) $request->get('resn');

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

        $tree->setPreference('HIDE_LIVE_PEOPLE', $request->get('HIDE_LIVE_PEOPLE'));
        $tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', $request->get('KEEP_ALIVE_YEARS_BIRTH', '0'));
        $tree->setPreference('KEEP_ALIVE_YEARS_DEATH', $request->get('KEEP_ALIVE_YEARS_DEATH', '0'));
        $tree->setPreference('MAX_ALIVE_AGE', $request->get('MAX_ALIVE_AGE', '100'));
        $tree->setPreference('REQUIRE_AUTHENTICATION', $request->get('REQUIRE_AUTHENTICATION'));
        $tree->setPreference('SHOW_DEAD_PEOPLE', $request->get('SHOW_DEAD_PEOPLE'));
        $tree->setPreference('SHOW_LIVING_NAMES', $request->get('SHOW_LIVING_NAMES'));
        $tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $request->get('SHOW_PRIVATE_RELATIONSHIPS'));

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title()), 'success'));

        // Coming soon...
        if ((bool) $request->get('all_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->title())), 'success');
        }
        if ((bool) $request->get('new_trees')) {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->title())), 'success');
        }

        return redirect(route('admin-trees', ['ged' => $tree->name()]));
    }

    /**
     * Names of our privacy levels
     *
     * @return array
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
     * @return array
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
                    $row->record = GedcomRecord::getInstance($row->xref, $tree);
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
