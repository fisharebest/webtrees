<?php

/**
 * Example theme.  Here we are extending an existing theme.
 * Instead, you could extend AbstractModule and implement ModuleThemeInterface directly.
 */

declare(strict_types=1);

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Module\MinimalTheme;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\View;

return new class extends MinimalTheme implements ModuleCustomInterface {
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Custom theme';
    }

    /**
     * Bootstrap the module
     */
    public function boot(): void
    {
        // Register a namespace for our views.
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');

        // Replace an existing view with our own version.
        View::registerCustomView('::chart-box', $this->name() . '::chart-box');
    }

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    /**
     * Add our own stylesheet to the existing stylesheets.
     *
     * @return array<string>
     */
    public function stylesheets(): array
    {
        $stylesheets = parent::stylesheets();

        // NOTE - a future version of webtrees will allow the modules to be stored in a private folder.
        // Only files in the /public/ folder will be accessible via the webserver.
        // Since modules cannot copy their files to the /public/ folder, they need to provide them via a callback.
        $stylesheets[] = $this->assetUrl('css/theme.css');

        return $stylesheets;
    }
};
