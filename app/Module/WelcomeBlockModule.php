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
use Fisharebest\Webtrees\Http\RequestHandlers\RegisterPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;

/**
 * Class WelcomeBlockModule
 */
class WelcomeBlockModule extends AbstractModule implements ModuleBlockInterface
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
        return I18N::translate('Home page');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Home page” module */
        return I18N::translate('A greeting message for site visitors.');
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
        $individual = $tree->significantIndividual(Auth::user());

        $links = [];

        $pedigree_chart = $this->module_service
            ->findByComponent(ModuleChartInterface::class, $tree, Auth::user())
            ->first(static function (ModuleInterface $module): bool {
                return $module instanceof PedigreeChartModule;
            });

        if ($pedigree_chart instanceof PedigreeChartModule) {
            $links[] = [
                $pedigree_chart->chartUrl($individual),
                'url'   => $pedigree_chart->chartUrl($individual),
                'title' => I18N::translate('Default chart'),
                'icon'  => 'icon-pedigree',
            ];
        }

        $links[] = [
            'url'   => $individual->url(),
            'title' => I18N::translate('Default individual'),
            'icon'  => 'icon-indis',
        ];

        if (Site::getPreference('USE_REGISTRATION_MODULE') === '1' && !Auth::check()) {
            $links[] = [
                'url'   => route(RegisterPage::class, ['tree' => $tree->name()]),
                'title' => I18N::translate('Request a new user account'),
                'icon'  => 'icon-user_add',
            ];
        }

        $content = view('modules/gedcom_block/welcome', ['links' => $links]);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => e($individual->tree()->title()),
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
}
