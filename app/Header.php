<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees;

use Closure;
use Exception;
use Fisharebest\Webtrees\Http\RequestHandlers\HeaderPage;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * A GEDCOM header (HEAD) object.
 */
class Header extends GedcomRecord
{
    public const RECORD_TYPE = 'HEAD';

    protected const ROUTE_NAME = HeaderPage::class;

    /**
     * A closure which will create a record from a database row.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::header()
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return Factory::header()->mapper($tree);
    }

    /**
     * Get an instance of a header object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::header()
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Header|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?Header
    {
        return Factory::header()->make($xref, $tree, $gedcom);
    }

    /**
     * Generate a private version of this record
     *
     * @param int $access_level
     *
     * @return string
     */
    protected function createPrivateGedcomRecord(int $access_level): string
    {
        return '0 HEAD' . $this->xref . "@ SUBM\n1 NAME " . I18N::translate('Private');
    }

    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $this->getAllNames[] = [
            'type'   => static::RECORD_TYPE,
            'sort'   => I18N::translate('Header'),
            'full'   => I18N::translate('Header'),
            'fullNN' => I18N::translate('Header'),
        ];
    }
}
