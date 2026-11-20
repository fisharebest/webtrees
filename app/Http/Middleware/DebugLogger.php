<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Database\Exceptions\QueryException;
use Fisharebest\Database\StatementLoggerInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_column;
use function array_slice;
use function array_sum;
use function count;
use function intdiv;
use function is_string;
use function max;
use function mb_strlen;
use function mb_substr;
use function memory_get_peak_usage;
use function microtime;
use function ord;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function strtr;

class DebugLogger implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $debug = Validator::attributes($request)->boolean('debug', false);

        if (!$debug) {
            return $handler->handle($request);
        }

        $start = microtime(true);

        $logger = new class implements StatementLoggerInterface {
            /** @var list<array{query:string,bindings:list<float|int|string|null>,time:float}> */
            public array $queries = [];

            public function log(string $sql, array $bindings, float $duration, QueryException|null $error): void
            {
                $this->queries[] = ['query' => $sql, 'bindings' => $bindings, 'time' => $duration * 1000];
            }
        };

        $connection      = DB::connection();
        $previous_logger = $connection->statementLogger();
        $connection->setStatementLogger($logger);

        try {
            $response = $handler->handle($request);
        } finally {
            $connection->setStatementLogger($previous_logger);
        }

        $queries  = $logger->queries;
        $slowest  = $queries === [] ? 0.0 : max(array_column($queries, 'time'));
        $total    = array_sum(array_column($queries, 'time'));
        $message  = sprintf('Queries: %d, slowest: %.3f ms, total: %.3f ms', count($queries), $slowest, $total);
        $response = $response->withAddedHeader('x-debug-sql', $message);

        $queries = array_slice($queries, 0, 1000); // Too many headers will break nginx/apache

        foreach ($queries as $query) {
            $sql      = $query['query'];
            $time     = $query['time'];
            $bindings = $query['bindings'];
            foreach ($bindings as $binding) {
                if (is_string($binding)) {
                    // RFC7230 allows only visible ASCII.  "?" breaks later substitutions.
                    $binding = strtr($binding, ["\n" => '\n', "\t" => '\t']);
                    $callback = static fn (array $matches): string => '\\x' . sprintf('%02X', ord($matches[0]));
                    $binding  = preg_replace_callback('/[^ !->@-~]/', $callback, $binding);
                    $binding = "'" . $binding . "'";

                    if (mb_strlen($binding) > 30) {
                        $binding = mb_substr($binding, 0, 27) . '...';
                    }
                } else {
                    $binding = (string) $binding;
                }

                $sql = preg_replace('/\?/', $binding, $sql, 1);
            }

            $message  = sprintf('%s (%.3f ms)', $sql, $time);
            $response = $response->withAddedHeader('x-debug-sql', $message);
        }

        $message = sprintf('%.3f seconds', microtime(true) - $start);
        $response = $response->withAddedHeader('x-debug-processing-time', $message);

        $message = sprintf('%d KB', intdiv(memory_get_peak_usage(), 1024));
        $response = $response->withAddedHeader('x-debug-memory-peak-usage', $message);

        return $response;
    }
}
