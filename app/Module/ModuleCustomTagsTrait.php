<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait ModuleCustomTagsTrait
 */
trait ModuleCustomTagsTrait
{
    use ViewResponseTrait;

    public function boot(): void
    {
        $element_factory = Registry::elementFactory();
        $element_factory->register($this->customTags());

        foreach ($this->customSubTags() as $tag => $children) {
            foreach ($children as $child) {
                $element_factory->make($tag)->subtag(...$child);
            }
        }
    }

    /**
     * @see https://www.gencom.org.nz/GEDCOM_tags.html
     *
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [];
    }

    /**
     * @return array<string,array<int,array<int,string>>>
     */
    public function customSubTags(): array
    {
        return [];
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Custom GEDCOM tags” module */
        return I18N::translate('Support for non-standard GEDCOM tags.') . ' — ' . $this->customTagApplication();
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return '';
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse('modules/custom-tags/config', [
            'element_factory' => Registry::elementFactory(),
            'subtags'         => $this->customSubTags(),
            'tags'            => $this->customTags(),
            'title'           => $this->title(),
        ]);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Custom GEDCOM tags') . ' — ' . $this->customTagApplication();
    }
}
