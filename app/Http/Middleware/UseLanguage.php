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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Factories\LanguageFactory;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UseLanguage implements MiddlewareInterface
{
    private LanguageFactory $language_factory;

    public function __construct(LanguageFactory $language_factory)
    {
        $this->language_factory = $language_factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $language_tag = Session::get('language');

        if (is_string($language_tag)) {
            $language = $this->language_factory->fromLanguageTag($language_tag);
        } else {
            $language = $this->language_factory->fromRequest($request);
            Session::put('language', $language->languageTag());
        }

        I18N::init($language->languageTag());

        return $handler->handle($request);
    }
}
