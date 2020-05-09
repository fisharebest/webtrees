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

namespace Fisharebest\Webtrees;

use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

use function array_map;
use function explode;
use function implode;
use function parse_url;
use function session_name;
use function session_regenerate_id;
use function session_register_shutdown;
use function session_set_cookie_params;
use function session_set_save_handler;
use function session_start;
use function session_status;
use function session_write_close;

use const PHP_SESSION_ACTIVE;
use const PHP_URL_HOST;
use const PHP_URL_PATH;
use const PHP_URL_SCHEME;
use const PHP_VERSION_ID;

/**
 * Session handling
 */
class Session
{
    private const SESSION_NAME = 'WT2_SESSION';

    /**
     * Start a session
     *
     * @param ServerRequestInterface $request
     *
     * @return void
     */
    public static function start(ServerRequestInterface $request): void
    {
        // Store sessions in the database
        session_set_save_handler(new SessionDatabaseHandler($request));

        $url    = $request->getAttribute('base_url');
        $secure = parse_url($url, PHP_URL_SCHEME) === 'https';
        $domain = (string) parse_url($url, PHP_URL_HOST);
        $path   = (string) parse_url($url, PHP_URL_PATH);

        // Paths containing UTF-8 characters need special handling.
        $path = implode('/', array_map('rawurlencode', explode('/', $path)));

        session_name(self::SESSION_NAME);
        session_register_shutdown();
        // Since PHP 7.3, we can set "SameSite: Lax" to help protect against CSRF attacks.
        if (PHP_VERSION_ID > 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => $path . '/',
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            session_set_cookie_params(0, $path . '/', $domain, $secure, true);
        }
        session_start();

        // A new session? Prevent session fixation attacks by choosing a new session ID.
        if (self::get('initiated') !== true) {
            self::regenerate(true);
            self::put('initiated', true);
        }
    }

    /**
     * Save/close the session.  This releases the session lock.
     * Closing early can help concurrent connections.
     */
    public static function save(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    /**
     * Read a value from the session
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        return $_SESSION[$name] ?? $default;
    }

    /**
     * Read a value from the session and remove it.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(string $name, $default = null)
    {
        $value = self::get($name, $default);
        self::forget($name);

        return $value;
    }

    /**
     * After any change in authentication level, we should use a new session ID.
     *
     * @param bool $destroy
     *
     * @return void
     */
    public static function regenerate(bool $destroy = false): void
    {
        if ($destroy) {
            self::clear();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($destroy);
        }
    }

    /**
     * Remove all stored data from the session.
     *
     * @return void
     */
    public static function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * Write a value to the session
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public static function put(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Remove a value from the session
     *
     * @param string $name
     *
     * @return void
     */
    public static function forget(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * Cross-Site Request Forgery tokens - ensure that the user is submitting
     * a form that was generated by the current session.
     *
     * @return string
     */
    public static function getCsrfToken(): string
    {
        if (!self::has('CSRF_TOKEN')) {
            self::put('CSRF_TOKEN', Str::random(32));
        }

        return self::get('CSRF_TOKEN');
    }

    /**
     * Does a session variable exist?
     *
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset($_SESSION[$name]);
    }
}
