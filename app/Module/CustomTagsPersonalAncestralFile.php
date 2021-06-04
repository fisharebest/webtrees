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
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsPersonalAncestralFile
 */
class CustomTagsPersonalAncestralFile extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return array<string,ElementInterface>
     *
     * @see https://wiki-de.genealogy.net/GEDCOM/_Nutzerdef-Tag
     */
    public function customTags(): array
    {
        return [
            'INDI:NAME:_ADPN' => new NamePersonal(I18N::translate('Adopted name'), []),
            'INDI:NAME:_AKA'  => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_AKAN' => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:_EMAIL'     => new AddressEmail(I18N::translate('Email address')),
            'URL'             => new CustomElement(I18N::translate('URL')),
            '_HEB'            => new CustomElement(I18N::translate('Hebrew')),
            '_NAME'           => new CustomElement(I18N::translate('Mailing name')),
            '_SCBK'           => new CustomElement(I18N::translate('Scrapbook')),
            '_SSHOW'          => new CustomElement(I18N::translate('Slide show')),
            '_TYPE'           => new CustomElement(I18N::translate('Media type')),
            '_URL'            => new CustomElement(I18N::translate('URL')),
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Personal Ancestral File™';
    }
}
