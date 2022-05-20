<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function var_dump;
use function view;

/**
 * Middleware to check if a new version of webtrees is available.
 */
class CheckForNewVersion implements MiddlewareInterface
{
    private EmailService $email_service;

    private UpgradeService $upgrade_service;

    private UserService $user_service;

    /**
     * @param EmailService   $email_service
     * @param UpgradeService $upgrade_service
     * @param UserService    $user_service
     */
    public function __construct(EmailService $email_service, UpgradeService $upgrade_service, UserService $user_service)
    {
        $this->email_service   = $email_service;
        $this->upgrade_service = $upgrade_service;
        $this->user_service    = $user_service;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            if ($request->getMethod() === RequestMethodInterface::METHOD_GET && $this->upgrade_service->isUpgradeAvailable()) {
                $latest_version       = $this->upgrade_service->latestVersion();
                $latest_version_email = Site::getPreference('LATEST_WT_VERSION_EMAIL');

                // Have we emailed about this version before?
                if ($latest_version !== $latest_version_email) {
                    Site::setPreference('LATEST_WT_VERSION_EMAIL', $latest_version);

                    // Yuck.  The email service needs a setting from config.ini.php - which is in the request.
                    Webtrees::set(ServerRequestInterface::class, $request);

                    foreach ($this->user_service->administrators() as $administrator) {
                        $this->email_service->send(
                            new SiteUser(),
                            $administrator,
                            new SiteUser(),
                            I18N::translate('A new version of webtrees is available.'),
                            view('emails/new-version-text', [
                                'latest_version' => $latest_version,
                                'recipient'      => $administrator,
                                'url'            => $request->getAttribute('base_url'),
                            ]),
                            view('emails/new-version-html', [
                                'latest_version' => $latest_version,
                                'recipient'      => $administrator,
                                'url'            => $request->getAttribute('base_url'),
                            ])
                        );
                    }
                }
            }
        } catch (Throwable $ex) {
            // We couldn't fetch the latest version or send an email? Nothing we can do...
        }

        return $handler->handle($request);
    }
}
