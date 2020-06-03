<?php

/**
 * Example module.
 */

declare(strict_types=1);

namespace MyCustomNamespace;

use Fisharebest\Localization\Translation;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;

return new class extends AbstractModule implements ModuleCustomInterface {
    use ModuleCustomTrait;

    /**
     * Constructor.  The constructor is called on *all* modules, even ones that are disabled.
     * This is a good place to load business logic ("services").  Type-hint the parameters and
     * they will be injected automatically.
     */
    public function __construct()
    {
        // NOTE:  If your module is dependent on any of the business logic ("services"),
        // then you would type-hint them in the constructor and let webtrees inject them
        // for you.  However, we can't use dependency injection on anonymous classes like
        // this one. For an example of this, see the example-server-configuration module.
    }

    /**
     * Bootstrap.  This function is called on *enabled* modules.
     * It is a good place to register routes and views.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'My custom module';
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return 'This module doesn‘t do anything';
    }

    /**
     * The person or organisation who created this module.
     *
     * @return string
     */
    public function customModuleAuthorName(): string
    {
        return 'Greg Roach';
    }

    /**
     * The version of this module.
     *
     * @return string
     */
    public function customModuleVersion(): string
    {
        return '1.0.0';
    }

    /**
     * A URL that will provide the latest version of this module.
     *
     * @return string
     */
    public function customModuleLatestVersionUrl(): string
    {
        return 'https://www.example.com/latest-version.txt';
    }

    /**
     * Where to get support for this module.  Perhaps a github repository?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return 'https://www.example.com/support';
    }

    /**
     * Additional/updated translations.
     *
     * @param string $language
     *
     * @return string[]
     */
    public function customTranslations(string $language): array
    {
        switch ($language) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return $this->englishTranslations();

            case 'fr':
            case 'fr-CA':
                return $this->frenchTranslations();

            case 'some-other-language':
                // Arrays are preferred, and faster.
                // If your module uses .MO files, then you can convert them to arrays like this.
                return (new Translation('path/to/file.mo'))->asArray();

            default:
                return [];
        }
    }

    /**
     * @return array<string,string>
     */
    protected function englishTranslations(): array
    {
        // Note the special characters used in plural and context-sensitive translations.
        return [
            'Individual'                                      => 'Fish',
            'Individuals'                                     => 'Fishes',
            '%s individual' . I18N::PLURAL . '%s individuals' => '%s fish' . I18N::PLURAL . '%s fishes',
            'Unknown given name' . I18N::CONTEXT . '…'        => '?fish?',
            'Unknown surname' . I18N::CONTEXT . '…'           => '?FISH?',
        ];
    }

    /**
     * @return array<string,string>
     */
    protected function frenchTranslations(): array
    {
        return [
            'Individual'                                      => 'Poisson',
            'Individuals'                                     => 'Poissons',
            '%s individual' . I18N::PLURAL . '%s individuals' => '%s poisson' . I18N::PLURAL . '%s poissons',
            'Unknown given name' . I18N::CONTEXT . '…'        => '?poission?',
            'Unknown surname' . I18N::CONTEXT . '…'           => '?POISSON?',
        ];
    }
};
