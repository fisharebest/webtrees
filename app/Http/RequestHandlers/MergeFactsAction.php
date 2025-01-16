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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\LinkedRecordService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function in_array;
use function preg_replace;
use function redirect;
use function route;
use function str_replace;

/**
 * Merge records
 */
class MergeFactsAction implements RequestHandlerInterface
{
    private LinkedRecordService $linked_record_service;

    /**
     * @param LinkedRecordService $linked_record_service
     */
    public function __construct(LinkedRecordService $linked_record_service)
    {
        $this->linked_record_service = $linked_record_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xref1 = Validator::parsedBody($request)->isXref()->string('xref1');
        $xref2 = Validator::parsedBody($request)->isXref()->string('xref2');
        $keep1 = Validator::parsedBody($request)->array('keep1');
        $keep2 = Validator::parsedBody($request)->array('keep2');

        // Merge record2 into record1
        $record1 = Registry::gedcomRecordFactory()->make($xref1, $tree);
        $record2 = Registry::gedcomRecordFactory()->make($xref2, $tree);

        if (
            $record1 === null ||
            $record2 === null ||
            $record1 === $record2 ||
            $record1->tag() !== $record2->tag() ||
            $record1->isPendingDeletion() ||
            $record2->isPendingDeletion()
        ) {
            return redirect(route(MergeRecordsPage::class, [
                'tree'  => $tree->name(),
                'xref1' => $xref1,
                'xref2' => $xref2,
            ]));
        }

        // If we are not auto-accepting, then we can show a link to the pending deletion
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $record2_name = $record2->fullName();
        } else {
            $record2_name = '<a class="alert-link" href="' . e($record2->url()) . '">' . $record2->fullName() . '</a>';
        }

        // Update records that link to the one we will be removing.
        $linking_records = $this->linked_record_service->allLinkedRecords($record2);

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
                    '/(\n1.*@.+@.*(?:\n[2-9].*)*)((?:\n1.*(?:\n[2-9].*)*)*\1)/',
                    '$2',
                    $gedcom
                );
                $record->updateRecord($gedcom, true);
            }
        }

        // Update any linked user-accounts
        DB::table('user_gedcom_setting')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('setting_name', [UserInterface::PREF_TREE_ACCOUNT_XREF, UserInterface::PREF_TREE_DEFAULT_XREF])
            ->where('setting_value', '=', $xref2)
            ->update(['setting_value' => $xref1]);

        // Merge stories, etc.
        DB::table('block')
            ->where('gedcom_id', '=', $tree->id())
            ->where('xref', '=', $xref2)
            ->update(['xref' => $xref1]);

        // Merge hit counters
        $hits = DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('page_parameter', [$xref1, $xref2])
            ->groupBy(['page_name'])
            ->pluck(new Expression('SUM(page_count) AS total'), 'page_name');

        foreach ($hits as $page_name => $page_count) {
            DB::table('hit_counter')
                ->where('gedcom_id', '=', $tree->id())
                ->where('page_name', '=', $page_name)
                ->where('page_parameter', '=', $xref1)
                ->update(['page_count' => $page_count]);
        }

        DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->where('page_parameter', '=', $xref2)
            ->delete();

        $gedcom = '0 @' . $record1->xref() . '@ ' . $record1->tag();

        foreach ($record1->facts() as $fact) {
            if (in_array($fact->id(), $keep1, true)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }

        foreach ($record2->facts() as $fact) {
            if (in_array($fact->id(), $keep2, true)) {
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

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
