<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Iodev\Whois\Loaders\CurlLoader;
use Iodev\Whois\Modules\Asn\AsnRouteInfo;
use Iodev\Whois\Whois;
use IPLib\Address\AddressInterface;
use IPLib\Factory as IPFactory;
use IPLib\Range\RangeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function array_filter;
use function array_map;
use function assert;
use function gethostbyaddr;
use function gethostbyname;
use function preg_match_all;
use function random_int;
use function response;
use function str_contains;
use function str_ends_with;

/**
 * Middleware to block bad robots before they waste our valuable CPU cycles.
 */
class BadBotBlocker implements MiddlewareInterface
{
    // Cache whois requests.  Try to avoid all caches expiring at the same time.
    private const WHOIS_TTL_MIN = 28 * 86400;
    private const WHOIS_TTL_MAX = 35 * 86400;
    private const WHOIS_TIMEOUT = 5;

    // Bad robots - SEO optimisers, advertisers, etc.  This list is shared with robots.txt.
    public const BAD_ROBOTS = [
        'admantx',
        'Adsbot',
        'AhrefsBot',
        'AspiegelBot',
        'Barkrowler',
        'BLEXBot',
        'DataForSEO',
        'DotBot',
        'Grapeshot',
        'ia_archiver',
        'Linguee',
        'MJ12bot',
        'panscient',
        'PetalBot',
        'proximic',
        'SemrushBot',
        'Turnitin',
        'XoviBot',
        'ZoominfoBot',
    ];

    /**
     * Some search engines use reverse/forward DNS to verify the IP address.
     *
     * @see https://developer.amazon.com/support/amazonbot
     * @see https://support.google.com/webmasters/answer/80553?hl=en
     * @see https://www.bing.com/webmaster/help/which-crawlers-does-bing-use-8c184ec0
     * @see https://www.bing.com/webmaster/help/how-to-verify-bingbot-3905dc26
     * @see https://yandex.com/support/webmaster/robot-workings/check-yandex-robots.html
     */
    private const ROBOT_REV_FWD_DNS = [
        'Amazonbot'   => ['.crawl.amazon.com'],
        'bingbot'     => ['.search.msn.com'],
        'BingPreview' => ['.search.msn.com'],
        'Google'      => ['.google.com', '.googlebot.com'],
        'Mail.RU_Bot' => ['mail.ru'],
        'msnbot'      => ['.search.msn.com'],
        'Qwantify'    => ['.search.qwant.com'],
        'Sogou'       => ['.crawl.sogou.com'],
        'Yahoo'       => ['.crawl.yahoo.net'],
        'Yandex'      => ['.yandex.ru', '.yandex.net', '.yandex.com'],
    ];

    /**
     * Some search engines only use reverse DNS to verify the IP address.
     *
     * @see https://help.baidu.com/question?prod_id=99&class=0&id=3001
     * @see https://napoveda.seznam.cz/en/full-text-search/seznambot-crawler
     * @see https://www.ionos.de/terms-gtc/faq-crawler
     */
    private const ROBOT_REV_ONLY_DNS = [
        'Baiduspider' => ['.baidu.com', '.baidu.jp'],
        'FreshBot'    => ['.seznam.cz'],
        'IonCrawl'    => ['.1und1.org'],
    ];

    /**
     * Some search engines operate from designated IP addresses.
     *
     * @see https://www.apple.com/go/applebot
     * @see https://help.duckduckgo.com/duckduckgo-help-pages/results/duckduckbot
     */
    private const ROBOT_IPS = [
        'AppleBot'    => [
            '17.0.0.0/8',
        ],
        'Ask Jeeves'  => [
            '65.214.45.143',
            '65.214.45.148',
            '66.235.124.192',
            '66.235.124.7',
            '66.235.124.101',
            '66.235.124.193',
            '66.235.124.73',
            '66.235.124.196',
            '66.235.124.74',
            '63.123.238.8',
            '202.143.148.61',
        ],
        'DuckDuckBot' => [
            '23.21.227.69',
            '50.16.241.113',
            '50.16.241.114',
            '50.16.241.117',
            '50.16.247.234',
            '52.204.97.54',
            '52.5.190.19',
            '54.197.234.188',
            '54.208.100.253',
            '54.208.102.37',
            '107.21.1.8',
        ],
    ];

