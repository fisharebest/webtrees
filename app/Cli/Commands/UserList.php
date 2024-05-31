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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserList extends Command
{
    public function __construct(private readonly UserService $user_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'user-list')
            ->setDescription(description: 'List users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->user_service->all()->sort(callback: fn ($a, $b) => $a->id() <=> $b->id());

        $table = new Table(output: $output);

        $table->setHeaders(headers: ['ID', 'Username', 'Real Name', 'Email', 'Admin', 'Approved', 'Verified', 'Language', 'Timezone', 'Contact', 'Registered', 'Last login']);

        foreach ($users as $user) {
            $registered = (int) $user->getPreference(setting_name: UserInterface::PREF_TIMESTAMP_REGISTERED);
            $last_login = (int) $user->getPreference(setting_name: UserInterface::PREF_TIMESTAMP_ACTIVE);

            if ($registered === 0) {
                $registered = 'Never';
            } else {
                $registered = Registry::timestampFactory()->make(timestamp: $registered)->format(format: 'Y-m-d H:i:s');
            }

            if ($last_login === 0) {
                $last_login = 'Never';
            } else {
                $last_login = Registry::timestampFactory()->make(timestamp: $last_login)->format(format: 'Y-m-d H:i:s');
            }

            $table->addRow(row: [
                $user->id(),
                $user->userName(),
                $user->realName(),
                $user->email(),
                Auth::isAdmin(user: $user) ? 'Yes' : 'No',
                $user->getPreference(setting_name: UserInterface::PREF_IS_ACCOUNT_APPROVED) ? 'Yes' : 'No',
                $user->getPreference(setting_name: UserInterface::PREF_IS_EMAIL_VERIFIED) ? 'Yes' : 'No',
                $user->getPreference(setting_name: UserInterface::PREF_LANGUAGE),
                $user->getPreference(setting_name: UserInterface::PREF_TIME_ZONE),
                $user->getPreference(setting_name: UserInterface::PREF_CONTACT_METHOD),
                $registered,
                $last_login,
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
