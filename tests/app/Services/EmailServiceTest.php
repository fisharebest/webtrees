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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Contracts\UserInterface;

/**
 * Test harness for the class EmailService
 *
 * @covers \Fisharebest\Webtrees\Services\EmailService
 */
class EmailServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Services\EmailService::send
     * @covers \Fisharebest\Webtrees\Services\EmailService::message
     * @covers \Fisharebest\Webtrees\Services\EmailService::transport
     */
    public function testSend(): void
    {
        $email_service = new EmailService();

        $user_from = $this->createMock(UserInterface::class);
        $user_from->method('email')->willReturn('user.from@example.com');

        $user_from = $this->createMock(UserInterface::class);
        $user_from->method('email')->willReturn('user.from@example.com');

        $user_to = $this->createMock(UserInterface::class);
        $user_to->method('email')->willReturn('user.to@example.com');

        $user_reply_to = $this->createMock(UserInterface::class);
        $user_reply_to->method('email')->willReturn('user.replyto@example.com');

        Site::setPreference('SMTP_ACTIVE', 'internal');

        self::assertTrue($email_service->send($user_from, $user_to, $user_reply_to, 'Test No DKIM', 'Test Plain Message', '<p>Test Html Message</p>'));

        Site::setPreference('DKIM_DOMAIN', 'example.com');
        Site::setPreference('DKIM_SELECTOR', 'sel');
        Site::setPreference('DKIM_KEY', '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCqGKukO1De7zhZj6+H0qtjTkVxwTCpvKe4eCZ0FPqri0cb2JZfXJ/DgYSF6vUp
wmJG8wVQZKjeGcjDOL5UlsuusFncCzWBQ7RKNUSesmQRMSGkVb1/3j+skZ6UtW+5u09lHNsj6tQ5
1s1SPrCBkedbNf0Tp0GbMJDyR4e9T04ZZwIDAQABAoGAFijko56+qGyN8M0RVyaRAXz++xTqHBLh
3tx4VgMtrQ+WEgCjhoTwo23KMBAuJGSYnRmoBZM3lMfTKevIkAidPExvYCdm5dYq3XToLkkLv5L2
pIIVOFMDG+KESnAFV7l2c+cnzRMW0+b6f8mR1CJzZuxVLL6Q02fvLi55/mbSYxECQQDeAw6fiIQX
GukBI4eMZZt4nscy2o12KyYner3VpoeE+Np2q+Z3pvAMd/aNzQ/W9WaI+NRfcxUJrmfPwIGm63il
AkEAxCL5HQb2bQr4ByorcMWm/hEP2MZzROV73yF41hPsRC9m66KrheO9HPTJuo3/9s5p+sqGxOlF
L0NDt4SkosjgGwJAFklyR1uZ/wPJjj611cdBcztlPdqoxssQGnh85BzCj/u3WqBpE2vjvyyvyI5k
X6zk7S0ljKtt2jny2+00VsBerQJBAJGC1Mg5Oydo5NwD6BiROrPxGo2bpTbu/fhrT8ebHkTz2epl
U9VQQSQzY1oZMVX8i1m5WUTLPz2yLJIBQVdXqhMCQBGoiuSoSjafUhV7i1cEGpb88h5NBYZzWXGZ
37sJ5QsW+sJyoNde3xH8vdXhzU7eT82D6X/scw9RZz+/6rCJ4p0=
-----END RSA PRIVATE KEY-----');

        self::assertTrue($email_service->send($user_from, $user_to, $user_reply_to, 'Test DKIM', 'Test Plain Message', '<p>Test Html Message</p>'));
    }

    /**
     * Data provider for testing email validity
     *
     * @return array<array<bool|string>>
     */
    public function emailProvider(): array
    {
        return [
            // Valid emails
            ['Abc@webtrees.com', true],
            ['ABC@webtrees.com', true],
            ['Abc.123@webtrees.com', true],
            ['user+mailbox/tree=family@webtrees.com', true],
            ['!#$%&\'*+-/=?^_`.{|}~@webtrees.com', true],
            ['"Abc@def"@webtrees.com', true],
            ['"John\ Doe"@webtrees.com', true],
            ['"Joe.\\Smith"@webtrees.com', true],
            ['généalogie@webtrees.com', true],
            // Invalid
            ['example@invalid.example.com', false],
            ['example', false],
            ['example@with space', false],
            ['example@webtrees.', false],
            ['example@webtr\ees.com', false],
            ['example(comment)@example.com', false],
            ["\x80\x81\x82@\x83\x84\x85.\x86\x87\x88", false],
            ['user  name@example.com', false],
            ['example.@example.com', false],
            ['example(example]example@example.co.uk', false],
            ['a@b.c+&%$.d', false],
            ['a.b+&%$.c@d', false],
            ['example@généalogie', false]
        ];
    }

    /**
     * @dataProvider emailProvider
     *
     * @covers \Fisharebest\Webtrees\Services\EmailService::isValidEmail
     *
     * @param string $email
     * @param bool $is_valid
     */
    public function testIsValidEmail(string $email, bool $is_valid): void
    {
        self::assertSame($is_valid, (new EmailService())->isValidEmail($email));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\EmailService::mailSslOptions
     */
    public function testMailSslOptions(): void
    {
        $options = (new EmailService())->mailSslOptions();
        self::assertCount(3, $options);
        self::assertArrayHasKey('ssl', $options);
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\EmailService::mailTransportOptions
     */
    public function testMailTransportOptions(): void
    {
        $options = (new EmailService())->mailTransportOptions();
        self::assertCount(function_exists('proc_open') ? 2 : 1, $options);
        self::assertArrayHasKey('external', $options);
    }
}
