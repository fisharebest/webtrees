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

use Fisharebest\Webtrees\Tree;

/**
 * Interface ModuleInterface - Classes and libraries for module system
 */
interface ModuleInterface
{
    /**
     * Early initialisation.  Called before most of the middleware.
     */
    public function boot(): void;

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * A unique internal name for this module (based on the installation folder).
     *
     * @return string
     */
    public function name(): string;

    /**
     * Has the module been disabled in the control panel?
     *
     * @param bool $enabled
     *
     * @return self
     */
    public function setEnabled(bool $enabled): self;

    /**
     * Has the module been disabled in the control panel?
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Get a the current access level for a module
     *
     * @param Tree   $tree
     * @param string $interface
     *
     * @return int
     */
    public function accessLevel(Tree $tree, string $interface): int;

    /**
     * Get a module setting. Return a default if the setting is not set.
     *
     * @param string $setting_name
     * @param string $default
     *
     * @return string
     */
    public function getPreference(string $setting_name, string $default = ''): string;

    /**
     * Set a module setting.
     *
     * Since module settings are NOT NULL, setting a value to NULL will cause
     * it to be deleted.
     *
     * @param string $setting_name
     * @param string $setting_value
     *
     * @return void
     */
    public function setPreference(string $setting_name, string $setting_value): void;

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourcesFolder(): string;
}
