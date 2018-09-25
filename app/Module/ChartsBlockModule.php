<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Http\Controllers\PedigreeChartController;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\InteractiveTree\TreeView;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ChartsBlockModule
 */
class ChartsBlockModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module/block */
        return I18N::translate('Charts');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Charts” module */
        return I18N::translate('An alternative way to display charts.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree     $tree
     * @param int      $block_id
     * @param bool     $template
     * @param string[] $cfg
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, bool $template = true, array $cfg = []): string
    {
        global $ctype;

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

        $title = $this->getTitle();

        if ($person) {
            $content = '';
            switch ($type) {
                case 'pedigree':
                    $title     = I18N::translate('Pedigree of %s', $person->getFullName());
                    $chart_url = route('pedigree-chart', [
                        'xref'        => $person->getXref(),
                        'ged'         => $person->getTree()->getName(),
                        'generations' => 3,
                        'layout'      => PedigreeChartController::PORTRAIT,
                    ]);
                    $content = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;
                case 'descendants':
                    $title     = I18N::translate('Descendants of %s', $person->getFullName());
                    $chart_url = route('descendants-chart', [
                        'xref'        => $person->getXref(),
                        'ged'         => $person->getTree()->getName(),
                        'generations' => 2,
                        'chart_style' => 0,
                    ]);
                    $content = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;
                case 'hourglass':
                    $title     = I18N::translate('Hourglass chart of %s', $person->getFullName());
                    $chart_url = route('hourglass-chart', [
                        'xref'        => $person->getXref(),
                        'ged'         => $person->getTree()->getName(),
                        'generations' => 2,
                        'layout'      => PedigreeChartController::PORTRAIT,
                    ]);
                    $content = view('modules/charts/chart', [
                        'block_id'  => $block_id,
                        'chart_url' => $chart_url,
                    ]);
                    break;
                case 'treenav':
                    $title   = I18N::translate('Interactive tree of %s', $person->getFullName());
                    $mod     = new InteractiveTreeModule(WT_MODULES_DIR . 'tree');
                    $tv      = new TreeView();
                    $content .= '<script>$("head").append(\'<link rel="stylesheet" href="' . $mod->css() . '" type="text/css" />\');</script>';
                    $content .= '<script src="' . $mod->js() . '"></script>';
                    list($html, $js) = $tv->drawViewport($person, 2);
                    $content .= $html . '<script>' . $js . '</script>';
                    break;
            }
        } else {
            $content = I18N::translate('You must select an individual and a chart type in the block preferences');
        }

        if ($template) {
            if ($ctype === 'gedcom' && Auth::isManager($tree)) {
                $config_url = route('tree-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } elseif ($ctype === 'user' && Auth::check()) {
                $config_url = route('user-page-block-edit', [
                    'block_id' => $block_id,
                    'ged'      => $tree->getName(),
                ]);
            } else {
                $config_url = '';
            }

            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
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
    public function isGedcomBlock(): bool
    {
        return true;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
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
    public function editBlockConfiguration(Tree $tree, int $block_id)
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
