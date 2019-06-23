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

use Exception;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Controller for the processing GEDCOM files.
 */
class GedcomFileController extends AbstractBaseController
{
    /** @var string */
    protected $layout = 'layouts/ajax';

    /**
     * Import the next chunk of a GEDCOM file.
     *
     * @param TimeoutService $timeout_service
     * @param Tree           $tree
     *
     * @return ResponseInterface
     */
    public function import(TimeoutService $timeout_service, Tree $tree): ResponseInterface
    {
        try {
            // Only allow one process to import each gedcom at a time
            DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->lockForUpdate()
                ->get();

            // What is the current import status?
            $import_offset = (int) DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->where('imported', '=', '1')
                ->sum(DB::raw('LENGTH(chunk_data)'));

            $import_total = (int) DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->sum(DB::raw('LENGTH(chunk_data)'));

            // Finished?
            if ($import_offset === $import_total) {
                $tree->setPreference('imported', '1');

                $html = view('admin/import-complete');

                return response($html);
            }

            // Calculate progress so far
            $progress = $import_offset / $import_total;

            $first_time = ($import_offset === 0);

            // Run for a short period of time. This keeps the resource requirements low.
            do {
                $data = DB::table('gedcom_chunk')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('imported', '=', '0')
                    ->orderBy('gedcom_chunk_id')
                    ->select(['gedcom_chunk_id', 'chunk_data'])
                    ->first();

                // If we are loading the first (header) record, make sure the encoding is UTF-8.
                if ($first_time) {
                    // Remove any byte-order-mark
                    if (Str::startsWith($data->chunk_data, Gedcom::UTF8_BOM)) {
                        $data->chunk_data = Str::after($data->chunk_data, Gedcom::UTF8_BOM);
                        // Put it back in the database, so we can do character conversion
                        DB::table('gedcom_chunk')
                            ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                            ->update(['chunk_data' => $data->chunk_data]);
                    }

                    if (!Str::startsWith($data->chunk_data, '0 HEAD')) {
                        return $this->viewResponse('admin/import-fail', [
                            'error' => I18N::translate('Invalid GEDCOM file - no header record found.'),
                        ]);
                    }

                    // What character set is this? Need to convert it to UTF8
                    if (preg_match('/[\r\n][ \t]*1 CHAR(?:ACTER)? (.+)/', $data->chunk_data, $match)) {
                        $charset = strtoupper(trim($match[1]));
                    } else {
                        $charset = 'ASCII';
                    }
                    // MySQL supports a wide range of collation conversions. These are ones that
                    // have been encountered "in the wild".
                    switch ($charset) {
                        case 'ASCII':
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => DB::raw('CONVERT(CONVERT(chunk_data USING ascii) USING utf8)')]);
                            break;
                        case 'IBMPC':   // IBMPC, IBM WINDOWS and MS-DOS could be anything. Mostly it means CP850.
                        case 'IBM WINDOWS':
                        case 'MS-DOS':
                        case 'CP437':
                        case 'CP850':
                            // CP850 has extra letters with diacritics to replace box-drawing chars in CP437.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => DB::raw('CONVERT(CONVERT(chunk_data USING cp850) USING utf8)')]);
                            break;
                        case 'ANSI': // ANSI could be anything. Most applications seem to treat it as latin1.
                        case 'WINDOWS':
                        case 'CP1252':
                        case 'ISO8859-1':
                        case 'ISO-8859-1':
                        case 'LATIN1':
                        case 'LATIN-1':
                            // Convert from ISO-8859-1 (western european) to UTF8.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => DB::raw('CONVERT(CONVERT(chunk_data USING latin1) USING utf8)')]);
                            break;
                        case 'CP1250':
                        case 'ISO8859-2':
                        case 'ISO-8859-2':
                        case 'LATIN2':
                        case 'LATIN-2':
                            // Convert from ISO-8859-2 (eastern european) to UTF8.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => DB::raw('CONVERT(CONVERT(chunk_data USING latin2) USING utf8)')]);
                            break;
                        case 'MACINTOSH':
                            // Convert from MAC Roman to UTF8.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => DB::raw('CONVERT(CONVERT(chunk_data USING macroman) USING utf8)')]);
                            break;
                        case 'UTF8':
                        case 'UTF-8':
                            // Already UTF-8 so nothing to do!
                            break;
                        case 'ANSEL':
                        default:
                            return $this->viewResponse('admin/import-fail', [
                                'error' => I18N::translate('Error: converting GEDCOM files from %s encoding to UTF-8 encoding not currently supported.', $charset),
                            ]);
                    }
                    $first_time = false;

                    // Re-fetch the data, now that we have performed character set conversion.
                    $data = DB::table('gedcom_chunk')
                        ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                        ->select(['gedcom_chunk_id', 'chunk_data'])
                        ->first();
                }

                if (!$data) {
                    break;
                }

                $data->chunk_data = str_replace("\r", "\n", $data->chunk_data);

                // Import all the records in this chunk of data
                foreach (preg_split('/\n+(?=0)/', $data->chunk_data) as $rec) {
                    try {
                        FunctionsImport::importRecord($rec, $tree, false);
                    } catch (Throwable $ex) {
                        // Make sure the error message includes the GEDCOM record being imported.
                        throw new Exception($ex->getMessage() . '<pre>' . e($rec) . '</pre>');
                    }
                }

                // Mark the chunk as imported
                DB::table('gedcom_chunk')
                    ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                    ->update(['imported' => 1]);
            } while (!$timeout_service->isTimeLimitUp());

            return $this->viewResponse('admin/import-progress', [
                'progress' => $progress,
            ]);
        } catch (Exception $ex) {
            DB::connection()->rollBack();

            return $this->viewResponse('admin/import-fail', [
                'error' => $ex->getMessage(),
            ]);
        }
    }
}
