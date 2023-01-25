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

use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\HtmlService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function redirect;
use function route;

/**
 * Class FrequentlyAskedQuestionsModule
 */
class FrequentlyAskedQuestionsModule extends AbstractModule implements ModuleConfigInterface, ModuleMenuInterface
{
    use ModuleConfigTrait;
    use ModuleMenuTrait;

    private HtmlService $html_service;

    private TreeService $tree_service;

    /**
     * FrequentlyAskedQuestionsModule constructor.
     *
     * @param HtmlService $html_service
     * @param TreeService $tree_service
     */
    public function __construct(HtmlService $html_service, TreeService $tree_service)
    {
        $this->html_service = $html_service;
        $this->tree_service = $tree_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module. Abbreviation for “Frequently Asked Questions” */
        return I18N::translate('FAQ');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “FAQ” module */
        return I18N::translate('A list of frequently asked questions and answers.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 8;
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
        if ($this->faqsExist($tree, I18N::languageTag())) {
            return new Menu($this->title(), route('module', [
                'module' => $this->name(),
                'action' => 'Show',
                'tree'   => $tree->name(),
            ]), 'menu-faq');
        }

        return null;
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
            $trees = $this->tree_service->all();

            $tree = $trees->get(Site::getPreference('DEFAULT_GEDCOM')) ?? $trees->first();

            if ($tree instanceof Tree) {
                return redirect(route('module', ['module' => $this->name(), 'action' => 'Admin', 'tree' => $tree->name()]));
            }

            return redirect(route(ControlPanel::class));
        }

        $faqs = $this->faqsForTree($tree);

        $min_block_order = (int) DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->min('block_order');

        $max_block_order = (int) DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->max('block_order');

        $title = I18N::translate('Frequently asked questions') . ' — ' . $tree->title();

        return $this->viewResponse('modules/faq/config', [
            'action'          => route('module', ['module' => $this->name(), 'action' => 'Admin']),
            'faqs'            => $faqs,
            'max_block_order' => $max_block_order,
            'min_block_order' => $min_block_order,
            'module'          => $this->name(),
            'title'           => $title,
            'tree'            => $tree,
            'tree_names'      => $this->tree_service->titles(),
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
    public function postAdminDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $block_id = Validator::queryParams($request)->integer('block_id');

        DB::table('block_setting')->where('block_id', '=', $block_id)->delete();

        DB::table('block')->where('block_id', '=', $block_id)->delete();

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminMoveDownAction(ServerRequestInterface $request): ResponseInterface
    {
        $block_id = Validator::queryParams($request)->integer('block_id');

        $block_order = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('block_order');

        $swap_block = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('block_order', '>', $block_order)
            ->orderBy('block_order')
            ->first();

        if ($block_order !== null && $swap_block !== null) {
            DB::table('block')
                ->where('block_id', '=', $block_id)
                ->update([
                    'block_order' => $swap_block->block_order,
                ]);

            DB::table('block')
                ->where('block_id', '=', $swap_block->block_id)
                ->update([
                    'block_order' => $block_order,
                ]);
        }

        return response();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminMoveUpAction(ServerRequestInterface $request): ResponseInterface
    {
        $block_id = Validator::queryParams($request)->integer('block_id');

        $block_order = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('block_order');

        $swap_block = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('block_order', '<', $block_order)
            ->orderBy('block_order', 'desc')
            ->first();

        if ($block_order !== null && $swap_block !== null) {
            DB::table('block')
                ->where('block_id', '=', $block_id)
                ->update([
                    'block_order' => $swap_block->block_order,
                ]);

            DB::table('block')
                ->where('block_id', '=', $swap_block->block_id)
                ->update([
                    'block_order' => $block_order,
                ]);
        }

        return response();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $block_id = Validator::queryParams($request)->integer('block_id', 0);

        if ($block_id === 0) {
            // Creating a new faq
            $header      = '';
            $body        = '';
            $gedcom_id   = null;
            $block_order = 1 + (int) DB::table('block')->where('module_name', '=', $this->name())->max('block_order');

            $languages = [];

            $title = I18N::translate('Add an FAQ');
        } else {
            // Editing an existing faq
            $header      = $this->getBlockSetting($block_id, 'header');
            $body        = $this->getBlockSetting($block_id, 'faqbody');
            $gedcom_id   = DB::table('block')->where('block_id', '=', $block_id)->value('gedcom_id');
            $block_order = DB::table('block')->where('block_id', '=', $block_id)->value('block_order');

            $languages = explode(',', $this->getBlockSetting($block_id, 'languages'));

            $title = I18N::translate('Edit the FAQ');
        }

        $gedcom_ids = $this->tree_service->all()
            ->mapWithKeys(static function (Tree $tree): array {
                return [$tree->id() => $tree->title()];
            })
            ->all();

        $gedcom_ids = ['' => I18N::translate('All')] + $gedcom_ids;

        return $this->viewResponse('modules/faq/edit', [
            'block_id'    => $block_id,
            'block_order' => $block_order,
            'header'      => $header,
            'body'        => $body,
            'languages'   => $languages,
            'title'       => $title,
            'gedcom_id'   => $gedcom_id,
            'gedcom_ids'  => $gedcom_ids,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $block_id    = Validator::queryParams($request)->integer('block_id', 0);
        $body        = Validator::parsedBody($request)->string('body');
        $header      = Validator::parsedBody($request)->string('header');
        $languages   = Validator::parsedBody($request)->array('languages');
        $gedcom_id   = Validator::parsedBody($request)->string('gedcom_id');
        $block_order = Validator::parsedBody($request)->integer('block_order');

        if ($gedcom_id === '') {
            $gedcom_id = null;
        }

        $body    = $this->html_service->sanitize($body);
        $header  = $this->html_service->sanitize($header);

        if ($block_id !== 0) {
            DB::table('block')
                ->where('block_id', '=', $block_id)
                ->update([
                    'gedcom_id'   => $gedcom_id,
                    'block_order' => $block_order,
                ]);
        } else {
            DB::table('block')->insert([
                'gedcom_id'   => $gedcom_id,
                'module_name' => $this->name(),
                'block_order' => $block_order,
            ]);

            $block_id = (int) DB::connection()->getPdo()->lastInsertId();
        }

        $this->setBlockSetting($block_id, 'faqbody', $body);
        $this->setBlockSetting($block_id, 'header', $header);
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));

        $url = route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
        ]);

        return redirect($url);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getShowAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        // Filter foreign languages.
        $faqs = $this->faqsForTree($tree)
            ->filter(static function (object $faq): bool {
                return $faq->languages === '' || in_array(I18N::languageTag(), explode(',', $faq->languages), true);
            });

        return $this->viewResponse('modules/faq/show', [
            'faqs'  => $faqs,
            'title' => I18N::translate('Frequently asked questions'),
            'tree'  => $tree,
        ]);
    }

    /**
     * @param Tree $tree
     *
     * @return Collection<int,object>
     */
    private function faqsForTree(Tree $tree): Collection
    {
        return DB::table('block')
            ->join('block_setting AS bs1', 'bs1.block_id', '=', 'block.block_id')
            ->join('block_setting AS bs2', 'bs2.block_id', '=', 'block.block_id')
            ->join('block_setting AS bs3', 'bs3.block_id', '=', 'block.block_id')
            ->where('module_name', '=', $this->name())
            ->where('bs1.setting_name', '=', 'header')
            ->where('bs2.setting_name', '=', 'faqbody')
            ->where('bs3.setting_name', '=', 'languages')
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->orderBy('block_order')
            ->select(['block.block_id', 'block_order', 'gedcom_id', 'bs1.setting_value AS header', 'bs2.setting_value AS faqbody', 'bs3.setting_value AS languages'])
            ->get()
            ->map(static function (object $row): object {
                $row->block_id    = (int) $row->block_id;
                $row->block_order = (int) $row->block_order;
                $row->gedcom_id   = (int) $row->gedcom_id;

                return $row;
            });
    }

    /**
     * @param Tree   $tree
     * @param string $language
     *
     * @return bool
     */
    private function faqsExist(Tree $tree, string $language): bool
    {
        return DB::table('block')
            ->join('block_setting', 'block_setting.block_id', '=', 'block.block_id')
            ->where('module_name', '=', $this->name())
            ->where('setting_name', '=', 'languages')
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->select(['setting_value AS languages'])
            ->get()
            ->filter(static function (object $faq) use ($language): bool {
                return $faq->languages === '' || in_array($language, explode(',', $faq->languages), true);
            })
            ->isNotEmpty();
    }
}
