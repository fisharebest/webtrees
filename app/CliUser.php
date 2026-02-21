<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\UserInterface;

/**
 * User for command-line invoked functions.
 */
class CliUser extends VolatileUser
{
    public function __construct(string $real_name = 'CLI_USER')
    {
        parent::__construct('_CLI_', $real_name);
    }

    public function getPreference(string $setting_name, string $default = ''): string
    {
        if ($setting_name === UserInterface::PREF_IS_ADMINISTRATOR) {
            return '1';
        }
        return parent::getPreference($setting_name, $default);
    }

}