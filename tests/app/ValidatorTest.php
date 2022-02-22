<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use LogicException;

/**
 * Test harness for the class Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers \Fisharebest\Webtrees\Validator::optionalArray
     */
    public function testArrayParameter(): void
    {
        $parameters = ['param' => ['test'], 'invalid' => 'not_array'];
        $validator = new Validator($parameters);

        self::assertSame(['test'], $validator->optionalArray('param'));
        self::assertNull($validator->optionalArray('invalid'));
        self::assertNull($validator->optionalArray('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::optionalInteger
     */
    public function testIntegerParameter(): void
    {
        $parameters = ['param' => '42', 'invalid' => 'not_int', 'integer' => 42];
        $validator = new Validator($parameters);

        self::assertSame(42, $validator->optionalInteger('param'));
        self::assertNull($validator->optionalInteger('invalid'));
        self::assertNull($validator->optionalInteger('integer'));
        self::assertNull($validator->optionalInteger('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::optionalString
     */
    public function testStringParameter(): void
    {
        $parameters = ['param' => 'test', 'invalid' => ['not_string']];
        $validator = new Validator($parameters);

        self::assertSame('test', $validator->optionalString('param'));
        self::assertNull($validator->optionalString('invalid'));
        self::assertNull($validator->optionalString('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::array
     */
    public function testRequiredArrayParameter(): void
    {
        $parameters = ['param' => ['test'], 'invalid' => 'not_array'];
        $validator = new Validator($parameters);

        self::assertSame(['test'], $validator->array('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->array('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::integer
     */
    public function testRequiredIntegerParameter(): void
    {
        $parameters = ['param' => '42', 'invalid' => 'not_int'];
        $validator = new Validator($parameters);

        self::assertSame(42, $validator->integer('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->integer('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::string
     */
    public function testRequiredStringParameter(): void
    {
        $parameters = ['param' => 'test', 'invalid' => ['not_string']];
        $validator = new Validator($parameters);

        self::assertSame('test', $validator->string('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isBetween
     */
    public function testIsBetweenParameter(): void
    {
        $parameters = [
            'param'     => '42',
            'invalid'   => '10',
            'wrongtype' => 'not_integer',
        ];
        $validator = (new Validator($parameters))->isBetween(40, 45);

        self::assertSame(42, $validator->optionalInteger('param'));
        self::assertNull($validator->optionalInteger('invalid'));
        self::assertNull($validator->optionalInteger('wrongtype'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isXref
     */
    public function testIsXrefParameter(): void
    {
        $parameters = [
            'param' => 'X1',
            'invalid' => '@X1@',
        ];
        $validator = (new Validator($parameters))->isXref();

        self::assertSame('X1', $validator->optionalString('param'));
        self::assertNull($validator->optionalString('invalid'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameter(): void
    {
        $parameters = [
            'param'     => 'http://example.local/wt/page',
            'noscheme'  => '//example.local/wt/page',
            'https'     => 'https://example.local/wt/page',
            'invalid'   => 'http://example.com/wt/page',
            'wrongtype' => ['42']
        ];
        $validator = (new Validator($parameters))->isLocalUrl('http://example.local/wt');

        self::assertSame('http://example.local/wt/page', $validator->optionalString('param'));
        self::assertSame('//example.local/wt/page', $validator->optionalString('noscheme'));
        self::assertNull($validator->optionalString('https'));
        self::assertNull($validator->optionalString('invalid'));
        self::assertNull($validator->optionalString('wrongtype'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlWithInvalidBaseUrl(): void
    {
        $this->expectException(LogicException::class);
        (new Validator(['param' => 'test']))->isLocalUrl('http://:invalid.url/')->optionalString('param');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::parsedBody
     */
    public function testParsedBody(): void
    {
        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        self::assertNull(Validator::parsedBody($request)->optionalString('param'));

        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        self::assertSame('test', Validator::parsedBody($request)->optionalString('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     */
    public function testQueryParams(): void
    {
        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        self::assertNull(Validator::queryParams($request)->optionalString('param'));

        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        self::assertSame('test', Validator::queryParams($request)->optionalString('param'));
    }
}
