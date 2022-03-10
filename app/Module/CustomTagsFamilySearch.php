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
use Fisharebest\Webtrees\Elements\FamilySearchFamilyTreeId;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsFamilySearch
 */
class CustomTagsFamilySearch extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
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
     */
    public function customTags(): array
    {
        return [
            'INDI:_FSFTID' => /* I18N: familysearch.org */ new FamilySearchFamilyTreeId(I18N::translate('FamilySearch ID')),
        ];
    }

    public function customSubTags(): array
    {
        return [
            'INDI' => [['_FSFTID', '0:1']],
        ];
    }
    
    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'FamilySearch';
    }
}
