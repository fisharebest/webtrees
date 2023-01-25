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
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function redirect;
use function route;

/**
 * Class StoriesModule
 */
class StoriesModule extends AbstractModule implements ModuleConfigInterface, ModuleMenuInterface, ModuleTabInterface
{
    use ModuleTabTrait;
    use ModuleConfigTrait;
    use ModuleMenuTrait;

    private HtmlService $html_service;

    private TreeService $tree_service;

    /**
     * StoriesModule constructor.
     *
     * @param HtmlService $html_service
     * @param TreeService $tree_service
     */
    public function __construct(HtmlService $html_service, TreeService $tree_service)
    {
        $this->html_service = $html_service;
        $this->tree_service = $tree_service;
    }

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected int $access_level = Auth::PRIV_HIDE;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Stories” module */
        return I18N::translate('Add narrative stories to individuals in the family tree.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 7;
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 9;
    }

    /**
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/stories/tab', [
            'is_admin'   => Auth::isAdmin(),
            'individual' => $individual,
            'stories'    => $this->getStoriesForIndividual($individual),
            'tree'       => $individual->tree(),
        ]);
    }

    /**
     * @param Individual $individual
     *
     * @return array<object>
     */
    private function getStoriesForIndividual(Individual $individual): array
    {
        $block_ids = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('xref', '=', $individual->xref())
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->pluck('block_id');

        $stories = [];
        foreach ($block_ids as $block_id) {
            $block_id = (int) $block_id;

            // Only show this block for certain languages
            $languages = $this->getBlockSetting($block_id, 'languages');
            if ($languages === '' || in_array(I18N::languageTag(), explode(',', $languages), true)) {
                $stories[] = (object) [
                    'block_id'   => $block_id,
                    'title'      => $this->getBlockSetting($block_id, 'title'),
                    'story_body' => $this->getBlockSetting($block_id, 'story_body'),
                ];
            }
        }

        return $stories;
    }

    /**
     * Is this tab empty? If so, we don't always need to display it.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasTabContent(Individual $individual): bool
    {
        return Auth::isManager($individual->tree()) || $this->getStoriesForIndividual($individual) !== [];
    }

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function isGrayedOut(Individual $individual): bool
    {
        return $this->getStoriesForIndividual($individual) === [];
    }

    /**
     * Can this tab load asynchronously?
     *
     * @return bool
     */
    public function canLoadAjax(): bool
    {
        return false;
    }

    /**
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree): ?Menu
    {
        return new Menu($this->title(), route('module', [
            'module' => $this->name(),
            'action' => 'ShowList',
            'tree'    => $tree->name(),
        ]), 'menu-story');
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Stories');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        // This module can't run without a tree
        $tree = Validator::attributes($request)->treeOptional();

        if (!$tree instanceof Tree) {
            $tree = $this->tree_service->all()->first();
            if ($tree instanceof Tree) {
                return redirect(route('module', ['module' => $this->name(), 'action' => 'Admin', 'tree' => $tree->name()]));
            }

            return redirect(route(ControlPanel::class));
        }

        $stories = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('gedcom_id', '=', $tree->id())
            ->orderBy('xref')
            ->get();

        foreach ($stories as $story) {
            $block_id = (int) $story->block_id;
            $xref     = (string) $story->xref;

            $story->individual = Registry::individualFactory()->make($xref, $tree);
            $story->title      = $this->getBlockSetting($block_id, 'title');
            $story->languages  = $this->getBlockSetting($block_id, 'languages');
        }

        $tree_names = $this->tree_service->all()->map(static function (Tree $tree): string {
            return $tree->title();
        });

        return $this->viewResponse('modules/stories/config', [
            'module'     => $this->name(),
            'stories'    => $stories,
            'title'      => $this->title() . ' — ' . $tree->title(),
            'tree'       => $tree,
            'tree_names' => $tree_names,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
            'tree'   => Validator::parsedBody($request)->string('tree'),
        ]));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree     = Validator::attributes($request)->tree();
        $block_id = Validator::queryParams($request)->integer('block_id', 0);
        $url      = Validator::queryParams($request)->string('url', '');

        if ($block_id === 0) {
            // Creating a new story
            $story_title = '';
            $story_body  = '';
            $languages   = [];
            $xref        = Validator::queryParams($request)->isXref()->string('xref', '');
            $title       = I18N::translate('Add a story') . ' — ' . e($tree->title());
        } else {
            // Editing an existing story
            $xref = (string) DB::table('block')
                ->where('block_id', '=', $block_id)
                ->value('xref');

            $story_title = $this->getBlockSetting($block_id, 'title');
            $story_body  = $this->getBlockSetting($block_id, 'story_body');
            $languages   = explode(',', $this->getBlockSetting($block_id, 'languages'));
            $title       = I18N::translate('Edit the story') . ' — ' . e($tree->title());
        }

        $individual = Registry::individualFactory()->make($xref, $tree);

        return $this->viewResponse('modules/stories/edit', [
            'block_id'    => $block_id,
            'languages'   => $languages,
            'story_body'  => $story_body,
            'story_title' => $story_title,
            'title'       => $title,
            'tree'        => $tree,
            'url'         => $url,
            'individual'  => $individual,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $block_id    = Validator::queryParams($request)->integer('block_id', 0);
        $xref        = Validator::parsedBody($request)->string('xref');
        $story_body  = Validator::parsedBody($request)->string('story_body');
        $story_title = Validator::parsedBody($request)->string('story_title');
        $languages   = Validator::parsedBody($request)->array('languages');
        $default_url = route('module', ['module' => $this->name(), 'action' => 'Admin', 'tree' => $tree->name()]);
        $url         = Validator::parsedBody($request)->isLocalUrl()->string('url', $default_url);
        $story_body  = $this->html_service->sanitize($story_body);

        if ($block_id !== 0) {
            DB::table('block')
                ->where('block_id', '=', $block_id)
                ->update([
                    'gedcom_id' => $tree->id(),
                    'xref'      => $xref,
                ]);
        } else {
            DB::table('block')->insert([
                'gedcom_id'   => $tree->id(),
                'xref'        => $xref,
                'module_name' => $this->name(),
                'block_order' => 0,
            ]);

            $block_id = (int) DB::connection()->getPdo()->lastInsertId();
        }

        $this->setBlockSetting($block_id, 'story_body', $story_body);
        $this->setBlockSetting($block_id, 'title', $story_title);
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $block_id = Validator::queryParams($request)->integer('block_id');

        DB::table('block_setting')
            ->where('block_id', '=', $block_id)
            ->delete();

        DB::table('block')
            ->where('block_id', '=', $block_id)
            ->delete();

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
            'tree'    => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getShowListAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        $stories = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('gedcom_id', '=', $tree->id())
            ->get()
            ->map(function (object $story) use ($tree): object {
                $block_id = (int) $story->block_id;
                $xref     = (string) $story->xref;

                $story->individual = Registry::individualFactory()->make($xref, $tree);
                $story->title      = $this->getBlockSetting($block_id, 'title');
                $story->languages  = $this->getBlockSetting($block_id, 'languages');

                return $story;
            })->filter(static function (object $story): bool {
                // Filter non-existent and private individuals.
                return $story->individual instanceof Individual && $story->individual->canShow();
            })->filter(static function (object $story): bool {
                // Filter foreign languages.
                return $story->languages === '' || in_array(I18N::languageTag(), explode(',', $story->languages), true);
            });

        return $this->viewResponse('modules/stories/list', [
            'stories' => $stories,
            'title'   => $this->title(),
            'tree'    => $tree,
        ]);
    }
}
