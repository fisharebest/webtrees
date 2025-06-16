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

namespace Fisharebest\Webtrees\Cli\Commands;

use Fisharebest\Webtrees\Services\MaintenanceModeService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class SiteOffline extends AbstractCommand
{
    public function __construct(
        private readonly MaintenanceModeService $maintenance_mode_service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'site-offline')
            ->setDescription(description: 'Set webtrees offline - disable web access.')
            ->addArgument(name: 'message', mode: InputArgument::OPTIONAL, description: 'A message to display to users.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $this->stringArgument(input: $input, name: 'message');

        $io = new SymfonyStyle(input: $input, output: $output);

        $file = $this->maintenance_mode_service->file();

        try {
            $this->maintenance_mode_service->offline($message);
        } catch (Throwable $ex) {
            $io->error(message: 'Failed to write file ' . $file);
            $io->error(message: $ex->getMessage());

            return self::FAILURE;
        }

        $io->success(message: $file . ' created. Site is offline.');

        return self::SUCCESS;
    }
}
