<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Factories\MarkdownFactory;
use Fisharebest\Webtrees\Http\RequestHandlers\NotePage;
use Illuminate\Support\Str;

use function explode;
use function htmlspecialchars_decode;
use function preg_match;
use function preg_replace;
use function strip_tags;

use const ENT_QUOTES;

/**
 * A GEDCOM note (NOTE) object.
 */
class Note extends GedcomRecord
{
    public const RECORD_TYPE = 'NOTE';

    protected const ROUTE_NAME = NotePage::class;

    /**
     * Get the text contents of the note
     *
     * @return string
     */
    public function getNote(): string
    {
        if (preg_match('/^0 @' . Gedcom::REGEX_XREF . '@ ' . static::RECORD_TYPE . ' ?(.*(?:\n1 CONT ?.*)*)/', $this->gedcom . $this->pending, $match)) {
            return preg_replace("/\n1 CONT ?/", "\n", $match[1]);
        }

        return '';
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
     * a maximum of 100 characters from the first non-empty line.
     *
     * @return void
     */
    public function extractNames(): void
    {
        if ($this->tree->getPreference('FORMAT_TEXT') === 'markdown') {
            $html = Registry::markdownFactory()->markdown($this->getNote());
        } else {
            $html = Registry::markdownFactory()->autolink($this->getNote());
        }

        $first_line = self::firstLineOfTextFromHtml($html);

        if ($first_line !== '') {
            $this->addName(static::RECORD_TYPE, Str::limit($first_line, 100, I18N::translate('…')), $this->gedcom());
        }
    }

    /**
     * Notes are converted to HTML for display.  We want the first line
     *
     * @param string $html
     *
     * @return string
     */
    public static function firstLineOfTextFromHtml(string $html): string
    {
        $html = strtr($html, [
            '</blockquote>' => MarkdownFactory::BREAK,
            '</h1>'         => MarkdownFactory::BREAK,
            '</h2>'         => MarkdownFactory::BREAK,
            '</h3>'         => MarkdownFactory::BREAK,
            '</h4>'         => MarkdownFactory::BREAK,
            '</h5>'         => MarkdownFactory::BREAK,
            '</h6>'         => MarkdownFactory::BREAK,
            '</li>'         => MarkdownFactory::BREAK,
            '</p>'          => MarkdownFactory::BREAK,
            '</pre>'        => MarkdownFactory::BREAK,
            '</td>'         => ' ',
            '</th>'         => ' ',
            '<hr>'          => MarkdownFactory::BREAK,
        ]);

        $html = strip_tags($html, ['br']);

        [$first] = explode(MarkdownFactory::BREAK, $html, 2);

        return htmlspecialchars_decode($first, ENT_QUOTES);
    }
}
