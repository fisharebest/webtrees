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
    const EOL = "<br>\r\n"; // End-of-line that works for both TEXT and HTML messages

    /**
     * Send an external email message
     * Caution! gmail may rewrite the "From" header unless you have added the address to your account.
     *
     * @param Tree   $tree
     * @param string $to_email
     * @param string $to_name
     * @param string $replyto_email
     * @param string $replyto_name
     * @param string $subject
     * @param string $message
     *
     * @return bool
     */
    public static function send(Tree $tree, $to_email, $to_name, $replyto_email, $replyto_name, $subject, $message)
    {
        try {
            // Swiftmailer uses the PHP default tmp directory.  On some servers, this
            // is outside the open_basedir list.  Therefore we must set one explicitly.
            File::mkdir(WT_DATA_DIR . 'tmp');

            Swift_Preferences::getInstance()->setTempDir(WT_DATA_DIR . 'tmp');

            $mail = Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom(Site::getPreference('SMTP_FROM_NAME'), $tree->getPreference('title'))
                ->setTo($to_email, $to_name)
                ->setReplyTo($replyto_email, $replyto_name)
                ->setBody($message, 'text/html')
                ->addPart(Filter::unescapeHtml($message), 'text/plain');

            Swift_Mailer::newInstance(self::transport())->send($mail);
        } catch (Exception $ex) {
            Log::addErrorLog('Mail: ' . $ex->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Send an automated system message (such as a password reminder) from a tree to a user.
     *
     * @param Tree   $tree
     * @param User   $user
     * @param string $subject
     * @param string $message
     *
     * @return bool
     */
    public static function systemMessage(Tree $tree, User $user, $subject, $message)
    {
        return self::send(
            $tree,
            $user->getEmail(), $user->getRealName(),
            Site::getPreference('SMTP_FROM_NAME'), $tree->getPreference('title'),
            $subject,
            $message
        );
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

                if (Site::getPreference('SMTP_AUTH')) {
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
