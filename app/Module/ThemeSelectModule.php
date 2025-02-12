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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;

use function app;
use function assert;
use function view;

/**
 * Class ThemeSelectModule
 */
class ThemeSelectModule extends AbstractModule implements ModuleBlockInterface
{
    use ModuleBlockTrait;

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Theme change');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Theme change” module */
        return I18N::translate('An alternative way to select a new theme.');
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
        $theme = app(ModuleThemeInterface::class);
        assert($theme instanceof ModuleThemeInterface);

        $menu = $theme->menuThemes();

        if ($menu instanceof Menu) {
            $content = '<ul class="nav text-justify" role="menu">' . view('components/menu-item', ['menu' => $menu]) . '</ul>';

            if ($context !== self::CONTEXT_EMBED) {
                return view('modules/block-template', [
                    'block'      => Str::kebab($this->name()),
                    'id'         => $block_id,
                    'config_url' => '',
                    'title'      => $this->title(),
                    'content'    => $content,
                ]);
            }

            return $content;
        }

        return '';
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
        return true;
    }
}
