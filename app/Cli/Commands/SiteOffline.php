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

namespace Fisharebest\Webtrees\Cli\Commands;

use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_exists;
use function file_put_contents;

final class SiteOffline extends Command
{
    protected function configure(): void
    {
        $this
            ->setName(name: 'site-offline')
            ->setDescription(description: 'Set webtrees offline - disable web access.')
            ->addArgument(name: 'message', mode: InputArgument::OPTIONAL, description: 'A message to display to users.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $message */
        $message = $input->getArgument(name: 'message') ?? '';

        $io = new SymfonyStyle(input: $input, output: $output);

        $file_exists = file_exists(filename: Webtrees::OFFLINE_FILE);

        try {
            file_put_contents(filename: Webtrees::OFFLINE_FILE, data: $message);
        } catch (Throwable $ex) {
            $io->error(message: 'Failed to write file ' . Webtrees::OFFLINE_FILE);
            $io->error(message: $ex->getMessage());

            return Command::FAILURE;
        }

        if ($file_exists) {
            $io->success(message: Webtrees::OFFLINE_FILE . ' updated. Site is offline.');
        } else {
            $io->success(message: Webtrees::OFFLINE_FILE . ' created. Site is offline.');
        }

        return Command::SUCCESS;
    }
}
