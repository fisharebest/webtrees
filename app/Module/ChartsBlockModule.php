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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ChartsBlockModule
 */
class ChartsBlockModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    /**
     * @var ModuleService
     */
    private $module_service;

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
     * @param Tree     $tree
     * @param int      $block_id
     * @param string   $ctype
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $ctype = '', array $cfg = []): string
    {
        $PEDIGREE_ROOT_ID = $tree->getPreference('PEDIGREE_ROOT_ID');
        $gedcomid         = $tree->getUserPreference(Auth::user(), 'gedcomid');

        $type = $this->getBlockSetting($block_id, 'type', 'pedigree');
        $pid  = $this->getBlockSetting($block_id, 'pid', Auth::check() ? ($gedcomid ?: $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

        extract($cfg, EXTR_OVERWRITE);

        $person = Individual::getInstance($pid, $tree);
        if (!$person) {
            $pid = $PEDIGREE_ROOT_ID;
            $this->setBlockSetting($block_id, 'pid', $pid);
            $person = Individual::getInstance($pid, $tree);
        }

        $title = $this->title();

        if ($person) {
            switch ($type) {
                default:
                case 'pedigree':
                    /** @var PedigreeChartModule $module */
                    $module    = $this->module_service->findByInterface(PedigreeChartModule::class)->first();
                    $title     = $module->chartTitle($person);
                    $chart_url = $module->chartUrl($person, [
                        'ajax'        => true,
                        'generations' => 3,
                        'layout'      => PedigreeChartModule::ORIENTATION_RIGHT,
                    ]);
                    $content   = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;

                case 'descendants':
                    /** @var DescendancyChartModule $module */
                    $module    = $this->module_service->findByInterface(DescendancyChartModule::class)->first();
                    $title     = $module->chartTitle($person);
                    $chart_url = $module->chartUrl($person, [
                        'ajax'        => true,
                        'generations' => 2,
                        'chart_style' => DescendancyChartModule::CHART_STYLE_LIST,
                    ]);
                    $content   = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;

                case 'hourglass':
                    /** @var HourglassChartModule $module */
                    $module    = $this->module_service->findByInterface(HourglassChartModule::class)->first();
                    $title     = $module->chartTitle($person);
                    $chart_url = $module->chartUrl($person, [
                        'ajax'        => true,
                        'generations' => 2,
                    ]);
                    $content   = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;

                case 'treenav':
                    /** @var InteractiveTreeModule $module */
                    $module  = $this->module_service->findByInterface(InteractiveTreeModule::class);
                    $title   = I18N::translate('Interactive tree of %s', $person->fullName());
                    $tv      = new TreeView();
                    $content = '<script>$("head").append(\'<link rel="stylesheet" href="' . $module->css() . '" type="text/css" />\');</script>';
                    $content .= '<script src="' . $module->js() . '"></script>';
                    [$html, $js] = $tv->drawViewport($person, 2);
                    $content .= $html . '<script>' . $js . '</script>';
                    break;
            }
        } else {
            $content = I18N::translate('You must select an individual and a chart type in the block preferences');
        }

        if ($ctype !== '') {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->name(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $config_url,
                'title'      => strip_tags($title),
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isUserBlock(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
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
        $this->setBlockSetting($block_id, 'type', $request->get('type', 'pedigree'));
        $this->setBlockSetting($block_id, 'pid', $request->get('pid', ''));
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id): void
    {
        $PEDIGREE_ROOT_ID = $tree->getPreference('PEDIGREE_ROOT_ID');
        $gedcomid         = $tree->getUserPreference(Auth::user(), 'gedcomid');

        $type = $this->getBlockSetting($block_id, 'type', 'pedigree');
        $pid  = $this->getBlockSetting($block_id, 'pid', Auth::check() ? ($gedcomid ?: $PEDIGREE_ROOT_ID) : $PEDIGREE_ROOT_ID);

        $charts = [
            'pedigree'    => I18N::translate('Pedigree'),
            'descendants' => I18N::translate('Descendants'),
            'hourglass'   => I18N::translate('Hourglass chart'),
            'treenav'     => I18N::translate('Interactive tree'),
        ];
        uasort($charts, 'Fisharebest\Webtrees\I18N::strcasecmp');

        $individual = Individual::getInstance($pid, $tree);

        echo view('modules/charts/config', [
            'charts'     => $charts,
            'individual' => $individual,
            'tree'       => $tree,
            'type'       => $type,
        ]);
    }
}
