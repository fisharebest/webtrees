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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_fill_keys;
use function array_filter;
use function array_key_exists;
use function assert;
use function explode;

/**
 * Search for genealogy data
 */
class SearchAdvancedPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private const DEFAULT_ADVANCED_FIELDS = [
        'NAME:GIVN',
        'NAME:SURN',
        'BIRT:DATE',
        'BIRT:PLAC',
        'FAMS:MARR:DATE',
        'FAMS:MARR:PLAC',
        'DEAT:DATE',
        'DEAT:PLAC',
        'FAMC:HUSB:NAME:GIVN',
        'FAMC:HUSB:NAME:SURN',
        'FAMC:WIFE:NAME:GIVN',
        'FAMC:WIFE:NAME:SURN',
    ];

    private const OTHER_ADVANCED_FIELDS = [
        'ADOP:DATE',
        'ADOP:PLAC',
        'AFN',
        'BAPL:DATE',
        'BAPL:PLAC',
        'BAPM:DATE',
        'BAPM:PLAC',
        'BARM:DATE',
        'BARM:PLAC',
        'BASM:DATE',
        'BASM:PLAC',
        'BLES:DATE',
        'BLES:PLAC',
        'BURI:DATE',
        'BURI:PLAC',
        'CENS:DATE',
        'CENS:PLAC',
        'CHAN:DATE',
        'CHAN:_WT_USER',
        'CHR:DATE',
        'CHR:PLAC',
        'CREM:DATE',
        'CREM:PLAC',
        'DSCR',
        'EMIG:DATE',
        'EMIG:PLAC',
        'ENDL:DATE',
        'ENDL:PLAC',
        'EVEN',
        'EVEN:TYPE',
        'EVEN:DATE',
        'EVEN:PLAC',
        'FACT',
        'FACT:TYPE',
        'FAMS:CENS:DATE',
        'FAMS:CENS:PLAC',
        'FAMS:DIV:DATE',
        'FAMS:NOTE',
        'FAMS:SLGS:DATE',
        'FAMS:SLGS:PLAC',
        'FCOM:DATE',
        'FCOM:PLAC',
        'IMMI:DATE',
        'IMMI:PLAC',
        'NAME:NICK',
        'NAME:_MARNM',
        'NAME:_HEB',
        'NAME:ROMN',
        'NATI',
        'NATU:DATE',
        'NATU:PLAC',
        'NOTE',
        'OCCU',
        'ORDN:DATE',
        'ORDN:PLAC',
        'REFN',
        'RELI',
        'RESI:DATE',
        'RESI:EMAIL',
        'RESI:PLAC',
        'SLGC:DATE',
        'SLGC:PLAC',
        'TITL',
    ];

    private SearchService $search_service;

    /**
     * SearchController constructor.
     *
     * @param SearchService $search_service
     */
    public function __construct(SearchService $search_service)
    {
        $this->search_service = $search_service;
    }

    /**
     * A structured search.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $default_fields = array_fill_keys(self::DEFAULT_ADVANCED_FIELDS, '');

        $params = $request->getQueryParams();

        $fields    = $params['fields'] ?? $default_fields;
        $modifiers = $params['modifiers'] ?? [];

        $other_fields = $this->otherFields($tree, $fields);
        $date_options = $this->dateOptions();
        $name_options = $this->nameOptions();

        if (array_filter($fields) !== []) {
            $individuals = $this->search_service->searchIndividualsAdvanced([$tree], $fields, $modifiers);
        } else {
            $individuals = new Collection();
        }

        $title = I18N::translate('Advanced search');

        return $this->viewResponse('search-advanced-page', [
            'date_options' => $date_options,
            'fields'       => $fields,
            'field_labels' => $this->customFieldLabels(),
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
     * @param Tree     $tree
     * @param string[] $fields
     *
     * @return array<string,string>
     */
    private function otherFields(Tree $tree, array $fields): array
    {
        $default_facts     = new Collection(self::OTHER_ADVANCED_FIELDS);
        $indi_facts_add    = new Collection(explode(',', $tree->getPreference('INDI_FACTS_ADD')));
        $indi_facts_unique = new Collection(explode(',', $tree->getPreference('INDI_FACTS_UNIQUE')));

        return $default_facts
            ->merge($indi_facts_add)
            ->merge($indi_facts_unique)
            ->unique()
            ->reject(static function (string $field) use ($fields): bool {
                return
                    array_key_exists($field, $fields) ||
                    array_key_exists($field . ':DATE', $fields) ||
                    array_key_exists($field . ':PLAC', $fields);
            })
            ->mapWithKeys(static function (string $fact): array {
                return [$fact => GedcomTag::getLabel($fact)];
            })
            ->all();
    }


    /**
     * We use some pseudo-GEDCOM tags for some of our fields.
     *
     * @return array<string,string>
     */
    private function customFieldLabels(): array
    {
        return [
            'FAMS:DIV:DATE'       => I18N::translate('Date of divorce'),
            'FAMS:NOTE'           => I18N::translate('Spouse note'),
            'FAMS:SLGS:DATE'      => I18N::translate('Date of LDS spouse sealing'),
            'FAMS:SLGS:PLAC'      => I18N::translate('Place of LDS spouse sealing'),
            'FAMS:MARR:DATE'      => I18N::translate('Date of marriage'),
            'FAMS:MARR:PLAC'      => I18N::translate('Place of marriage'),
            'FAMC:HUSB:NAME:GIVN' => I18N::translate('Given names'),
            'FAMC:HUSB:NAME:SURN' => I18N::translate('Surname'),
            'FAMC:WIFE:NAME:GIVN' => I18N::translate('Given names'),
            'FAMC:WIFE:NAME:SURN' => I18N::translate('Surname'),
        ];
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
