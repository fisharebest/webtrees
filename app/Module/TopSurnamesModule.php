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
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TopSurnamesModule
 */
class TopSurnamesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Default values for new blocks.
    private const DEFAULT_NUMBER = '10';
    private const DEFAULT_STYLE  = 'table';

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. Top=Most common */
        return I18N::translate('Top surnames');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Top surnames” module */
        return I18N::translate('A list of the most popular surnames.');
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
        $num       = (int) $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

        extract($cfg, EXTR_OVERWRITE);

        // Use the count of base surnames.
        $top_surnames = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->whereNotIn('n_surn', ['@N.N.', ''])
            ->groupBy('n_surn')
            ->orderByDesc(DB::raw('COUNT(n_surn)'))
            ->take($num)
            ->pluck('n_surn');

        $all_surnames = [];

        foreach ($top_surnames as $top_surname) {
            $variants = DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where(DB::raw('n_surn /*! COLLATE utf8_bin */'), '=', $top_surname)
                ->groupBy('surname')
                ->select([DB::raw('n_surname /*! COLLATE utf8_bin */ AS surname'), DB::raw('count(*) AS total')])
                ->pluck('total', 'surname')
                ->all();

            $all_surnames[$top_surname] = $variants;
        }
        
        //find a module providing individual lists
        $module = app(ModuleService::class)->findByComponent(ModuleListInterface::class, $tree, Auth::user())->first(static function (ModuleInterface $module) {
            return $module instanceof IndividualListModule;
        });
        
        switch ($infoStyle) {
            case 'tagcloud':
                uksort($all_surnames, [I18N::class, 'strcasecmp']);
                $content = FunctionsPrintLists::surnameTagCloud($all_surnames, $module, true, $tree);
                break;
            case 'list':
                uasort($all_surnames, [$this, 'surnameCountSort']);
                $content = FunctionsPrintLists::surnameList($all_surnames, 1, true, $module, $tree);
                break;
            case 'array':
                uasort($all_surnames, [$this, 'surnameCountSort']);
                $content = FunctionsPrintLists::surnameList($all_surnames, 2, true, $module, $tree);
                break;
            case 'table':
            default:
                $content = view('lists/surnames-table', [
                    'surnames' => $all_surnames,
                    'module'   => $module,
                    'families' => false,
                    'tree'     => $tree,
                ]);
                break;
        }

        if ($ctype !== '') {
            $num = count($top_surnames);
            if ($num === 1) {
                // I18N: i.e. most popular surname.
                $title = I18N::translate('Top surname');
            } else {
                // I18N: Title for a list of the most common surnames, %s is a number. Note that a separate translation exists when %s is 1
                $title = I18N::plural('Top %s surname', 'Top %s surnames', $num, I18N::number($num));
            }

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
                'title'      => $title,
                'content'    => $content,
            ]);
        }

        return $content;
    }

    /** {@inheritdoc} */
    public function loadAjax(): bool
    {
        return false;
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
        $this->setBlockSetting($block_id, 'num', $request->get('num', self::DEFAULT_NUMBER));
        $this->setBlockSetting($block_id, 'infoStyle', $request->get('infoStyle', self::DEFAULT_STYLE));
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
        $num       = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

        $info_styles = [
            /* I18N: An option in a list-box */
            'list'     => I18N::translate('bullet list'),
            /* I18N: An option in a list-box */
            'array'    => I18N::translate('compact list'),
            /* I18N: An option in a list-box */
            'table'    => I18N::translate('table'),
            /* I18N: An option in a list-box */
            'tagcloud' => I18N::translate('tag cloud'),
        ];

        echo view('modules/top10_surnames/config', [
            'num'         => $num,
            'infoStyle'   => $infoStyle,
            'info_styles' => $info_styles,
        ]);
    }

    /**
     * Sort (lists of counts of similar) surname by total count.
     *
     * @param string[] $a
     * @param string[] $b
     *
     * @return int
     */
    private function surnameCountSort(array $a, array $b): int
    {
        return array_sum($b) - array_sum($a);
    }
}
