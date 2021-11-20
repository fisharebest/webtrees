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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Session;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

use function view;

/**
 * Completely Automated Public Turing test to tell Computers and Humans Apart.
 */
class CaptchaService
{
    // If the form is completed faster than this, then suspect a robot.
    private const MINIMUM_FORM_TIME = 3.0;

    /**
     * Create the captcha
     *
     * @return string
     */
    public function createCaptcha(): string
    {
        $x = Uuid::uuid4()->toString();
        $y = Uuid::uuid4()->toString();
        $z = Uuid::uuid4()->toString();

        Session::put('captcha-t', microtime(true));
        Session::put('captcha-x', $x);
        Session::put('captcha-y', $y);
        Session::put('captcha-z', $z);

        return view('captcha', [
            'x' => $x,
            'y' => $y,
            'z' => $z,
        ]);
    }

    /**
     * Check the user's response.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function isRobot(ServerRequestInterface $request): bool
    {
        $t = Session::pull('captcha-t');
        $x = Session::pull('captcha-x');
        $y = Session::pull('captcha-y');
        $z = Session::pull('captcha-z');

        $value_x = $request->getParsedBody()[$x] ?? '';
        $value_y = $request->getParsedBody()[$y] ?? '';

        // The captcha uses javascript to copy value z from field y to field x.
        // Expect it in both fields.
        if ($value_x !== $z || $value_y !== $z) {
            return true;
        }

        // If the form was returned too quickly, the probably a robot.
        return microtime(true) < $t + self::MINIMUM_FORM_TIME;
    }
}
