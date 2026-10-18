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

namespace Fisharebest\Webtrees\Tests\Unit\Services\Composer;

use Fisharebest\Webtrees\Services\Composer\VendorModuleService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function file_put_contents;
use function is_dir;
use function mkdir;
use function rmdir;
use function scandir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

/**
 * Test that vendor-module discovery loads only packages at their standard
 * vendor location, supports themes, and skips relocated / nested self-entries.
 */
#[CoversClass(VendorModuleService::class)]
class VendorModuleServiceTest extends TestCase
{
    private string $vendorRoot;

    private string $relocatedRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendorRoot    = sys_get_temp_dir() . '/wt-vms-vendor-' . uniqid();
        $this->relocatedRoot = sys_get_temp_dir() . '/wt-vms-relocated-' . uniqid();

        // Packages at their standard <vendor>/<package> location.
        $this->writeModuleFixture($this->vendorRoot . '/acme/real-module', $this->customModuleSource());
        $this->writeModuleFixture($this->vendorRoot . '/acme/real-theme', $this->themeModuleSource());

        // A package relocated away from its standard location (e.g. moved into
        // modules_v4/ by the installer-plugin, or a nested-vendor self-entry).
        $this->writeModuleFixture($this->relocatedRoot . '/reloc-module', $this->customModuleSource());
    }

    protected function tearDown(): void
    {
        $this->removeRecursively($this->vendorRoot);
        $this->removeRecursively($this->relocatedRoot);

        parent::tearDown();
    }

    public function testLoadsAModuleAtItsStandardVendorPath(): void
    {
        $service = $this->serviceFor(
            ['acme/real-module'],
            ['acme/real-module' => $this->vendorRoot . '/acme/real-module'],
        );

        $modules = $service->getVendorModules();

        self::assertCount(1, $modules);
        self::assertArrayHasKey('_real-module_', $modules->all());
    }

    public function testSkipsARelocatedModule(): void
    {
        $service = $this->serviceFor(
            ['acme/reloc-module'],
            ['acme/reloc-module' => $this->relocatedRoot . '/reloc-module'],
        );

        $modules = $service->getVendorModules();

        // The package does not sit at <vendor>/acme/reloc-module, so it was
        // relocated (installer-plugin) or is a nested-vendor self-entry —
        // ModuleService::customModules() already loads it, and loading it
        // again here would redeclare its class.
        self::assertCount(0, $modules);
    }

    public function testLoadsAThemeAtItsStandardVendorPath(): void
    {
        $service = $this->serviceFor(
            ['acme/real-theme'],
            ['acme/real-theme' => $this->vendorRoot . '/acme/real-theme'],
        );

        $modules = $service->getVendorModules();

        self::assertCount(1, $modules);
        self::assertArrayHasKey('_real-theme_', $modules->all());
    }

    /**
     * Build a service whose Composer seams return the supplied fixtures and
     * whose main vendor directory is the test's temporary vendor root.
     *
     * @param list<string>          $packages
     * @param array<string, string> $installPaths
     */
    private function serviceFor(array $packages, array $installPaths): VendorModuleService
    {
        return new class ($packages, $installPaths, $this->vendorRoot) extends VendorModuleService {
            /**
             * @param list<string>          $packages
             * @param array<string, string> $installPaths
             */
            public function __construct(
                private readonly array $packages,
                private readonly array $installPaths,
                private readonly string $vendorRoot,
            ) {
            }

            /**
             * @return list<string>
             */
            protected function getInstalledWebtreesModules(): array
            {
                return $this->packages;
            }

            protected function getPackageInstallPath(string $packageName): ?string
            {
                return $this->installPaths[$packageName] ?? null;
            }

            protected function mainVendorDirectory(): string
            {
                return $this->vendorRoot;
            }
        };
    }

    private function writeModuleFixture(string $directory, string $source): void
    {
        mkdir($directory, 0o777, true);
        file_put_contents($directory . '/module.php', $source);
    }

    private function customModuleSource(): string
    {
        return <<<'PHP'
            <?php

            use Fisharebest\Webtrees\Module\AbstractModule;
            use Fisharebest\Webtrees\Module\ModuleCustomInterface;
            use Fisharebest\Webtrees\Module\ModuleCustomTrait;

            return new class extends AbstractModule implements ModuleCustomInterface {
                use ModuleCustomTrait;

                public function title(): string
                {
                    return 'Fixture Custom Module';
                }
            };
            PHP;
    }

    private function themeModuleSource(): string
    {
        return <<<'PHP'
            <?php

            use Fisharebest\Webtrees\Module\AbstractModule;
            use Fisharebest\Webtrees\Module\ModuleThemeInterface;
            use Fisharebest\Webtrees\Module\ModuleThemeTrait;

            return new class extends AbstractModule implements ModuleThemeInterface {
                use ModuleThemeTrait;

                public function title(): string
                {
                    return 'Fixture Theme';
                }
            };
            PHP;
    }

    private function removeRecursively(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . '/' . $entry;

            if (is_dir($path)) {
                $this->removeRecursively($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
