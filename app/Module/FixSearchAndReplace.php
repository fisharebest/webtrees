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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\DataFixService;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Throwable;

use function addcslashes;
use function asort;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function view;

/**
 * Class FixSearchAndReplace
 */
class FixSearchAndReplace extends AbstractModule implements ModuleDataFixInterface
{
    use ModuleDataFixTrait;

    // A regular expression that never matches.
    private const INVALID_REGEX = '/(?!)/';

    /** @var DataFixService */
    private $data_fix_service;

    /**
     * FixMissingDeaths constructor.
     *
     * @param DataFixService $data_fix_service
     */
    public function __construct(DataFixService $data_fix_service)
    {
        $this->data_fix_service = $data_fix_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Search and replace');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of a “Data fix” module */
        return I18N::translate('Search and replace text, using simple searches or advanced pattern matching.');
    }

    /**
     * Options form.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function fixOptions(Tree $tree): string
    {
        $methods = [
            'exact'     => I18N::translate('Match the exact text, even if it occurs in the middle of a word.'),
            'words'     => I18N::translate('Match the exact text, unless it occurs in the middle of a word.'),
            'wildcards' => I18N::translate('Use a “?” to match a single character, use “*” to match zero or more characters.'),
            /* I18N: https://en.wikipedia.org/wiki/Regular_expression */
            'regex'     => I18N::translate('Regular expression'),
        ];

        $types = [
            Family::RECORD_TYPE     => I18N::translate('Families'),
            Individual::RECORD_TYPE => I18N::translate('Individuals'),
            Location::RECORD_TYPE   => I18N::translate('Locations'),
            Media::RECORD_TYPE      => I18N::translate('Media objects'),
            Note::RECORD_TYPE       => I18N::translate('Notes'),
            Repository::RECORD_TYPE => I18N::translate('Repositories'),
            Source::RECORD_TYPE     => I18N::translate('Sources'),
            Submitter::RECORD_TYPE  => I18N::translate('Submitters'),
        ];

        asort($types);

        return view('modules/fix-search-and-replace/options', [
            'default_method' => 'exact',
            'default_type'   => Individual::RECORD_TYPE,
            'methods'        => $methods,
            'types'          => $types,
        ]);
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function familiesToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Family::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('families')->where('f_file', '=', $tree->id());
        $this->recordQuery($query, 'f_gedcom', $params);

        return $query->pluck('f_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function individualsToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Individual::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('individuals')
            ->where('i_file', '=', $tree->id());

        $this->recordQuery($query, 'i_gedcom', $params);

        return $query->pluck('i_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function locationsToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Note::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE);

        $this->recordQuery($query, 'o_gedcom', $params);

        return $query->pluck('o_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function mediaToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Media::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('media')
            ->where('m_file', '=', $tree->id());

        $this->recordQuery($query, 'm_gedcom', $params);

        return $query->pluck('m_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function notesToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Note::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Note::RECORD_TYPE);

        $this->recordQuery($query, 'o_gedcom', $params);

        return $query->pluck('o_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function repositoriesToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Repository::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', Repository::RECORD_TYPE);

        $this->recordQuery($query, 'o_gedcom', $params);

        return $query->pluck('o_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function sourcesToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Source::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = $this->sourcesToFixQuery($tree, $params);

        $this->recordQuery($query, 's_gedcom', $params);

        return $query->pluck('s_id');
    }

    /**
     * A list of all records that need examining.  This may include records
     * that do not need updating, if we can't detect this quickly using SQL.
     *
     * @param Tree                 $tree
     * @param array<string,string> $params
     *
     * @return Collection<string>|null
     */
    protected function submittersToFix(Tree $tree, array $params): ?Collection
    {
        if ($params['type'] !== Submitter::RECORD_TYPE || $params['search'] === '') {
            return null;
        }

        $query = $this->submittersToFixQuery($tree, $params);

        $this->recordQuery($query, 'o_gedcom', $params);

        return $query->pluck('o_id');
    }

    /**
     * Does a record need updating?
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return bool
     */
    public function doesRecordNeedUpdate(GedcomRecord $record, array $params): bool
    {
        return preg_match($this->createRegex($params), $record->gedcom()) === 1;
    }

    /**
     * Show the changes we would make
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    public function previewUpdate(GedcomRecord $record, array $params): string
    {
        $old = $record->gedcom();
        $new = $this->updateGedcom($record, $params);

        return $this->data_fix_service->gedcomDiff($record->tree(), $old, $new);
    }

    /**
     * Fix a record
     *
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return void
     */
    public function updateRecord(GedcomRecord $record, array $params): void
    {
        $record->updateRecord($this->updateGedcom($record, $params), false);
    }

    /**
     * @param GedcomRecord         $record
     * @param array<string,string> $params
     *
     * @return string
     */
    private function updateGedcom(GedcomRecord $record, array $params): string
    {
        // Allow "\n" to indicate a line-feed in replacement text.
        // Back-references such as $1, $2 are handled automatically.
        $replace = strtr($params['replace'], ['\n' => "\n"]);

        $regex = $this->createRegex($params);

        return preg_replace($regex, $replace, $record->gedcom());
    }

    /**
     * Create a regular expression from the search pattern.
     *
     * @param array<string,string> $params
     *
     * @return string
     */
    private function createRegex(array $params): string
    {
        $search = $params['search'];
        $method = $params['method'];
        $case   = $params['case'];

        switch ($method) {
            case 'exact':
                return '/' . preg_quote($search, '/') . '/u' . $case;

            case 'words':
                return '/\b' . preg_quote($search, '/') . '\b/u' . $case;

            case 'wildcards':
                return '/\b' . strtr(preg_quote($search, '/'), ['\*' => '.*', '\?' => '.']) . '\b/u' . $case;

            case 'regex':
                $regex = '/' . addcslashes($search, '/') . '/u' . $case;

                try {
                    // A valid regex on an empty string returns zero.
                    // An invalid regex on an empty string returns false and throws a warning.
                    preg_match($regex, '');
                } catch (Throwable $ex) {
                    $regex = self::INVALID_REGEX;
                }

                return $regex;
        }

        throw new HttpNotFoundException();
    }

    /**
     * Create a regular expression from the search pattern.
     *
     * @param Builder              $query
     * @param string               $column
     * @param array<string,string> $params
     *
     * @return void
     */
    private function recordQuery(Builder $query, string $column, array $params): void
    {
        $search = $params['search'];
        $method = $params['method'];
        $like   = '%' . addcslashes($search, '\\%_') . '%';

        switch ($method) {
            case 'exact':
            case 'words':
                $query->where($column, 'LIKE', $like);
                break;

            case 'wildcards':
                $like = strtr($like, ['?' => '_', '*' => '%']);
                $query->where($column, 'LIKE', $like);
                break;

            case 'regex':
                // Substituting newlines seems to be necessary on *some* versions
                //.of MySQL (e.g. 5.7), and harmless on others (e.g. 8.0).
                $search = strtr($search, ['\n' => "\n"]);

                switch (DB::connection()->getDriverName()) {
                    case 'sqlite':
                    case 'mysql':
                        $query->where($column, 'REGEXP', $search);
                        break;

                    case 'pgsql':
                        $query->where($column, '~', $search);
                        break;

                    case 'sqlsvr':
                        // Not available
                        break;
                }
                break;
        }
    }
}
