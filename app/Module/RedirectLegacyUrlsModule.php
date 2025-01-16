<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Aura\Router\RouterContainer;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectAncestryPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectBranchesPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectCalendarPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectCompactPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectDescendencyPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectFamilyBookPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectFamilyPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectFamListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectFanChartPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectGedRecordPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectHourGlassPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectIndiListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectIndividualPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectLifeSpanPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectMediaListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectMediaViewerPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectModulePhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectNoteListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectNotePhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectPedigreePhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectPlaceListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectRelationshipPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectRepoListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectReportEnginePhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectRepositoryPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectSourceListPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectSourcePhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectStatisticsPhp;
use Fisharebest\Webtrees\Http\RequestHandlers\RedirectTimeLinePhp;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;

/**
 * Class RedirectLegacyUrlsModule - rewrite URLs from phpGedView and webtrees 1.x
 */
class RedirectLegacyUrlsModule extends AbstractModule
{
    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        $router_container = Registry::container()->get(RouterContainer::class);
        $router           = $router_container->getMap();

        // Legacy URLs from older software.
        $router->get(RedirectAncestryPhp::class, '/ancestry.php', RedirectAncestryPhp::class);
        $router->get(RedirectBranchesPhp::class, '/branches.php', RedirectBranchesPhp::class);
        $router->get(RedirectCalendarPhp::class, '/calendar.php', RedirectCalendarPhp::class);
        $router->get(RedirectCompactPhp::class, '/compact.php', RedirectCompactPhp::class);
        $router->get(RedirectDescendencyPhp::class, '/compact.php', RedirectDescendencyPhp::class);
        $router->get(RedirectFamilyPhp::class, '/family.php', RedirectFamilyPhp::class);
        $router->get(RedirectFamListPhp::class, '/famlist.php', RedirectFamListPhp::class);
        $router->get(RedirectFamilyBookPhp::class, '/familybook.php', RedirectFamilyBookPhp::class);
        $router->get(RedirectFanChartPhp::class, '/fanchart.php', RedirectFanChartPhp::class);
        $router->get(RedirectGedRecordPhp::class, '/gedrecord.php', RedirectGedRecordPhp::class);
        $router->get(RedirectHourGlassPhp::class, '/hourglass.php', RedirectHourGlassPhp::class);
        $router->get(RedirectIndiListPhp::class, '/indilist.php', RedirectIndiListPhp::class);
        $router->get(RedirectIndividualPhp::class, '/individual.php', RedirectIndividualPhp::class);
        $router->get(RedirectLifeSpanPhp::class, '/lifespan.php', RedirectLifeSpanPhp::class);
        $router->get(RedirectMediaListPhp::class, '/medialist.php', RedirectMediaListPhp::class);
        $router->get(RedirectMediaViewerPhp::class, '/mediaviewer.php', RedirectMediaViewerPhp::class);
        $router->get(RedirectModulePhp::class, '/module.php', RedirectModulePhp::class);
        $router->get(RedirectNotePhp::class, '/note.php', RedirectNotePhp::class);
        $router->get(RedirectNoteListPhp::class, '/notelist.php', RedirectNoteListPhp::class);
        $router->get(RedirectPedigreePhp::class, '/pedigree.php', RedirectPedigreePhp::class);
        $router->get(RedirectPlaceListPhp::class, '/placelist.php', RedirectPlaceListPhp::class);
        $router->get(RedirectRelationshipPhp::class, '/relationship.php', RedirectRelationshipPhp::class);
        $router->get(RedirectRepositoryPhp::class, '/repository.php', RedirectRepositoryPhp::class);
        $router->get(RedirectRepoListPhp::class, '/repolist.php', RedirectRepoListPhp::class);
        $router->get(RedirectReportEnginePhp::class, '/reportengine.php', RedirectReportEnginePhp::class);
        $router->get(RedirectSourceListPhp::class, '/sourcelist.php', RedirectSourceListPhp::class);
        $router->get(RedirectSourcePhp::class, '/source.php', RedirectSourcePhp::class);
        $router->get(RedirectStatisticsPhp::class, '/statistics.php', RedirectStatisticsPhp::class);
        $router->get(RedirectTimeLinePhp::class, '/timeline.php', RedirectTimeLinePhp::class);
    }

    public function description(): string
    {
        /* I18N: Description of the “Legacy URLs” module */
        return I18N::translate('Redirect old URLs from webtrees version 1.');
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module - historic/obsolete URLs. */
        return I18N::translate('Legacy URLs');
    }
}
