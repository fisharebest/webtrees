<?php

namespace MyCustomNamespace;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Tree;

/**
 * Example module
 */
return new class extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

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
     * Where to get support for this module.  Perhaps a github respository?
     *
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return 'https://www.example.com/support';
    }

    /**
     *  Constructor.
     */
    public function __construct()
    {
        // IMPORTANT - the constructor is called on *all* modules, even ones that are disabled.
        // It is also called before the webtrees framework is initialised, and so other components
        // will not yet exist.
    }

    /**
     *  Boostrap.
     *
     * @param UserInterface $user A user (or visitor) object.
     * @param Tree|null     $tree Note that $tree can be null (if all trees are private).
     */
    public function boot(UserInterface $user, ?Tree $tree): void
    {
        // The boot() function is called after the framework has been booted.
        // We can now use the current user, tree, etc.
        if (!Auth::isAdmin($user) && $tree !== null) {
            return;
        }
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
        // Here we are using an array for translations.
        // If you had .MO files, you could use them with:
        // return (new Translation('path/to/file.mo'))->asArray();

        switch ($language) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return $this->englishTranslations();

            case 'fr':
            case 'fr-CA':
                return $this->frenchTranslations();

            default:
                return [];
        }
    }

    /**
     * @return array
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
     * @return array
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
