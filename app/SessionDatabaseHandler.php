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

namespace Fisharebest\Webtrees;

use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;
use stdClass;

/**
 * Session handling - stores sessions in the database.
 */
class SessionDatabaseHandler implements SessionHandlerInterface
{
    /** @var ServerRequestInterface */
    private $request;

    /** @var stdClass|null The row from the session table */
    private $row;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $save_path
     * @param string $name
     *
     * @return bool
     */
    public function open($save_path, $name): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @param string $session_id
     *
     * @return string
     */
    public function read($session_id): string
    {
        $this->row = DB::table('session')
            ->where('session_id', '=', $session_id)
            ->first();


        return $this->row->session_data ?? '';
    }

    /**
     * @param string $session_id
     * @param string $session_data
     *
     * @return bool
     */
    public function write($session_id, $session_data): bool
    {
        $ip_address   = $this->request->getAttribute('client-ip');
        $session_time = Carbon::now();
        $user_id      = (int) Auth::id();

        if ($this->row === null) {
            DB::table('session')->insert([
                'session_id'   => $session_id,
                'session_time' => $session_time,
                'user_id'      => $user_id,
                'ip_address'   => $ip_address,
                'session_data' => $session_data,
            ]);
        } else {
            $updates = [];

            // The user ID can change if we masquerade as another user.
            if ((int) $this->row->user_id !== $user_id) {
                $updates['user_id'] = $user_id;
            }

            if ($this->row->ip_address !== $ip_address) {
                $updates['ip_address'] = $ip_address;
            }

            if ($this->row->session_data !== $session_data) {
                $updates['session_data'] = $session_data;
            }

            if ($session_time->subMinute()->gt($this->row->session_time)) {
                $updates['session_time'] = $session_time;
            }

            if ($updates !== []) {
                DB::table('session')
                    ->where('session_id', '=', $session_id)
                    ->update($updates);
            }
        }

        return true;
    }

    /**
     * @param string $session_id
     *
     * @return bool
     */
    public function destroy($session_id): bool
    {
        DB::table('session')
            ->where('session_id', '=', $session_id)
            ->delete();

        return true;
    }

    /**
     * @param int $maxlifetime
     *
     * @return bool
     */
    public function gc($maxlifetime): bool
    {
        DB::table('session')
            ->where('session_time', '<', Carbon::now()->subSeconds($maxlifetime))
            ->delete();

        return true;
    }
}
