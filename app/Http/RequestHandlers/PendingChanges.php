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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function key;
use function preg_match;
use function reset;
use function route;

/**
 * Show all pending changes.
 */
class PendingChanges implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var TreeService */
    private $tree_service;

    /**
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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $url = $request->getQueryParams()['url'] ?? route(TreePage::class, ['tree' => $tree->name()]);

        $rows = DB::table('change')
            ->join('user', 'user.user_id', '=', 'change.user_id')
            ->join('gedcom', 'gedcom.gedcom_id', '=', 'change.gedcom_id')
            ->where('status', '=', 'pending')
            ->orderBy('change.gedcom_id')
            ->orderBy('change.xref')
            ->orderBy('change.change_id')
            ->select(['change.*', 'user.user_name', 'user.real_name', 'gedcom_name'])
            ->get();

        $changes = [];
        foreach ($rows as $row) {
            $row->change_time = Carbon::make($row->change_time);

            $change_tree = $this->tree_service->all()->get($row->gedcom_name);

            preg_match('/^0 (?:@' . Gedcom::REGEX_XREF . '@ )?(' . Gedcom::REGEX_TAG . ')/', $row->old_gedcom . $row->new_gedcom, $match);

            switch ($match[1]) {
                case Individual::RECORD_TYPE:
                    $row->record = Factory::individual()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Family::RECORD_TYPE:
                    $row->record = Factory::family()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Source::RECORD_TYPE:
                    $row->record = Factory::source()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Repository::RECORD_TYPE:
                    $row->record = Factory::repository()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Media::RECORD_TYPE:
                    $row->record = Factory::media()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Note::RECORD_TYPE:
                    $row->record = Factory::note()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Submitter::RECORD_TYPE:
                    $row->record = Factory::submitter()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Submission::RECORD_TYPE:
                    $row->record = Factory::submission()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Location::RECORD_TYPE:
                    $row->record = Factory::location()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                case Header::RECORD_TYPE:
                    $row->record = Factory::header()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
                default:
                    $row->record = Factory::gedcomRecord()->new($row->xref, $row->old_gedcom, $row->new_gedcom, $change_tree);
                    break;
            }

            $changes[$row->gedcom_name][$row->xref][] = $row;
        }

        $title = I18N::translate('Pending changes');

        // If the current tree has changes, activate that tab.  Otherwise activate the first tab.
        if (($changes[$tree->id()] ?? []) === []) {
            reset($changes);
            $active_tree_name = key($changes);
        } else {
            $active_tree_name = $tree->name();
        }

        return $this->viewResponse('pending-changes-page', [
            'active_tree_name' => $active_tree_name,
            'changes'          => $changes,
            'title'            => $title,
            'tree'             => $tree,
            'trees'            => $this->tree_service->all(),
            'url'              => $url,
        ]);
    }
}
