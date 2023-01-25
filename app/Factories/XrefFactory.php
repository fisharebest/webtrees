<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\XrefFactoryInterface;
use Fisharebest\Webtrees\Site;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Make an XREF.
 */
class XrefFactory implements XrefFactoryInterface
{
    /**
     * Create a new XREF.
     *
     * @param string $record_type
     *
     * @return string
     */
    public function make(string $record_type): string
    {
        return $this->generate('X', '');
    }

    /**
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    protected function generate(string $prefix, string $suffix): string
    {
        // Lock the row, so that only one new XREF may be generated at a time.
        $num = (int) DB::table('site_setting')
            ->where('setting_name', '=', 'next_xref')
            ->lockForUpdate()
            ->value('setting_value');

        $increment = 1.0;

        do {
            $num += (int) $increment;

            // This exponential increment allows us to scan over large blocks of
            // existing data in a reasonable time.
            $increment *= 1.01;

            $xref = $prefix . $num . $suffix;

            // Records may already exist with this sequence number.
            $already_used =
                DB::table('individuals')->where('i_id', '=', $xref)->exists() ||
                DB::table('families')->where('f_id', '=', $xref)->exists() ||
                DB::table('sources')->where('s_id', '=', $xref)->exists() ||
                DB::table('media')->where('m_id', '=', $xref)->exists() ||
                DB::table('other')->where('o_id', '=', $xref)->exists() ||
                DB::table('change')->where('xref', '=', $xref)->exists();
        } while ($already_used);

        Site::setPreference('next_xref', (string) $num);

        return $xref;
    }
}
