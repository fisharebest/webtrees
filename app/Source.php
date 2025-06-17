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

use Fisharebest\Webtrees\Http\RequestHandlers\SourcePage;

/**
 * A GEDCOM source (SOUR) object.
 */
class Source extends GedcomRecord
{
    public const string RECORD_TYPE = 'SOUR';

    protected const string ROUTE_NAME = SourcePage::class;

    /**
     * Each object type may have its own special rules, and re-implement this function.
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        // Hide sources if they are attached to private repositories ...
        preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
        foreach ($matches[1] as $match) {
            $repo = Registry::repositoryFactory()->make($match, $this->tree);
            if ($repo instanceof Repository && !$repo->canShow($access_level)) {
                return false;
            }
        }

        // ... otherwise apply default behavior
        return parent::canShowByType($access_level);
    }

    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $this->extractNamesFromFacts(1, 'TITL', $this->facts(['TITL']));
    }

    /**
     * Lock the database row, to prevent concurrent edits.
     */
    public function lock(): void
    {
        DB::table('sources')
            ->where('s_file', '=', $this->tree->id())
            ->where('s_id', '=', $this->xref())
            ->lockForUpdate()
            ->get();
    }
}
