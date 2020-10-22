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

use Exception;
use Fisharebest\Webtrees\Exceptions\GedcomErrorException;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function preg_match;
use function preg_split;
use function response;
use function str_replace;
use function str_starts_with;
use function strlen;
use function strtoupper;
use function substr;
use function trim;
use function view;

/**
 * Load a chunk of GEDCOM data.
 */
class GedcomLoad implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var TimeoutService */
    private $timeout_service;

    /**
     * GedcomLoad constructor.
     *
     * @param TimeoutService $timeout_service
     */
    public function __construct(TimeoutService $timeout_service)
    {
        $this->timeout_service = $timeout_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        try {
            // Only allow one process to import each gedcom at a time
            DB::table('gedcom_chunk')
                ->where('gedcom_id', '=', $tree->id())
                ->lockForUpdate()
                ->get();

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
                $tree->setPreference('imported', '1');

                $html = view('admin/import-complete', ['tree' => $tree]);

                return response($html);
            }

            // Calculate progress so far
            $progress = $import_offset / $import_total;

            $first_time = ($import_offset === 0);

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

                // If we are loading the first (header) record, make sure the encoding is UTF-8.
                if ($first_time) {
                    // Remove any byte-order-mark
                    if (str_starts_with($data->chunk_data, Gedcom::UTF8_BOM)) {
                        $data->chunk_data = substr($data->chunk_data, strlen(Gedcom::UTF8_BOM));
                        // Put it back in the database, so we can do character conversion
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

                    // What character set is this? Need to convert it to UTF8
                    if (preg_match('/[\r\n][ \t]*1 CHAR(?:ACTER)? ([^\r\n]+)/', $data->chunk_data, $match)) {
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
                                ->update(['chunk_data' => new Expression('CONVERT(CONVERT(chunk_data USING ascii) USING utf8)')]);
                            break;
                        case 'IBMPC':   // IBMPC, IBM WINDOWS and MS-DOS could be anything. Mostly it means CP850.
                        case 'IBM WINDOWS':
                        case 'MS-DOS':
                        case 'CP437':
                        case 'CP850':
                            // CP850 has extra letters with diacritics to replace box-drawing chars in CP437.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => new Expression('CONVERT(CONVERT(chunk_data USING cp850) USING utf8)')]);
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
                                ->update(['chunk_data' => new Expression('CONVERT(CONVERT(chunk_data USING latin1) USING utf8)')]);
                            break;
                        case 'CP1250':
                        case 'ISO8859-2':
                        case 'ISO-8859-2':
                        case 'LATIN2':
                        case 'LATIN-2':
                            // Convert from ISO-8859-2 (eastern european) to UTF8.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => new Expression('CONVERT(CONVERT(chunk_data USING latin2) USING utf8)')]);
                            break;
                        case 'MACINTOSH':
                            // Convert from MAC Roman to UTF8.
                            DB::table('gedcom_chunk')
                                ->where('gedcom_id', '=', $tree->id())
                                ->update(['chunk_data' => new Expression('CONVERT(CONVERT(chunk_data USING macroman) USING utf8)')]);
                            break;
                        case 'UTF8':
                        case 'UTF-8':
                            // Already UTF-8 so nothing to do!
                            break;
                        case 'ANSEL':
                        default:
                            return $this->viewResponse('admin/import-fail', [
                                'error' => I18N::translate('Error: converting GEDCOM files from %s encoding to UTF-8 encoding not currently supported.', $charset),
                                'tree'  => $tree,
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
                    } catch (GedcomErrorException $exception) {
                        $errors .= $exception->getMessage();
                    }
                }

                // Mark the chunk as imported
                DB::table('gedcom_chunk')
                    ->where('gedcom_chunk_id', '=', $data->gedcom_chunk_id)
                    ->update(['imported' => 1]);
            } while (!$this->timeout_service->isTimeLimitUp());

            return $this->viewResponse('admin/import-progress', [
                'errors'   => $errors,
                'progress' => $progress,
                'tree'     => $tree,
            ]);
        } catch (Exception $ex) {
            DB::connection()->rollBack();

            return $this->viewResponse('admin/import-fail', [
                'error' => $ex->getMessage(),
                'tree'  => $tree,
            ]);
        }
    }
}
