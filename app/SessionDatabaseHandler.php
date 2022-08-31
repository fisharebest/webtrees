<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

    private ?object $row = null;

    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return bool
     */
    public function open(string $path, string $name): bool
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
     * @param string $id
     *
     * @return string
     */
    public function read(string $id): string
    {
        $this->row = DB::table('session')
            ->where('session_id', '=', $id)
            ->first();

        return $this->row->session_data ?? '';
    }

    /**
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function write(string $id, string $data): bool
    {
        $ip_address = Validator::attributes($this->request)->string('client-ip');
        $user_id    = Auth::id();
        $now        = Registry::timestampFactory()->now();

        if ($this->row === null) {
            DB::table('session')->insert([
                'session_id'   => $id,
                'session_time' => $now->toDateTimeString(),
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
            if ($now->subtractMinutes(1)->timestamp() > Registry::timestampFactory()->fromString($this->row->session_time)->timestamp()) {
                $updates['session_time'] =  $now->toDateTimeString();
            }

            if ($updates !== []) {
                DB::table('session')
                    ->where('session_id', '=', $id)
                    ->update($updates);
            }
        }

        return true;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function destroy(string $id): bool
    {
        DB::table('session')
            ->where('session_id', '=', $id)
            ->delete();

        return true;
    }

    /**
     * @param int $max_lifetime
     *
     * @return int
     */
    public function gc(int $max_lifetime): int
    {
        return DB::table('session')
            ->where('session_time', '<', date('Y-m-d H:i:s', time() - $max_lifetime))
            ->delete();
    }
}
