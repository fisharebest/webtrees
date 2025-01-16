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

use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_exists;

final class SiteOnline extends Command
{
    protected function configure(): void
    {
        $this
            ->setName(name: 'site-online')
            ->setDescription(description: 'Set webtrees online - enable web access.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $file_exists = file_exists(filename: Webtrees::OFFLINE_FILE);

        if (!$file_exists) {
            $io->success(message: Webtrees::OFFLINE_FILE . ' does not exist. Site is already online.');
        }

        try {
            unlink(filename: Webtrees::OFFLINE_FILE);
            $io->success(message: Webtrees::OFFLINE_FILE . ' deleted. Site is online.');
        } catch (Throwable $ex) {
            $io->error(message: 'Unable to delete ' . Webtrees::OFFLINE_FILE . ' - ' . $ex->getMessage());
        }

        return Command::SUCCESS;
    }
}
