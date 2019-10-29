<?php

/**
 * An example module to modify PHP and database configuration.
 */

declare(strict_types=1);

namespace MyCustomNamespace;

require __DIR__ . '/ExampleServerConfigurationModule.php';

return app(ExampleServerConfigurationModule::class);
