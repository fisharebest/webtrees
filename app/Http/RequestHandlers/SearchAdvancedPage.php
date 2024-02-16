<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_fill_keys;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function implode;
use function strtr;

/**
 * Search for genealogy data
 */
class SearchAdvancedPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private const DEFAULT_ADVANCED_FIELDS = [
        'INDI:NAME:GIVN',
        'INDI:NAME:SURN',
        'INDI:BIRT:DATE',
        'INDI:BIRT:PLAC',
        'FAM:MARR:DATE',
        'FAM:MARR:PLAC',
        'INDI:DEAT:DATE',
        'INDI:DEAT:PLAC',
        'FATHER:NAME:GIVN',
        'FATHER:NAME:SURN',
        'MOTHER:NAME:GIVN',
        'MOTHER:NAME:SURN',
    ];

    private const OTHER_ADVANCED_FIELDS = [
        'INDI:ADOP:DATE',
        'INDI:ADOP:PLAC',
        'INDI:AFN',
        'INDI:BAPL:DATE',
        'INDI:BAPL:PLAC',
        'INDI:BAPM:DATE',
        'INDI:BAPM:PLAC',
        'INDI:BARM:DATE',
        'INDI:BARM:PLAC',
        'INDI:BASM:DATE',
        'INDI:BASM:PLAC',
        'INDI:BLES:DATE',
        'INDI:BLES:PLAC',
        'INDI:BURI:DATE',
        'INDI:BURI:PLAC',
        'INDI:CENS:DATE',
        'INDI:CENS:PLAC',
        'INDI:CHAN:DATE',
        'INDI:CHAN:_WT_USER',
        'INDI:CHR:DATE',
        'INDI:CHR:PLAC',
        'INDI:CREM:DATE',
        'INDI:CREM:PLAC',
        'INDI:DSCR',
        'INDI:EMIG:DATE',
        'INDI:EMIG:PLAC',
        'INDI:ENDL:DATE',
        'INDI:ENDL:PLAC',
        'INDI:EVEN',
        'INDI:EVEN:TYPE',
        'INDI:EVEN:DATE',
        'INDI:EVEN:PLAC',
        'INDI:FACT',
        'INDI:FACT:TYPE',
        'INDI:FCOM:DATE',
        'INDI:FCOM:PLAC',
        'INDI:IMMI:DATE',
        'INDI:IMMI:PLAC',
        'INDI:NAME:NICK',
        'INDI:NAME:_MARNM',
        'INDI:NAME:_HEB',
        'INDI:NAME:ROMN',
        'INDI:NATI',
        'INDI:NATU:DATE',
        'INDI:NATU:PLAC',
        'INDI:NOTE',
        'INDI:OCCU',
        'INDI:ORDN:DATE',
        'INDI:ORDN:PLAC',
        'INDI:REFN',
        'INDI:RELI',
        'INDI:RESI:DATE',
        'INDI:RESI:EMAIL',
        'INDI:RESI:PLAC',
        'INDI:SLGC:DATE',
        'INDI:SLGC:PLAC',
        'INDI:TITL',
        'FAM:DIV:DATE',
        'FAM:SLGS:DATE',
        'FAM:SLGS:PLAC',
    ];

    private SearchService $search_service;

    /**
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree           = Validator::attributes($request)->tree();
        $default_fields = array_fill_keys(self::DEFAULT_ADVANCED_FIELDS, '');
        $fields         = Validator::queryParams($request)->array('fields') ?: $default_fields;
        $modifiers      = Validator::queryParams($request)->array('modifiers');
        $other_fields   = $this->otherFields($fields);
        $date_options   = $this->dateOptions();
        $name_options   = $this->nameOptions();

        $fields = array_map(static fn (string $x): string => preg_replace('/^\s+|\s+$/uD', '', $x), $fields);

        $search_fields = array_filter($fields, static fn (string $x): bool => $x !== '');

        if ($search_fields !== []) {
            // Log search requests for visitors
            if (Auth::id() === null) {
                $fn      = static fn (string $x, string $y): string => $x . '=' . $y;
                $message = 'Advanced: ' . implode(', ', array_map($fn, array_keys($search_fields), $search_fields));
                Log::addSearchLog($message, [$tree]);
            }
            $individuals = $this->search_service->searchIndividualsAdvanced([$tree], $search_fields, $modifiers);
        } else {
            $individuals = new Collection();
        }

        $title = I18N::translate('Advanced search');

        return $this->viewResponse('search-advanced-page', [
            'date_options' => $date_options,
            'fields'       => $fields,
            'field_labels' => $this->fieldLabels(),
            'individuals'  => $individuals,
            'modifiers'    => $modifiers,
            'name_options' => $name_options,
            'other_fields' => $other_fields,
            'title'        => $title,
            'tree'         => $tree,
        ]);
    }

    /**
     * Extra search fields to add to the advanced search
     *
     * @param array<string> $fields
     *
     * @return array<string,string>
     */
    private function otherFields(array $fields): array
    {
        $default_facts = new Collection(self::OTHER_ADVANCED_FIELDS);

        $comparator = static function (string $x, string $y): int {
            $element_factory = Registry::elementFactory();

            $label1 = $element_factory->make(strtr($x, [':DATE' => '', ':PLAC' => '', ':TYPE' => '']))->label();
            $label2 = $element_factory->make(strtr($y, [':DATE' => '', ':PLAC' => '', ':TYPE' => '']))->label();

            return I18N::comparator()($label1, $label2) ?: strcmp($x, $y);
        };

        return $default_facts
            ->reject(fn (string $field): bool => array_key_exists($field, $fields))
            ->sort($comparator)
            ->mapWithKeys(fn (string $fact): array => [$fact => Registry::elementFactory()->make($fact)->label()])
            ->all();
    }


    /**
     * We use some pseudo-GEDCOM tags for some of our fields.
     *
     * @return array<string,string>
     */
    private function fieldLabels(): array
    {
        $return = [];

        foreach (array_merge(self::OTHER_ADVANCED_FIELDS, self::DEFAULT_ADVANCED_FIELDS) as $field) {
            $tmp = strtr($field, ['MOTHER:' => 'INDI:', 'FATHER:' => 'INDI:']);
            $return[$field] = Registry::elementFactory()->make($tmp)->label();
        }


        return $return;
    }

    /**
     * For the advanced search
     *
     * @return array<string>
     */
    private function dateOptions(): array
    {
        return [
            0  => I18N::translate('Exact date'),
            1  => I18N::plural('±%s year', '±%s years', 1, I18N::number(1)),
            2  => I18N::plural('±%s year', '±%s years', 2, I18N::number(2)),
            5  => I18N::plural('±%s year', '±%s years', 5, I18N::number(5)),
            10 => I18N::plural('±%s year', '±%s years', 10, I18N::number(10)),
            20 => I18N::plural('±%s year', '±%s years', 20, I18N::number(20)),
        ];
    }

    /**
     * For the advanced search
     *
     * @return array<string>
     */
    private function nameOptions(): array
    {
        return [
            'EXACT'    => I18N::translate('Exact'),
            'BEGINS'   => I18N::translate('Begins with'),
            'CONTAINS' => I18N::translate('Contains'),
            'SDX'      => I18N::translate('Sounds like'),
        ];
    }
}
