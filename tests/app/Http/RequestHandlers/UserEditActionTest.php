<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserEditAction::class)]
class UserEditActionTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(UserEditAction::class));
    }

    public function testHandleUpdatesUserAndRedirects(): void
    {
        $user_service = new UserService();
        $admin_user   = $user_service->create('admin1', 'Admin One', 'admin1@example.com', 'adminpass');
        $edit_user    = $user_service->create('editme', 'Edit Me', 'editme@example.com', 'editpass');
        $mail_service = new EmailService();
        $tree_service = new TreeService(new GedcomImportService());

        $handler = new UserEditAction($mail_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => (string) $edit_user->id(),
            'username'       => 'editme-new',
            'real_name'      => 'Edited Name',
            'email'          => 'editme-new@example.com',
            'password'       => 'newpassword',
            'theme'          => '',
            'language'       => 'en-US',
            'timezone'       => 'UTC',
            'comment'        => 'Test comment',
            'contact-method' => 'mailto',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '1',
            'approved'       => '1',
        ])
            ->withAttribute('user', $admin_user);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }

    public function testHandleThrowsNotFoundForNonExistingUser(): void
    {
        $this->expectException(HttpNotFoundException::class);

        $user_service = new UserService();
        $admin_user   = $user_service->create('admin2', 'Admin Two', 'admin2@example.com', 'adminpass');
        $mail_service = new EmailService();
        $tree_service = new TreeService(new GedcomImportService());

        $handler = new UserEditAction($mail_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => '99999',
            'username'       => 'nobody',
            'real_name'      => 'Nobody',
            'email'          => 'nobody@example.com',
            'password'       => '',
            'theme'          => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact-method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $admin_user);
        $handler->handle($request);
    }

    public function testHandleRedirectsOnDuplicateEmail(): void
    {
        $user_service = new UserService();
        $admin_user   = $user_service->create('admin3', 'Admin Three', 'admin3@example.com', 'adminpass');
        $other_user   = $user_service->create('other1', 'Other User', 'taken@example.com', 'otherpass');
        $edit_user    = $user_service->create('editdup', 'Edit Dup', 'editdup@example.com', 'editpass');
        $mail_service = new EmailService();
        $tree_service = new TreeService(new GedcomImportService());

        $handler = new UserEditAction($mail_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => (string) $edit_user->id(),
            'username'       => 'editdup',
            'real_name'      => 'Edit Dup',
            'email'          => 'taken@example.com',
            'password'       => '',
            'theme'          => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact-method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $admin_user);
        $response = $handler->handle($request);

        // Duplicate email redirects back to edit page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('user_id', $response->getHeaderLine('Location'));
    }

    public function testHandleRedirectsOnDuplicateUsername(): void
    {
        $user_service = new UserService();
        $admin_user   = $user_service->create('admin4', 'Admin Four', 'admin4@example.com', 'adminpass');
        $other_user   = $user_service->create('takenname', 'Taken Name', 'takenname@example.com', 'otherpass');
        $edit_user    = $user_service->create('editdup2', 'Edit Dup2', 'editdup2@example.com', 'editpass');
        $mail_service = new EmailService();
        $tree_service = new TreeService(new GedcomImportService());

        $handler = new UserEditAction($mail_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => (string) $edit_user->id(),
            'username'       => 'takenname',
            'real_name'      => 'Edit Dup2',
            'email'          => 'editdup2@example.com',
            'password'       => '',
            'theme'          => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact-method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $admin_user);
        $response = $handler->handle($request);

        // Duplicate username redirects back to edit page
        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
        self::assertStringContainsString('user_id', $response->getHeaderLine('Location'));
    }

    public function testHandleWithoutPasswordDoesNotChangePassword(): void
    {
        $user_service = new UserService();
        $admin_user   = $user_service->create('admin5', 'Admin Five', 'admin5@example.com', 'adminpass');
        $edit_user    = $user_service->create('nopasschg', 'No Pass Change', 'nopasschg@example.com', 'oldpass');
        $mail_service = new EmailService();
        $tree_service = new TreeService(new GedcomImportService());

        $handler = new UserEditAction($mail_service, $tree_service, $user_service);
        $request = self::createRequest(RequestMethodInterface::METHOD_POST, [], [
            'user_id'        => (string) $edit_user->id(),
            'username'       => 'nopasschg',
            'real_name'      => 'No Pass Change',
            'email'          => 'nopasschg@example.com',
            'password'       => '',
            'theme'          => '',
            'language'       => '',
            'timezone'       => '',
            'comment'        => '',
            'contact-method' => '',
            'auto_accept'    => '',
            'canadmin'       => '',
            'visible-online' => '',
            'verified'       => '',
            'approved'       => '',
        ])
            ->withAttribute('user', $admin_user);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());

        // User can still authenticate with old password
        $found_user = $user_service->findByIdentifier('nopasschg');
        self::assertNotNull($found_user);
        self::assertTrue($found_user->checkPassword('oldpass'));
    }
}
