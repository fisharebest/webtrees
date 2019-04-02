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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class StoriesModule
 */
class StoriesModule extends AbstractModule implements ModuleConfigInterface, ModuleMenuInterface, ModuleTabInterface
{
    use ModuleTabTrait;
    use ModuleConfigTrait;
    use ModuleMenuTrait;

    /** @var int The default access level for this module.  It can be changed in the control panel. */
    protected $access_level = Auth::PRIV_HIDE;

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

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        return view('modules/stories/tab', [
            'is_admin'   => Auth::isAdmin(),
            'individual' => $individual,
            'stories'    => $this->getStoriesForIndividual($individual),
        ]);
    }

    /**
     * @param Individual $individual
     *
     * @return stdClass[]
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
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree): ?Menu
    {
        $menu = new Menu($this->title(), route('module', [
            'module' => $this->name(),
            'action' => 'ShowList',
            'ged'    => $tree->name(),
        ]), 'menu-story');

        return $menu;
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
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function getAdminAction(Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $stories = DB::table('block')
            ->where('module_name', '=', $this->name())
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
            'title'      => $this->title() . ' — ' . $tree->title(),
            'tree'       => $tree,
            'tree_names' => Tree::getNameList(),
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function getAdminEditAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postAdminEditAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
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
                'module_name' => $this->name(),
                'block_order' => 0,
            ]);

            $block_id = (int) DB::connection()->getPdo()->lastInsertId();
        }

        $this->setBlockSetting($block_id, 'story_body', $story_body);
        $this->setBlockSetting($block_id, 'title', $story_title);
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
            'ged'    => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     *
     * @return ResponseInterface
     */
    public function postAdminDeleteAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $block_id = (int) $request->get('block_id');

        DB::table('block_setting')
            ->where('block_id', '=', $block_id)
            ->delete();

        DB::table('block')
            ->where('block_id', '=', $block_id)
            ->delete();

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
            'ged'    => $tree->name(),
        ]);

        return redirect($url);
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function getShowListAction(Tree $tree): ResponseInterface
    {
        $stories = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('gedcom_id', '=', $tree->id())
            ->get()
            ->map(function (stdClass $story) use ($tree): stdClass {
                $block_id = (int) $story->block_id;

                $story->individual = Individual::getInstance($story->xref, $tree);
                $story->title      = $this->getBlockSetting($block_id, 'title');
                $story->languages  = $this->getBlockSetting($block_id, 'languages');

                return $story;
            })->filter(static function (stdClass $story): bool {
                // Filter non-existant and private individuals.
                return $story->individual instanceof Individual && $story->individual->canShow();
            })->filter(static function (stdClass $story): bool {
                // Filter foreign languages.
                return $story->languages === '' || in_array(WT_LOCALE, explode(',', $story->languages));
            });

        return $this->viewResponse('modules/stories/list', [
            'stories' => $stories,
            'title'   => $this->title(),
        ]);
    }
}
