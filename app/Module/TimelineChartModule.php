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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TimelineChartModule
 */
class TimelineChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // The user can alter the vertical scale
    protected const SCALE_MIN     = 1;
    protected const SCALE_MAX     = 200;
    protected const SCALE_DEFAULT = 10;

    // GEDCOM events that may have DATE data, but should not be displayed
    protected const NON_FACTS = [
        'BAPL',
        'ENDL',
        'SLGC',
        'SLGS',
        '_TODO',
        'CHAN',
    ];

    // Box height
    protected const BHEIGHT = 30;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Timeline');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “TimelineChart” module */
        return I18N::translate('A timeline displaying individual events.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-timeline';
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
                'module'  => $this->name(),
                'action'  => 'Chart',
                'xrefs[]' => $individual->xref(),
                'ged'     => $individual->tree()->name(),
            ] + $parameters);
    }

    /**
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $ajax  = (bool) $request->get('ajax');
        $scale = (int) $request->get('scale', self::SCALE_DEFAULT);
        $scale = min($scale, self::SCALE_MAX);
        $scale = max($scale, self::SCALE_MIN);

        $xrefs = $request->get('xrefs', []);

        // Find the requested individuals.
        $individuals = (new Collection($xrefs))
            ->unique()
            ->map(static function (string $xref) use ($tree): ?Individual {
                return Individual::getInstance($xref, $tree);
            })
            ->filter()
            ->filter(GedcomRecord::accessFilter());

        // Generate URLs omitting each xref.
        $remove_urls = [];

        foreach ($individuals as $exclude) {
            $xrefs_1 = $individuals
                ->filter(static function (Individual $individual) use ($exclude): bool {
                    return $individual->xref() !== $exclude->xref();
                })
                ->map(static function (Individual $individual): string {
                    return $individual->xref();
                });

            $remove_urls[$exclude->xref()] = route('module', [
                'module' => $this->name(),
                'action' => 'Chart',
                'ged'    => $tree->name(),
                'scale'  => $scale,
                'xrefs'  => $xrefs_1->all(),
            ]);
        }

        $individuals = array_map(static function (string $xref) use ($tree): ?Individual {
            return Individual::getInstance($xref, $tree);
        }, $xrefs);

        $individuals = array_filter($individuals, static function (?Individual $individual): bool {
            return $individual instanceof Individual && $individual->canShow();
        });

        if ($ajax) {
            return $this->chart($tree, $xrefs, $scale);
        }

        $ajax_url = route('module', [
            'ajax'   => true,
            'module' => $this->name(),
            'action' => 'Chart',
            'ged'    => $tree->name(),
            'scale'  => $scale,
            'xrefs'  => $xrefs,
        ]);

        $reset_url = route('module', [
            'module' => $this->name(),
            'action' => 'Chart',
            'ged'    => $tree->name(),
        ]);

        $zoom_in_url = route('module', [
            'module' => $this->name(),
            'action' => 'Chart',
            'ged'    => $tree->name(),
            'scale'  => min(self::SCALE_MAX, $scale + (int) ($scale * 0.2 + 1)),
            'xrefs'  => $xrefs,
        ]);

        $zoom_out_url = route('module', [
            'module' => $this->name(),
            'action' => 'Chart',
            'ged'    => $tree->name(),
            'scale'  => max(self::SCALE_MIN, $scale - (int) ($scale * 0.2 + 1)),
            'xrefs'  => $xrefs,
        ]);

        return $this->viewResponse('modules/timeline-chart/page', [
            'ajax_url'     => $ajax_url,
            'individuals'  => $individuals,
            'module_name'  => $this->name(),
            'remove_urls'  => $remove_urls,
            'reset_url'    => $reset_url,
            'title'        => $this->title(),
            'scale'        => $scale,
            'zoom_in_url'  => $zoom_in_url,
            'zoom_out_url' => $zoom_out_url,
        ]);
    }

    /**
     * @param Tree  $tree
     * @param array $xrefs
     * @param int   $scale
     *
     * @return ResponseInterface
     */
    protected function chart(Tree $tree, array $xrefs, int $scale): ResponseInterface
    {
        $xrefs = array_unique($xrefs);

        /** @var Individual[] $individuals */
        $individuals = array_map(static function (string $xref) use ($tree): ?Individual {
            return Individual::getInstance($xref, $tree);
        }, $xrefs);

        $individuals = array_filter($individuals, static function (?Individual $individual): bool {
            return $individual instanceof Individual && $individual->canShow();
        });

        $baseyear    = (int) date('Y');
        $topyear     = 0;
        $indifacts   = new Collection();
        $birthyears  = [];
        $birthmonths = [];
        $birthdays   = [];

        foreach ($individuals as $individual) {
            $bdate = $individual->getBirthDate();
            if ($bdate->isOK()) {
                $date = new GregorianDate($bdate->minimumJulianDay());

                $birthyears [$individual->xref()] = $date->year;
                $birthmonths[$individual->xref()] = max(1, $date->month);
                $birthdays  [$individual->xref()] = max(1, $date->day);
            }
            // find all the fact information
            $facts = $individual->facts();
            foreach ($individual->spouseFamilies() as $family) {
                foreach ($family->facts() as $fact) {
                    $facts->push($fact);
                }
            }
            foreach ($facts as $event) {
                // get the fact type
                $fact = $event->getTag();
                if (!in_array($fact, self::NON_FACTS)) {
                    // check for a date
                    $date = $event->date();
                    if ($date->isOK()) {
                        $date     = new GregorianDate($date->minimumJulianDay());
                        $baseyear = min($baseyear, $date->year);
                        $topyear  = max($topyear, $date->year);

                        if (!$individual->isDead()) {
                            $topyear = max($topyear, (int) date('Y'));
                        }

                        $indifacts->push($event);
                    }
                }
            }
        }

        // do not add the same fact twice (prevents marriages from being added multiple times)
        $indifacts = $indifacts->unique();

        if ($scale === 0) {
            $scale = (int) (($topyear - $baseyear) / 20 * $indifacts->count() / 4);
            if ($scale < 6) {
                $scale = 6;
            }
        }
        if ($scale < 2) {
            $scale = 2;
        }
        $baseyear -= 5;
        $topyear  += 5;

        $indifacts = Fact::sortFacts($indifacts);

        $html = view('modules/timeline-chart/chart', [
            'baseyear'    => $baseyear,
            'bheight'     => self::BHEIGHT,
            'birthdays'   => $birthdays,
            'birthmonths' => $birthmonths,
            'birthyears'  => $birthyears,
            'indifacts'   => $indifacts,
            'individuals' => $individuals,
            'placements'  => [],
            'scale'       => $scale,
            'topyear'     => $topyear,
        ]);

        return response($html);
    }
}
