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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function addcslashes;
use function array_map;
use function implode;

final class UserList extends Command
{
    public function __construct(private readonly UserService $user_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'user-list')
            ->setDescription(description: 'List users')
            ->addOption(
                name: 'format',
                shortcut: 'f',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Output format (table, json, csv)',
                default: 'table',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $format = $input->getOption(name: 'format');

        $io = new SymfonyStyle(input: $input, output: $output);

        $users = $this->user_service->all()->sort(callback: fn ($a, $b) => $a->id() <=> $b->id());

        $headers = ['ID', 'Username', 'Real Name', 'Email', 'Admin', 'Approved', 'Verified', 'Language', 'Timezone', 'Contact', 'Registered', 'Last login'];

        $rows = $users->map(callback: fn (User $user): array => [
            'id'         => $user->id(),
            'username'   => $user->userName(),
            'real_name'  => $user->realName(),
            'email'      => $user->email(),
            'admin'      => $user->getPreference(setting_name: UserInterface::PREF_IS_ADMINISTRATOR) ? 'yes' : 'no',
            'approved'   => $user->getPreference(setting_name: UserInterface::PREF_IS_ACCOUNT_APPROVED) ? 'yes' : 'no',
            'verified'   => $user->getPreference(setting_name: UserInterface::PREF_IS_EMAIL_VERIFIED) ? 'yes' : 'no',
            'language'   => $user->getPreference(setting_name: UserInterface::PREF_LANGUAGE),
            'timezone'   => $user->getPreference(setting_name: UserInterface::PREF_TIME_ZONE),
            'contact'    => $user->getPreference(setting_name: UserInterface::PREF_CONTACT_METHOD),
            'registered' => $this->formatTimestamp(timestamp: (int) $user->getPreference(setting_name: UserInterface::PREF_TIMESTAMP_REGISTERED)),
            'last_login' => $this->formatTimestamp(timestamp: (int) $user->getPreference(setting_name: UserInterface::PREF_TIMESTAMP_ACTIVE)),
        ])
        ->values()
        ->all();

        switch ($format) {
            case 'table':
                $table = new Table(output: $output);
                $table->setHeaders(headers: $headers);
                $table->setRows(rows: $rows);
                $table->render();
                break;

            case 'csv':
                $output->writeln(messages: $this->quoteCsvRow(columns: $headers));

                foreach ($rows as $row) {
                    $output->writeln(messages: $this->quoteCsvRow(columns: $row));
                }
                break;

            case 'json':
                $output->writeln(messages: json_encode(value: $rows, flags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
                break;

            default:
                $io->error(message: 'Invalid format: ‘' . $format . '’');

                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function formatTimestamp(int $timestamp): string
    {

        if ($timestamp === 0) {
            return '';
        }

        return Registry::timestampFactory()->make(timestamp: $timestamp)->format(format: 'Y-m-d H:i:s');
    }

    /**
     * @param array<string|int> $columns
     */
    private function quoteCsvRow(array $columns): string
    {
        $columns = array_map(callback: $this->quoteCsvValue(...), array: $columns);

        return implode(separator: ',', array: $columns);
    }

    private function quoteCsvValue(string|int $value): string
    {
        return '"' . addcslashes(string: (string) $value, characters: '"') . '"';
    }
}
