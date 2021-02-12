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

namespace Fisharebest\Webtrees\Elements;

use Exception;
use Fisharebest\Webtrees\Tree;
use Ramsey\Uuid\Uuid;

use function dechex;
use function hexdec;
use function strtoupper;
use function strtr;
use function substr;

/**
 * _UID fields, as created by PAF and other applications
 */
class PafUid extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 34;

    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        try {
            $uid = strtr(Uuid::uuid4()->toString(), ['-' => '']);
        } catch (Exception $ex) {
            // uuid4() can fail if there is insufficient entropy in the system.
            return '';
        }

        $checksum_a = 0; // a sum of the bytes
        $checksum_b = 0; // a sum of the incremental values of $checksum_a

        // Compute checksums
        for ($i = 0; $i < 32; $i += 2) {
            $checksum_a += hexdec(substr($uid, $i, 2));
            $checksum_b += $checksum_a & 0xff;
        }

        return strtoupper($uid . substr(dechex($checksum_a), -2) . substr(dechex($checksum_b), -2));
    }
}
