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

namespace Fisharebest\Webtrees;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Module\IndividualListModule;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;

use function array_map;
use function preg_match_all;

/**
 * Test the individual lists.
 *
 * @coversNothing
 */
class IndividualListTest extends TestCase
{
    protected static bool $uses_database = true;

    private Tree $tree;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        I18N::init('en-US');

        $user_service = new UserService();
        $tree_service = new TreeService(new GedcomImportService());
        $this->tree   = $tree_service->create('name', 'title');
        $this->user   = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $this->user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        $this->user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($this->user);
        // The default "John Doe" individual will confuse the test results...
        Registry::individualFactory()->make('X1', $this->tree)->deleteRecord();
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function testCollationOfInitials(): void
    {
        $module = new IndividualListModule();

        $this->tree->createIndividual("0 @@ INDI\n1 NAME /Âaa/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /aaa/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /Ååå/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /æææ/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /Caa/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /Css/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME /Dza/");

        I18N::init('en-US');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Findividual-list&amp;alpha=([^"&]+)/', $html, $matches);
        self::assertEquals(['A', 'C', 'D', 'Æ'], array_map(rawurldecode(...), $matches[1]));

        I18N::init('sv');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Findividual-list&amp;alpha=([^"&]+)/', $html, $matches);
        self::assertEquals(['A', 'C', 'D', 'Å', 'Æ'], array_map(rawurldecode(...), $matches[1]));

        I18N::init('hu');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Findividual-list&amp;alpha=([^"&]+)/', $html, $matches);
        self::assertEquals(['A', 'C', 'CS', 'DZ', 'Æ'], array_map(rawurldecode(...), $matches[1]));
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function xtestRedirectToCanonicalSurname(): void
    {
        $module = new IndividualListModule();

        I18N::init('en-US');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'Muller'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertStringContainsString('surname=MULLER', $response->getHeaderLine('Location'));

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'MÜLLER'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertStringContainsString('surname=MULLER', $response->getHeaderLine('Location'));

        I18N::init('de');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'MÜLLER'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_MOVED_PERMANENTLY, $response->getStatusCode());
        self::assertStringContainsString('surname=MUELLER', $response->getHeaderLine('Location'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function testCollationOfSurnames(): void
    {
        $module = new IndividualListModule();

        $i1 = $this->tree->createIndividual("0 @@ INDI\n1 NAME /Muller/");
        $i2 = $this->tree->createIndividual("0 @@ INDI\n1 NAME /Müller/");
        $i3 = $this->tree->createIndividual("0 @@ INDI\n1 NAME /Mueller/");

        I18N::init('en-US');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'MULLER'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i1->xref(), $i2->xref()], $matches[1], 'English, so U should match U and Ü');

        I18N::init('de');
        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'MULLER'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i1->xref()], $matches[1], 'German, so U should only match U');

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'MUELLER'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i2->xref(), $i3->xref()], $matches[1], 'German, so UE should also match Ü');
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function xtestUnknownVersusMissingSurname(): void
    {
        $module = new IndividualListModule();

        $i1 = $this->tree->createIndividual("0 @@ INDI\n1 NAME John //");
        $i2 = $this->tree->createIndividual("0 @@ INDI\n1 NAME John");

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => '@'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i1->xref()], $matches[1]);

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => ','], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i2->xref()], $matches[1]);
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function xtestAllSurnamesExcludesUnknownAndMissing(): void
    {
        $module = new IndividualListModule();

        $i1 = $this->tree->createIndividual("0 @@ INDI\n1 NAME John /Black/");
        $i2 = $this->tree->createIndividual("0 @@ INDI\n1 NAME Mary /White/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME Peter //");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME Paul");

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['show_all' => 'yes'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/individual-list&amp;surname=([A-Z]+)/', $html, $matches);
        self::assertEqualsCanonicalizing(['BLACK', 'WHITE'], $matches[1]);

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['show_all' => 'yes', 'show' => 'indi'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i1->xref(), $i2->xref()], $matches[1]);
    }

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     */
    public function xtestSurnameInitial(): void
    {
        $module = new IndividualListModule();

        $i1 = $this->tree->createIndividual("0 @@ INDI\n1 NAME John /Black/");
        $i2 = $this->tree->createIndividual("0 @@ INDI\n1 NAME Mary /Brown/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME Peter /White/");
        $this->tree->createIndividual("0 @@ INDI\n1 NAME Paul /Green/");

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => 'B'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/individual-list&amp;surname=([A-Z]+)/', $html, $matches);
        self::assertEqualsCanonicalizing(['BLACK', 'BROWN'], $matches[1]);

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => 'B', 'show' => 'indi'], [], [], ['tree' => $this->tree]);
        $response = $module->handle($request);
        $html     = $response->getBody()->getContents();
        preg_match_all('/%2Fname%2Findividual%2F(X\d+)%2F/', $html, $matches);
        self::assertEqualsCanonicalizing([$i1->xref(), $i2->xref()], $matches[1]);
    }
}
