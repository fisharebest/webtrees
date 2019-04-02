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

namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Algorithm\MyersDiff;
use Fisharebest\Webtrees\GedcomRecord;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class BatchUpdateBasePlugin
 */
abstract class BatchUpdateBasePlugin
{
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
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    public function getOptions(ServerRequestInterface $request): void
    {
    }

    /**
     * Default option is just the "don't update CHAN record"
     *
     * @return string
     */
    public function getOptionsForm(): string
    {
        return '';
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
        $old_lines   = explode("\n", $record->gedcom());
        $new_lines   = explode("\n", $this->updateRecord($record));
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
                'ged'  => $record->tree()->name(),
                'xref' => $record->xref(),
            ])) . '">@\\1@</a>',
            $gedrec
        );
    }
}
