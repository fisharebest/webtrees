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
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class HtmlBlockModule
 */
class HtmlBlockModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

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

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “HTML” module */
        return I18N::translate('Add your own text and graphics.');
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
        $statistics = app(Statistics::class);

        $title          = $this->getBlockSetting($block_id, 'title', '');
        $content        = $this->getBlockSetting($block_id, 'html', '');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
        $languages      = $this->getBlockSetting($block_id, 'languages');

        // Only show this block for certain languages
        if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
            return '';
        }

        // Retrieve text, process embedded variables
        $title   = $statistics->embedTags($title);
        $content = $statistics->embedTags($content);

        $block_timestamp = (int) $this->getBlockSetting($block_id, 'timestamp', (string) Carbon::now()->unix());

        if ($show_timestamp === '1') {
            $content .= '<br>' . view('components/datetime', ['timestamp' => Carbon::createFromTimestamp($block_timestamp)]);
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
        $languages = (array) $request->get('lang');
        $this->setBlockSetting($block_id, 'title', $request->get('title', ''));
        $this->setBlockSetting($block_id, 'html', $request->get('html', ''));
        $this->setBlockSetting($block_id, 'show_timestamp', $request->get('show_timestamp', ''));
        $this->setBlockSetting($block_id, 'timestamp', (string) Carbon::now()->unix());
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));
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
        $title          = $this->getBlockSetting($block_id, 'title', '');
        $html           = $this->getBlockSetting($block_id, 'html', '');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
        $languages      = explode(',', $this->getBlockSetting($block_id, 'languages'));
        $all_trees      = Tree::getNameList();

        $templates = [
            $html                                    => I18N::translate('Custom'),
            view('modules/html/template-keywords')   => I18N::translate('Keyword examples'),
            view('modules/html/template-narrative')  => I18N::translate('Narrative description'),
            view('modules/html/template-statistics') => I18N::translate('Statistics'),
        ];

        echo view('modules/html/config', [
            'all_trees'      => $all_trees,
            'html'           => $html,
            'languages'      => $languages,
            'show_timestamp' => $show_timestamp,
            'templates'      => $templates,
            'title'          => $title,
        ]);
    }
}
