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

namespace Fisharebest\Webtrees\Services\Composer;

use Composer\Autoload\ClassLoader;
use Composer\InstalledVersions;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Illuminate\Support\Collection;
use Throwable;

use function dirname;
use function realpath;

/**
 * Service for loading Webtrees modules from the vendor directory using Composer's InstalledVersions API.
 *
 * This service provides seamless integration between Composer-managed packages and Webtrees' module system,
 * enabling modules to be distributed and installed as standard Composer packages. It represents a modern
 * approach to module management, similar to how major PHP frameworks like Symfony, Laravel, and TYPO3
 * handle their extension ecosystems.
 *
 * The service acts as a bridge between Composer's package management and Webtrees' module system by:
 * - Discovering installed packages through Composer's InstalledVersions API
 * - Identifying which packages are Webtrees modules based on their type
 * - Loading and initializing modules from the vendor directory
 * - Integrating vendor modules with Webtrees' existing module infrastructure
 *
 * @author  Rico Sonntag <mail@ricosonntag.de>
 * @license https://opensource.org/licenses/GPL-3.0 GNU General Public License v3.0
 * @link    https://github.com/fisharebest/webtrees/
 */
class VendorModuleService
{
    /**
     * The Composer package types identifying Webtrees modules and themes.
     * Mirrors the package types handled by the webtrees-module-installer-plugin.
     *
     * @var list<string> The package type identifiers
     */
    private const array MODULE_TYPES = ['webtrees-module', 'webtrees-theme'];

    /**
     * Discovers and loads all Webtrees modules from the vendor directory.
     *
     * This is the primary entry point for vendor module discovery, called by the ModuleService
     * during Webtrees' bootstrap process. It orchestrates the entire discovery and loading
     * process for Composer-installed modules.
     *
     * @return Collection<string, ModuleCustomInterface|ModuleThemeInterface> A collection of successfully loaded
     *                                                   vendor modules and themes. Empty when none are found or on error.
     */
    public function getVendorModules(): Collection
    {
        // Check if Composer's runtime API is available
        if (!$this->isComposerAvailable()) {
            return new Collection();
        }

        return Collection::make($this->getInstalledWebtreesModules())
            ->map(function (string $packageName): ModuleCustomInterface|ModuleThemeInterface|null {
                $module = $this->loadVendorModule($packageName);

                if (
                    !($module instanceof ModuleCustomInterface)
                    && !($module instanceof ModuleThemeInterface)
                ) {
                    return null;
                }

                $module->setName($this->generateModuleName($packageName));

                return $module;
            })
            ->filter()
            ->mapWithKeys(
                static fn (ModuleCustomInterface|ModuleThemeInterface $module): array => [
                    $module->name() => $module,
                ]
            );
    }

    /**
     * Check if Composer's runtime API is available.
     *
     * @return bool
     */
    private function isComposerAvailable(): bool
    {
        return class_exists(InstalledVersions::class);
    }

    /**
     * Get a list of all installed Composer packages of a Webtrees module or theme type.
     *
     * @return string[]
     */
    protected function getInstalledWebtreesModules(): array
    {
        $packages = [];

        foreach (self::MODULE_TYPES as $type) {
            $packages = [...$packages, ...InstalledVersions::getInstalledPackagesByType($type)];
        }

        return $packages;
    }

    /**
     * Get the installation path for a Composer package.
     *
     * @param string $packageName The Composer package name in vendor/package format
     *
     * @return null|string
     */
    protected function getPackageInstallPath(string $packageName): ?string
    {
        try {
            return InstalledVersions::getInstallPath($packageName);
        } catch (Throwable $exception) {
            $this->logError(
                'Error retrieving installation path',
                $exception
            );

            return null;
        }
    }

    /**
     * Absolute path of the Composer vendor directory this service is autoloaded
     * from. Composer exposes no dedicated accessor (composer/composer#2904), so
     * — like drupal-finder — we ask each registered class loader which one can
     * resolve this class; its registered vendor directory is the one we live
     * in. Robust against a renamed `config.vendor-dir` and against the nested
     * vendor directories that bundled modules register for their own autoload.
     */
    protected function mainVendorDirectory(): string
    {
        foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
            if ($loader->findFile(self::class) !== false) {
                return $vendorDir;
            }
        }

        return '';
    }

    /**
     * Loads a Webtrees module from its Composer package location.
     *
     * This method handles the complete process of loading a module from the vendor directory,
     * including file discovery, safe loading, validation, and configuration.
     *
     * @param string $packageName The Composer package name to load
     *
     * @return ModuleInterface|null The loaded and configured module instance,
     *                              or null if loading fails for any reason
     */
    private function loadVendorModule(string $packageName): ?ModuleInterface
    {
        // Get the installation path using Composer's API
        $packagePath = $this->getPackageInstallPath($packageName);

        if ($packagePath === null) {
            return null;
        }

        // Only load a package that sits at its standard Composer location
        // (<vendor>/<package>). A package relocated into modules_v4/ by the
        // installer-plugin — or a nested-vendor self-entry from a module that
        // bundles its own installed.php — reports a different install path and
        // is already discovered by ModuleService::customModules(); loading it
        // here too would include its module.php a second time and redeclare
        // its class.
        $packageRealPath = realpath($packagePath);
        $standardPath    = realpath($this->mainVendorDirectory() . '/' . $packageName);

        if (
            ($packageRealPath === false)
            || ($standardPath === false)
            || ($packageRealPath !== $standardPath)
        ) {
            return null;
        }

        $moduleFile = null;

        // Look for the module.php file
        if (file_exists($packagePath . DIRECTORY_SEPARATOR . 'module.php')) {
            $moduleFile = $packagePath . DIRECTORY_SEPARATOR . 'module.php';
        }

        if ($moduleFile === null) {
            return null;
        }

        // Load and return module
        return $this->loadModuleFile($moduleFile);
    }

    /**
     * Loads a module.php file in an isolated scope to prevent variable pollution.
     *
     * @param string $filename The absolute path to the module.php file to load
     *
     * @return ModuleInterface|null The module instance if successfully loaded,
     *                              null if loading fails or invalid return type
     */
    private function loadModuleFile(string $filename): ?ModuleInterface
    {
        try {
            return include $filename;
        } catch (Throwable $exception) {
            $this->logError(
                'Fatal error in vendor module: ' . basename(dirname($filename)),
                $exception
            );
        }

        return null;
    }

    /**
     * Logs error messages for debugging and administrative visibility.
     *
     * @param string         $message   The primary error message describing the problem
     * @param Throwable|null $exception Optional exception providing additional details
     *
     * @return void
     */
    private function logError(string $message, ?Throwable $exception = null): void
    {
        $fullMessage = $message;

        if ($exception !== null) {
            $fullMessage .= ': ' . $exception->getMessage();
        }

        // Use FlashMessages for user-visible errors in admin interface
        FlashMessages::addMessage(
            $fullMessage,
            'danger'
        );
    }

    /**
     * Generates a unique module name from a Composer package name.
     *
     * This method creates a unique identifier for vendor modules that distinguishes them
     * from traditional modules while maintaining readability. The generated name is used
     * as the module's internal identifier within Webtrees.
     *
     * @param string $packageName The full Composer package name
     *
     * @return string The generated module name in _package_ format
     *                Always starts and ends with an underscore
     */
    private function generateModuleName(string $packageName): string
    {
        $moduleName = substr(
            $packageName,
            strpos($packageName, '/') + 1
        );

        return '_' . $moduleName . '_';
    }
}
