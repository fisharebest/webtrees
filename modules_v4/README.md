# THIRD-PARTY MODULES

Many webtrees functions are provided by “modules”.
Modules allows you to add additional features to webtrees.

## Installing and uninstalling modules

A module is a folder containing a file called `module.php`.
There may be other files in the folder.

To install a module, copy its folder to here.

To uninstall it, delete its folder from here.

Note that module names (i.e. their folder names) must not contain
spaces or the characters `.`, `[` and `]`.

TIP: renaming a module from `<module>` to `<module.disable>`
is a quick way to hide it from webtrees.  This works because
modules containing `.` are ignored.

## Writing modules

To write a module, you need to understand the PHP programming langauge.

The rest of this document is aimed at PHP developers.

TIP: The built-in modules can be found in `app/Module/*.php`.
These contain lots of usefule examples that you can copy/paste.

## Creating a custom module.

This is the minimum code needed to create a custom module.

```php
<?php

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;

return new class extends AbstractModule implements ModuleCustomInterface {
    use ModuleCustomTrait;
    
    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'My Custom module';
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
};
```

If you plan to share your modules with other webtrees users, you should
provide them with support/contact/version information.  This way they will
know where to go for updates, support, etc.
Look at the functions and comments in `app/ModuleCustomTrait.php`.

## Available interfaces

Custom modules *must* implement `ModuleCustomInterface` interface.
They *may* implement one or more of the following

* `ModuleAnalyticsInterface` - adds a tracking/analytics provider.
* `ModuleBlockInterface` - adds a block to the home pages.
* `ModuleChartInterface` - adds a chart to the chart menu.
* `ModuleConfigInterface` - adds a configuration page to the control panel.
* `ModuleMenuInterface` - adds an entry to the main menu.
* `ModuleReportInterface` - adds a report to the report menu.
* `ModuleSidebarInterface` - adds a sidebar to the individual pages.
* `ModuleTabInterface` - adds a tab to the individual pages.
* `ModuleThemeInterface` - adds a theme (this interface is still being developed).

For each interface that you implement, you must also use the corresponding trait.
If you don't do this, your module may break whenever the interface is updated.

Where possible, the interfaces won't change - however new methods may be added
and existing methods may be deprecated.

## How to extend/modify an existing modules

To create a module that is just a modified version of an existing module,
you can extend the existing module (instead of extending `AbstractModule`).

```php
<?php 
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\PedigreeChartModule;

return new class extends PedigreeChartModule implements ModuleCustomInterface {
    use ModuleCustomTrait;
    
    /**
     * @return string
     */
    public function description(): string
    {
        return 'A modified version of the pedigree chart';
    }
    
    // Change the default layout...
    public const DEFAULT_ORIENTATION = self::OLDEST_AT_TOP;
};
```
