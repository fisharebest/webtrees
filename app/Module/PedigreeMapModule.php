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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function intdiv;

/**
 * Class PedigreeMapModule
 */
class PedigreeMapModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    public const DEFAULT_GENERATIONS = '4';

    // Limits
    public const MAXIMUM_GENERATIONS = 10;

    private const LINE_COLORS = [
        '#FF0000',
        // Red
        '#00FF00',
        // Green
        '#0000FF',
        // Blue
        '#FFB300',
        // Gold
        '#00FFFF',
        // Cyan
        '#FF00FF',
        // Purple
        '#7777FF',
        // Light blue
        '#80FF80'
        // Light green
    ];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Pedigree map');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “OSM” module */
        return I18N::translate('Show the birthplace of ancestors on a map.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-pedigreemap';
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        /* I18N: %s is an individual’s name */
        return I18N::translate('Pedigree map of %s', $individual->fullName());
    }

    /**
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'PedigreeMap',
                'xref'   => $individual->xref(),
                'ged'    => $individual->tree()->name(),
            ] + $parameters);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ChartService           $chart_service
     *
     * @return ResponseInterface
     */
    public function getMapDataAction(ServerRequestInterface $request, Tree $tree, ChartService $chart_service): ResponseInterface
    {
        $xref        = $request->getQueryParams()['reference'];
        $indi        = Individual::getInstance($xref, $tree);
        $color_count = count(self::LINE_COLORS);

        $facts = $this->getPedigreeMapFacts($request, $tree, $chart_service);

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];

        $sosa_points = [];

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

            $icon = ['color' => 'Gold', 'name' => 'bullseye '];
            if ($latitude !== 0.0 || $longitude !== 0.0) {
                $polyline         = null;
                $color            = self::LINE_COLORS[log($id, 2) % $color_count];
                $icon['color']    = $color; //make icon color the same as the line
                $sosa_points[$id] = [$latitude, $longitude];
                $sosa_parent      = intdiv($id, 2);
                if (array_key_exists($sosa_parent, $sosa_points)) {
                    // Would like to use a GeometryCollection to hold LineStrings
                    // rather than generate polylines but the MarkerCluster library
                    // doesn't seem to like them
                    $polyline = [
                        'points'  => [
                            $sosa_points[$sosa_parent],
                            [$latitude, $longitude],
                        ],
                        'options' => [
                            'color' => $color,
                        ],
                    ];
                }
                $geojson['features'][] = [
                    'type'       => 'Feature',
                    'id'         => $id,
                    'valid'      => true,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [$longitude, $latitude],
                    ],
                    'properties' => [
                        'polyline' => $polyline,
                        'icon'     => $icon,
                        'tooltip'  => strip_tags($fact->place()->fullName()),
                        'summary'  => view('modules/pedigree-map/events', $this->summaryData($indi, $fact, $id)),
                        'zoom'     => $location->zoom() ?: 2,
                    ],
                ];
            }
        }

        $code = empty($facts) ? StatusCodeInterface::STATUS_NO_CONTENT : StatusCodeInterface::STATUS_OK;

        return response($geojson, $code);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ChartService           $chart_service
     *
     * @return array
     */
    private function getPedigreeMapFacts(ServerRequestInterface $request, Tree $tree, ChartService $chart_service): array
    {
        $xref        = $request->getQueryParams()['reference'];
        $individual  = Individual::getInstance($xref, $tree);
        $generations = (int) $request->getQueryParams()['generations'];
        $ancestors   = $chart_service->sosaStradonitzAncestors($individual, $generations);
        $facts       = [];
        foreach ($ancestors as $sosa => $person) {
            if ($person->canShow()) {
                $birth = $person->facts(['BIRT'])->first();
                if ($birth instanceof Fact && $birth->place()->gedcomName() !== '') {
                    $facts[$sosa] = $birth;
                }
            }
        }

        return $facts;
    }

    /**
     * @param Individual $individual
     * @param Fact       $fact
     * @param int        $sosa
     *
     * @return array
     */
    private function summaryData(Individual $individual, Fact $fact, int $sosa): array
    {
        $record      = $fact->record();
        $name        = '';
        $url         = '';
        $tag         = $fact->label();
        $addbirthtag = false;

        if ($record instanceof Family) {
            // Marriage
            $spouse = $record->spouse($individual);
            if ($spouse) {
                $url  = $spouse->url();
                $name = $spouse->fullName();
            }
        } elseif ($record !== $individual) {
            // Birth of a child
            $url  = $record->url();
            $name = $record->fullName();
            $tag  = GedcomTag::getLabel('_BIRT_CHIL', $record);
        }

        if ($sosa > 1) {
            $addbirthtag = true;
            $tag         = ucfirst($this->getSosaName($sosa));
        }

        return [
            'tag'    => $tag,
            'url'    => $url,
            'name'   => $name,
            'value'  => $fact->value(),
            'date'   => $fact->date()->display(true),
            'place'  => $fact->place(),
            'addtag' => $addbirthtag,
        ];
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree    $tree
     *
     * @return object
     */
    public function getPedigreeMapAction(ServerRequestInterface $request, Tree $tree)
    {
        $xref        = $request->getQueryParams()['xref'];
        $individual  = Individual::getInstance($xref, $tree);
        $generations = $request->getQueryParams()['generations'] ?? self::DEFAULT_GENERATIONS;

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        return $this->viewResponse('modules/pedigree-map/page', [
            'module_name'    => $this->name(),
            /* I18N: %s is an individual’s name */
            'title'          => I18N::translate('Pedigree map of %s', $individual->fullName()),
            'tree'           => $tree,
            'individual'     => $individual,
            'generations'    => $generations,
            'maxgenerations' => self::MAXIMUM_GENERATIONS,
            'map'            => view(
                'modules/pedigree-map/chart',
                [
                    'module'      => $this->name(),
                    'ref'         => $individual->xref(),
                    'type'        => 'pedigree',
                    'generations' => $generations,
                ]
            ),
        ]);
    }

    /**
     * builds and returns sosa relationship name in the active language
     *
     * @param int $sosa Sosa number
     *
     * @return string
     */
    private function getSosaName(int $sosa): string
    {
        $path = '';

        while ($sosa > 1) {
            if ($sosa % 2 === 1) {
                $path = 'mot' . $path;
            } else {
                $path = 'fat' . $path;
            }
            $sosa = intdiv($sosa, 2);
        }

        return Functions::getRelationshipNameFromPath($path);
    }
}
