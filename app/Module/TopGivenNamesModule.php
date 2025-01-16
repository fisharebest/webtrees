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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TopGivenNamesModule
 */
class TopGivenNamesModule extends AbstractModule implements ModuleBlockInterface
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
        return I18N::translate('Top given names');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Top given names” module */
        return I18N::translate('A list of the most popular given names.');
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
        $statistics = app(Statistics::class);

        $num       = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $infoStyle = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

        extract($config, EXTR_OVERWRITE);

        switch ($infoStyle) {
            case 'list':
                $content = view('modules/top10_givnnames/block', [
                    'males'   => $statistics->commonGivenMaleListTotals('1', $num),
                    'females' => $statistics->commonGivenFemaleListTotals('1', $num),
                ]);
                break;
            default:
            case 'table':
                $content = view('modules/top10_givnnames/block', [
                    'males'   => $statistics->commonGivenMaleTable('1', $num),
                    'females' => $statistics->commonGivenFemaleTable('1', $num),
                ]);
                break;
        }

        if ($context !== self::CONTEXT_EMBED) {
            $num = (int) $num;

            if ($num === 1) {
                // I18N: i.e. most popular given name.
                $title = I18N::translate('Top given name');
            } else {
                // I18N: Title for a list of the most common given names, %s is a number. Note that a separate translation exists when %s is 1
                $title = I18N::plural('Top %s given name', 'Top %s given names', $num, I18N::number($num));
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
     * @param int $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $num        = Validator::parsedBody($request)->integer('num');
        $info_style = Validator::parsedBody($request)->string('infoStyle');

        $this->setBlockSetting($block_id, 'num', (string) $num);
        $this->setBlockSetting($block_id, 'infoStyle', $info_style);
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
        $num        = (int) $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER);
        $info_style = $this->getBlockSetting($block_id, 'infoStyle', self::DEFAULT_STYLE);

        $info_styles = [
            /* I18N: An option in a list-box */
            'list'  => I18N::translate('list'),
            /* I18N: An option in a list-box */
            'table' => I18N::translate('table'),
        ];

        return view('modules/top10_givnnames/config', [
            'info_style'  => $info_style,
            'info_styles' => $info_styles,
            'num'         => $num,
        ]);
    }
}
