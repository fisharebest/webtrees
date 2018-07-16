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
namespace Fisharebest\Webtrees;

use Exception;
use Swift_Mailer;
use Swift_MailTransport;
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
    public static function send(User $from, User $to, User $reply_to, $subject, $message_text, $message_html)
    {
        try {
            // Swiftmailer uses the PHP default tmp directory.  On some servers, this
            // is outside the open_basedir list.  Therefore we must set one explicitly.
            File::mkdir(WT_DATA_DIR . 'tmp');
            Swift_Preferences::getInstance()->setTempDir(WT_DATA_DIR . 'tmp');

            $message_text = preg_replace('/\r?\n/', "\r\n", $message_text);
            $message_html = preg_replace('/\r?\n/', "\r\n", $message_html);

            $message = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($from->getEmail(), $from->getRealName())
                ->setTo($to->getEmail(), $to->getRealName())
                ->setReplyTo($reply_to->getEmail(), $reply_to->getRealName())
                ->setBody($message_html, 'text/html')
                ->addPart($message_text, 'text/plain');

            Swift_Mailer::newInstance(self::transport())->send($message);
        } catch (Exception $ex) {
            DebugBar::addThrowable($ex);

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
            case 'internal':
                return Swift_MailTransport::newInstance();
            case 'sendmail':
                return Swift_SendmailTransport::newInstance();
            case 'external':
                $transport = Swift_SmtpTransport::newInstance()
                    ->setHost(Site::getPreference('SMTP_HOST'))
                    ->setPort(Site::getPreference('SMTP_PORT'))
                    ->setLocalDomain(Site::getPreference('SMTP_HELO'));

                if (Site::getPreference('SMTP_AUTH') === '1') {
                    $transport
                        ->setUsername(Site::getPreference('SMTP_AUTH_USER'))
                        ->setPassword(Site::getPreference('SMTP_AUTH_PASS'));
                }

                if (Site::getPreference('SMTP_SSL') !== 'none') {
                    $transport->setEncryption(Site::getPreference('SMTP_SSL'));
                }

                return $transport;
            default:
                // For testing
                return Swift_NullTransport::newInstance();
        }
    }
}
