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

namespace CustomAuthor\CustomProject;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleInterface;

/**
 * This is an example of a custom module.  Modules in folders containing a "."
 * do not get loaded.  Rename it to make it appear.
 *
 * All modules should implement ModuleInterface and extend AbstractModule.
 *
 * To provide any additional functions, such as tabs or menus, you should
 * implement ModuleXxxInterface and use the corresponding ModuleXxxTrait.
 * The trait provides a default implementation of every method required by
 * the interface.  Provide implementations of those that you need.
 * We return an anonymouse class here.  This prevents conflict with existing
 * class names.
 */
return new class extends AbstractModule implements ModuleInterface, ModuleCustomInterface {
    // We implement ModuleCustomInterface, so we must also use the corresponding trait.
    use ModuleCustomTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Custom module');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Custom module') . ' – ' . I18N::translate('Description');
    }
};
