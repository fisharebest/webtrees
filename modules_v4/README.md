# THIRD-PARTY MODULES

Many webtrees functions are provided by “modules”.
Modules allows you to add additional features to webtrees and modify existing features.

## Installing and uninstalling modules

A module is a folder containing a file called `module.php`.
There may be other files in the folder.

To install a module, copy its folder to `modules_v4`.

To uninstall it, delete its folder from `modules_v4`.

Note that module names (i.e. their folder names) must not contain
spaces or the characters `.`, `[` and `]`.

TIP: renaming a module from `<module>` to `<module.disable>`
is a quick way to hide it from webtrees.  This works because
modules containing `.` are ignored.

## Writing modules

To write a module, you need to understand the PHP programming langauge.

The rest of this document is aimed at PHP developers.

TIP: The built-in modules can be found in `app/Module/*.php`.
These contain lots of useful examples that you can copy/paste.

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
They *may* implement one or more of the following interfaces:

* `ModuleAnalyticsInterface` - adds a tracking/analytics provider.
* `ModuleBlockInterface` - adds a block to the home pages.
* `ModuleChartInterface` - adds a chart to the chart menu.
* `ModuleListInterface` - adds a list to the list menu.
* `ModuleConfigInterface` - adds a configuration page to the control panel.
* `ModuleMenuInterface` - adds an entry to the main menu.
* `ModuleReportInterface` - adds a report to the report menu.
* `ModuleSidebarInterface` - adds a sidebar to the individual pages.
* `ModuleTabInterface` - adds a tab to the individual pages.
* `ModuleThemeInterface` - adds a theme (this interface is still being developed).

For each module interface that you implement, you must also use the corresponding trait.
If you don't do this, your module may break whenever the module interface is updated.

Where possible, the interfaces won't change - however new methods may be added
and existing methods may be deprecated.

Modules may also implement the following interfaces, which allow them to integrate
more deeply into the application.

* `MiddlewareInterface` - allows a module to intercept the HTTP request/response cycle.

## How to extend/modify an existing modules

To create a module that is just a modified version of an existing module,
you can extend the existing module (instead of extending `AbstractModule`).

```php
<?php 
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\PedigreeChartModule;

/**
 * Creating an anoymous class will prevent conflicts with other custom modules.
 */
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
    public const DEFAULT_ORIENTATION = self::ORIENTATION_DOWN;
};
```

## Dependency Injection

webtrees uses the “Dependency Injection” pattern extensively.  This is a system for
automatically generating objects.  The advantages over using `new SomeClass()` are

* Easier testing - you can pass "dummy" objects to your class.
* Run-time resolution - you can request an Interface, and webtrees will find a specific instance for you.
* Can swap implementations at runtime.

Note that you cannot type-hint the following objects in the constructor, as they are not
created until after the modules. 

* other modules
* interfaces, such as `UserInterface` (the current user)
* the current tree `Tree` or objects that depend on it (`Statistics`)
as these objects are not created until after the module is created.

Instead, you can fetch these items when they are needed from the "application container" using:
``` $user = app(UserInterface::class)```

```php
<?php 
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Services\TimeoutService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Creating an anoymous class will prevent conflicts with other custom modules.
 */
return new class extends AbstractModule implements ModuleCustomInterface {
    use ModuleCustomTrait;
    
    /** @var TimeoutService */
    private $timeout_service;
    
    /**
     * This module needs the timeout service.
     * 
     * @param TimeoutService $timeout_service
     */
    public function __construct(TimeoutService $timeout_service)
    {
        $this->timeout_service = $timeout_service;   
        
        // You can replace core webtrees classes by providing alternate implementations:
        app()->bind('name of webtrees class', 'name of replacement class');
        app()->bind('name of webtrees class', new \stdClass());
    }

    /**
     * Methods that are called in response to HTTP requests use
     * dependency-injection.  You'll almost certainly need the request
     * object.  The restrictions on the constructor do not apply here.
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function getFooBarAction(Request $request): Response
    {
        return new Response($request->get('foo'));    
    }
};
```
