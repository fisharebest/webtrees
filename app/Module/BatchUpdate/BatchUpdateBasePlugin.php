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

namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BatchUpdateBasePlugin
 */
abstract class BatchUpdateBasePlugin
{
    /** @var bool User option; update change record */
    public $chan = false;

    /**
     * @param GedcomRecord $record
     *
     * @return bool
     */
    abstract public function doesRecordNeedUpdate(GedcomRecord $record): bool;

    /**
     * @param GedcomRecord $record
     *
     * @return string
     */
    abstract public function updateRecord(GedcomRecord $record): string;

    /**
     * Default is to operate on INDI records
     *
     * @return string[]
     */
    public function getRecordTypesToUpdate(): array
    {
        return ['INDI'];
    }

    /**
     * Default option is just the "don't update CHAN record"
     *
     * @param Request $request
     */
    public function getOptions(Request $request)
    {
        $this->chan = (bool) $request->get('chan');
    }

    /**
     * Default option is just the "don't update CHAN record"
     *
     * @return string
     */
    public function getOptionsForm(): string
    {
        return
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Keep the existing “last change” information') . '</label>' .
            '<div class="col-sm-9">' .
            Bootstrap4::radioButtons('chan', [
                0 => I18N::translate('no'),
                1 => I18N::translate('yes'),
            ], ($this->chan ? 1 : 0), true, ['onchange' => 'this.form.submit();']) .
            '</div></div>';
    }

    /**
     * Default previewer for plugins with no custom preview.
     *
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function getActionPreview(GedcomRecord $record): string
    {
        $old_lines   = preg_split('/[\n]+/', $record->getGedcom());
        $new_lines   = preg_split('/[\n]+/', $this->updateRecord($record));
        $algorithm   = new MyersDiff();
        $differences = $algorithm->calculate($old_lines, $new_lines);
        $diff_lines  = [];

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

        return '<pre class="gedcom-data">' . self::createEditLinks(implode("\n", $diff_lines), $record) . '</pre>';
    }

    /**
     * Decorate inserted text
     *
     * @param string $text
     *
     * @return string
     */
    public static function decorateInsertedText($text): string
    {
        return '<ins>' . $text . '</ins>';
    }

    /**
     * Decorate deleted text
     *
     * @param string $text
     *
     * @return string
     */
    public static function decorateDeletedText($text): string
    {
        return '<del>' . $text . '</del>';
    }

    /**
     * Converted gedcom links into editable links
     *
     * @param string       $gedrec
     * @param GedcomRecord $record
     *
     * @return string
     */
    public static function createEditLinks($gedrec, GedcomRecord $record): string
    {
        return preg_replace(
            "/@([^#@\n]+)@/m",
            '<a href="' . e(route('edit-raw-record', [
                'ged'  => $record->getTree()->getName(),
                'xref' => $record->getXref(),
            ])) . '">@\\1@</a>',
            $gedrec
        );
    }
}
