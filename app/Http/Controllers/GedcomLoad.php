<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Exceptions\GedcomErrorException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Illuminate\Database\DetectsConcurrencyErrors;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;

use function preg_split;
use function str_replace;
use function str_starts_with;

final class GedcomLoad
{
    use ViewResponseTrait;
    use DetectsConcurrencyErrors;

    private GedcomImportService $gedcom_import_service;

    private TimeoutService $timeout_service;

    public function __construct(
        GedcomImportService $gedcom_import_service,
        TimeoutService $timeout_service
    ) {
        $this->gedcom_import_service = $gedcom_import_service;
        $this->timeout_service       = $timeout_service;
    }

    public function post(Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        try {
            // What is the current import status?
            $import_offset = DB::query()
                ->from('gedcom_chunk')
                ->whereEqual('gedcom_id', $tree->id())
                ->whereEqual('imported', '1')
                ->count();

            $import_total = DB::query()
                ->from('gedcom_chunk')
                ->whereEqual('gedcom_id', $tree->id())
                ->count();

            // Calculate progress so far
            $progress = $import_offset / $import_total;

            // Finished?
            if ($import_offset === $import_total) {
                if (!$tree->imported()) {
                    return $this->viewResponse('admin/import-fail', [
                        'error' => I18N::translate('Invalid GEDCOM file - no trailer record found.'),
                        'tree'  => $tree,
                    ]);
                }

                return $this->viewResponse('admin/import-complete', ['tree' => $tree]);
            }

            // If we are loading the first (header) record, then delete old data.
            if ($import_offset === 0) {
                $queries = [
                    'individuals' => DB::query(table: 'individuals')->whereEqual('i_file', $tree->id()),
                    'families'    => DB::query(table: 'families')->whereEqual('f_file', $tree->id()),
                    'sources'     => DB::query(table: 'sources')->whereEqual('s_file', $tree->id()),
                    'other'       => DB::query(table: 'other')->whereEqual('o_file', $tree->id()),
                    'places'      => DB::query(table: 'places')->whereEqual('p_file', $tree->id()),
                    'placelinks'  => DB::query(table: 'placelinks')->whereEqual('pl_file', $tree->id()),
                    'name'        => DB::query(table: 'name')->whereEqual('n_file', $tree->id()),
                    'dates'       => DB::query(table: 'dates')->whereEqual('d_file', $tree->id()),
                    'change'      => DB::query(table: 'change')->whereEqual('gedcom_id', $tree->id()),
                ];

                if ($tree->getPreference('keep_media') === '1') {
                    $queries['link'] = DB::query(table: 'link')
                        ->whereEqual('l_file', $tree->id())
                        ->whereNotEqual('l_type', 'OBJE');
                } else {
                    $queries['link']       = DB::query(table: 'link')->whereEqual('l_file', $tree->id());
                    $queries['media_file'] = DB::query(table: 'media_file')->whereEqual('m_file', $tree->id());
                    $queries['media']      = DB::query(table: 'media')->whereEqual('m_file', $tree->id());
                }

                foreach ($queries as $table => $query) {
                    $count = $query->count();

                    while ($count > 0) {
                        $count -= $query->limit(10000)->delete();

                        if ($this->timeout_service->isTimeLimitUp()) {
                            return $this->viewResponse('admin/import-progress', [
                                'errors'   => '',
                                'progress' => 0.0,
                                'status'   => I18N::translate('Deleting…') . ' ' . $table . ' (' . $count . ')',
                                'tree'     => $tree,
                            ]);
                        }
                    }
                }
            }

            $first_time = $import_offset === 0;

            // Collect up any errors, and show them later.
            $errors = '';

            // Run for a short period of time. This keeps the resource requirements low.
            do {
                $data = DB::query()
                    ->select('gedcom_chunk_id', 'chunk_data')
                    ->from('gedcom_chunk')
                    ->whereEqual('gedcom_id', $tree->id())
                    ->whereEqual('imported', '0')
                    ->orderBy('gedcom_chunk_id')
                    ->first();

                if ($data === null) {
                    break;
                }

                // Mark the chunk as imported.  This will create a row-lock, to prevent other
                // processes from reading it until we have finished.
                $n = DB::query(table: 'gedcom_chunk')
                    ->whereEqual('gedcom_chunk_id', $data->gedcom_chunk_id)
                    ->whereEqual('imported', '0')
                    ->update(['imported' => 1]);

                // Another process has already imported this data?
                if ($n === 0) {
                    break;
                }

                if ($first_time) {
                    if (!str_starts_with($data->chunk_data, '0 HEAD')) {
                        return $this->viewResponse('admin/import-fail', [
                            'error' => I18N::translate('Invalid GEDCOM file - no header record found.'),
                            'tree'  => $tree,
                        ]);
                    }

                    $first_time = false;
                }

                $data->chunk_data = str_replace("\r", "\n", $data->chunk_data);

                // Import all the records in this chunk of data
                foreach (preg_split('/\n+(?=0)/', $data->chunk_data) as $rec) {
                    try {
                        $this->gedcom_import_service->importRecord($rec, $tree, false);
                    } catch (GedcomErrorException $exception) {
                        $errors .= $exception->getMessage();
                    }
                }

                // Do not need the data any more.
                DB::query(table: 'gedcom_chunk')
                    ->whereEqual('gedcom_chunk_id', $data->gedcom_chunk_id)
                    ->update(['chunk_data' => '']);
            } while (!$this->timeout_service->isTimeLimitUp());

            return $this->viewResponse('admin/import-progress', [
                'errors'   => $errors,
                'progress' => $progress,
                'status'   => '',
                'tree'     => $tree,
            ]);
        } catch (Exception $ex) {
            DB::rollBack();

            // Deadlock? Try again.
            if ($this->causedByConcurrencyError($ex)) {
                return $this->viewResponse('admin/import-progress', [
                    'errors'   => '',
                    'progress' => $progress ?? 0,
                    'status'   => e($ex->getMessage()),
                    'tree'     => $tree,
                ]);
            }

            return $this->viewResponse('admin/import-fail', [
                'error' => e($ex->getMessage()),
                'tree'  => $tree,
            ]);
        }
    }
}
