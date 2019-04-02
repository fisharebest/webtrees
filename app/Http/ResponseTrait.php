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

namespace Fisharebest\Webtrees\Http;

/**
 * Convert a Symfony response into a PSR-7 response.
 */
trait ResponseTrait
{
    /**
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return static
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $request = clone $this;
        $request->setStatusCode($code, !empty($reasonPhrase) ? $reasonPhrase : null);

        return $request;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->statusText;
    }
}
