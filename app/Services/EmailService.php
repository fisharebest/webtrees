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

use Exception;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ServerRequestInterface;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;
use Swift_SendmailTransport;
use Swift_Signers_DKIMSigner;
use Swift_SmtpTransport;
use Swift_Transport;

use function assert;
use function checkdnsrr;
use function filter_var;
use function function_exists;
use function str_replace;
use function strrchr;
use function substr;

use const FILTER_VALIDATE_DOMAIN;
use const FILTER_VALIDATE_EMAIL;

/**
 * Send emails.
 */
class EmailService
{
    /**
     * Send an external email message
     * Caution! gmail may rewrite the "From" header unless you have added the address to your account.
     *
     * @param UserInterface $from
     * @param UserInterface $to
     * @param UserInterface $reply_to
     * @param string        $subject
     * @param string        $message_text
     * @param string        $message_html
     *
     * @return bool
     */
    public function send(UserInterface $from, UserInterface $to, UserInterface $reply_to, string $subject, string $message_text, string $message_html): bool
    {
        $message   = $this->message($from, $to, $reply_to, $subject, $message_text, $message_html);
        $transport = $this->transport();
        $mailer    = new Swift_Mailer($transport);

        try {
            $mailer->send($message);
        } catch (Exception $ex) {
            Log::addErrorLog('MailService: ' . $ex->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Create a message
     *
     * @param UserInterface $from
     * @param UserInterface $to
     * @param UserInterface $reply_to
     * @param string        $subject
     * @param string        $message_text
     * @param string        $message_html
     *
     * @return Swift_Message
     */
    protected function message(UserInterface $from, UserInterface $to, UserInterface $reply_to, string $subject, string $message_text, string $message_html): Swift_Message
    {
        // Mail needs MS-DOS line endings
        $message_text = str_replace("\n", "\r\n", $message_text);
        $message_html = str_replace("\n", "\r\n", $message_html);

        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setFrom($from->email(), $from->realName())
            ->setTo($to->email(), $to->realName())
            ->setReplyTo($reply_to->email(), $reply_to->realName())
            ->setBody($message_html, 'text/html');

        $dkim_domain   = Site::getPreference('DKIM_DOMAIN');
        $dkim_selector = Site::getPreference('DKIM_SELECTOR');
        $dkim_key      = Site::getPreference('DKIM_KEY');

        if ($dkim_domain !== '' && $dkim_selector !== '' && $dkim_key !== '') {
            $signer = new Swift_Signers_DKIMSigner($dkim_key, $dkim_domain, $dkim_selector);
            $signer
                ->setHeaderCanon('relaxed')
                ->setBodyCanon('relaxed');

            $message->attachSigner($signer);
        } else {
            // DKIM body hashes don't work with multipart/alternative content.
            $message->addPart($message_text, 'text/plain');
        }

        return $message;
    }

    /**
     * Create a transport mechanism for sending mail
     *
     * @return Swift_Transport
     */
    protected function transport(): Swift_Transport
    {
        switch (Site::getPreference('SMTP_ACTIVE')) {
            case 'sendmail':
                // Local sendmail (requires PHP proc_* functions)
                $request = app(ServerRequestInterface::class);
                assert($request instanceof ServerRequestInterface);

                $sendmail_command = $request->getAttribute('sendmail_command', '/usr/sbin/sendmail -bs');

                return new Swift_SendmailTransport($sendmail_command);

            case 'external':
                // SMTP
                $smtp_helo = Site::getPreference('SMTP_HELO');
                $smtp_host = Site::getPreference('SMTP_HOST');
                $smtp_port = (int) Site::getPreference('SMTP_PORT', '25');
                $smtp_auth = (bool) Site::getPreference('SMTP_AUTH');
                $smtp_user = Site::getPreference('SMTP_AUTH_USER');
                $smtp_pass = Site::getPreference('SMTP_AUTH_PASS');
                $smtp_encr = Site::getPreference('SMTP_SSL');

                if ($smtp_encr === 'none') {
                    $smtp_encr = null;
                }

                $transport = new Swift_SmtpTransport($smtp_host, $smtp_port, $smtp_encr);

                $transport->setLocalDomain($smtp_helo);

                if ($smtp_auth) {
                    $transport
                        ->setUsername($smtp_user)
                        ->setPassword($smtp_pass);
                }

                return $transport;

            default:
                // For testing
                return new Swift_NullTransport();
        }
    }

    /**
     * Many mail relays require a valid sender email.
     *
     * @param string $email
     *
     * @return bool
     */
    public function isValidEmail(string $email): bool
    {
        $at_domain = strrchr($email, '@');

        if ($at_domain === false) {
            return false;
        }

        $domain = substr($at_domain, 1);

        $email_valid  = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $domain_valid = filter_var($domain, FILTER_VALIDATE_DOMAIN) !== false;

        // Some web hosts disable checkdnsrr.
        if ($domain_valid && function_exists('checkdnsrr')) {
            $domain_valid = checkdnsrr($domain);
        }

        return $email_valid && $domain_valid;
    }

    /**
     * A list SSL modes (e.g. for an edit control).
     *
     * @return array<string>
     */
    public function mailSslOptions(): array
    {
        return [
            'none' => I18N::translate('none'),
            /* I18N: Secure Sockets Layer - a secure communications protocol*/
            'ssl'  => I18N::translate('ssl'),
            /* I18N: Transport Layer Security - a secure communications protocol */
            'tls'  => I18N::translate('tls'),
        ];
    }

    /**
     * A list SSL modes (e.g. for an edit control).
     *
     * @return array<string>
     */
    public function mailTransportOptions(): array
    {
        $options = [
            /* I18N: "sendmail" is the name of some mail software */
            'sendmail' => I18N::translate('Use sendmail to send messages'),
            'external' => I18N::translate('Use SMTP to send messages'),
        ];

        if (!function_exists('proc_open')) {
            unset($options['sendmail']);
        }

        return $options;
    }
}
