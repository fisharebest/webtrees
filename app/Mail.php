<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

namespace Fisharebest\Webtrees;

use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_NullTransport;
use Swift_Preferences;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Transport;

/**
 * Send mail messages.
 */
class Mail
{
    /**
     * Send an external email message
     * Caution! gmail may rewrite the "From" header unless you have added the address to your account.
     *
     * @param User   $from
     * @param User   $to
     * @param User   $reply_to
     * @param string $subject
     * @param string $message_text
     * @param string $message_html
     *
     * @return bool
     */
    public static function send(User $from, User $to, User $reply_to, $subject, $message_text, $message_html): bool
    {
        try {
            // Swiftmailer uses the PHP default tmp directory.  On some servers, this
            // is outside the open_basedir list.  Therefore we must set one explicitly.
            File::mkdir(WT_DATA_DIR . 'tmp');
            Swift_Preferences::getInstance()->setTempDir(WT_DATA_DIR . 'tmp');

            $message_text = preg_replace('/\r?\n/', "\r\n", $message_text);
            $message_html = preg_replace('/\r?\n/', "\r\n", $message_html);

            $message = (new Swift_Message($subject))
                ->setFrom($from->getEmail(), $from->getRealName())
                ->setTo($to->getEmail(), $to->getRealName())
                ->setReplyTo($reply_to->getEmail(), $reply_to->getRealName())
                ->setBody($message_html, 'text/html')
                ->addPart($message_text, 'text/plain');

            $mailer = new Swift_Mailer(self::transport());
            $mailer->send($message);
        } catch (Exception $ex) {
            Log::addErrorLog('Mail: ' . $ex->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Create a transport mechanism for sending mail
     *
     * @return Swift_Transport
     */
    public static function transport()
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

                $transport =(new Swift_SmtpTransport($smtp_host, $smtp_port, $smtp_encr));

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
