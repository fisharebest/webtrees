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
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

// HelpText has no constructor dependencies (T2).
// It uses view() and response() which require the database for I18N/theme.
#[CoversClass(HelpText::class)]
class HelpTextTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(HelpText::class));
    }

    /**
     * The DATE help topic renders with STATUS_OK.
     */
    public function testHandleDateTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'DATE');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }

    /**
     * The NAME help topic renders with STATUS_OK.
     */
    public function testHandleNameTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'NAME');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }

    /**
     * The SURN help topic renders with STATUS_OK.
     */
    public function testHandleSurnTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'SURN');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The PLAC help topic renders with STATUS_OK.
     */
    public function testHandlePlacTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'PLAC');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The OBJE help topic renders with STATUS_OK.
     */
    public function testHandleObjeTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'OBJE');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The RESN help topic renders with STATUS_OK.
     */
    public function testHandleResnTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'RESN');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The ROMN help topic renders with STATUS_OK.
     */
    public function testHandleRomnTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'ROMN');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The _HEB help topic renders with STATUS_OK.
     */
    public function testHandleHebTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', '_HEB');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The data-fixes help topic renders with STATUS_OK.
     */
    public function testHandleDataFixesTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'data-fixes');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The edit_SOUR_EVEN help topic renders with STATUS_OK.
     */
    public function testHandleSourceEventsTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'edit_SOUR_EVEN');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The pending_changes help topic renders with STATUS_OK.
     */
    public function testHandlePendingChangesTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'pending_changes');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The relationship-privacy help topic renders with STATUS_OK.
     */
    public function testHandleRelationshipPrivacyTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'relationship-privacy');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * An unknown topic renders the default help text.
     */
    public function testHandleUnknownTopic(): void
    {
        $handler  = new HelpText();
        $request  = self::createRequest()->withAttribute('topic', 'unknown-topic-xyz');
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }
}
