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

namespace Fisharebest\Webtrees\Module;

/**
 * Interface ModuleInterface - Classes and libraries for module system
 */
interface ModuleInterface
{
    /**
     * Create a new module.
     *
     * @param string $directory Where is this module installed
     */
    public function __construct(string $directory);

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * What is the default access level for this module?
     *
     * Some modules are aimed at admins or managers, and are not generally shown to users.
     *
     * @return int Returns one of: Auth::PRIV_HIDE, Auth::PRIV_PRIVATE, Auth::PRIV_USER, Auth::PRIV_NONE
     */
    public function defaultAccessLevel(): int;

    /**
     * Provide a unique internal name for this module
     *
     * @return string
     */
    public function getName(): string;
}
