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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TelemetryDataService;
use Fisharebest\Webtrees\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function json_encode;
use function redirect;
use function route;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class TelemetrySubmitAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly TelemetryDataService $telemetry_data_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $payload = $this->telemetry_data_service->assemblePayload();
        $json    = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $endpointUrl    = Site::getPreference('TELEMETRY_ENDPOINT_URL') ?: 'https://vjdwtasmykejamkdprro.supabase.co';
        $publishableKey = Site::getPreference('TELEMETRY_PUBLISHABLE_KEY') ?: 'sb_publishable_mzCwYJ8Fs2yjE2gvQgDDeg_Vwpj5uDQ';

        $url = rtrim($endpointUrl, '/') . '/rest/v1/rpc/submit_webtrees_telemetry';

        try {
            $client = new Client([
                'timeout' => 15,
            ]);

            $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'apikey'        => $publishableKey,
                    'Authorization' => 'Bearer ' . $publishableKey,
                    'Prefer'        => 'return=minimal',
                ],
                'body' => $json,
            ]);

            FlashMessages::addMessage(I18N::translate('Telemetry data has been sent successfully. Thank you!'), 'success');
        } catch (GuzzleException $exception) {
            $errorDetail = '';
            if ($exception instanceof RequestException && $exception->getResponse() !== null) {
                $errorDetail = (string) $exception->getResponse()->getBody();
            }
            if ($errorDetail === '') {
                $errorDetail = $exception->getMessage();
            }
            FlashMessages::addMessage(I18N::translate('Failed to send telemetry data.') . ' ' . $errorDetail, 'danger');
        }

        return redirect(route(ControlPanel::class));
    }
}
