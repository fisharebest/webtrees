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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function bin2hex;
use function random_bytes;

final class UserCreate extends AbstractCommand
{
    public function __construct(private readonly UserService $user_service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'user-create')
            ->setDescription(description: 'Create a new user')
            ->addOption(name: 'username', mode: InputOption::VALUE_REQUIRED, description: 'The username of the new user')
            ->addOption(name: 'realname', mode: InputOption::VALUE_REQUIRED, description: 'The real name of the new user')
            ->addOption(name: 'email', mode: InputOption::VALUE_REQUIRED, description: 'The email of the new user')
            ->addOption(name: 'password', mode: InputOption::VALUE_REQUIRED, description: 'The password of the new user')
            ->addOption(name: 'timezone', mode: InputOption::VALUE_REQUIRED, description: 'Set the timezone', default: 'UTC')
            ->addOption(name: 'language', mode: InputOption::VALUE_REQUIRED, description: 'Set the language', default: 'en-US')
            ->addOption(name: 'admin', mode: InputOption::VALUE_NONE, description: 'Make the new user an administrator');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle(input: $input, output: $output);

        $username = $this->stringOption(input: $input, name: 'username');
        $realname = $this->stringOption(input: $input, name: 'realname');
        $email    = $this->stringOption(input: $input, name: 'email');
        $password = $this->stringOption(input: $input, name: 'password');
        $admin    = $this->boolOption(input: $input, name: 'admin');
        $timezone = $this->stringOption(input: $input, name: 'timezone');
        $language = $this->stringOption(input: $input, name: 'language');

        $errors = false;

        if ($username === '') {
            $io->error(message: 'Missing required option: --username');
            $errors = true;
        }

        if ($realname === '') {
            $io->error(message: 'Missing required option: --realname');
            $errors = true;
        }

        if ($email === '') {
            $io->error(message: 'Missing required option: --email');
            $errors = true;
        }

        if ($timezone === '') {
            $io->error(message: 'Missing required option: --timezone');
            $errors = true;
        }

        if ($errors) {
            return Command::INVALID;
        }

        $user = $this->user_service->findByUserName(user_name: $username);

        if ($user !== null) {
            $io->error(message: 'A user with the username "' . $username . '" already exists.');

            return self::FAILURE;
        }

        $user = $this->user_service->findByEmail(email: $email);

        if ($user !== null) {
            $io->error(message: 'A user with the email "' . $email . '" already exists');

            return self::FAILURE;
        }

        if ($password === '') {
            $password = bin2hex(string: random_bytes(length: 12));
            $io->info(message: 'Generated password: ' . $password);
        }

        $user = $this->user_service->create(user_name: $username, real_name: $realname, email: $email, password: $password);
        $user->setPreference(setting_name: UserInterface::PREF_TIME_ZONE, setting_value: $timezone);
        $user->setPreference(setting_name: UserInterface::PREF_LANGUAGE, setting_value: $language);
        $user->setPreference(setting_name: UserInterface::PREF_IS_ACCOUNT_APPROVED, setting_value: '1');
        $user->setPreference(setting_name: UserInterface::PREF_IS_EMAIL_VERIFIED, setting_value: '1');
        $user->setPreference(setting_name: UserInterface::PREF_CONTACT_METHOD, setting_value: 'messaging');
        $io->success('User ' . $user->id() . ' created.');

        if ($admin) {
            $user->setPreference(setting_name: UserInterface::PREF_IS_ADMINISTRATOR, setting_value: '1');
            $io->success(message: 'User granted administrator role.');
        }

        DB::exec(sql: 'COMMIT');

        return self::SUCCESS;
    }
}
