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

use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class BatchUpdateSearchReplacePlugin Batch Update plugin: search/replace
 */
class BatchUpdateSearchReplacePlugin extends BatchUpdateBasePlugin
{
    /** @var string Search string */
    private $search;

    /** @var string Replace string */
    private $replace;

    /** @var string simple/wildcards/regex */
    private $method;

    /** @var string Search string, converted to a regex */
    private $regex;

    /** @var string "i" for case insensitive, "" for case sensitive */
    private $case;

    /** @var string Message for bad user parameters */
    private $error;

    /**
     * User-friendly name for this plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return I18N::translate('Search and replace');
    }

    /**
     * Description / help-text for this plugin.
     *
     * @return string
     */
    public function getDescription(): string
    {
        /* I18N: Description of the “Search and replace” option of the batch update module */
        return I18N::translate('Search and replace text, using simple searches or advanced pattern matching.');
    }

    /**
     * This plugin will update all types of record.
     *
     * @return string[]
     */
    public function getRecordTypesToUpdate(): array
    {
        return [
            'INDI',
            'FAM',
            'SOUR',
            'REPO',
            'NOTE',
            'OBJE',
        ];
    }

    /**
     * Does this record need updating?
     *
     * @param GedcomRecord $record
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record): bool
    {
        $gedcom = $record->getGedcom();

        return !$this->error && preg_match('/(?:' . $this->regex . ')/mu' . $this->case, $gedcom);
    }

    /**
     * Apply any updates to this record
     *
     * @param GedcomRecord $record
     *
     * @return string
     */
    public function updateRecord(GedcomRecord $record): string
    {
        $old_gedcom = $record->getGedcom();

        // Allow "\n" to indicate a line-feed in replacement text.
        // Back-references such as $1, $2 are handled automatically.
        $new_gedcom = preg_replace('/' . $this->regex . '/mu' . $this->case, str_replace('\n', "\n", $this->replace), $old_gedcom);

        return $new_gedcom;
    }

    /**
     * Process the user-supplied options.
     *
     * @param Request $request
     */
    public function getOptions(Request $request)
    {
        parent::getOptions($request);

        $this->search  = $request->get('search', '');
        $this->replace = $request->get('replace', '');
        $this->method  = $request->get('method', 'exact');
        $this->case    = $request->get('case', '');

        $this->error = '';
        switch ($this->method) {
            case 'exact':
                $this->regex = preg_quote($this->search, '/');
                break;
            case 'words':
                $this->regex = '\b' . preg_quote($this->search, '/') . '\b';
                break;
            case 'wildcards':
                $this->regex = '\b' . str_replace([
                        '\*',
                        '\?',
                    ], [
                        '.*',
                        '.',
                    ], preg_quote($this->search, '/')) . '\b';
                break;
            case 'regex':
                $this->regex = $this->search;
                // Check for invalid regular expressions.
                // A valid regex on a null string returns zero.
                // An invalid regex on a null string returns false and throws a warning.
                try {
                    preg_match('/' . $this->search . '/', null);
                } catch (Throwable $ex) {
                    DebugBar::addThrowable($ex);

                    /* I18N: http://en.wikipedia.org/wiki/Regular_expression */
                    $this->error = '<div class="alert alert-danger">' . I18N::translate('The regular expression appears to contain an error. It can’t be used.') . '</div>';
                }
                break;
        }
    }

    /**
     * Generate a form to ask the user for options.
     *
     * @return string
     */
    public function getOptionsForm(): string
    {
        $descriptions = [
            'exact'     => I18N::translate('Match the exact text, even if it occurs in the middle of a word.'),
            'words'     => I18N::translate('Match the exact text, unless it occurs in the middle of a word.'),
            'wildcards' => I18N::translate('Use a “?” to match a single character, use “*” to match zero or more characters.'),
            /* I18N: http://en.wikipedia.org/wiki/Regular_expression */
            'regex'     => I18N::translate('Regular expressions are an advanced pattern matching technique.') . '<br>' . I18N::translate('See %s for more information.', '<a href="http://php.net/manual/regexp.reference.php">php.net/manual/regexp.reference.php</a>'),
        ];

        return
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Search text/pattern') . '</label>' .
            '<div class="col-sm-9">' .
            '<input class="form-control" name="search" size="40" value="' . e($this->search) .
            '" onchange="this.form.submit();">' .
            '</div></div>' .
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Replacement text') . '</label>' .
            '<div class="col-sm-9">' .
            '<input class="form-control" name="replace" size="40" value="' . e($this->replace) .
            '" onchange="this.form.submit();"></td></tr>' .
            '</div></div>' .
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Search method') . '</label>' .
            '<div class="col-sm-9">' .
            '<select class="form-control" name="method" onchange="this.form.submit();">' .
            '<option value="exact" ' . ($this->method == 'exact' ? 'selected' : '') . '>' . I18N::translate('Exact text') . '</option>' .
            '<option value="words" ' . ($this->method == 'words' ? 'selected' : '') . '>' . I18N::translate('Whole words only') . '</option>' .
            '<option value="wildcards" ' . ($this->method == 'wildcards' ? 'selected' : '') . '>' . I18N::translate('Wildcards') . '</option>' .
            '<option value="regex" ' . ($this->method == 'regex' ? 'selected' : '') . '>' . I18N::translate('Regular expression') . '</option>' .
            '</select>' .
            '<p class="small text-muted">' . $descriptions[$this->method] . '</p>' . $this->error .
            '</div></div>' .
            '<div class="row form-group">' .
            '<label class="col-sm-3 col-form-label">' . I18N::translate('Case insensitive') . '</label>' .
            '<div class="col-sm-9">' .
            Bootstrap4::radioButtons('case', [
                ''  => I18N::translate('no'),
                'i' => I18N::translate('yes'),
            ], ($this->case ? 'i' : ''), true, ['onchange' => 'this.form.submit();']) .
            '<p class="small text-muted">' .
            /* I18N: Help text for "Case insensitive" searches */
            I18N::translate('Match both upper and lower case letters.') . '</p>' .
            '</div></div>' .
            parent::getOptionsForm();
    }
}
