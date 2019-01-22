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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RelationshipsChartModule
 */
class RelationshipsChartModule extends AbstractModule implements ModuleInterface, ModuleChartInterface, ModuleConfigInterface
{
    use ModuleChartTrait;
    use ModuleConfigTrait;

    /** It would be more correct to use PHP_INT_MAX, but this isn't friendly in URLs */
    public const UNLIMITED_RECURSION = 99;

    /** By default new trees allow unlimited recursion */
    public const DEFAULT_RECURSION = '99';

    /** By default new trees search for all relationships (not via ancestors) */
    public const DEFAULT_ANCESTORS = '0';

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Relationships');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “RelationshipsChart” module */
        return I18N::translate('A chart displaying relationships between two individuals.');
    }

    /**
     * A main menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function chartMenu(Individual $individual): Menu
    {
        $gedcomid = $individual->tree()->getUserPreference(Auth::user(), 'gedcomid');

        if ($gedcomid !== '') {
            return new Menu(
                I18N::translate('Relationship to me'),
                $this->chartUrl($individual, ['xref2' => $gedcomid]),
                $this->chartMenuClass(),
                $this->chartUrlAttributes()
            );
        }

        return new Menu(
            $this->title(),
            $this->chartUrl($individual),
            $this->chartMenuClass(),
            $this->chartUrlAttributes()
        );
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-relationship';
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
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('relationships', [
            'xref1' => $individual->xref(),
            'ged'   => $individual->tree()->name(),
        ] + $parameters);
    }

    /**
     * @return Response
     */
    public function getAdminAction(): Response
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('modules/relationships_chart/config', [
            'all_trees'         => Tree::getAll(),
            'ancestors_options' => $this->ancestorsOptions(),
            'default_ancestors' => self::DEFAULT_ANCESTORS,
            'default_recursion' => self::DEFAULT_RECURSION,
            'recursion_options' => $this->recursionOptions(),
            'title'             => I18N::translate('Chart preferences') . ' — ' . $this->title(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function postAdminAction(Request $request): RedirectResponse
    {
        foreach (Tree::getAll() as $tree) {
            $recursion = $request->get('relationship-recursion-' . $tree->id(), '');
            $ancestors = $request->get('relationship-ancestors-' . $tree->id(), '');

            $tree->setPreference('RELATIONSHIP_RECURSION', $recursion);
            $tree->setPreference('RELATIONSHIP_ANCESTORS', $ancestors);
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->title()), 'success');

        return new RedirectResponse($this->getConfigLink());
    }

    /**
     * Possible options for the ancestors option
     *
     * @return string[]
     */
    private function ancestorsOptions(): array
    {
        return [
            0 => I18N::translate('Find any relationship'),
            1 => I18N::translate('Find relationships via ancestors'),
        ];
    }

    /**
     * Possible options for the recursion option
     *
     * @return string[]
     */
    private function recursionOptions(): array
    {
        return [
            0                         => I18N::translate('none'),
            1                         => I18N::number(1),
            2                         => I18N::number(2),
            3                         => I18N::number(3),
            self::UNLIMITED_RECURSION => I18N::translate('unlimited'),
        ];
    }
}
