<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use function str_pad;
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
            return strtolower(strtr(Uuid::uuid4()->toString(), ['-' => '']));
        } catch (RandomSourceException) {
            // uuid4() can fail if there is insufficient entropy in the system.
            return '';
        }
    }

    /**
     * An identifier for use in CSS/HTML
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
     * @param string $uid - exactly 32 hex characters
     *
     * @return string
     */
    public function pafUidChecksum(string $uid): string
    {
        $checksum_a = 0; // a sum of the bytes
        $checksum_b = 0; // a sum of the incremental values of $checksum_a

        for ($i = 0; $i < 32; $i += 2) {
            $checksum_a += hexdec(substr($uid, $i, 2));
            $checksum_b += $checksum_a & 0xff;
        }

        $digit1 = str_pad(dechex($checksum_a), 2, '0', STR_PAD_LEFT);
        $digit2 = str_pad(dechex($checksum_b), 2, '0', STR_PAD_LEFT);

        return strtoupper($digit1 . $digit2);
    }
}
