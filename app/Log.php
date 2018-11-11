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

use Symfony\Component\HttpFoundation\Request;

/**
 * Record webtrees events in the database
 */
class Log
{
    // We can log the following types of message in the wt_log table.
    const TYPE_AUTHENTICATION = 'auth';
    const TYPE_CONFIGURATION  = 'config';
    const TYPE_EDIT           = 'edit';
    const TYPE_ERROR          = 'error';
    const TYPE_MEDIA          = 'media';
    const TYPE_SEARCH         = 'search';

    /**
     * Store a new message (of the appropriate type) in the message log.
     *
     * @param string    $message
     * @param string    $log_type
     * @param Tree|null $tree
     *
     * @return void
     */
    private static function addLog($message, $log_type, Tree $tree = null)
    {
        $request = Request::create('', '', [], [], [], $_SERVER);

        Database::prepare(
            "INSERT INTO `##log` (log_type, log_message, ip_address, user_id, gedcom_id) VALUES (?, ?, ?, ?, ?)"
        )->execute([
            $log_type,
            $message,
            $request->getClientIp(),
            Auth::id(),
            $tree ? $tree->id() : null,
        ]);
    }

    /**
     * Store an authentication message in the message log.
     *
     * @param string $message
     *
     * @return void
     */
    public static function addAuthenticationLog($message)
    {
        self::addLog($message, self::TYPE_AUTHENTICATION, null);
    }

    /**
     * Store a configuration message in the message log.
     *
     * @param string    $message
     * @param Tree|null $tree
     *
     * @return void
     */
    public static function addConfigurationLog($message, Tree $tree = null)
    {
        self::addLog($message, self::TYPE_CONFIGURATION, $tree);
    }

    /**
     * Store an edit message in the message log.
     *
     * @param string $message
     * @param Tree   $tree
     *
     * @return void
     */
    public static function addEditLog($message, Tree $tree)
    {
        self::addLog($message, self::TYPE_EDIT, $tree);
    }

    /**
     * Store an error message in the message log.
     *
     * @param string $message
     *
     * @return void
     */
    public static function addErrorLog($message)
    {
        self::addLog($message, self::TYPE_ERROR);
    }

    /**
     * Store an media management message in the message log.
     *
     * @param string $message
     *
     * @return void
     */
    public static function addMediaLog($message)
    {
        self::addLog($message, self::TYPE_MEDIA, null);
    }

    /**
     * Store a search event in the message log.
     *
     * Unlike most webtrees activity, search is not restricted to a single tree,
     * so we need to record which trees were searchecd.
     *
     * @param string $message
     * @param Tree[] $trees Which trees were searched
     *
     * @return void
     */
    public static function addSearchLog($message, array $trees)
    {
        foreach ($trees as $tree) {
            self::addLog($message, self::TYPE_SEARCH, $tree);
        }
    }
}
