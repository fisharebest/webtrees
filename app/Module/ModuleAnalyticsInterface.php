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

namespace Fisharebest\Webtrees\Module;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ModuleAnalyticsInterface - Classes and libraries for module system
 */
interface ModuleAnalyticsInterface extends ModuleInterface
{
    /**
     * Should we add this tracker?
     *
     * @return bool
     */
    public function analyticsCanShow(): bool;

    /**
     * Form fields to edit the parameters.
     *
     * @return string
     */
    public function analyticsFormFields(): string;

    /**
     * The parameters that need to be embedded in the snippet.
     *
     * @return array<string>
     */
    public function analyticsParameters(): array;

    /**
     * Embed placeholders in the snippet.
     *
     * @param array<string> $parameters
     *
     * @return string
     */
    public function analyticsSnippet(array $parameters): string;

    /**
     * Is this a tracker, as opposed to just a site-verification.
     *
     * @return bool
     */
    public function isTracker(): bool;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface;
}
