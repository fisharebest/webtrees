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
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

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
}
