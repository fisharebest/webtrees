<?php
declare(strict_types = 1);
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserWelcomeModule
 */
class UserWelcomeModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('My page');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “My page” module */
        return I18N::translate('A greeting message and useful links for a user.');
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
        $gedcomid   = $tree->getUserPreference(Auth::user(), 'gedcomid');
        $individual = Individual::getInstance($gedcomid, $tree);
        $links      = [];

        if ($individual) {
            if (Module::isActiveChart($tree, 'pedigree_chart')) {
                $links[] = [
                    'url'   => route('pedigree', [
                        'xref' => $individual->getXref(),
                        'ged'  => $individual->getTree()->getName(),
                    ]),
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
            'url'   => route('my-account', []),
            'title' => I18N::translate('My account'),
            'icon'  => 'icon-mypage',
        ];
        $content = view('modules/user_welcome/welcome', ['links' => $links]);

        /* I18N: A %s is the user’s name */
        $title = I18N::translate('Welcome %s', Auth::user()->getRealName());

        if ($template) {
            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $title,
                'content'    => $content,
            ]);
        }

        return $content;
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
        return false;
    }

    /**
     * Update the configuration for a block.
     *
     * @param Request $request
     * @param int     $block_id
     *
     * @return void
     */
    public function saveBlockConfiguration(Request $request, int $block_id)
    {
    }

    /**
     * An HTML form to edit block settings
     *
     * @param Tree $tree
     * @param int  $block_id
     *
     * @return void
     */
    public function editBlockConfiguration(Tree $tree, int $block_id)
    {
    }
}
