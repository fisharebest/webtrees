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

namespace Fisharebest\Webtrees\Services;

use Throwable;

use function fclose;
use function fsockopen;
use function fwrite;
use function is_resource;
use function preg_match_all;
use function sprintf;
use function stream_get_contents;
use function stream_set_timeout;

class NetworkService
{
    private const array  WHOIS_HOSTS           = ['whois.radb.net', 'whois.ripe.net'];
    private const string WHOIS_QUERY_FORMAT    = "-i origin %s\r\n";
    private const int    WHOIS_TIMEOUT_SECONDS = 5;

    /**
     * @return list<string>
     */
    public function findIpRangesForAsn(string $asn): array
    {
        $query = sprintf(self::WHOIS_QUERY_FORMAT, $asn);

        foreach (self::WHOIS_HOSTS as $host) {
            try {
                $stream = fsockopen(hostname: $host, port: 43, timeout: self::WHOIS_TIMEOUT_SECONDS);

                stream_set_timeout(stream: $stream, seconds: self::WHOIS_TIMEOUT_SECONDS);

                fwrite(stream: $stream, data: $query);

                $text = stream_get_contents(stream: $stream);
            } catch (Throwable) {
                continue;
            } finally {
                if (is_resource(value: $stream)) {
                    fclose(stream: $stream);
                }
            }

            preg_match_all(pattern: '/\nroute6?:[ \t]*([0-9a-f.:]+\/[0-9]+)/i', subject: $text, matches: $matches);

            if ($matches[1] !== []) {
                return $matches[1];
            }
        }

        return [];
    }
}
