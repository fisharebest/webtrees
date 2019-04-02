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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class FrequentlyAskedQuestionsModule
 */
class FrequentlyAskedQuestionsModule extends AbstractModule implements ModuleConfigInterface, ModuleMenuInterface
{
    use ModuleConfigTrait;
    use ModuleMenuTrait;

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
        if ($this->faqsExist($tree, WT_LOCALE)) {
            return new Menu($this->title(), route('module', [
                'module' => $this->name(),
                'action' => 'Show',
                'ged'    => $tree->name(),
            ]), 'menu-help');
        }

        return null;
    }

    /**
     * @param Tree $tree
     *
     * @return ResponseInterface
     */
    public function getAdminAction(Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $faqs = $this->faqsForTree($tree);

        $min_block_order = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->min('block_order');

        $max_block_order = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where(static function (Builder $query) use ($tree): void {
                $query
                    ->whereNull('gedcom_id')
                    ->orWhere('gedcom_id', '=', $tree->id());
            })
            ->max('block_order');

        $title = I18N::translate('Frequently asked questions') . ' — ' . $tree->title();

        return $this->viewResponse('modules/faq/config', [
            'faqs'            => $faqs,
            'max_block_order' => $max_block_order,
            'min_block_order' => $min_block_order,
            'title'           => $title,
            'tree'            => $tree,
            'tree_names'      => Tree::getNameList(),
        ]);
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

        DB::table('block_setting')->where('block_id', '=', $block_id)->delete();

        DB::table('block')->where('block_id', '=', $block_id)->delete();

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
    public function postAdminMoveDownAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $block_id = (int) $request->get('block_id');

        $block_order = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('block_order');

        $swap_block = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('block_order', '=', static function (Builder $query) use ($block_order): void {
                $query
                    ->from('block')
                    ->where('module_name', '=', $this->name())
                    ->where('block_order', '>', $block_order)
                    ->select(DB::raw('MIN(block_order)'));
            })
            ->select(['block_order', 'block_id'])
            ->first();

        if ($swap_block !== null) {
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
    public function postAdminMoveUpAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $block_id = (int) $request->get('block_id');

        $block_order = DB::table('block')
            ->where('block_id', '=', $block_id)
            ->value('block_order');

        $swap_block = DB::table('block')
            ->where('module_name', '=', $this->name())
            ->where('block_order', '=', static function (Builder $query) use ($block_order): void {
                $query
                    ->from('block')
                    ->where('module_name', '=', $this->name())
                    ->where('block_order', '<', $block_order)
                    ->select(DB::raw('MAX(block_order)'));
            })
            ->select(['block_order', 'block_id'])
            ->first();

        if ($swap_block !== null) {
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
    public function getAdminEditAction(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $block_id = (int) $request->get('block_id');

        if ($block_id === 0) {
            // Creating a new faq
            $header  = '';
            $faqbody = '';

            $block_order = 1 + (int) DB::table('block')
                    ->where('module_name', '=', $this->name())
                    ->max('block_order');

            $languages = [];

            $title = I18N::translate('Add an FAQ');
        } else {
            // Editing an existing faq
            $header  = $this->getBlockSetting($block_id, 'header');
            $faqbody = $this->getBlockSetting($block_id, 'faqbody');

            $block_order = DB::table('block')
                ->where('block_id', '=', $block_id)
                ->value('block_order');

            $languages = explode(',', $this->getBlockSetting($block_id, 'languages'));

            $title = I18N::translate('Edit the FAQ');
        }

        $tree_names = ['' => I18N::translate('All')] + Tree::getIdList();

        return $this->viewResponse('modules/faq/edit', [
            'block_id'    => $block_id,
            'block_order' => $block_order,
            'header'      => $header,
            'faqbody'     => $faqbody,
            'languages'   => $languages,
            'title'       => $title,
            'tree'        => $tree,
            'tree_names'  => $tree_names,
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
        $faqbody     = $request->get('faqbody', '');
        $header      = $request->get('header', '');
        $languages   = $request->get('languages', []);
        $gedcom_id   = (int) $request->get('gedcom_id') ?: null;
        $block_order = (int) $request->get('block_order');

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

        $this->setBlockSetting($block_id, 'faqbody', $faqbody);
        $this->setBlockSetting($block_id, 'header', $header);
        $this->setBlockSetting($block_id, 'languages', implode(',', $languages));

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
    public function getShowAction(Tree $tree): ResponseInterface
    {
        // Filter foreign languages.
        $faqs = $this->faqsForTree($tree)
            ->filter(static function (stdClass $faq): bool {
                return $faq->languages === '' || in_array(WT_LOCALE, explode(',', $faq->languages));
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
     * @return Collection
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
            ->get();
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
            ->filter(static function (stdClass $faq) use ($language): bool {
                return $faq->languages === '' || in_array($language, explode(',', $faq->languages), true);
            })
            ->isNotEmpty();
    }
}