    /**
     * Some search engines operate from within a designated autonomous system.
     *
     * @see https://developers.facebook.com/docs/sharing/webmasters/crawler
     * @see https://www.facebook.com/peering/
     */
    private const ROBOT_ASNS = [
        'facebook' => ['AS32934', 'AS63293'],
        'twitter'  => ['AS13414'],
    ];

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ua      = Validator::serverParams($request)->string('HTTP_USER_AGENT', '');
        $ip      = Validator::attributes($request)->string('client-ip');
        $address = IPFactory::parseAddressString($ip);
        assert($address instanceof AddressInterface);

        foreach (self::BAD_ROBOTS as $robot) {
            if (str_contains($ua, $robot)) {
                return $this->response();
            }
        }

        foreach (self::ROBOT_REV_FWD_DNS as $robot => $valid_domains) {
            if (str_contains($ua, $robot) && !$this->checkRobotDNS($ip, $valid_domains, false)) {
                return $this->response();
            }
        }

        foreach (self::ROBOT_REV_ONLY_DNS as $robot => $valid_domains) {
            if (str_contains($ua, $robot) && !$this->checkRobotDNS($ip, $valid_domains, true)) {
                return $this->response();
            }
        }

        foreach (self::ROBOT_IPS as $robot => $valid_ips) {
            if (str_contains($ua, $robot)) {
                foreach ($valid_ips as $ip) {
                    $range = IPFactory::parseRangeString($ip);

                    if ($range instanceof RangeInterface && $range->contains($address)) {
                        continue 2;
                    }
                }

                return $this->response();
            }
        }

        foreach (self::ROBOT_ASNS as $robot => $asns) {
            foreach ($asns as $asn) {
                if (str_contains($ua, $robot)) {
                    foreach ($this->fetchIpRangesForAsn($asn) as $range) {
                        if ($range->contains($address)) {
                            continue 2;
                        }
                    }

                    return $this->response();
                }
            }
        }

        // Allow sites to block access from entire networks.
        $block_asn = Validator::attributes($request)->string('block_asn', '');
        preg_match_all('/(AS\d+)/', $block_asn, $matches);

        foreach ($matches[1] as $asn) {
            foreach ($this->fetchIpRangesForAsn($asn) as $range) {
                if ($range->contains($address)) {
                    return $this->response();
                }
            }
        }

        return $handler->handle($request);
    }

    /**
     * Check that an IP address belongs to a robot operator using a forward/reverse DNS lookup.
     *
     * @param string        $ip
     * @param array<string> $valid_domains
     * @param bool          $reverse_only
     *
     * @return bool
     */
    private function checkRobotDNS(string $ip, array $valid_domains, bool $reverse_only): bool
    {
        $host = gethostbyaddr($ip);

        if ($host === false) {
            return false;
        }

        foreach ($valid_domains as $domain) {
            if (str_ends_with($host, $domain)) {
                return $reverse_only || $ip === gethostbyname($host);
            }
        }

        return false;
    }

    /**
     * Perform a whois search for an ASN.
     *
     * @param string $asn - The autonomous system number to query
     *
     * @return array<RangeInterface>
     */
    private function fetchIpRangesForAsn(string $asn): array
    {
        return Registry::cache()->file()->remember('whois-asn-' . $asn, static function () use ($asn): array {
            $mapper = static fn (AsnRouteInfo $route_info): ?RangeInterface => IPFactory::parseRangeString($route_info->route ?: $route_info->route6);

            try {
                $loader = new CurlLoader(self::WHOIS_TIMEOUT);
                $whois  = new Whois($loader);
                $info   = $whois->loadAsnInfo($asn);
                $routes = $info->routes;
                $ranges = array_map($mapper, $routes);

                return array_filter($ranges);
            } catch (Throwable $ex) {
                return [];
            }
        }, random_int(self::WHOIS_TTL_MIN, self::WHOIS_TTL_MAX));
    }

    /**
     * @return ResponseInterface
     */
    private function response(): ResponseInterface
    {
        return response('Not acceptable', StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
    }
}
