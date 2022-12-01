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

use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Test harness for the class Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Validator::array
     */
    public function testRequiredArrayParameter(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = ['param' => ['test'], 'invalid' => 'not_array'];
        $validator  = new Validator($parameters, $request);

        self::assertSame(['test'], $validator->array('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->array('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::integer
     */
    public function testRequiredIntegerParameter(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = [
            'int_type_positive'    => 42,
            'int_type_negative'    => -42,
            'string_type_positive' => '42',
            'string_type_negative' => '-42',
            'invalid'              => 'not_int',
        ];
        $validator  = new Validator($parameters, $request);

        self::assertSame(42, $validator->integer('int_type_positive'));
        self::assertSame(-42, $validator->integer('int_type_negative'));
        self::assertSame(42, $validator->integer('string_type_positive'));
        self::assertSame(-42, $validator->integer('string_type_negative'));

        $this->expectException(HttpBadRequestException::class);
        $validator->integer('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::string
     */
    public function testRequiredStringParameter(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = ['param' => 'test', 'invalid' => ['not_string']];
        $validator  = new Validator($parameters, $request);

        self::assertSame('test', $validator->string('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isBetween
     */
    public function testIsBetweenParameter(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = ['param' => '42', 'invalid' => '10', 'wrongtype' => 'not_integer'];
        $validator  = (new Validator($parameters, $request))->isBetween(40, 45);

        self::assertSame(42, $validator->integer('param'));
        self::assertSame(42, $validator->integer('invalid', 42));
        self::assertSame(42, $validator->integer('wrongtype', 42));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isXref
     */
    public function testIsXrefParameter(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = ['param' => 'X1', 'invalid' => '@X1@'];
        $validator  = (new Validator($parameters, $request))->isXref();

        self::assertSame('X1', $validator->string('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('base_url')->willReturn('http://example.local/wt');

        $parameters = ['param' => 'http://example.local/wt/page', 'noscheme' => '//example.local/wt/page'];
        $validator  = (new Validator($parameters, $request))->isLocalUrl();

        self::assertSame('http://example.local/wt/page', $validator->string('param'));
        self::assertSame('//example.local/wt/page', $validator->string('noscheme'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongScheme(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('base_url')->willReturn('http://example.local/wt');

        $parameters = ['https' => 'https://example.local/wt/page'];
        $validator  = (new Validator($parameters, $request))->isLocalUrl();

        $this->expectException(HttpBadRequestException::class);
        $validator->string('https');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongDomain(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('base_url')->willReturn('http://example.local/wt');

        $parameters = ['invalid' => 'http://example.com/wt/page'];
        $validator  = (new Validator($parameters, $request))->isLocalUrl();

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongType(): void
    {
        $request    = $this->createStub(ServerRequestInterface::class);
        $parameters = ['wrongtype' => ['42']];
        $validator  = (new Validator($parameters, $request))->isLocalUrl();

        $this->expectException(HttpBadRequestException::class);
        $validator->string('wrongtype');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::parsedBody
     */
    public function testParsedBody(): void
    {
        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        self::assertSame('test', Validator::parsedBody($request)->string('param'));

        $this->expectException(HttpBadRequestException::class);
        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        Validator::parsedBody($request)->string('param');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     */
    public function testQueryParams(): void
    {
        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        self::assertSame('test', Validator::queryParams($request)->string('param'));

        $this->expectException(HttpBadRequestException::class);
        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        Validator::queryParams($request)->string('param');
    }
}
