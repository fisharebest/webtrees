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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function assert;
use function json_encode;
use function response;

/**
 * Run a data-fix.
 */
class DataFixUpdateAll implements RequestHandlerInterface
{
    // Process this number of records in each HTTP request
    private const CHUNK_SIZE = 250;

    /** @var DataFixService */
    private $data_fix_service;

    /** @var ModuleService */
    private $module_service;

    /**
     * DataFix constructor.
     *
     * @param DataFixService $data_fix_service
     * @param ModuleService  $module_service
     */
    public function __construct(
        DataFixService $data_fix_service,
        ModuleService $module_service
    ) {
        $this->data_fix_service = $data_fix_service;
        $this->module_service   = $module_service;
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

        $data_fix = $request->getAttribute('data_fix', '');
        $module   = $this->module_service->findByName($data_fix);
        assert($module instanceof ModuleDataFixInterface);

        $params = (array) $request->getQueryParams();
        $rows   = $module->recordsToFix($tree, $params);

        if ($rows->isEmpty()) {
            return response([]);
        }

        $start = $request->getQueryParams()['start'] ?? '';
        $end   = $request->getQueryParams()['end'] ?? '';

        if ($start === '' || $end === '') {
            return $this->createUpdateRanges($tree, $module, $rows, $params);
        }

        /** @var Collection<GedcomRecord> $records */
        $records = $rows->map(function (stdClass $row) use ($tree): ?GedcomRecord {
            return $this->data_fix_service->getRecordByType($row->xref, $tree, $row->type);
        })->filter(static function (?GedcomRecord $record) use ($module, $params): bool {
            return $record instanceof GedcomRecord && !$record->isPendingDeletion() && $module->doesRecordNeedUpdate($record, $params);
        });

        foreach ($records as $record) {
            $module->updateRecord($record, $params);
        }

        return response();
    }

    /**
     * @param Tree                   $tree
     * @param ModuleDataFixInterface $module
     * @param Collection<stdClass>   $rows
     * @param array<string>          $params
     *
     * @return ResponseInterface
     */
    private function createUpdateRanges(
        Tree $tree,
        ModuleDataFixInterface $module,
        Collection $rows,
        array $params
    ): ResponseInterface {
        $total = $rows->count();

        $updates = $rows
            ->chunk(self::CHUNK_SIZE)
            ->map(static function (Collection $chunk) use ($module, $params, $tree, $total): stdClass {
                static $count = 0;

                $count += $chunk->count();

                $start = $chunk->first()->xref;
                $end   = $chunk->last()->xref;
                $url   = route(self::class, [
                        'tree'     => $tree->name(),
                        'data_fix' => $module->name(),
                        'start'    => $start,
                        'end'      => $end,
                    ] + $params);

                return (object) [
                    'url'      => $url,
                    'percent'  => (100.0 * $count / $total) . '%',
                    'progress' => I18N::percentage($count / $total, 1),
                ];
            })
            ->all();

        return response(json_encode($updates));
    }
}
