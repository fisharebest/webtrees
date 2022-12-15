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

namespace Fisharebest\Webtrees;

use Aura\Router\Route;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Test harness for the class Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Validator::attributes
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testAttributes(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getAttributes')
            ->willReturn(['param' => 'test']);

        self::assertSame('test', Validator::attributes($request)->string('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::parsedBody
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testParsedBody(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getParsedBody')
            ->willReturn(['param' => 'test']);

        self::assertSame('test', Validator::parsedBody($request)->string('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testQueryParams(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => 'test']);

        self::assertSame('test', Validator::queryParams($request)->string('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::serverParams
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testServerParams(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getServerParams')
            ->willReturn(['param' => 'test']);

        self::assertSame('test', Validator::serverParams($request)->string('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testNonUTF8QueryParameterName(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(["\xFF" => 'test']);

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request);
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testNonUTF8QueryParameterValue(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['test' => "\xFF"]);

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request);
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::array
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredArrayParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => ['test'], 'invalid' => 'not_array']);


        self::assertSame(['test'], Validator::queryParams($request)->array('param'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->array('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::boolean
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredBooleanParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'a'    => '1',
                'b'    => 'on',
                'c'    => true,
                'd' => '0',
                'e' => '',
                'f' => false,
            ]);

        self::assertSame(true, Validator::queryParams($request)->boolean('a'));
        self::assertSame(true, Validator::queryParams($request)->boolean('b'));
        self::assertSame(true, Validator::queryParams($request)->boolean('c'));
        self::assertSame(false, Validator::queryParams($request)->boolean('d'));
        self::assertSame(false, Validator::queryParams($request)->boolean('e'));
        self::assertSame(false, Validator::queryParams($request)->boolean('f'));
        self::assertSame(false, Validator::queryParams($request)->boolean('g', false));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->boolean('h');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::integer
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredIntegerParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'int_type_positive'    => 42,
                'int_type_negative'    => -42,
                'string_type_positive' => '42',
                'string_type_negative' => '-42',
                'invalid'              => 'not_int',
            ]);

        self::assertSame(42, Validator::queryParams($request)->integer('int_type_positive'));
        self::assertSame(-42, Validator::queryParams($request)->integer('int_type_negative'));
        self::assertSame(42, Validator::queryParams($request)->integer('string_type_positive'));
        self::assertSame(-42, Validator::queryParams($request)->integer('string_type_negative'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->integer('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::route
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredRouteParameter(): void
    {
        $route = $this->createStub(Route::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'valid-route' => $route,
                'not-route'   => '',
            ]);

        self::assertSame($route, Validator::queryParams($request)->route('valid-route'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->route('not-route');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::string
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredStringParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => 'test', 'invalid' => ['not_string']]);

        self::assertSame('test', Validator::queryParams($request)->string('param'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::tree
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredTreeParameter(): void
    {
        $tree = $this->createStub(Tree::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'valid-tree' => $tree,
                'not-tree'   => '',
            ]);

        self::assertSame($tree, Validator::queryParams($request)->tree('valid-tree'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->tree('no-tree');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::treeOptional
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testOptionalTreeParameter(): void
    {
        $tree = $this->createStub(Tree::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'valid-tree' => $tree,
                'not-tree'   => '',
            ]);

        self::assertSame($tree, Validator::queryParams($request)->treeOptional('valid-tree'));
        self::assertSame(null, Validator::queryParams($request)->treeOptional('missing-tree'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->treeOptional('not-tree');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::user
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testRequiredUserParameter(): void
    {
        $user = $this->createStub(UserInterface::class);

        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn([
                'valid-user' => $user,
                'not-user'   => '',
            ]);

        self::assertSame($user, Validator::queryParams($request)->user('valid-user'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->user('not-user');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isBetween
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsBetweenParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => '42', 'invalid' => '10', 'wrongtype' => 'not_integer']);

        self::assertSame(42, Validator::queryParams($request)->isBetween(40, 45)->integer('param'));
        self::assertSame(42, Validator::queryParams($request)->isBetween(40, 45)->integer('invalid', 42));
        self::assertSame(42, Validator::queryParams($request)->isBetween(40, 45)->integer('wrongtype', 42));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isInArray
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsInArray(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => 'foo']);

        self::assertSame('foo', Validator::queryParams($request)->isInArray(['foo', 'bar'])->string('param'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isInArray(['baz'])->string('param');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isInArrayKeys
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsInArrayKeys(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => 'foo']);

        self::assertSame('foo', Validator::queryParams($request)->isInArrayKeys(['foo' => 1, 'bar' => 2])->string('param'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isInArrayKeys(['baz' => 3])->string('param');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isNotEmpty
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsNotEmpty(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['empty' => '', 'not-empty' => 'foo']);

        self::assertSame('foo', Validator::queryParams($request)->isNotEmpty()->string('not-empty'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isNotEmpty()->string('empty');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isTag
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsTagParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['valid' => 'BIRT', 'invalid' => '@X1@']);

        self::assertSame('BIRT', Validator::queryParams($request)->isTag()->string('valid'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isTag()->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isXref
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsXrefParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['valid' => 'X1', 'invalid' => '@X1@', 'valid-array' => ['X1'], 'invalid-array' => ['@X1@']]);

        self::assertSame('X1', Validator::queryParams($request)->isXref()->string('valid'));
        self::assertSame(['X1'], Validator::queryParams($request)->isXref()->array('valid-array'));
        self::assertSame([], Validator::queryParams($request)->isXref()->array('invalid-array'));

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isXref()->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsLocalUrlParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getAttribute')
            ->with('base_url')->willReturn('http://example.local/wt');
        $request
            ->method('getQueryParams')
            ->willReturn(['param' => 'http://example.local/wt/page', 'noscheme' => '//example.local/wt/page']);


        self::assertSame('http://example.local/wt/page', Validator::queryParams($request)->isLocalUrl()->string('param'));
        self::assertSame('//example.local/wt/page', Validator::queryParams($request)->isLocalUrl()->string('noscheme'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsLocalUrlParameterWrongScheme(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getAttribute')
            ->with('base_url')
            ->willReturn('http://example.local/wt');
        $request
            ->method('getQueryParams')
            ->willReturn(['https' => 'https://example.local/wt/page']);

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isLocalUrl()->string('https');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsLocalUrlParameterWrongDomain(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getAttribute')
            ->with('base_url')
            ->willReturn('http://example.local/wt');
        $request
            ->method('getQueryParams')
            ->willReturn(['invalid' => 'http://example.com/wt/page']);

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isLocalUrl()->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     * @covers \Fisharebest\Webtrees\Validator::__construct
     */
    public function testIsLocalUrlParameterWrongType(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method('getQueryParams')
            ->willReturn(['wrongtype' => ['42']]);

        $this->expectException(HttpBadRequestException::class);

        Validator::queryParams($request)->isLocalUrl()->isLocalUrl()->string('wrongtype');
    }
}
