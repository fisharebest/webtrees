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

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function dirname;
use function e;
use function ob_end_clean;
use function ob_get_level;
use function preg_replace;
use function preg_replace_callback;
use function response;
use function str_replace;
use function view;

use const PHP_EOL;

readonly class AbstractExceptionHandler
{
    /**
     * Exceptions can be thrown while buffering output.
     * We need to close these buffers, or we won't be able to send output.
     */
    protected function closeOutputBuffers(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Create a human-friendly stack dump.
     */
    protected function stackTrace(Throwable $exception): string
    {
        $trace =
            $exception->getMessage() . ' ' . $exception->getFile() . ':' . $exception->getLine() .
            PHP_EOL .
            $exception->getTraceAsString();

        // Redact the path name
        $trace = str_replace(dirname(__DIR__, 3), '', $trace);

        // Remove namespaces from FQCNs
        $trace = preg_replace_callback(
            '/^(#\d+ .+): (.+)$/m',
            static fn (array $matches): string => $matches[1] . ': ' . preg_replace('/(?:[a-z0-9_]\w*\\\\)+([a-z0-9_]\w*)/i', '$1', $matches[2]),
            $trace
        );

        $html = e($trace);

        $previous = $exception->getPrevious();

        if ($previous instanceof Throwable) {
            $html .= '<br><br>' . $this->stackTrace($previous);
        }

        return $html;
    }

    protected function isAjaxRequest(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') !== '';
    }

    protected function ajaxResponse(string $alert, HttpStatusCode $status_code): ResponseInterface
    {
        return response(view('components/alert-danger', ['alert' => $alert]), $status_code);
    }
}
