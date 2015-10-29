<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\BatchUpdateModule;

/**
 * Class BatchUpdateBasePlugin
 *
 * Each plugin should extend this class, and implement these two functions:
 *
 * bool doesRecordNeedUpdate($xref, $gedrec)
 * string updateRecord($xref, $gedrec)
 */
class BatchUpdateBasePlugin {
	/** @var bool User option; update change record */
	public $chan = false;

	/**
	 * Default is to operate on INDI records
	 *
	 * @return string[]
	 */
	public function getRecordTypesToUpdate() {
		return array('INDI');
	}

	/**
	 * Default option is just the "don't update CHAN record"
	 */
	public function getOptions() {
		$this->chan = Filter::getBool('chan');
	}

	/**
	 * Default option is just the "don't update CHAN record"
	 *
	 * @return string
	 */
	public function getOptionsForm() {
		return
			'<div class="form-group">' .
			'<label class="control-label col-sm-3">' . I18N::translate('Keep the existing “last change” information') . '</label>' .
			'<div class="col-sm-9">' .
			FunctionsEdit::radioButtons('chan', array(0 => I18N::translate('no'), 1 => I18N::translate('yes')), ($this->chan ? 1 : 0), 'class="radio-inline" onchange="this.form.submit();"') .
			'</div></div>';
	}

	/**
	 * Default buttons are update and update_all
	 *
	 * @param string $xref
	 *
	 * @return string[]
	 */
	public function getActionButtons($xref) {
		if (Auth::user()->getPreference('auto_accept')) {
			return array(
				BatchUpdateModule::createSubmitButton(I18N::translate('Update'), $xref, 'update'),
				BatchUpdateModule::createSubmitButton(I18N::translate('Update all'), $xref, 'update_all'),
			);
		} else {
			return array(
				BatchUpdateModule::createSubmitButton(I18N::translate('Update'), $xref, 'update'),
			);
		}
	}

	/**
	 * Default previewer for plugins with no custom preview.
	 *
	 * @param GedcomRecord $record
	 *
	 * @return string
	 */
	public function getActionPreview(GedcomRecord $record) {
		$old_lines   = preg_split('/[\n]+/', $record->getGedcom());
		$new_lines   = preg_split('/[\n]+/', $this->updateRecord($record->getXref(), $record->getGedcom()));
		$algorithm   = new MyersDiff;
		$differences = $algorithm->calculate($old_lines, $new_lines);
		$diff_lines  = array();

		foreach ($differences as $difference) {
			switch ($difference[1]) {
			case MyersDiff::DELETE:
				$diff_lines[] = self::decorateDeletedText($difference[0]);
				break;
			case MyersDiff::INSERT:
				$diff_lines[] = self::decorateInsertedText($difference[0]);
				break;
			default:
				$diff_lines[] = $difference[0];
			}
		}

		return '<pre class="gedcom-data">' . self::createEditLinks(implode("\n", $diff_lines)) . '</pre>';
	}

	/**
	 * Decorate inserted text
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function decorateInsertedText($text) {
		return '<ins>' . $text . '</ins>';
	}

	/**
	 * Decorate deleted text
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function decorateDeletedText($text) {
		return '<del>' . $text . '</del>';
	}

	/**
	 * Converted gedcom links into editable links
	 *
	 * @param string $gedrec
	 *
	 * @return string
	 */
	public static function createEditLinks($gedrec) {
		return preg_replace(
			"/@([^#@\n]+)@/m",
			'<a href="#" onclick="return edit_raw(\'\\1\');">@\\1@</a>',
			$gedrec
		);
	}
}
