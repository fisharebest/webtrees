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
namespace Fisharebest\Webtrees\LeafletProvider;

/**
 * A map provider for LeafletJS.
 */
class OpenStreetMap implements LeafletProviderInterface
{
    /**
     * @return string
     */
    public function name(): string
    {
        return "OpenStreetMap";
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    }

    /**
     * @return mixed[]
     */
    public function options(): array
    {
        return array(
            'attribution' => '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            'maxZoom'     => 19,
        );
    }
}
