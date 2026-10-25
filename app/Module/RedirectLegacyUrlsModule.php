<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Http\Controllers\RedirectAncestryPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectBranchesPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectCompactPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectDescendencyPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectFamilyBookPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectFamilyPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectFamListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectFanChartPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectGedRecordPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectHourGlassPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectIndiListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectIndividualPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectLifeSpanPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectMediaListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectMediaViewerPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectModulePhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectNoteListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectNotePhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectPedigreePhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectPlaceListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectRelationshipPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectRepoListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectReportEnginePhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectRepositoryPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectSourceListPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectSourcePhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectStatisticsPhp;
use Fisharebest\Webtrees\Http\Controllers\RedirectTimeLinePhp;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;

/**
 * Class RedirectLegacyUrlsModule - rewrite URLs from phpGedView and webtrees 1.x
 */
class RedirectLegacyUrlsModule extends AbstractModule
{
    /**
     * Initialization.
     */
    public function boot(): void
    {
        $routes = Registry::container()->get(RouteCollection::class);

        // Legacy URLs from older software.
        $routes->add('/ancestry.php', RedirectAncestryPhp::class);
        $routes->add('/branches.php', RedirectBranchesPhp::class);
        $routes->add('/compact.php', RedirectCompactPhp::class);
        $routes->add('/descendency.php', RedirectDescendencyPhp::class);
        $routes->add('/family.php', RedirectFamilyPhp::class);
        $routes->add('/famlist.php', RedirectFamListPhp::class);
        $routes->add('/familybook.php', RedirectFamilyBookPhp::class);
        $routes->add('/fanchart.php', RedirectFanChartPhp::class);
        $routes->add('/gedrecord.php', RedirectGedRecordPhp::class);
        $routes->add('/hourglass.php', RedirectHourGlassPhp::class);
        $routes->add('/indilist.php', RedirectIndiListPhp::class);
        $routes->add('/individual.php', RedirectIndividualPhp::class);
        $routes->add('/lifespan.php', RedirectLifeSpanPhp::class);
        $routes->add('/medialist.php', RedirectMediaListPhp::class);
        $routes->add('/mediaviewer.php', RedirectMediaViewerPhp::class);
        $routes->add('/module.php', RedirectModulePhp::class);
        $routes->add('/note.php', RedirectNotePhp::class);
        $routes->add('/notelist.php', RedirectNoteListPhp::class);
        $routes->add('/pedigree.php', RedirectPedigreePhp::class);
        $routes->add('/placelist.php', RedirectPlaceListPhp::class);
        $routes->add('/relationship.php', RedirectRelationshipPhp::class);
        $routes->add('/repository.php', RedirectRepositoryPhp::class);
        $routes->add('/repolist.php', RedirectRepoListPhp::class);
        $routes->add('/reportengine.php', RedirectReportEnginePhp::class);
        $routes->add('/sourcelist.php', RedirectSourceListPhp::class);
        $routes->add('/source.php', RedirectSourcePhp::class);
        $routes->add('/statistics.php', RedirectStatisticsPhp::class);
        $routes->add('/timeline.php', RedirectTimeLinePhp::class);
    }

    public function description(): string
    {
        /* I18N: Description of the “Legacy URLs” module */
        return I18N::translate('Redirect old URLs from webtrees version 1.');
    }

    public function title(): string
    {
        /* I18N: Name of a module - historic/obsolete URLs. */
        return I18N::translate('Legacy URLs');
    }
}
