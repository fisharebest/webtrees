<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\NullTransport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Crypto\DkimOptions;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\RfcComplianceException;
use Symfony\Component\Mime\Message;

use function assert;
use function checkdnsrr;
use function function_exists;
use function str_replace;
use function strrchr;
use function substr;

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
        try {
            $message   = $this->message($from, $to, $reply_to, $subject, $message_text, $message_html);
            $transport = $this->transport();
            $mailer    = new Mailer($transport);
            $mailer->send($message);
        } catch (RfcComplianceException $ex) {
            Log::addErrorLog('Cannot create email  ' . $ex->getMessage());

            return false;
        } catch (TransportExceptionInterface $ex) {
            Log::addErrorLog('Cannot send email: ' . $ex->getMessage());

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
     * @return Message
     */
    protected function message(UserInterface $from, UserInterface $to, UserInterface $reply_to, string $subject, string $message_text, string $message_html): Message
    {
        // Mail needs MS-DOS line endings
        $message_text = str_replace("\n", "\r\n", $message_text);
        $message_html = str_replace("\n", "\r\n", $message_html);

        $message = (new Email())
            ->subject($subject)
            ->from(new Address($from->email(), $from->realName()))
            ->to(new Address($to->email(), $to->realName()))
            ->replyTo(new Address($reply_to->email(), $reply_to->realName()))
            ->html($message_html);

        $dkim_domain   = Site::getPreference('DKIM_DOMAIN');
        $dkim_selector = Site::getPreference('DKIM_SELECTOR');
        $dkim_key      = Site::getPreference('DKIM_KEY');

        if ($dkim_domain !== '' && $dkim_selector !== '' && $dkim_key !== '') {
            $signer = new DkimSigner($dkim_key, $dkim_domain, $dkim_selector);
            $options = (new DkimOptions())
                ->headerCanon('relaxed')
                ->bodyCanon('relaxed');

            return $signer->sign($message, $options->toArray());
        }

        // DKIM body hashes don't work with multipart/alternative content.
        $message->text($message_text);

        return $message;
    }

    /**
     * Create a transport mechanism for sending mail
     *
     * @return TransportInterface
     */
    protected function transport(): TransportInterface
    {
        switch (Site::getPreference('SMTP_ACTIVE')) {
            case 'sendmail':
                // Local sendmail (requires PHP proc_* functions)
                $request = app(ServerRequestInterface::class);
                assert($request instanceof ServerRequestInterface);

                $sendmail_command = Validator::attributes($request)->string('sendmail_command', '/usr/sbin/sendmail -bs');

                return new SendmailTransport($sendmail_command);

            case 'external':
                // SMTP
                $smtp_helo = Site::getPreference('SMTP_HELO');
                $smtp_host = Site::getPreference('SMTP_HOST');
                $smtp_port = (int) Site::getPreference('SMTP_PORT');
                $smtp_auth = (bool) Site::getPreference('SMTP_AUTH');
                $smtp_user = Site::getPreference('SMTP_AUTH_USER');
                $smtp_pass = Site::getPreference('SMTP_AUTH_PASS');
                $smtp_encr = Site::getPreference('SMTP_SSL') === 'ssl';

                $transport = new EsmtpTransport($smtp_host, $smtp_port, $smtp_encr);

                $transport->setLocalDomain($smtp_helo);

                if ($smtp_auth) {
                    $transport
                        ->setUsername($smtp_user)
                        ->setPassword($smtp_pass);
                }

                return $transport;

            default:
                // For testing
                return new NullTransport();
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
        try {
            $address = new Address($email);
        } catch (RfcComplianceException) {
            return false;
        }

        // Some web hosts disable checkdnsrr.
        if (function_exists('checkdnsrr')) {
            $domain = substr(strrchr($address->getAddress(), '@') ?: '@', 1);
            return checkdnsrr($domain);
        }

        return true;
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
            /* I18N: Use SMTP over SSL/TLS, or Implicit TLS - a secure communications protocol */
            'ssl'  => I18N::translate('SSL/TLS'),
            /* I18N: Use SMTP with STARTTLS, or Explicit TLS - a secure communications protocol */
            'tls'  => I18N::translate('STARTTLS'),
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
