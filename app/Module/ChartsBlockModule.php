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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function extract;
use function uasort;
use function view;

use const EXTR_OVERWRITE;

/**
 * Class ChartsBlockModule
 */
class ChartsBlockModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private ModuleService $module_service;

    /**
     * ChartsBlockModule constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/block */
        return I18N::translate('Charts');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Charts” module */
        return I18N::translate('An alternative way to display charts.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $PEDIGREE_ROOT_ID = $tree->getPreference('PEDIGREE_ROOT_ID');
        $gedcomid         = $tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);
        $default_xref     = $gedcomid ?: $PEDIGREE_ROOT_ID;

        $type = $this->getBlockSetting($block_id, 'type', 'pedigree');
        $xref = $this->getBlockSetting($block_id, 'pid', $default_xref);

        extract($config, EXTR_OVERWRITE);

        $individual = Registry::individualFactory()->make($xref, $tree);

        $title = $this->title();

        if ($individual instanceof Individual) {
            switch ($type) {
                default:
                case 'pedigree':
                    $module = $this->module_service->findByInterface(PedigreeChartModule::class)->first();
                    if ($module instanceof PedigreeChartModule) {
                        $title     = $module->chartTitle($individual);
                        $chart_url = $module->chartUrl($individual, [
                            'ajax'        => true,
                            'generations' => $this->getBlockSetting($block_id, 'pedigree_generations', '3'),
                            'layout'      => $this->getBlockSetting(
                                $block_id,
                                'pedigree_style',
                                PedigreeChartModule::DEFAULT_STYLE
                            ),
                            'style'       => $this->getBlockSetting(
                                $block_id,
                                'pedigree_style',
                                PedigreeChartModule::DEFAULT_STYLE
                            ),
                            // Note: some modules use 'layout', others 'style'
                        ]);
                        $content   = view('modules/charts/chart', [
                            'block_id'  => $block_id,
                            'chart_url' => $chart_url,
                        ]);
                    } else {
                        $title   = I18N::translate('Pedigree');
                        $content = I18N::translate('The module “%s” has been disabled.', $title);
                    }
                    break;

                case 'descendants':
                    $module = $this->module_service->findByInterface(DescendancyChartModule::class)->first();

                    if ($module instanceof DescendancyChartModule) {
                        $title     = $module->chartTitle($individual);
                        $chart_url = $module->chartUrl($individual, [
                            'ajax'        => true,
                            'generations' => $this->getBlockSetting($block_id, 'descendants_generations', '2'),
                            'chart_style' => DescendancyChartModule::CHART_STYLE_TREE,
                        ]);
                        $content   = view('modules/charts/chart', [
                            'block_id'  => $block_id,
                            'chart_url' => $chart_url,
                        ]);
                    } else {
                        $title   = I18N::translate('Descendants');
                        $content = I18N::translate('The module “%s” has been disabled.', $title);
                    }

                    break;

                case 'hourglass':
                    $module = $this->module_service->findByInterface(HourglassChartModule::class)->first();

                    if ($module instanceof HourglassChartModule) {
                        $title     = $module->chartTitle($individual);
                        $chart_url = $module->chartUrl($individual, [
                            'ajax'        => true,
                            'generations' => $this->getBlockSetting($block_id, 'hourglass_generations', '2'),
                        ]);
                        $content   = view('modules/charts/chart', [
                            'block_id'  => $block_id,
                            'chart_url' => $chart_url,
                        ]);
                    } else {
                        $title   = I18N::translate('Hourglass chart');
                        $content = I18N::translate('The module “%s” has been disabled.', $title);
                    }
                    break;

                case 'treenav':
                    $module = $this->module_service->findByInterface(InteractiveTreeModule::class)->first();

                    if ($module instanceof InteractiveTreeModule) {
                        $title  = I18N::translate('Interactive tree of %s', $individual->fullName());
                        $tv     = new TreeView();
                        [$html, $js] = $tv->drawViewport($individual, 2);
                        $content = $html . '<script>' . $js . '</script>';
                    } else {
                        $title   = I18N::translate('Interactive tree');
                        $content = I18N::translate('The module “%s” has been disabled.', $title);
                    }

                    break;
            }
        } else {
            $content = I18N::translate('You must select an individual and a chart type in the block preferences');
        }

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => $title,
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /**
     * Should this block load asynchronously using AJAX?
     *
     * Simple blocks are faster in-line, more complex ones can be loaded later.
     *
     * @return bool
     */
    public function loadAjax(): bool
    {
        return true;
    }

    /**
     * Can this block be shown on the tree’s home page?
     *
     * @return bool
     */
    public function isTreeBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param ServerRequestInterface $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $type = Validator::parsedBody($request)->string('type');
        $xref = Validator::parsedBody($request)->isXref()->string('xref');
        $pedigree_generations = Validator::parsedBody($request)->integer('pedigree_generations');
        $pedigree_style = Validator::parsedBody($request)->string('pedigree_style');
        $descendants_generations = Validator::parsedBody($request)->integer('descendants_generations');
        $hourglass_generations = Validator::parsedBody($request)->integer('hourglass_generations');

        $this->setBlockSetting($block_id, 'type', $type);
        $this->setBlockSetting($block_id, 'pid', $xref);
        $this->setBlockSetting($block_id, 'pedigree_generations', (string) $pedigree_generations);
        $this->setBlockSetting($block_id, 'pedigree_style', $pedigree_style);
        $this->setBlockSetting($block_id, 'descendants_generations', (string) $descendants_generations);
        $this->setBlockSetting($block_id, 'hourglass_generations', (string) $hourglass_generations);
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return string
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): string
    {
        $PEDIGREE_ROOT_ID = $tree->getPreference('PEDIGREE_ROOT_ID');
        $gedcomid         = $tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);
        $default_xref     = $gedcomid ?: $PEDIGREE_ROOT_ID;

        $type = $this->getBlockSetting($block_id, 'type', 'pedigree');
        $xref = $this->getBlockSetting($block_id, 'pid', $default_xref);

        $charts = [];
        // Only add charts that are available
        $pedigreeModule = $this->module_service->findByInterface(PedigreeChartModule::class)->first();
        if ($pedigreeModule instanceof PedigreeChartModule) {
            $charts['pedigree'] = I18N::translate('Pedigree');
            $pedigree_max_generations = $pedigreeModule::MAXIMUM_GENERATIONS;
            $pedigree_min_generations = $pedigreeModule::MINIMUM_GENERATIONS;
            $pedigree_styles = $pedigreeModule->styles(I18N::direction());
        }
        $descendantsModule = $this->module_service->findByInterface(DescendancyChartModule::class)->first();
        if ($descendantsModule instanceof DescendancyChartModule) {
            $charts['descendants'] = I18N::translate('Descendants');
            $descendants_max_generations = $descendantsModule::MAXIMUM_GENERATIONS;
            $descendants_min_generations = $descendantsModule::MINIMUM_GENERATIONS;
        }
        $hourglassModule = $this->module_service->findByInterface(HourglassChartModule::class)->first();
        if ($hourglassModule instanceof HourglassChartModule) {
            $charts['hourglass'] = I18N::translate('Hourglass chart');
            $hourglass_max_generations = $hourglassModule::MAXIMUM_GENERATIONS;
            $hourglass_min_generations = $hourglassModule::MINIMUM_GENERATIONS;
        }
        $treeModule = $this->module_service->findByInterface(InteractiveTreeModule::class)->first();
        if ($treeModule instanceof InteractiveTreeModule) {
            $charts['treenav'] = I18N::translate('Interactive tree');
        }
        uasort($charts, I18N::comparator());

        $pedigree_generations = $this->getBlockSetting($block_id, 'pedigree_generations', '3');
        $pedigree_style = $this->getBlockSetting($block_id, 'pedigree_style', $pedigreeModule::DEFAULT_STYLE);
        $descendants_generations = $this->getBlockSetting($block_id, 'descendants_generations', '2');
        $hourglass_generations = $this->getBlockSetting($block_id, 'hourglass_generations', '2');

        $individual = Registry::individualFactory()->make($xref, $tree);

        return view('modules/charts/config', [
            'charts'                      => $charts,
            'individual'                  => $individual,
            'tree'                        => $tree,
            'type'                        => $type,
            'pedigree_generations'        => $pedigree_generations ?? null,
            'pedigree_max_generations'    => $pedigree_max_generations ?? null,
            'pedigree_min_generations'    => $pedigree_min_generations ?? null,
            'pedigree_style'              => $pedigree_style ?? null,
            'pedigree_styles'             => $pedigree_styles ?? null,
            'descendants_generations'     => $descendants_generations ?? null,
            'descendants_max_generations' => $descendants_max_generations ?? null,
            'descendants_min_generations' => $descendants_min_generations ?? null,
            'hourglass_generations'       => $hourglass_generations ?? null,
            'hourglass_max_generations'   => $hourglass_max_generations ?? null,
            'hourglass_min_generations'   => $hourglass_min_generations ?? null,
        ]);
    }
}
