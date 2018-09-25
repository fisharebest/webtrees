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
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HtmlBlockModule
 */
class HtmlBlockModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('HTML');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “HTML” module */
        return I18N::translate('Add your own text and graphics.');
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

        $title          = $this->getBlockSetting($block_id, 'title', '');
        $content        = $this->getBlockSetting($block_id, 'html', '');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
        $languages      = $this->getBlockSetting($block_id, 'languages');

        // Only show this block for certain languages
        if ($languages && !in_array(WT_LOCALE, explode(',', $languages))) {
            return '';
        }

        $stats = new Stats($tree);

        /*
        * Retrieve text, process embedded variables
        */
        $title   = $stats->embedTags($title);
        $content = $stats->embedTags($content);

        if ($show_timestamp === '1') {
            $content .= '<br>' . FunctionsDate::formatTimestamp((int) $this->getBlockSetting($block_id, 'timestamp', (string) WT_TIMESTAMP) + WT_TIMESTAMP_OFFSET);
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
        $languages = (array) $request->get('lang');
        $this->setBlockSetting($block_id, 'title', $request->get('title', ''));
        $this->setBlockSetting($block_id, 'html', $request->get('html', ''));
        $this->setBlockSetting($block_id, 'show_timestamp', $request->get('show_timestamp', ''));
        $this->setBlockSetting($block_id, 'timestamp', $request->get('timestamp', ''));
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
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
        $templates = [
            I18N::translate('Keyword examples')      => view('modules/html/template-keywords', []),
            I18N::translate('Narrative description') => view('modules/html/template-narrative', []),
            I18N::translate('Statistics')            => view('modules/html/template-statistics', []),
        ];

        $title          = $this->getBlockSetting($block_id, 'title', '');
        $html           = $this->getBlockSetting($block_id, 'html', '');
        $show_timestamp = $this->getBlockSetting($block_id, 'show_timestamp', '0');
        $languages      = explode(',', $this->getBlockSetting($block_id, 'languages'));
        $all_trees      = Tree::getNameList();

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
