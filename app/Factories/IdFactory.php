<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\IdFactoryInterface;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Uuid;

use function dechex;
use function hexdec;
use function sprintf;
use function str_pad;
use function str_split;
use function strtoupper;
use function substr;

use const STR_PAD_LEFT;

/**
 * Create a unique identifier.
 */
class IdFactory implements IdFactoryInterface
{
    /**
     * @return string
     */
    public function uuid(): string
    {
        try {
            return strtolower(Uuid::uuid4()->toString());
        } catch (RandomSourceException) {
            // uuid4() can fail if there is insufficient entropy in the system.
            return '';
        }
    }

    /**
     * An identifier for use in CSS/HTML
     *
     * @param string $prefix
     *
     * @return string
     */
    public function id(string $prefix = 'id-'): string
    {
        return $prefix . $this->uuid();
    }

    /**
     * A value for _UID fields, as created by PAF
     *
     * @return string
     */
    public function pafUid(): string
    {
        $uid = strtoupper(strtr($this->uuid(), ['-' => '']));

        if ($uid === '') {
            return '';
        }

        return $uid . $this->pafUidChecksum($uid);
    }

    /**
     * Based on the C implementation in "GEDCOM Unique Identifiers" by Gordon Clarke, dated 2007-06-08
     */
    public function pafUidChecksum(string $uid): string
    {
        $checksum_a = 0; // a sum of the bytes
        $checksum_b = 0; // a sum of the incremental values of $checksum_a

        foreach (str_split($uid, 2) as $byte) {
            $checksum_a += hexdec($byte);
            $checksum_b += $checksum_a;
        }
        for ($i = 0; $i < 32; $i += 2) {
            $checksum_a += hexdec(substr($uid, $i, 2));
            $checksum_b += $checksum_a;
        }

        return sprintf('%02X%02X', $checksum_a & 0xff, $checksum_b & 0xff);
    }
}
