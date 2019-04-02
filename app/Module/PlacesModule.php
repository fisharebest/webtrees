<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class PlacesMapModule
 */
class PlacesModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    private static $map_providers  = null;
    private static $map_selections = null;

    public const ICONS = [
        'BIRT' => ['color' => 'Crimson', 'name' => 'birthday-cake'],
        'MARR' => ['color' => 'Green', 'name' => 'venus-mars'],
        'DEAT' => ['color' => 'Black', 'name' => 'plus'],
        'CENS' => ['color' => 'MediumBlue', 'name' => 'users'],
        'RESI' => ['color' => 'MediumBlue', 'name' => 'home'],
        'OCCU' => ['color' => 'MediumBlue', 'name' => 'briefcase'],
        'GRAD' => ['color' => 'MediumBlue', 'name' => 'graduation-cap'],
        'EDUC' => ['color' => 'MediumBlue', 'name' => 'university'],
    ];

    public const DEFAULT_ICON = ['color' => 'Gold', 'name' => 'bullseye '];

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
        /* I18N: Description of the “OSM” module */
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

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/places/tab', [
            'data' => $this->getMapData($individual),
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
            $location = new Location($fact->place()->gedcomName());

            // Use the co-ordinates from the fact (if they exist).
            $latitude  = $fact->latitude();
            $longitude = $fact->longitude();

            // Use the co-ordinates from the location otherwise.
            if ($latitude === 0.0 && $longitude === 0.0) {
                $latitude  = $location->latitude();
                $longitude = $location->longitude();
            }

            $icon = self::ICONS[$fact->getTag()] ?? self::DEFAULT_ICON;

            if ($latitude !== 0.0 || $longitude !== 0.0) {
                $geojson['features'][] = [
                    'type'       => 'Feature',
                    'id'         => $id,
                    'valid'      => true,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [$longitude, $latitude],
                    ],
                    'properties' => [
                        'polyline' => null,
                        'icon'     => $icon,
                        'tooltip'  => strip_tags($fact->place()->fullName()),
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
            $tag  = GedcomTag::getLabel('_BIRT_CHIL', $record);
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

    /**
     * @param Individual $individual
     *
     * @return Collection
     * @return Fact[]
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
                if ($childsBirth && $childsBirth->place()->gedcomName() !== '') {
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
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getProviderStylesAction(ServerRequestInterface $request): ResponseInterface
    {
        $styles = $this->getMapProviderData($request);

        return response($styles);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array|null
     */
    private function getMapProviderData(ServerRequestInterface $request): ?array
    {
        if (self::$map_providers === null) {
            $providersFile        = WT_ROOT . Webtrees::MODULES_PATH . 'openstreetmap/providers/providers.xml';
            self::$map_selections = [
                'provider' => $this->getPreference('provider', 'openstreetmap'),
                'style'    => $this->getPreference('provider_style', 'mapnik'),
            ];

            try {
                $xml = simplexml_load_string(file_get_contents($providersFile));
                // need to convert xml structure into arrays & strings
                foreach ($xml as $provider) {
                    $style_keys = array_map(
                        function (string $item): string {
                            return preg_replace('/[^a-z\d]/i', '', strtolower($item));
                        },
                        (array) $provider->styles
                    );

                    $key = preg_replace('/[^a-z\d]/i', '', strtolower((string) $provider->name));

                    self::$map_providers[$key] = [
                        'name'   => (string) $provider->name,
                        'styles' => array_combine($style_keys, (array) $provider->styles),
                    ];
                }
            } catch (Exception $ex) {
                // Default provider is OpenStreetMap
                self::$map_selections = [
                    'provider' => 'openstreetmap',
                    'style'    => 'mapnik',
                ];
                self::$map_providers  = [
                    'openstreetmap' => [
                        'name'   => 'OpenStreetMap',
                        'styles' => ['mapnik' => 'Mapnik'],
                    ],
                ];
            }
        }

        //Ugly!!!
        switch ($request->get('action')) {
            case 'BaseData':
                $varName = (self::$map_selections['style'] === '') ? '' : self::$map_providers[self::$map_selections['provider']]['styles'][self::$map_selections['style']];
                $payload = [
                    'selectedProvIndex' => self::$map_selections['provider'],
                    'selectedProvName'  => self::$map_providers[self::$map_selections['provider']]['name'],
                    'selectedStyleName' => $varName,
                ];
                break;
            case 'ProviderStyles':
                $provider = $request->get('provider', 'openstreetmap');
                $payload  = self::$map_providers[$provider]['styles'];
                break;
            case 'AdminConfig':
                $providers = [];
                foreach (self::$map_providers as $key => $provider) {
                    $providers[$key] = $provider['name'];
                }
                $payload = [
                    'providers'     => $providers,
                    'selectedProv'  => self::$map_selections['provider'],
                    'styles'        => self::$map_providers[self::$map_selections['provider']]['styles'],
                    'selectedStyle' => self::$map_selections['style'],
                ];
                break;
            default:
                $payload = null;
        }

        return $payload;
    }
}
