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
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StoriesModule
 */
class StoriesModule extends AbstractModule implements ModuleTabInterface, ModuleConfigInterface, ModuleMenuInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Stories');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Stories” module */
        return I18N::translate('Add narrative stories to individuals in the family tree.');
    }

    /**
     * The URL to a page where the user can modify the configuration of this module.
     *
     * @return string
     */
    public function getConfigLink(): string
    {
        return route('module', [
            'module' => $this->getName(),
            'action' => 'Admin',
        ]);
    }

    /** {@inheritdoc} */
    public function defaultTabOrder(): int
    {
        return 55;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/stories/tab', [
            'is_admin'   => Auth::isAdmin(),
            'individual' => $individual,
            'stories'    => $this->getStoriesForIndividual($individual),
        ]);
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return Auth::isManager($individual->tree()) || !empty($this->getStoriesForIndividual($individual));
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return !empty($this->getStoriesForIndividual($individual));
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return false;
    }

    /**
     * @param Individual $individual
     *
     * @return stdClass[]
     */
    private function getStoriesForIndividual(Individual $individual): array
    {
        $block_ids = DB::table('block')
            ->where('module_name', '=', $this->getName())
            ->where('xref', '=', $individual->xref())
            ->where('gedcom_id', '=', $individual->tree()->id())
            ->pluck('block_id');

        $stories = [];
        foreach ($block_ids as $block_id) {
            $block_id = (int) $block_id;

            // Only show this block for certain languages
            $languages = $this->getBlockSetting($block_id, 'languages', '');
            if ($languages === '' || in_array(WT_LOCALE, explode(',', $languages))) {
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
     * The user can re-order menus. Until they do, they are shown in this order.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 30;
    }

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int
     */
    public function defaultAccessLevel(): int
    {
        return Auth::PRIV_HIDE;
    }

    /**
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree)
    {
        $menu = new Menu($this->getTitle(), route('module', [
            'module' => $this->getName(),
            'action' => 'ShowList',
            'ged'    => $tree->name(),
        ]), 'menu-story');

        return $menu;
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function getAdminAction(Tree $tree): Response
    {
        $this->layout = 'layouts/administration';

        $stories = DB::table('block')
            ->where('module_name', '=', $this->getName())
            ->where('gedcom_id', '=', $tree->id())
            ->orderBy('xref')
            ->get();

        foreach ($stories as $story) {
            $block_id = (int) $story->block_id;

            $story->individual = Individual::getInstance($story->xref, $tree);
            $story->title      = $this->getBlockSetting($block_id, 'title');
            $story->languages  = $this->getBlockSetting($block_id, 'languages');
        }

        return $this->viewResponse('modules/stories/config', [
            'stories'    => $stories,
            'title'      => $this->getTitle() . ' — ' . $tree->title(),
            'tree'       => $tree,
            'tree_names' => Tree::getNameList(),
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function getAdminEditAction(Request $request, Tree $tree): Response
    {
        $this->layout = 'layouts/administration';

        $block_id = (int) $request->get('block_id');

        if ($block_id === 0) {
            // Creating a new story
            $individual  = Individual::getInstance($request->get('xref', ''), $tree);
            $story_title = '';
            $story_body  = '';
            $languages   = [];

            $title = I18N::translate('Add a story') . ' — ' . e($tree->title());
        } else {
            // Editing an existing story
            $xref = (string) DB::table('block')
                ->where('block_id', '=', $block_id)
                ->value('xref');

            $individual  = Individual::getInstance($xref, $tree);
            $story_title = $this->getBlockSetting($block_id, 'title', '');
            $story_body  = $this->getBlockSetting($block_id, 'story_body', '');
            $languages   = explode(',', $this->getBlockSetting($block_id, 'languages'));

            $title = I18N::translate('Edit the story') . ' — ' . e($tree->title());
        }

        return $this->viewResponse('modules/stories/edit', [
            'block_id'    => $block_id,
            'languages'   => $languages,
            'story_body'  => $story_body,
            'story_title' => $story_title,
            'title'       => $title,
            'tree'        => $tree,
            'individual'  => $individual,
        ]);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return RedirectResponse
     */
    public function postAdminEditAction(Request $request, Tree $tree): RedirectResponse
    {
        $block_id    = (int) $request->get('block_id');
        $xref        = $request->get('xref', '');
        $story_body  = $request->get('story_body', '');
        $story_title = $request->get('story_title', '');
        $languages   = $request->get('languages', []);

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
                'module_name' => $this->getName(),
                'block_order' => 0,
            ]);

            $block_id = (int) DB::connection()->getPdo()->lastInsertId();
        }

        $this->setBlockSetting($block_id, 'story_body', $story_body);
        $this->setBlockSetting($block_id, 'title', $story_title);
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));

        $url = route('module', [
            'module' => 'stories',
            'action' => 'Admin',
            'ged'    => $tree->name(),
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return Response
     */
    public function postAdminDeleteAction(Request $request, Tree $tree): Response
    {
        $block_id = (int) $request->get('block_id');

        DB::table('block_setting')
            ->where('block_id', '=', $block_id)
            ->delete();

        DB::table('block')
            ->where('block_id', '=', $block_id)
            ->delete();

        $url = route('module', [
            'module' => 'stories',
            'action' => 'Admin',
            'ged'    => $tree->name(),
        ]);

        return new RedirectResponse($url);
    }

    /**
     * @param Tree $tree
     *
     * @return Response
     */
    public function getShowListAction(Tree $tree): Response
    {
        $stories = DB::table('block')
            ->where('module_name', '=', $this->getName())
            ->where('gedcom_id', '=', $tree->id())
            ->get()
            ->map(function (stdClass $story) use ($tree): stdClass {
                $block_id = (int) $story->block_id;

                $story->individual = Individual::getInstance($story->xref, $tree);
                $story->title      = $this->getBlockSetting($block_id, 'title');
                $story->languages  = $this->getBlockSetting($block_id, 'languages');

                return $story;
            })->filter(function (stdClass $story): bool {
                // Filter non-existant and private individuals.
                return $story->individual instanceof Individual && $story->individual->canShow();
            })->filter(function (stdClass $story): bool {
                // Filter foreign languages.
                return $story->languages === '' || in_array(WT_LOCALE, explode(',', $story->languages));
            });

        return $this->viewResponse('modules/stories/list', [
            'stories' => $stories,
            'title'   => $this->getTitle(),
        ]);
    }
}
