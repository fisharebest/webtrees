<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Exception;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Exceptions\GedcomErrorException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\DetectsConcurrencyErrors;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function preg_split;
use function str_replace;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * Load a chunk of GEDCOM data.
 */
class GedcomLoad implements RequestHandlerInterface
{
    use ViewResponseTrait;
    use DetectsConcurrencyErrors;

    private GedcomImportService $gedcom_import_service;

    private TimeoutService $timeout_service;

    /**
     * @param GedcomImportService $gedcom_import_service
     * @param TimeoutService      $timeout_service
     */
    public function __construct(
        GedcomImportService $gedcom_import_service,
        TimeoutService $timeout_service
    ) {
        $this->gedcom_import_service = $gedcom_import_service;
        $this->timeout_service       = $timeout_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $tree = Validator::attributes($request)->tree();

        try {
            // What is the current import status?
            $import_offset = DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->where('imported', '=', '1')
                ->count();

            $import_total = DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->count();

            // Finished?
            if ($import_offset === $import_total) {
                if ($tree->getPreference('imported') !== '1') {
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
                    'individuals' => DB::table('individuals')->where('i_file', '=', $tree->id()),
                    'families'    => DB::table('families')->where('f_file', '=', $tree->id()),
                    'sources'     => DB::table('sources')->where('s_file', '=', $tree->id()),
                    'other'       => DB::table('other')->where('o_file', '=', $tree->id()),
                    'places'      => DB::table('places')->where('p_file', '=', $tree->id()),
                    'placelinks'  => DB::table('placelinks')->where('pl_file', '=', $tree->id()),
                    'name'        => DB::table('name')->where('n_file', '=', $tree->id()),
                    'dates'       => DB::table('dates')->where('d_file', '=', $tree->id()),
                    'change'      => DB::table('change')->where('gedcom_id', '=', $tree->id()),
                ];

                if ($tree->getPreference('keep_media') === '1') {
                    $queries['link'] = DB::table('link')->where('l_file', '=', $tree->id())
                        ->where('l_type', '<>', 'OBJE');
                } else {
                    $queries['link']       = DB::table('link')->where('l_file', '=', $tree->id());
                    $queries['media_file'] = DB::table('media_file')->where('m_file', '=', $tree->id());
                    $queries['media']      = DB::table('media')->where('m_file', '=', $tree->id());
                }

                foreach ($queries as $table => $query) {
                    // take() and delete() together don't return the number of delete rows.
                    while ((clone $query)->count() > 0) {
                        (clone $query)->take(1000)->delete();

                        if ($this->timeout_service->isTimeLimitUp()) {
                            return $this->viewResponse('admin/import-progress', [
                                'errors'   => '',
                                'progress' => 0.0,
                                'status'   => I18N::translate('Deleting…') . ' ' . $table,
                                'tree'     => $tree,
                            ]);
                        }
                    }
                }
            }

            // Calculate progress so far
            $progress = $import_offset / $import_total;

            $first_time = $import_offset === 0;

            // Collect up any errors, and show them later.
            $errors = '';

            // Run for a short period of time. This keeps the resource requirements low.
            do {
                $data = DB::table('gedcom_chunk')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('imported', '=', '0')
                    ->orderBy('gedcom_chunk_id')
                    ->select(['gedcom_chunk_id', 'chunk_data'])
                    ->first();

                if ($data === null) {
                    break;
                }

                // Mark the chunk as imported.  This will create a row-lock, to prevent other
                // processes from reading it until we have finished.
                $n = DB::table('gedcom_chunk')
                    ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                    ->where('imported', '=', '0')
                    ->update(['imported' => 1]);

                // Another process has already imported this data?
                if ($n === 0) {
                    break;
                }

                if ($first_time) {
                    // Remove any byte-order-mark
                    if (str_starts_with($data->chunk_data, UTF8::BYTE_ORDER_MARK)) {
                        $data->chunk_data = substr($data->chunk_data, strlen(UTF8::BYTE_ORDER_MARK));
                        DB::table('gedcom_chunk')
                            ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                            ->update(['chunk_data' => $data->chunk_data]);
                    }

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
                DB::table('gedcom_chunk')
                    ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
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
                    'progress' => $progress,
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
