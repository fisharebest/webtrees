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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WelcomeBlockModule
 */
class WelcomeBlockModule extends AbstractModule implements ModuleBlockInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Home page');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Home page” module */
        return I18N::translate('A greeting message for site visitors.');
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
        $individual = $tree->getSignificantIndividual();

        $links = [];

        if (Module::isActiveChart($individual->getTree(), 'pedigree_chart')) {
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
            'title' => I18N::translate('Default individual'),
            'icon'  => 'icon-indis',
        ];

        if (Site::getPreference('USE_REGISTRATION_MODULE') === '1' && !Auth::check()) {
            $links[] = [
                'url'   => route('register'),
                'title' => I18N::translate('Request a new user account'),
                'icon'  => 'icon-user_add',
            ];
        }

        $content = view('modules/gedcom_block/welcome', ['links' => $links]);

        if ($template) {
            return view('modules/block-template', [
                'block'      => str_replace('_', '-', $this->getName()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $individual->getTree()->getTitle(),
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
        return false;
    }

    /** {@inheritdoc} */
    public function isGedcomBlock(): bool
    {
        return true;
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
