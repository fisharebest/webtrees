<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use PDOException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the processing GEDCOM files.
 */
class GedcomFileController extends AbstractBaseController {
	protected $layout = 'layouts/ajax';

	/**
	 * Import the next chunk of a GEDCOM file.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function import(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		// Only allow one process to import each gedcom at a time
		Database::prepare(
			"SELECT * FROM `##gedcom_chunk` WHERE gedcom_id = :tree_id FOR UPDATE"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		]);

		// What is the current import status?
		$row = Database::prepare(
			"SELECT" .
			" SUM(IF(imported, LENGTH(chunk_data), 0)) AS import_offset," .
			" SUM(LENGTH(chunk_data))                  AS import_total" .
			" FROM `##gedcom_chunk` WHERE gedcom_id = :tree_id"
		)->execute([
			'tree_id' => $tree->getTreeId(),
		])->fetchOneRow();

		// Finished?
		if ($row->import_offset == $row->import_total) {
			$tree->setPreference('imported', '1');

			$html = view('admin/import-complete');

			return new Response($html);
		}

		// Calculate progress so far
		$progress = $row->import_offset / $row->import_total;

		$first_time = ($row->import_offset == 0);
		// Run for one second. This keeps the resource requirements low.
		for ($end_time = microtime(true) + 1.0; microtime(true) < $end_time;) {
			$data = Database::prepare(
				"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data" .
				" FROM `##gedcom_chunk`" .
				" WHERE gedcom_id = :tree_id AND NOT imported" .
				" ORDER BY gedcom_chunk_id" .
				" LIMIT 1"
			)->execute([
				'tree_id' => $tree->getTreeId(),
			])->fetchOneRow();

			// If we are loading the first (header) record, make sure the encoding is UTF-8.
			if ($first_time) {
				// Remove any byte-order-mark
				Database::prepare(
					"UPDATE `##gedcom_chunk`" .
					" SET chunk_data=TRIM(LEADING :bom FROM chunk_data)" .
					" WHERE gedcom_chunk_id = :chunk_id"
				)->execute([
					'bom'      => WT_UTF8_BOM,
					'chunk_id' => $data->gedcom_chunk_id,
				]);
				// Re-fetch the data, now that we have removed the BOM
				$data = Database::prepare(
					"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data" .
					" FROM `##gedcom_chunk`" .
					" WHERE gedcom_chunk_id= :chunk_id"
				)->execute([
					'chunk_id' => $data->gedcom_chunk_id,
				])->fetchOneRow();

				if (substr($data->chunk_data, 0, 6) != '0 HEAD') {
					return $this->viewResponse('admin/import-fail', [
						'error' => I18N::translate('Invalid GEDCOM file - no header record found.'),
					]);
				}
				// What character set is this? Need to convert it to UTF8
				if (preg_match('/[\r\n][ \t]*1 CHAR(?:ACTER)? (.+)/', $data->chunk_data, $match)) {
					$charset = trim(strtoupper($match[1]));
				} else {
					$charset = 'ASCII';
				}
				// MySQL supports a wide range of collation conversions. These are ones that
				// have been encountered "in the wild".
				switch ($charset) {
					case 'ASCII':
						Database::prepare(
							"UPDATE `##gedcom_chunk`" .
							" SET chunk_data=CONVERT(CONVERT(chunk_data USING ascii) USING utf8)" .
							" WHERE gedcom_id = :tree_id"
						)->execute([
							'tree_id' => $tree->getTreeId(),
						]);
						break;
					case 'IBMPC':   // IBMPC, IBM WINDOWS and MS-DOS could be anything. Mostly it means CP850.
					case 'IBM WINDOWS':
					case 'MS-DOS':
					case 'CP437':
					case 'CP850':
						// CP850 has extra letters with diacritics to replace box-drawing chars in CP437.
						Database::prepare(
							"UPDATE `##gedcom_chunk`" .
							" SET chunk_data=CONVERT(CONVERT(chunk_data USING cp850) USING utf8)" .
							" WHERE gedcom_id = :tree_id"
						)->execute([
							'tree_id' => $tree->getTreeId(),
						]);
						break;
					case 'ANSI': // ANSI could be anything. Most applications seem to treat it as latin1.
					case 'WINDOWS':
					case 'CP1252':
					case 'ISO8859-1':
					case 'ISO-8859-1':
					case 'LATIN1':
					case 'LATIN-1':
						// Convert from ISO-8859-1 (western european) to UTF8.
						Database::prepare(
							"UPDATE `##gedcom_chunk`" .
							" SET chunk_data=CONVERT(CONVERT(chunk_data USING latin1) USING utf8)" .
							" WHERE gedcom_id= = :tree_id"
						)->execute([
							'tree_id' => $tree->getTreeId(),
						]);
						break;
					case 'CP1250':
					case 'ISO8859-2':
					case 'ISO-8859-2':
					case 'LATIN2':
					case 'LATIN-2':
						// Convert from ISO-8859-2 (eastern european) to UTF8.
						Database::prepare(
							"UPDATE `##gedcom_chunk`" .
							" SET chunk_data=CONVERT(CONVERT(chunk_data USING latin2) USING utf8)" .
							" WHERE gedcom_id = :tree_id"
						)->execute([
							'tree_id' => $tree->getTreeId(),
						]);
						break;
					case 'MACINTOSH':
						// Convert from MAC Roman to UTF8.
						Database::prepare(
							"UPDATE `##gedcom_chunk`" .
							" SET chunk_data=CONVERT(CONVERT(chunk_data USING macroman) USING utf8)" .
							" WHERE gedcom_id = :tree_id"
						)->execute([
							'tree_id' => $tree->getTreeId(),
						]);
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
				$data = Database::prepare(
					"SELECT gedcom_chunk_id, REPLACE(chunk_data, '\r', '\n') AS chunk_data" .
					" FROM `##gedcom_chunk`" .
					" WHERE gedcom_chunk_id = :chunk_id"
				)->execute([
					'chunk_id' => $data->gedcom_chunk_id,
				])->fetchOneRow();
			}

			if (!$data) {
				break;
			}
			try {
				// Import all the records in this chunk of data
				foreach (preg_split('/\n+(?=0)/', $data->chunk_data) as $rec) {
					FunctionsImport::importRecord($rec, $tree, false);
				}
				// Mark the chunk as imported
				Database::prepare(
					"UPDATE `##gedcom_chunk` SET imported=TRUE WHERE gedcom_chunk_id = :chunk_id"
				)->execute([
					'chunk_id' => $data->gedcom_chunk_id,
				]);
			} catch (PDOException $ex) {
				DebugBar::addThrowable($ex);

				return $this->viewResponse('admin/import-fail', [
					'error' => $ex->getMessage(),
				]);
			}
		}

		return $this->viewResponse('admin/import-progress', [
			'progress' => $progress,
		]);
	}
}
