<?php
declare(strict_types = 1);
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
namespace Fisharebest\Webtrees\Functions;

use Fisharebest\Webtrees\Database;

/**
 * Class FunctionsDb - common functions
 */
class FunctionsDb
{
    /**
     * Fetch all records linked to a record - when deleting an object, we must
     * also delete all links to it.
     *
     * @param string $xref
     * @param int    $gedcom_id
     *
     * @return string[]
     */
    public static function fetchAllLinks($xref, $gedcom_id): array
    {
        return
            Database::prepare(
                "SELECT l_from FROM `##link` WHERE l_file = ? AND l_to = ?" .
                " UNION " .
                "SELECT xref FROM `##change` WHERE status = 'pending' AND gedcom_id = ? AND new_gedcom LIKE" .
                " CONCAT('%@', ?, '@%') AND new_gedcom NOT LIKE CONCAT('0 @', ?, '@%')"
            )->execute([
                $gedcom_id,
                $xref,
                $gedcom_id,
                $xref,
                $xref,
            ])->fetchOneColumn();
    }
}
