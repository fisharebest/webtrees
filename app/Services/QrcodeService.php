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

namespace Fisharebest\Webtrees\Services;

use PragmaRX\Google2FA\Google2FA;
use chillerlan\QRCode\QRCode;
use Fisharebest\Webtrees\Contracts\UserInterface;

/**
 * Generate a QR code and secret for user setting up multi-factor authentication.
 */
class QrcodeService
{
    /**
     * Generate a QR code image based on 2FA secret and return both.
     *
     * @param UserInterface   $user
     * @return array<string, mixed>
     */

    public function genQRcode(UserInterface $user): array
    {
        $qrinfo = array();
        $google2fa = new Google2FA();
        /** @var array{secret: string} $qrinfo */
        $qrinfo['secret'] = $google2fa->generateSecretKey();
        /** @var string $servername */
        $servername = $_SERVER['SERVER_NAME'];
        $data = 'otpauth://totp/' . $user->id() . '?secret=' . $qrinfo['secret'] . '&issuer=' . $servername;
        $qrinfo['qrcode'] = (new QRCode())->render($data);
        return $qrinfo;
    }
}
