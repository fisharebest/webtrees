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

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use function session_status;

/**
 * Session handling
 */
class Session
{
    /**
     * Start a session
     *
     * @return void
     */
    public static function start(): void
    {
        $domain   = '';
        $path     = parse_url(WT_BASE_URL, PHP_URL_PATH);
        $secure   = parse_url(WT_BASE_URL, PHP_URL_SCHEME) === 'https';
        $httponly = true;

        // Paths containing UTF-8 characters need special handling.
        $path = implode('/', array_map('rawurlencode', explode('/', $path)));

        self::setSaveHandler();

        session_name('WT_SESSION');
        session_register_shutdown();
        session_set_cookie_params(0, $path, $domain, $secure, $httponly);
        session_start();

        // A new session? Prevent session fixation attacks by choosing a new session ID.
        if (!self::get('initiated')) {
            self::regenerate(true);
            self::put('initiated', true);
        }
    }

    /**
     * Initialise our session save handler
     *
     * @return void
     */
    private static function setSaveHandler(): void
    {
        session_set_save_handler(
            static function (): bool {
                return Session::open();
            },
            static function (): bool {
                return Session::close();
            },
            static function (string $id): string {
                return Session::read($id);
            },
            static function (string $id, string $data): bool {
                return Session::write($id, $data);
            },
            static function (string $id): bool {
                return Session::destroy($id);
            },
            static function (int $maxlifetime): bool {
                return Session::gc($maxlifetime);
            }
        );
    }

    /**
     * For session_set_save_handler()
     *
     * @return bool
     */
    private static function open(): bool
    {
        return true;
    }

    /**
     * For session_set_save_handler()
     *
     * @return bool
     */
    private static function close(): bool
    {
        return true;
    }

    /**
     * For session_set_save_handler()
     *
     * @param string $id
     *
     * @return string
     */
    private static function read(string $id): string
    {
        return (string) DB::table('session')
            ->where('session_id', '=', $id)
            ->value('session_data');
    }

    /**
     * For session_set_save_handler()
     *
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    private static function write(string $id, string $data): bool
    {
        $request = app(ServerRequestInterface::class);

        DB::table('session')->updateOrInsert([
            'session_id' => $id,
        ], [
            'session_time' => Carbon::now(),
            'user_id'      => (int) Auth::id(),
            'ip_address'   => $request->getClientIp(),
            'session_data' => $data,
        ]);

        return true;
    }

    /**
     * For session_set_save_handler()
     *
     * @param string $id
     *
     * @return bool
     */
    private static function destroy(string $id): bool
    {
        DB::table('session')
            ->where('session_id', '=', $id)
            ->delete();

        return true;
    }

    /**
     * For session_set_save_handler()
     *
     * @param int $maxlifetime
     *
     * @return bool
     */
    private static function gc(int $maxlifetime): bool
    {
        DB::table('session')
            ->where('session_time', '<', Carbon::now()->subSeconds($maxlifetime))
            ->delete();

        return true;
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
     * Set an explicit session ID. Typically used for search robots.
     *
     * @param string $id
     *
     * @return void
     */
    public static function setId(string $id): void
    {
        session_id($id);
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
