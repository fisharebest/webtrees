<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

/**
 * Class BatchUpdateCensusToMarkdownPlugin.php Batch Update plugin: convert old census format to markdown
 */
class BatchUpdateCensusToMarkdownPlugin extends BatchUpdateBasePlugin {
	/**
	 * User-friendly name for this plugin.
	 *
	 * @return string
	 */
	public function getName() {
		return I18N::translate('Convert census-assistant tables to markdown');
	}

	/**
	 * Default is to operate on INDI records
	 *
	 * @return string[]
	 */
	public function getRecordTypesToUpdate() {
		return ['NOTE'];
	}

	/**
	 * Description / help-text for this plugin.
	 *
	 * @return string
	 */
	public function getDescription() {
		return /* I18N: https://en.wikipedia.org/wiki/Markdown */ I18N::translate('Convert census-assistant notes to Markdown');
	}

	/**
	 * Does this record need updating?
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return bool
	 */
	public function doesRecordNeedUpdate($xref, $gedrec) {
		return preg_match('/\n\d CONT \.start_formatted_area\./', $gedrec) > 0;
	}

	/**
	 * Apply any updates to this record
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return string
	 */
	public function updateRecord($xref, $gedrec) {
		$new = '';
		foreach (explode("\n", $gedrec) as $line) {
			if (preg_match('/^(\d CONT )(\.b\..*)/', $line, $match)) {
				$headers = explode('|', str_replace('.b.', '', $match[2]));
				$new .= $match[1] . implode('|', $headers) . "\n";
				$new .= $match[1] . implode('|', array_fill(1, count($headers), '-----')) . "\n";
			} elseif (preg_match('/^\d CONT \.(start|end)_formatted_area\./', $line) === 0) {
				$new .= $line . "\n";
			}
		}

		return $new;
	}
}
