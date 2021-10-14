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
     * @covers \Fisharebest\Webtrees\Validator::array
     */
    public function testArrayParameter(): void
    {
        $parameters = ['param' => ['test'], 'invalid' => 'not_array'];
        $validator = new Validator($parameters);

        self::assertSame(['test'], $validator->array('param'));
        self::assertNull($validator->array('invalid'));
        self::assertNull($validator->array('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::integer
     */
    public function testIntegerParameter(): void
    {
        $parameters = ['param' => '42', 'invalid' => 'not_int', 'integer' => 42];
        $validator = new Validator($parameters);

        self::assertSame(42, $validator->integer('param'));
        self::assertNull($validator->integer('invalid'));
        self::assertNull($validator->integer('integer'));
        self::assertNull($validator->integer('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::string
     */
    public function testStringParameter(): void
    {
        $parameters = ['param' => 'test', 'invalid' => ['not_string']];
        $validator = new Validator($parameters);

        self::assertSame('test', $validator->string('param'));
        self::assertNull($validator->string('invalid'));
        self::assertNull($validator->string('param2'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::requiredArray
     */
    public function testRequiredArrayParameter(): void
    {
        $parameters = ['param' => ['test'], 'invalid' => 'not_array'];
        $validator = new Validator($parameters);

        self::assertSame(['test'], $validator->requiredArray('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->requiredArray('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::requiredInteger
     */
    public function testRequiredIntegerParameter(): void
    {
        $parameters = ['param' => '42', 'invalid' => 'not_int'];
        $validator = new Validator($parameters);

        self::assertSame(42, $validator->requiredInteger('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->requiredInteger('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::requiredString
     */
    public function testRequiredStringParameter(): void
    {
        $parameters = ['param' => 'test', 'invalid' => ['not_string']];
        $validator = new Validator($parameters);

        self::assertSame('test', $validator->requiredString('param'));

        $this->expectException(HttpBadRequestException::class);
        $validator->requiredString('invalid');
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

        self::assertSame(42, $validator->integer('param'));
        self::assertNull($validator->integer('invalid'));
        self::assertNull($validator->integer('wrongtype'));
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

        self::assertSame('X1', $validator->string('param'));
        self::assertNull($validator->string('invalid'));
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

        self::assertSame('http://example.local/wt/page', $validator->string('param'));
        self::assertSame('//example.local/wt/page', $validator->string('noscheme'));
        self::assertNull($validator->string('https'));
        self::assertNull($validator->string('invalid'));
        self::assertNull($validator->string('wrongtype'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlWithInvalidBaseUrl(): void
    {
        $this->expectException(LogicException::class);
        (new Validator(['param' => 'test']))->isLocalUrl('http://:invalid.url/')->string('param');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::parsedBody
     */
    public function testParsedBody(): void
    {
        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        self::assertNull(Validator::parsedBody($request)->string('param'));

        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        self::assertSame('test', Validator::parsedBody($request)->string('param'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::__construct
     * @covers \Fisharebest\Webtrees\Validator::queryParams
     */
    public function testQueryParams(): void
    {
        $request = self::createRequest()->withParsedBody(['param' => 'test']);
        self::assertNull(Validator::queryParams($request)->string('param'));

        $request = self::createRequest()->withQueryParams(['param' => 'test']);
        self::assertSame('test', Validator::queryParams($request)->string('param'));
    }
}
