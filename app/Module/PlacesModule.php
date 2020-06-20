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

namespace Fisharebest\Webtrees\Module;

use Exception;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Site;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Class PlacesMapModule
 */
class PlacesModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    protected const ICONS = [
        'BIRT' => ['color' => 'pink', 'name' => 'baby-carriage fas'],
        'BAPM' => ['color' => 'pink', 'name' => 'water fas'],
        'BARM' => ['color' => 'pink', 'name' => 'star-of-david fas'],
        'BASM' => ['color' => 'pink', 'name' => 'star-of-david fas'],
        'CHR'  => ['color' => 'pink', 'name' => 'water fas'],
        'CHRA' => ['color' => 'pink', 'name' => 'water fas'],
        'MARR' => ['color' => 'green', 'name' => 'infinity fas'],
        'DEAT' => ['color' => 'black', 'name' => 'times fas'],
        'BURI' => ['color' => 'purple', 'name' => 'times fas'],
        'CREM' => ['color' => 'black', 'name' => 'times fas'],
        'CENS' => ['color' => 'cyan', 'name' => 'list fas'],
        'RESI' => ['color' => 'cyan', 'name' => 'home fas'],
        'OCCU' => ['color' => 'cyan', 'name' => 'industry fas'],
        'GRAD' => ['color' => 'violet', 'name' => 'university fas'],
        'EDUC' => ['color' => 'violet', 'name' => 'university fas'],
    ];

    protected const DEFAULT_ICON = ['color' => 'gold', 'name' => 'bullseye fas'];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Places');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Places” module */
        return I18N::translate('Show the location of events on a map.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 8;
    }

    /**
     * Is this tab empty? If so, we don't always need to display it.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasTabContent(Individual $individual): bool
    {
        return Site::getPreference('map-provider') !== '' &&
            $this->getMapData($individual)->features !== [];
    }

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /**
     * Can this tab load asynchronously?
     *
     * @return bool
     */
    public function canLoadAjax(): bool
    {
        return true;
    }

    /**
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/places/tab', [
            'data'     => $this->getMapData($individual),
            'provider' => [
                'url'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'options' => [
                    'attribution' => '<a href="https://www.openstreetmap.org/copyright">&copy; OpenStreetMap</a> contributors',
                    'max_zoom'    => 19
                ]
            ]
        ]);
    }

    /**
     * @param Individual $indi
     *
     * @return stdClass
     */
    private function getMapData(Individual $indi): stdClass
    {
        $facts = $this->getPersonalFacts($indi);

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($facts as $id => $fact) {
            $location = new PlaceLocation($fact->place()->gedcomName());

            // Use the co-ordinates from the fact (if they exist).
            $latitude  = $fact->latitude();
            $longitude = $fact->longitude();

            // Use the co-ordinates from the location otherwise.
            if ($latitude === 0.0 && $longitude === 0.0) {
                $latitude  = $location->latitude();
                $longitude = $location->longitude();
            }

            if ($latitude !== 0.0 || $longitude !== 0.0) {
                $geojson['features'][] = [
                    'type'       => 'Feature',
                    'id'         => $id,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [$longitude, $latitude],
                    ],
                    'properties' => [
                        'icon'     => static::ICONS[$fact->getTag()] ?? static::DEFAULT_ICON,
                        'tooltip'  => $fact->place()->gedcomName(),
                        'summary'  => view('modules/places/event-sidebar', $this->summaryData($indi, $fact)),
                        'zoom'     => $location->zoom(),
                    ],
                ];
            }
        }

        return (object) $geojson;
    }

    /**
     * @param Individual $individual
     *
     * @return Collection<Fact>
     * @throws Exception
     */
    private function getPersonalFacts(Individual $individual): Collection
    {
        $facts = $individual->facts();

        foreach ($individual->spouseFamilies() as $family) {
            $facts = $facts->merge($family->facts());
            // Add birth of children from this family to the facts array
            foreach ($family->children() as $child) {
                $childsBirth = $child->facts(['BIRT'])->first();
                if ($childsBirth instanceof Fact && $childsBirth->place()->gedcomName() !== '') {
                    $facts->push($childsBirth);
                }
            }
        }

        $facts = Fact::sortFacts($facts);

        return $facts->filter(static function (Fact $item): bool {
            return $item->place()->gedcomName() !== '';
        });
    }

    /**
     * @param Individual $individual
     * @param Fact       $fact
     *
     * @return mixed[]
     */
    private function summaryData(Individual $individual, Fact $fact): array
    {
        $record = $fact->record();
        $name   = '';
        $url    = '';
        $tag    = $fact->label();

        if ($record instanceof Family) {
            // Marriage
            $spouse = $record->spouse($individual);
            if ($spouse instanceof Individual) {
                $url  = $spouse->url();
                $name = $spouse->fullName();
            }
        } elseif ($record !== $individual) {
            // Birth of a child
            $url  = $record->url();
            $name = $record->fullName();
            $tag  = I18N::translate('Birth of a child');
        }

        return [
            'tag'    => $tag,
            'url'    => $url,
            'name'   => $name,
            'value'  => $fact->value(),
            'date'   => $fact->date()->display(true),
            'place'  => $fact->place(),
            'addtag' => false,
        ];
    }
}
