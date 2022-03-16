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
use LogicException;

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

        self::assertSame(42, $validator->integer('param'));
        self::assertSame(42, $validator->integer('invalid', 42));
        self::assertSame(42, $validator->integer('wrongtype', 42));
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

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameter(): void
    {
        $parameters = [
            'param'     => 'http://example.local/wt/page',
            'noscheme'  => '//example.local/wt/page',
        ];
        $validator = (new Validator($parameters))->isLocalUrl('http://example.local/wt');

        self::assertSame('http://example.local/wt/page', $validator->string('param'));
        self::assertSame('//example.local/wt/page', $validator->string('noscheme'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongScheme(): void
    {
        $parameters = [
            'https'     => 'https://example.local/wt/page',
        ];
        $validator = (new Validator($parameters))->isLocalUrl('http://example.local/wt');

        $this->expectException(HttpBadRequestException::class);
        $validator->string('https');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongDomain(): void
    {
        $parameters = [
            'invalid'   => 'http://example.com/wt/page',
        ];
        $validator = (new Validator($parameters))->isLocalUrl('http://example.local/wt');

        $this->expectException(HttpBadRequestException::class);
        $validator->string('invalid');
    }

    /**
     * @covers \Fisharebest\Webtrees\Validator::isLocalUrl
     */
    public function testIsLocalUrlParameterWrongType(): void
    {
        $parameters = [
            'wrongtype' => ['42']
        ];
        $validator = (new Validator($parameters))->isLocalUrl('http://example.local/wt');

        $this->expectException(HttpBadRequestException::class);
        $validator->string('wrongtype');
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
