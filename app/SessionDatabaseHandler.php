<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use function date;
use function time;

/**
 * Session handling - stores sessions in the database.
 */
class SessionDatabaseHandler implements SessionHandlerInterface
{
    private ServerRequestInterface $request;

    private ?object $row;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        $this->row = DB::table('session')
            ->where('session_id', '=', $id)
            ->first();

        return $this->row->session_data ?? '';
    }

    public function write($id, $data): bool
    {
        $ip_address = Validator::attributes($this->request)->string('client-ip');
        $user_id    = (int) Auth::id();

        if ($this->row === null) {
            DB::table('session')->insert([
                'session_id'   => $id,
                'session_time' => date('Y-m-d H:i:s'),
                'user_id'      => $user_id,
                'ip_address'   => $ip_address,
                'session_data' => $data,
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

            if ($this->row->session_data !== $data) {
                $updates['session_data'] = $data;
            }

            // Only update session once a minute to reduce contention on the session table.
            if (date('Y-m-d H:i:s', time() - 60) > $this->row->session_time) {
                $updates['session_time'] =  date('Y-m-d H:i:s');
            }

            if ($updates !== []) {
                DB::table('session')
                    ->where('session_id', '=', $id)
                    ->update($updates);
            }
        }

        return true;
    }

    public function destroy($id): bool
    {
        DB::table('session')
            ->where('session_id', '=', $id)
            ->delete();

        return true;
    }

    #[\ReturnTypeWillChange]
    public function gc($max_lifetime)
    {
        return DB::table('session')
            ->where('session_time', '<', date('Y-m-d H:i:s', time() - $max_lifetime))
            ->delete();
    }
}
