<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Illuminate\Database\Capsule\Manager as DB;

use function explode;
use function htmlspecialchars_decode;
use function preg_match;
use function preg_replace;
use function strip_tags;
use function trim;

use const ENT_QUOTES;

/**
 * A GEDCOM note (NOTE) object.
 */
class Note extends GedcomRecord
{
    public const RECORD_TYPE = 'NOTE';

    protected const ROUTE_NAME = NotePage::class;

    private const NAME_LENGTH = 75;

    /**
     * Get the text contents of the note
     *
     * @return string
     */
    public function getNote(): string
    {
        if (preg_match('/^0 @' . Gedcom::REGEX_XREF . '@ NOTE ?(.*(?:\n1 CONT ?.*)*)/', $this->gedcom . $this->pending, $match)) {
            return preg_replace("/\n1 CONT ?/", "\n", $match[1]);
        }

        return '';
    }

    /**
     * Get the contents of the note formatted according to the tree option
     *
     * @return string
     */
    public function getHtml()
    {
        $text = $this->getNote();
        if ($text === '') {
            return $text;
        }

        // Obey FORMAT_TEXT option strictly. To restore previous behaviour whereby
        // text containing markdown tables is always interpreted as markdown
        // uncomment the end of the next line
        if ($this->tree()->getPreference('FORMAT_TEXT') === 'markdown') { // || preg_match('/\| ? -{3,}/', $text) === 1) {
            $paragraphs = '';
            // Split on "\n" except where the line is part of a markdown table (starts and ends with |)
            $note_paras = preg_split('/(?<!^\|)(?<!\|)\n/', $text);
            if ($note_paras !== false) {
                foreach (array_filter($note_paras) as $note_para) {
                    $paragraphs .= Registry::markdownFactory()->markdown($this->tree())->convertToHtml($note_para);
                }
            }
            return '<div class="markdown">' . $paragraphs . '</div>';
        }

        return '<div>' . nl2br($text) . '</div>';
    }

    /**
     * Each object type may have its own special rules, and re-implement this function.
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        // Hide notes if they are attached to private records
        $linked_ids = DB::table('link')
            ->where('l_file', '=', $this->tree->id())
            ->where('l_to', '=', $this->xref)
            ->pluck('l_from');

        foreach ($linked_ids as $linked_id) {
            $linked_record = Registry::gedcomRecordFactory()->make($linked_id, $this->tree);
            if ($linked_record instanceof GedcomRecord && !$linked_record->canShow($access_level)) {
                return false;
            }
        }

        // Apply default behavior
        return parent::canShowByType($access_level);
    }

    /**
     * Create a name for this note - apply (and remove) markup, then take
     * a maximum of NAME_LENGTH characters from the first non-empty line.
     *
     * @return void
     */
    public function extractNames(): void
    {
        if ($this->tree->getPreference('FORMAT_TEXT') === 'markdown') {
            $text = Registry::markdownFactory()->markdown()->convertToHtml($this->getNote());
        } else {
            $text = Registry::markdownFactory()->autolink()->convertToHtml($this->getNote());
        }


        // Take the first line
        [$text] = explode("\n", strip_tags(trim($text)));


        if ($text !== '') {
            $text = htmlspecialchars_decode($text, ENT_QUOTES | ENT_SUBSTITUTE);
            if (mb_strlen($text) >= self::NAME_LENGTH) {
                // truncate $text to words closest to NAME_LENGTH
                $last_space = (int) mb_strrpos(mb_substr($text, 0, self::NAME_LENGTH), " ", 0);
                $text = mb_substr($text, 0, $last_space) . I18N::translate('…');
            }

            $this->addName('NOTE', $text, $this->gedcom());
        }
    }
}
