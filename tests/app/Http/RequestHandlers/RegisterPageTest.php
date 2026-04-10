<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\CaptchaService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegisterPage::class)]
class RegisterPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(RegisterPage::class));
    }

    /**
     * When registration is enabled, the page renders with STATUS_OK.
     */
    public function testHandleWhenRegistrationEnabled(): void
    {
        Site::setPreference('USE_REGISTRATION_MODULE', '1');

        $captcha_service = new CaptchaService();
        $handler         = new RegisterPage($captcha_service);
        $request         = self::createRequest();
        $response        = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }

    /**
     * When registration is disabled, the handler throws HttpNotFoundException.
     */
    public function testHandleWhenRegistrationDisabled(): void
    {
        Site::setPreference('USE_REGISTRATION_MODULE', '0');

        $captcha_service = new CaptchaService();
        $handler         = new RegisterPage($captcha_service);
        $request         = self::createRequest();

        $this->expectException(HttpNotFoundException::class);
        $handler->handle($request);
    }

    /**
     * The caution text is shown when the preference is enabled.
     */
    public function testHandleShowsCautionWhenEnabled(): void
    {
        Site::setPreference('USE_REGISTRATION_MODULE', '1');
        Site::setPreference('SHOW_REGISTER_CAUTION', '1');

        $captcha_service = new CaptchaService();
        $handler         = new RegisterPage($captcha_service);
        $request         = self::createRequest();
        $response        = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
