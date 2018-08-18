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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;

/**
 * Class ThemeSelectModule
 */
class ThemeSelectModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        /* I18N: Name of a module */
        return I18N::translate('Theme change');
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        /* I18N: Description of the “Theme change” module */
        return I18N::translate('An alternative way to select a new theme.');
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
        $menu = Theme::theme()->menuThemes();

        if ($menu) {
            $content = '<ul class="nav text-justify">' . $menu->bootstrap4() . '</ul>';

            if ($template) {
                return view('modules/block-template', [
                    'block'      => str_replace('_', '-', $this->getName()),
                    'id'         => $block_id,
                    'config_url' => '',
                    'title'      => $this->getTitle(),
                    'content'    => $content,
                ]);
            } else {
                return $content;
            }
        } else {
            return '';
        }
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
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function configureBlock(Tree $tree, int $block_id)
    {
    }
}
