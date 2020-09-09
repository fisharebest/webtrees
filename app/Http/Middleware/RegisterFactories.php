<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Factories\CacheFactory;
use Fisharebest\Webtrees\Factories\FamilyFactory;
use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Fisharebest\Webtrees\Factories\GedcomRecordFactory;
use Fisharebest\Webtrees\Factories\HeaderFactory;
use Fisharebest\Webtrees\Factories\ImageFactory;
use Fisharebest\Webtrees\Factories\IndividualFactory;
use Fisharebest\Webtrees\Factories\LocationFactory;
use Fisharebest\Webtrees\Factories\MediaFactory;
use Fisharebest\Webtrees\Factories\NoteFactory;
use Fisharebest\Webtrees\Factories\RepositoryFactory;
use Fisharebest\Webtrees\Factories\SourceFactory;
use Fisharebest\Webtrees\Factories\SubmissionFactory;
use Fisharebest\Webtrees\Factories\SubmitterFactory;
use Fisharebest\Webtrees\Factories\XrefFactory;
use Fisharebest\Webtrees\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to register various factory objects.
 */
class RegisterFactories implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Factory::cache(new CacheFactory());
        Factory::family(new FamilyFactory());
        Factory::filesystem(new FilesystemFactory());
        Factory::gedcomRecord(new GedcomRecordFactory());
        Factory::header(new HeaderFactory());
        Factory::image(new ImageFactory());
        Factory::individual(new IndividualFactory());
        Factory::location(new LocationFactory());
        Factory::media(new MediaFactory());
        Factory::note(new NoteFactory());
        Factory::repository(new RepositoryFactory());
        Factory::source(new SourceFactory());
        Factory::submission(new SubmissionFactory());
        Factory::submitter(new SubmitterFactory());
        Factory::xref(new XrefFactory());

        return $handler->handle($request);
    }
}
