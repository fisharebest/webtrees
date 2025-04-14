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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\NetworkService;
use Fisharebest\Webtrees\Validator;
use IPLib\Address\AddressInterface;
use IPLib\Factory;
use IPLib\Range\RangeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

    /**
     * @see https://github.com/ai-robots-txt/ai.robots.txt for a list of AI crawlers.
     * We can't load this repository as a dependency as it's not a package.
     * Instead, the list from version 1.26 is copied here.
     */
    public const AI_ROBOTS = [
         'AI2Bot',
         'Ai2Bot-Dolma',
         'Amazonbot',
         'anthropic-ai',
         'Applebot',
         'Applebot-Extended',
         'Brightbot 1.0',
         'Bytespider',
         'CCBot',
         'ChatGPT-User',
         'Claude-Web',
         'ClaudeBot',
         'cohere-ai',
         'cohere-training-data-crawler',
         'Crawlspace',
         'Diffbot',
         'DuckAssistBot',
         'FacebookBot',
         'FriendlyCrawler',
         'Google-Extended',
         'GoogleOther',
         'GoogleOther-Image',
         'GoogleOther-Video',
         'GPTBot',
         'iaskspider/2.0',
         'ICC-Crawler',
         'ImagesiftBot',
         'img2dataset',
         'ISSCyberRiskCrawler',
         'Kangaroo Bot',
         'meta-externalagent',
         'meta-externalfetcher',
         'OAI-SearchBot',
         'omgili',
         'omgilibot',
         'PanguBot',
         'PerplexityBot',
         'PetalBot',
         'Scrapy',
         'SemrushBot-OCOB',
         'SemrushBot-SWA',
         'Sidetrade indexer bot',
         'Timpibot',
         'VelenPublicWebCrawler',
         'Webzio-Extended',
         'YouBot',
    ];

    // Other bad robots - SEO optimisers, advertisers, etc.  This list is shared with robots.txt.
    public const BAD_ROBOTS = [
        'admantx',
        'Adsbot',
        'AhrefsBot',
        'AliyunSecBot',
        'AntBot', // Aggressive crawler
        'AspiegelBot',
        'Awario', // Brand management
        'Barkrowler', // Crawler for babbar.tech
        'BLEXBot',
        'CensysInspect', // Vulnerability scanner
        'DataForSeoBot', // https://dataforseo.com/dataforseo-bot
        'DotBot',
        'Expanse', // Another pointless crawler
        'fidget-spinner-bot', // Agressive crawler
        'Foregenix', // Vulnerability scanner
        'Go-http-client', // Crawler library used by many bots
        'Grapeshot',
        'Honolulu-bot', // Aggressive crawer, no info available
        'ia_archiver',
        'internet-measurement', // Driftnet
        'IonCrawl',
        'Java', // Crawler library used by many bots
        'linabot', // Aggressive crawer, no info available
        'Linguee',
        'MegaIndex.ru',
        'MJ12bot',
        'netEstate NE',
        'panscient',
        'phxbot', // Badly written crawler
        'proximic',
        'python-requests', // Crawler library used by many bots
        'SeekportBot', // Pretends to be a search engine - but isn't
        'SemrushBot',
        'serpstatbot',
        'SEOkicks',
        'SiteKiosk',
        'test-bot', // Agressive crawler
        'TinyTestBot',
        'Turnitin',
        'wp_is_mobile', // Nothing to do with wordpress
        'XoviBot',
        'YisouSpider',
        'ZoominfoBot',
    ];

    /**
     * Some search engines use reverse/forward DNS to verify the IP address.
     *
     * @see https://support.google.com/webmasters/answer/80553?hl=en
     * @see https://www.bing.com/webmaster/help/which-crawlers-does-bing-use-8c184ec0
     * @see https://www.bing.com/webmaster/help/how-to-verify-bingbot-3905dc26
     * @see https://yandex.com/support/webmaster/robot-workings/check-yandex-robots.html
     * @see https://www.mojeek.com/bot.html
     */
    private const ROBOT_REV_FWD_DNS = [
        'BingPreview'      => ['.search.msn.com'],
        'Google'           => ['.google.com', '.googlebot.com'],
        'Mail.RU_Bot'      => ['.mail.ru'],
        'MicrosoftPreview' => ['.search.msn.com'],
        'MojeekBot'        => ['.mojeek.com'],
        'Qwantify'         => ['.qwant.com'],
        'Sogou'            => ['.crawl.sogou.com'],
        'Yahoo'            => ['.crawl.yahoo.net'],
        'Yandex'           => ['.yandex.ru', '.yandex.net', '.yandex.com'],
        'bingbot'          => ['.search.msn.com'],
        'msnbot'           => ['.search.msn.com'],
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
        'Neevabot'    => ['.neeva.com'],
        'SeznamBot'   => ['.seznam.cz'],
    ];

    /**
     * Some search engines operate from designated IP addresses.
     *
     * @see https://help.duckduckgo.com/duckduckgo-help-pages/results/duckduckbot
     */
    private const ROBOT_IPS = [
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
        'facebook' => ['AS32934'],
        'twitter'  => ['AS13414'],
    ];

    private NetworkService $network_service;

    public function __construct(NetworkService $network_service)
    {
        $this->network_service = $network_service;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ua      = Validator::serverParams($request)->string('HTTP_USER_AGENT', '');
        $ip      = Validator::attributes($request)->string('client-ip');
        $address = Factory::parseAddressString($ip);
        assert($address instanceof AddressInterface);

        foreach ([self::AI_ROBOTS, self::BAD_ROBOTS] as $robots) {
            foreach ($robots as $robot) {
                if (str_contains($ua, $robot)) {
                    return $this->response();
                }
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

        foreach (self::ROBOT_IPS as $robot => $valid_ip_ranges) {
            if (str_contains($ua, $robot)) {
                foreach ($valid_ip_ranges as $ip_range) {
                    $range = Factory::parseRangeString($ip_range);

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
     * @param list<string> $valid_domains
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
     * @return array<RangeInterface>
     */
    private function fetchIpRangesForAsn(string $asn): array
    {
        return Registry::cache()->file()->remember('whois-asn-' . $asn, function () use ($asn): array {
            $ranges = $this->network_service->findIpRangesForAsn($asn);
            $mapper = static fn (string $range): ?RangeInterface => Factory::parseRangeString($range);
            $ranges = array_map($mapper, $ranges);

            return array_filter($ranges);
        }, random_int(self::WHOIS_TTL_MIN, self::WHOIS_TTL_MAX));
    }

    private function response(): ResponseInterface
    {
        return response('Not acceptable', StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
    }
}
