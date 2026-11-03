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

namespace Fisharebest\Webtrees\Tests\Unit\Module;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversTrait(ModuleCustomTrait::class)]
class ModuleCustomTraitTest extends TestCase
{
    private function createCustomModule(): AbstractModule&ModuleCustomInterface
    {
        return new class () extends AbstractModule implements ModuleCustomInterface {
            use ModuleCustomTrait;

            public function title(): string
            {
                return 'Test Custom Module';
            }

            public function resourcesFolder(): string
            {
                return __DIR__ . '/';
            }
        };
    }

    public function testCustomModuleAuthorName(): void
    {
        $module = $this->createCustomModule();

        self::assertSame('', $module->customModuleAuthorName());
    }

    public function testCustomModuleVersion(): void
    {
        $module = $this->createCustomModule();

        self::assertSame('', $module->customModuleVersion());
    }

    public function testCustomModuleLatestVersionUrl(): void
    {
        $module = $this->createCustomModule();

        self::assertSame('', $module->customModuleLatestVersionUrl());
    }

    public function testCustomModuleSupportUrl(): void
    {
        $module = $this->createCustomModule();

        self::assertSame('', $module->customModuleSupportUrl());
    }

    public function testCustomTranslations(): void
    {
        $module = $this->createCustomModule();

        self::assertSame([], $module->customTranslations('en'));
    }

    public function testCustomModuleLatestVersionWithNoUrl(): void
    {
        $module = $this->createCustomModule();

        // With no URL configured, returns the current version
        self::assertSame('', $module->customModuleLatestVersion());
    }

    /**
     * Replace the HTTP client in the container with one that always returns $body.
     */
    private function mockHttpClient(string $body): void
    {
        $response_factory = Registry::container()->get(ResponseFactoryInterface::class);
        $stream_factory   = Registry::container()->get(StreamFactoryInterface::class);

        self::assertInstanceOf(ResponseFactoryInterface::class, $response_factory);
        self::assertInstanceOf(StreamFactoryInterface::class, $stream_factory);

        $response = $response_factory
            ->createResponse(200)
            ->withBody($stream_factory->createStream($body));

        $client = self::createStub(ClientInterface::class);
        $client->method('sendRequest')->willReturn($response);

        Registry::container()->set(ClientInterface::class, $client);
    }

    private function createCustomModuleWithUrl(string $name): AbstractModule&ModuleCustomInterface
    {
        $module = new class () extends AbstractModule implements ModuleCustomInterface {
            use ModuleCustomTrait;

            public function title(): string
            {
                return 'Test Custom Module';
            }

            public function resourcesFolder(): string
            {
                return __DIR__ . '/';
            }

            public function customModuleLatestVersionUrl(): string
            {
                return 'https://webtrees.test/latest-version';
            }
        };

        $module->setName($name);
        Registry::cache()->file()->forget($name . '-latest-version');

        return $module;
    }

    public function testExtractVersionPassesTheResponseThroughUnchanged(): void
    {
        $module = $this->createCustomModuleWithUrl('test-extract-version-default');

        $this->mockHttpClient('1.2.3');

        // The default implementation of extractVersion() is a pass-through.
        self::assertSame('1.2.3', $module->customModuleLatestVersion());
    }

    public function testResponseThatIsNotAVersionIsRejectedWithoutExtractVersion(): void
    {
        $module = $this->createCustomModuleWithUrl('test-extract-version-rejected');

        // A release feed, such as the one returned by the GitHub API, is JSON...
        $this->mockHttpClient('{"tag_name": "2.3.4"}');

        // ...so it does not look like a version and the module keeps its own.
        self::assertSame($module->customModuleVersion(), $module->customModuleLatestVersion());
    }

    public function testExtractVersionCanParseTheResponse(): void
    {
        $module = new class () extends AbstractModule implements ModuleCustomInterface {
            use ModuleCustomTrait;

            public function title(): string
            {
                return 'Test Custom Module';
            }

            public function resourcesFolder(): string
            {
                return __DIR__ . '/';
            }

            public function customModuleLatestVersionUrl(): string
            {
                return 'https://webtrees.test/latest-version';
            }

            // Modules that publish a release feed can read the version from it here,
            // instead of having to duplicate customModuleLatestVersion().
            protected function extractVersion(string $content): string
            {
                $data = json_decode($content, true);

                if (is_array($data) && is_string($data['tag_name'] ?? null)) {
                    return $data['tag_name'];
                }

                return '';
            }
        };

        $module->setName('test-extract-version-override');
        Registry::cache()->file()->forget('test-extract-version-override-latest-version');

        $this->mockHttpClient('{"tag_name": "2.3.4"}');

        self::assertSame('2.3.4', $module->customModuleLatestVersion());
    }
}
