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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function time;

class HtmlBlockModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private HtmlService $html_service;

    /**
     * HtmlBlockModule bootstrap.
     *
     * @param HtmlService $html_service
     */
    public function __construct(HtmlService $html_service)
    {
        $this->html_service = $html_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('HTML');
    }

    public function description(): string
    {
        /* I18N: Description of the “HTML” module */
        return I18N::translate('Add your own text and graphics.');
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
        $statistics = Registry::container()->get(Statistics::class);

        $title          = $this->getBlockSetting($block_id, 'title');
        $content        = $this->getBlockSetting($block_id, 'html');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp');
        $languages      = $this->getBlockSetting($block_id, 'languages');

        // Only show this block for certain languages
        if ($languages && !in_array(I18N::languageTag(), explode(',', $languages), true)) {
            return '';
        }

        // Retrieve text, process embedded variables
        $title   = $statistics->embedTags($title);
        $content = $statistics->embedTags($content);

        $block_timestamp = (int) $this->getBlockSetting($block_id, 'timestamp', (string) time());

        if ($show_timestamp === '1') {
            $content .= '<br>' . view('components/datetime', ['timestamp' => Registry::timestampFactory()->make($block_timestamp)]);
        }

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => e(strip_tags($title)),
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
     * @param int                    $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(ServerRequestInterface $request, int $block_id): void
    {
        $title          = Validator::parsedBody($request)->string('title');
        $html           = Validator::parsedBody($request)->string('html');
        $show_timestamp = Validator::parsedBody($request)->boolean('show_timestamp');
        $languages      = Validator::parsedBody($request)->array('languages');

        $this->setBlockSetting($block_id, 'title', $title);
        $this->setBlockSetting($block_id, 'html', $this->html_service->sanitize($html));
        $this->setBlockSetting($block_id, 'show_timestamp', (string) $show_timestamp);
        $this->setBlockSetting($block_id, 'timestamp', (string) time());
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));
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
        $title          = $this->getBlockSetting($block_id, 'title');
        $html           = $this->getBlockSetting($block_id, 'html');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
        $languages      = explode(',', $this->getBlockSetting($block_id, 'languages'));

        $templates = [
            $html                                                       => I18N::translate('Custom'),
            view('modules/html/template-keywords')                      => I18N::translate('Keyword examples'),
            view('modules/html/template-narrative')                     => I18N::translate('Narrative description'),
            view('modules/html/template-statistics', ['tree' => $tree]) => I18N::translate('Statistics'),
        ];

        return view('modules/html/config', [
            'html'           => $html,
            'languages'      => $languages,
            'show_timestamp' => $show_timestamp,
            'templates'      => $templates,
            'title'          => $title,
        ]);
    }
}
