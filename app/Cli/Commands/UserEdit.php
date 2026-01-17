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
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UserEdit extends AbstractCommand
{
    public function __construct(private readonly UserService $user_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'user')
            ->setDescription(description: 'Create/delete/edit a user')
            ->addArgument(name: 'user-name', mode: InputArgument::REQUIRED, description: 'The username')
            ->addOption(name: 'create', mode: InputOption::VALUE_NONE, description: 'Create a new user')
            ->addOption(name: 'delete', mode: InputOption::VALUE_NONE, description: 'Delete an existing user')
            ->addOption(name: 'real-name', mode: InputOption::VALUE_REQUIRED, description: 'Set the real name of the user')
            ->addOption(name: 'email', mode: InputOption::VALUE_REQUIRED, description: 'Set the email of the user')
            ->addOption(name: 'password', mode: InputOption::VALUE_REQUIRED, description: 'Set the password of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $user_name = $this->stringArgument(input: $input, name: 'user-name');
        $real_name = $this->stringOption(input: $input, name: 'real-name');
        $email     = $this->stringOption(input: $input, name: 'email');
        $password  = $this->stringOption(input: $input, name: 'password');
        $create    = $this->boolOption(input: $input, name: 'create');
        $delete    = $this->boolOption(input: $input, name: 'delete');

        if ($user_name === '') {
            $io->error(message: 'The user- name cannot be empty.');

            return Command::INVALID;
        }

        if ($delete && $create) {
            $io->error(message: 'Invalid options: cannot use --delete and --create at the same time.');

            return Command::INVALID;
        }

        if ($delete && $real_name !== '') {
            $io->error(message: 'Invalid options: cannot use --delete and --real-name at the same time.');

            return Command::INVALID;
        }

        if ($delete && $email !== '') {
            $io->error(message: 'Invalid options: cannot use --delete and --email at the same time.');

            return Command::INVALID;
        }

        if ($delete && $password !== '') {
            $io->error(message: 'Invalid options: cannot use --delete and --password at the same time.');

            return Command::INVALID;
        }

        $user = $this->user_service->all()->first(callback: static fn (User $user): bool => $user->userName() === $user_name);

        if ($create) {
            if ($user instanceof User) {
                $io->error(message: 'A user with the username "' . $user_name . '" already exists.');

                return Command::FAILURE;
            }

            if ($real_name === '') {
                $io->error(message: 'Invalid options: --real-name is required when using --create.');

                return Command::FAILURE;
            }

            if ($email === '') {
                $io->error(message: 'Invalid options: --email is required when using --create.');

                return Command::FAILURE;
            }

            if ($password === '') {
                $password = bin2hex(string: random_bytes(length: 8));
                $io->info(message: 'No password specified. Using a random password ‘' . $password . '’.');
            }

            $user = $this->user_service->create(
                user_name: $user_name,
                real_name: $real_name,
                email: $email,
                password: $password,
            );

            // Some preferences need to be set for all users.
            $user->setPreference(
                setting_name: UserInterface::PREF_LANGUAGE,
                setting_value: 'en-US',
            );
            $user->setPreference(
                setting_name: UserInterface::PREF_TIME_ZONE,
                setting_value: Site::getPreference('TIMEZONE'),
            );
            $user->setPreference(
                setting_name: UserInterface::PREF_IS_EMAIL_VERIFIED,
                setting_value: '1',
            );
            $user->setPreference(
                setting_name: UserInterface::PREF_IS_ACCOUNT_APPROVED,
                setting_value: '1',
            );
            $user->setPreference(
                setting_name: UserInterface::PREF_CONTACT_METHOD,
                setting_value: MessageService::CONTACT_METHOD_INTERNAL_AND_EMAIL,
            );
            $user->setPreference(
                setting_name: UserInterface::PREF_IS_VISIBLE_ONLINE,
                setting_value: '1',
            );
            $io->info(message: 'User ‘' . $user_name . '’ was created.  Account set to approved and verified.');

            return self::SUCCESS;
        }

        if ($user === null) {
            $io->error(message: 'User ‘' . $user_name . '’ does not exist.');

            return Command::FAILURE;
        }

        if ($delete) {
            $this->user_service->delete(user: $user);
            $io->success(message: 'User ‘' . $user_name . '’ was deleted..');

            return self::SUCCESS;
        }

        if ($real_name === '' && $email === '' && $password === '') {
            $io->info(message: 'Nothing to do. Specify --real-name, --email or --password.');

            return Command::INVALID;
        }

        if ($real_name !== '') {
            $user->setRealName($real_name);
            $io->info(message: 'Real name set to ‘' . $real_name . '’.');
        }

        if ($email !== '') {
            $user->setEmail($email);
            $io->info(message: 'E-mail set to ‘' . $email . '’.');
        }

        if ($password !== '') {
            $user->setPassword($password);
            $io->info(message: 'Password set to ‘' . $password . '’.');
        }

        return self::SUCCESS;
    }
}
