<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TopPageViewsModule
 */
class TopPageViewsModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private const DEFAULT_NUMBER_TO_SHOW = '10';

    private const PAGES = ['individual.php', 'family.php', 'source.php', 'repo.php', 'note.php', 'mediaviewer.php'];

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Most viewed pages');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Most viewed pages” module */
        return I18N::translate('A list of the pages that have been viewed the most number of times.');
    }

    /**
     * Generate the HTML content of this block.
     *
     * @param Tree          $tree
     * @param int           $block_id
     * @param string        $context
     * @param array<string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $num = (int) $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER_TO_SHOW);

        extract($config, EXTR_OVERWRITE);

        $query = DB::table('hit_counter')
            ->where('gedcom_id', '=', $tree->id())
            ->whereIn('page_name', self::PAGES)
            ->orderByDesc('page_count');

        $results = [];
        foreach ($query->cursor() as $row) {
            $record = Registry::gedcomRecordFactory()->make($row->page_parameter, $tree);

            if ($record instanceof GedcomRecord && $record->canShow()) {
                $results[] = [
                    'record' => $record,
                    'count'  => $row->page_count,
                ];
            }

            if (count($results) === $num) {
                break;
            }
        }

        $content = view('modules/top10_pageviews/list', ['results' => $results]);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => $this->configUrl($tree, $context, $block_id),
                'title'      => $this->title(),
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
     * Can this block be shown on the user’s home page?
     *
     * @return bool
     */
    public function isUserBlock(): bool
    {
        return false;
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
        $num = $this->getBlockSetting($block_id, 'num', self::DEFAULT_NUMBER_TO_SHOW);

        return view('modules/top10_pageviews/config', [
            'num' => $num,
        ]);
    }
}
