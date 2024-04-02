<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Cli;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Application;

use function parse_ini_file;

final class Console extends Application
{
    public function __construct()
    {
        parent::__construct(Webtrees::NAME, Webtrees::VERSION);
    }

    public function loadCommands(): self
    {
        $commands = glob(pattern: __DIR__ . '/Commands/*.php') ?: [];

        foreach ($commands as $command) {
            $class = __NAMESPACE__ . '\\Commands\\' . basename(path: $command, suffix: '.php');

            $this->add(Registry::container()->get($class));
        }

        return $this;
    }

    public function bootstrap(): self
    {
        I18N::init(code: 'en-US', setup: true);

        $config = parse_ini_file(filename: Webtrees::CONFIG_FILE);

        if ($config === false) {
            return $this;
        }

        DB::connect(
            driver: $config['dbtype'] ?? DB::MYSQL,
            host: $config['dbhost'],
            port: $config['dbport'],
            database: $config['dbname'],
            username: $config['dbuser'],
            password: $config['dbpass'],
            prefix: $config['tblpfx'],
            key: $config['dbkey'] ?? '',
            certificate: $config['dbcert'] ?? '',
            ca: $config['dbca'] ?? '',
            verify_certificate: (bool) ($config['dbverify'] ?? ''),
        );

        DB::exec('START TRANSACTION');

        return $this;
    }
}
