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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Functions\FunctionsEdit;

/**
 * Helper functions to generate markup for Datatables.
 *
 * @link https://www.datatables.net
 */
class Datatables
{
    /**
     * Generate the HTML attributes for I18N.
     *
     * @param int[] $lengths
     *
     * @return string[]
     */
    public static function languageAttributes(array $lengths = [10, 25, 100, -1]): array
    {
        $length_menu = FunctionsEdit::numericOptions($lengths);

        $language = [
            'paginate'       => [
                /* I18N: A button label, first page */
                'first'    => I18N::translate('first'),
                /* I18N: A button label, last page */
                'last'     => I18N::translate('last'),
                /* I18N: A button label, next page */
                'next'     => I18N::translate('next'),
                /* I18N: A button label, previous page */
                'previous' => I18N::translate('previous'),
            ],
            'emptyTable'     => I18N::translate('No records to display'),
            /* I18N: %s are placeholders for numbers */
            'info'           => I18N::translate('Showing %1$s to %2$s of %3$s', '_START_', '_END_', '_TOTAL_'),
            'infoEmpty'      => I18N::translate('Showing %1$s to %2$s of %3$s', 0, 0, 0),
            /* I18N: %s is a number */
            'infoFiltered'   => I18N::translate('(filtered from %s total entries)', '_MAX_'),
            /* I18N: %s is a number of records per page */
            'lengthMenu'     => I18N::translate('Display %s', '_MENU_'),
            'loadingRecords' => I18N::translate('Loading…'),
            'processing'     => I18N::translate('Calculating…'),
            'search'         => I18N::translate('Filter'),
            'zeroRecords'    => I18N::translate('No records to display'),
        ];

        return [
            'data-language'    => json_encode($language),
            'data-length-menu' => json_encode([
                array_keys($length_menu),
                array_values($length_menu),
            ]),
        ];
    }

    /**
     * Generate the HTML attributes for a table of events.
     *
     * @return string
     */
    public static function eventTableAttributes(): string
    {
        return Html::attributes([
                'class'           => 'table table-bordered table-sm datatables table-event',
                'data-columns'    => '[null, null, null, null]',
                'data-info'       => 'false',
                'data-paging'     => 'false',
                'data-searching'  => 'false',
                'data-state-save' => 'true',
            ] + self::languageAttributes());
    }

    /**
     * Generate the HTML attributes for a table of given names.
     *
     * @return string
     */
    public static function givenNameTableAttributes(): string
    {
        return Html::attributes([
            'class'           => 'table table-bordered table-sm datatables table-given-name',
            'data-columns'    => '[null, null]',
            'data-info'       => 'false',
            'data-paging'     => 'false',
            'data-searching'  => 'false',
            'data-state-save' => 'true',
            'data-order'      => '[[1, "desc"]]',
        ]);
    }
}
