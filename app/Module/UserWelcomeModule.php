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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;

/**
 * Class UserWelcomeModule
 */
class UserWelcomeModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('My page');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “My page” module */
        return I18N::translate('A greeting message and useful links for a user.');
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
        $gedcomid   = $tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);
        $individual = Registry::individualFactory()->make($gedcomid, $tree);
        $links      = [];

        $pedigree_chart = $this->module_service->findByComponent(ModuleChartInterface::class, $tree, Auth::user())
            ->first(static function (ModuleInterface $module): bool {
                return $module instanceof PedigreeChartModule;
            });

        if ($individual instanceof Individual) {
            if ($pedigree_chart instanceof PedigreeChartModule) {
                $links[] = [
                    'url'   => $pedigree_chart->chartUrl($individual),
                    'title' => I18N::translate('Default chart'),
                    'icon'  => 'icon-pedigree',
                ];
            }

            $links[] = [
                'url'   => $individual->url(),
                'title' => I18N::translate('My individual record'),
                'icon'  => 'icon-indis',
            ];
        }

        $links[] = [
            'url'   => route(AccountEdit::class, ['tree' => $tree->name()]),
            'title' => I18N::translate('My account'),
            'icon'  => 'icon-mypage',
        ];
        $content = view('modules/user_welcome/welcome', ['links' => $links]);

        $real_name = "\u{2068}" . e(Auth::user()->realName()) . "\u{2069}";

        /* I18N: A %s is the user’s name */
        $title = I18N::translate('Welcome %s', $real_name);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
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
        return false;
    }
}
