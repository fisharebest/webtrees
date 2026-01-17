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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleDataFixInterface;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function json_encode;
use function response;

use const JSON_THROW_ON_ERROR;

final class DataFixUpdateAll implements RequestHandlerInterface
{
    // Process this number of records in each HTTP request
    private const int CHUNK_SIZE = 250;

    private DataFixService $data_fix_service;

    private ModuleService $module_service;

    /**
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

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $data_fix = Validator::attributes($request)->string('data_fix', '');
        $module   = $this->module_service->findByName($data_fix);
        assert($module instanceof ModuleDataFixInterface);

        $params = $request->getQueryParams();
        $rows   = $module->recordsToFix($tree, $params);

        if ($rows->isEmpty()) {
            return response([]);
        }

        $start = Validator::queryParams($request)->string('start', '');
        $end   = Validator::queryParams($request)->string('end', '');

        if ($start === '' || $end === '') {
            return $this->createUpdateRanges($tree, $module, $rows, $params);
        }

        /** @var Collection<int,GedcomRecord> $records */
        $records = $rows
            ->map(fn (object $row): GedcomRecord|null => $this->data_fix_service->getRecordByType($row->xref, $tree, $row->type))
            ->filter(static fn (GedcomRecord|null $record): bool => $record instanceof GedcomRecord && !$record->isPendingDeletion() && $module->doesRecordNeedUpdate($record, $params));

        foreach ($records as $record) {
            $module->updateRecord($record, $params);
        }

        return response();
    }

    /**
     * @param Collection<int,object{xref:string,type:string}> $rows
     * @param array<string>                                   $params
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
            ->map(static function (Collection $chunk) use ($module, $params, $tree, $total): object {
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

        return response(json_encode($updates, JSON_THROW_ON_ERROR));
    }
}
