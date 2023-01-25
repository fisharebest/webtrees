<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository\Interfaces;

/**
 * A repository providing methods for hit count related statistics.
 */
interface HitCountRepositoryInterface
{
    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCount(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountUser(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountIndi(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountFam(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountSour(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountRepo(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountNote(string $page_parameter = ''): string;

    /**
     * How many times has a page been viewed.
     *
     * @param string $page_parameter
     *
     * @return string
     */
    public function hitCountObje(string $page_parameter = ''): string;
}
