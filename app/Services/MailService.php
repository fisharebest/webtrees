<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Exception;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Site;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;
use function str_replace;

/**
 * Send mail messages.
 */
class MailService
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
            // Mail needs MSDOS line endings
            $message_text = str_replace("\n", "\r\n", $message_text);
            $message_html = str_replace("\n", "\r\n", $message_html);

            $message = (new Swift_Message())
                ->setSubject($subject)
                ->setFrom($from->email(), $from->realName())
                ->setTo($to->email(), $to->realName())
                ->setReplyTo($reply_to->email(), $reply_to->realName())
                ->setBody($message_html, 'text/html')
                ->addPart($message_text, 'text/plain');

            $mailer = new Swift_Mailer($this->transport());
            $mailer->send($message);
        } catch (Exception $ex) {
            Log::addErrorLog('MailService: ' . $ex->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Create a transport mechanism for sending mail
     *
     * @return Swift_Transport
     */
    private function transport(): Swift_Transport
    {
        switch (Site::getPreference('SMTP_ACTIVE')) {
            case 'sendmail':
                // Local sendmail (requires PHP proc_* functions)
                return new Swift_SendmailTransport();

            case 'external':
                // SMTP
                $smtp_host = Site::getPreference('SMTP_HOST');
                $smtp_port = (int) Site::getPreference('SMTP_PORT', '25');
                $smtp_auth = Site::getPreference('SMTP_AUTH');
                $smtp_user = Site::getPreference('SMTP_AUTH_USER');
                $smtp_pass = Site::getPreference('SMTP_AUTH_PASS');
                $smtp_encr = Site::getPreference('SMTP_SSL');

                $transport = new Swift_SmtpTransport($smtp_host, $smtp_port, $smtp_encr);

                $transport->setLocalDomain(Site::getPreference('SMTP_HELO'));

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
}
