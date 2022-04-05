<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function array_sum;
use function count;
use function extract;
use function uasort;
use function uksort;
use function view;

use const EXTR_OVERWRITE;

/**
 * Class TopSurnamesModule
 */
class TopSurnamesModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    // Default values for new blocks.
    private const DEFAULT_NUMBER = '10';
    private const DEFAULT_STYLE  = 'table';

    private ModuleService $module_service;

    /**
     * TopSurnamesModule constructor.
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
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $num       = (int) $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

        extract($config, EXTR_OVERWRITE);

        // Use the count of base surnames.
        $top_surnames = DB::table('name')
            ->where('n_file', '=', $tree->id())
            ->where('n_type', '<>', '_MARNM')
            ->whereNotIn('n_surn', [Individual::NOMEN_NESCIO, ''])
            ->groupBy(['n_surn'])
            ->orderByDesc(new Expression('COUNT(n_surn)'))
            ->take($num)
            ->pluck('n_surn');

        $all_surnames = [];

        foreach ($top_surnames as $top_surname) {
            $variants = DB::table('name')
                ->where('n_file', '=', $tree->id())
                ->where(new Expression('n_surn /*! COLLATE utf8_bin */'), '=', $top_surname)
                ->groupBy(['surname'])
                ->select([new Expression('n_surname /*! COLLATE utf8_bin */ AS surname'), new Expression('count(*) AS total')])
                ->pluck('total', 'surname')
                ->map(static fn (string $n): int => (int) $n)
                ->all();

            $all_surnames[$top_surname] = $variants;
        }

        // Find a module providing individual lists.
        $module = $this->module_service
            ->findByComponent(ModuleListInterface::class, $tree, Auth::user())
            ->first(static function (ModuleInterface $module): bool {
                // The family list extends the individual list
                return
                    $module instanceof IndividualListModule &&
                    !$module instanceof FamilyListModule;
            });

        switch ($infoStyle) {
            case 'tagcloud':
                uksort($all_surnames, I18N::comparator());
                $content = view('lists/surnames-tag-cloud', [
                    'module'   => $module,
                    'surnames' => $all_surnames,
                    'totals'   => true,
                    'tree'     => $tree,
                ]);
                break;

            case 'list':
                uasort($all_surnames, static fn (array $a, array $b): int => array_sum($b) <=> array_sum($a));
                $content = view('lists/surnames-bullet-list', [
                    'module'   => $module,
                    'surnames' => $all_surnames,
                    'totals'   => true,
                    'tree'     => $tree,
                ]);
                break;

            case 'array':
                uasort($all_surnames, static fn (array $a, array $b): int => array_sum($b) <=> array_sum($a));
                $content = view('lists/surnames-compact-list', [
                    'module'   => $module,
                    'surnames' => $all_surnames,
                    'totals'   => true,
                    'tree'     => $tree,
                ]);
                break;

            case 'table':
            default:
                $content = view('lists/surnames-table', [
                    'families' => false,
                    'module'   => $module,
                    'order'    => [[1, 'desc']],
                    'surnames' => $all_surnames,
                    'tree'     => $tree,
                ]);
                break;
        }

        if ($context !== self::CONTEXT_EMBED) {
            $num = count($top_surnames);
            if ($num === 1) {
                // I18N: i.e. most popular surname.
                $title = I18N::translate('Top surname');
            } else {
                // I18N: Title for a list of the most common surnames, %s is a number. Note that a separate translation exists when %s is 1
                $title = I18N::plural('Top %s surname', 'Top %s surnames', $num, I18N::number($num));
            }

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
        return false;
    }

    /**
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
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
        $params = (array) $request->getParsedBody();

        $this->setBlockSetting($block_id, 'num', $params['num']);
        $this->setBlockSetting($block_id, 'infoStyle', $params['infoStyle']);
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
        $num        = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $info_style = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

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

        return view('modules/top10_surnames/config', [
            'num'         => $num,
            'info_style'  => $info_style,
            'info_styles' => $info_styles,
        ]);
    }
}
