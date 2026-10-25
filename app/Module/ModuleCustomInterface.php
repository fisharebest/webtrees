<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

/**
 * Interface ModuleCustomInterface - Classes and libraries for module system
 */
interface ModuleCustomInterface extends ModuleInterface
{
    /**
     * The person or organisation who created this module.
     */
    public function customModuleAuthorName(): string;

    /**
     * The version of this module.
     */
    public function customModuleVersion(): string;

    /**
     * A URL that will provide the latest version of this module.
     */
    public function customModuleLatestVersionUrl(): string;

    /**
     * Fetch the latest version of this module.
     */
    public function customModuleLatestVersion(): string;

    /**
     * Where to get support for this module.  Perhaps a github repository?
     */
    public function customModuleSupportUrl(): string;

    /**
     * Additional/updated translations.
     *
     *
     * @return array<string,string>
     */
    public function customTranslations(string $language): array;
}
