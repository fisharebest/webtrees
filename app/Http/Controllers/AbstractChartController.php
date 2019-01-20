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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Common logic for chart controllers.
 */
abstract class AbstractChartController extends AbstractBaseController
{
    /**
     * Check that a module is enabled for a tree.
     *
     * @param Tree   $tree
     * @param string $class_name
     *
     * @throws NotFoundHttpException
     * @return ModuleChartInterface
     */
    protected function checkModuleIsActive(Tree $tree, string $class_name): ModuleChartInterface
    {
        $module = Module::activeCharts($tree)
            ->filter(function (ModuleChartInterface $module) use ($class_name): bool {
                return $module instanceof $class_name;
            })
            ->first();

        if (!$module instanceof $class_name) {
            throw new NotFoundHttpException(I18N::translate('The module “%s” has been disabled.', $module));
        }

        return $module;
    }

    /**
     * Find all the individuals that are descended from an individual.
     *
     * @param Individual   $individual
     * @param int          $generations
     * @param Individual[] $array
     *
     * @return Individual[]
     */
    protected function descendants(Individual $individual, int $generations, array $array): array
    {
        if ($generations < 1) {
            return $array;
        }

        $array[$individual->xref()] = $individual;

        foreach ($individual->getSpouseFamilies() as $family) {
            $spouse = $family->getSpouse($individual);
            if ($spouse !== null && !array_key_exists($spouse->xref(), $array)) {
                $array[$spouse->xref()] = $spouse;
            }
            foreach ($family->getChildren() as $child) {
                $array = $this->descendants($child, $generations - 1, $array);
            }
        }

        return $array;
    }
}
