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
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EmailPreferencesPage::class)]
class EmailPreferencesPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(EmailPreferencesPage::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $email_service = self::createStub(EmailService::class);
        $email_service->method('mailSslOptions')->willReturn(['ssl' => 'SSL', 'tls' => 'TLS']);
        $email_service->method('mailTransportOptions')->willReturn(['internal' => 'PHP', 'smtp' => 'SMTP']);
        $email_service->method('isValidEmail')->willReturn(true);

        $handler  = new EmailPreferencesPage($email_service);
        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
