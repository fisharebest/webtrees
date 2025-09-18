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
use function count;
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
    public const string ROBOT_ATTRIBUTE_NAME = 'is-a-robot';

    // Cache whois requests.  Try to avoid all caches expiring at the same time.
    private const int WHOIS_TTL_MIN = 28 * 86400;
    private const int WHOIS_TTL_MAX = 35 * 86400;

    // An opinionated list of "bad" robots. Typically, these are AI and SEO crawlers.
    public const array BAD_ROBOTS = [
        'ADmantX',
        'AI2Bot',
        'Adsbot',
        'AISearchBot',
        'AhrefsBot',
        'Ai2Bot-Dolma',
        'AliyunSecBot',
        'Amazonbot',
        'Andibot',
        'AntBot',
        'Applebot',
        'AspiegelBot',
        'Awario',
        'BLEXBot',
        'Barkrowler',
        'Brightbot',
        'Bytespider',
        'CCBot',
        'CensysInspect',
        'ChatGPT-User',
        'Claude-SearchBot',
        'Claude-User',
        'Claude-Web',
        'ClaudeBot',
        'Cotoyogi',
        'Crawlspace',
        'DataForSeoBot',
        'Datenbank Crawler',
        'Devin',
        'Diffbot',
        'DotBot',
        'DuckAssistBot',
        'Echobot Bot',
        'EchoboxBot',
        'Expanse',
        'FacebookBot',
        'Factset_spyderbot',
        'FirecrawlAgent',
        'Foregenix',
        'FriendlyCrawler',
        'GPTBot',
        'Gemini-Deep-Research',
        'Go-http-client',
        'Google-CloudVertexBot',
        'Google-Extended',
        'GoogleAgent-Mariner',
        'GoogleOther',
        'Grapeshot',
        'Honolulu-bot',
        'ICC-Crawler',
        'ISSCyberRiskCrawler',
        'ImagesiftBot',
        'IonCrawl',
        'Java',
        'Kangaroo Bot',
        'Linguee',
        'MJ12bot',
        'MegaIndex.ru',
        'Meta-ExternalAgent',
        'Meta-ExternalFetcher',
        'MistralAI-User',
        'MyCentralAIScraperBot',
        'NovaAct',
        'OAI-SearchBot',
        'Operator',
        'PanguBot',
        'Panscient',
        'Perplexity-User',
        'PerplexityBot',
        'PetalBot',
        'PhindBot',
        'Poseidon Research Crawler',
        'QualifiedBot',
        'QuillBot',
        'SBIntuitionsBot',
        'SEOkicks',
        'Scrapy',
        'SeekportBot',
        'SemrushBot',
        'Sidetrade indexer bot',
        'SiteKiosk',
        'SummalyBot',
        'Thinkbot',
        'TikTokSpider',
        'Timpibot',
        'TinyTestBot',
        'Turnitin',
        'VelenPublicWebCrawler',
        'WARDBot',
        'Webzio-Extended',
        'XoviBot',
        'YandexAdditional',
        'YisouSpider',
        'YouBot',
        'ZoominfoBot',
        'aiHitBot',
        'aiohttp',
        'anthropic-ai',
        'bedrockbot',
        'cohere-ai',
        'cohere-training-data-crawler',
        'facebookexternalhit',
        'fidget-spinner-bot',
        'iaskspider',
        'img2dataset',
        'internet-measurement',
        'linabot',
        'meta-externalagent',
        'meta-externalfetcher',
        'netEstate',
        'omgili',
        'panscient',
        'phxbot',
        'proximic',
        'python-requests',
        'quillbot.com',
        'wpbot',
        'serpstatbot',
        'test-bot',
        'wp_is_mobile',
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
    private const array ROBOT_REV_FWD_DNS = [
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
    private const array ROBOT_REV_ONLY_DNS = [
        'Baiduspider' => ['.baidu.com', '.baidu.jp'],
        'FreshBot'    => ['.seznam.cz'],
        'Neevabot'    => ['.neeva.com'],
        'SeznamBot'   => ['.seznam.cz'],
    ];

    /**
     * Some search engines operate from designated IP addresses.
     * TODO: fetch current lists of IPs, rather than use hard-coded values.
     * See https://merj.com/blog/dont-block-what-you-want-duckduckgo-and-common-crawl-to-provide-ip-address-api-endpoints
     */

    /**
     * Some search engines operate from within a designated autonomous system.
     *
     * @see https://developers.facebook.com/docs/sharing/webmasters/crawler
     * @see https://www.facebook.com/peering/
     */
    private const array ROBOT_ASNS = [
        'facebook' => ['AS32934'],
        'twitter'  => ['AS13414'],
    ];

    public function __construct(private readonly NetworkService $network_service)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ua      = Validator::serverParams($request)->string('HTTP_USER_AGENT', '');
        $ip      = Validator::attributes($request)->string('client-ip');
        $address = Factory::parseAddressString($ip);
        assert($address instanceof AddressInterface);

        if ($ua === '') {
            return $this->response('Not acceptable: no-ua');
        }

        foreach (self::BAD_ROBOTS as $robot) {
            if (str_contains($ua, $robot)) {
                return $this->response('Not acceptable: bad-ua');
            }
        }

        $validated_bot =  false;

        foreach (self::ROBOT_REV_FWD_DNS as $robot => $valid_domains) {
            if (str_contains($ua, $robot)) {
                if ($this->checkRobotDNS($ip, $valid_domains, false)) {
                    $validated_bot = true;
                } else {
                    return $this->response('Not acceptable: bad-dns');
                }
            }
        }

        foreach (self::ROBOT_REV_ONLY_DNS as $robot => $valid_domains) {
            if (str_contains($ua, $robot)) {
                if ($this->checkRobotDNS($ip, $valid_domains, true)) {
                    $validated_bot = true;
                } else {
                    return $this->response('Not acceptable: bad-dns');
                }
            }
        }

        // TODO: fetch current lists of IPs, rather than use hard-coded values.

        foreach (self::ROBOT_ASNS as $robot => $asns) {
            foreach ($asns as $asn) {
                if (str_contains($ua, $robot)) {
                    foreach ($this->fetchIpRangesForAsn($asn) as $range) {
                        if ($range->contains($address)) {
                            $validated_bot = true;
                            continue 2;
                        }
                    }

                    return $this->response('Not acceptable: bad-dns');
                }
            }
        }

        // Allow sites to block access from entire networks.
        $block_asn = Validator::attributes($request)->string('block_asn', '');
        preg_match_all('/(AS\d+)/', $block_asn, $matches);

        foreach ($matches[1] as $asn) {
            foreach ($this->fetchIpRangesForAsn($asn) as $range) {
                if ($range->contains($address)) {
                    return $this->response('Not acceptable: bad-asn');
                }
            }
        }

        // No Cookies?  Few headers?  Probably a robot.
        $has_cookies     = $request->getCookieParams() !== [];
        $has_few_headers = count($request->getHeaders()) <= 11;
        $suspected_bot   = !$has_cookies && $has_few_headers;

        // Robots often claim to be a browser.
        $claims_to_be_human =
            str_contains($ua, 'Chrome/') ||
            str_contains($ua, 'Firefox/') ||
            str_contains($ua, 'Opera/') ||
            str_contains($ua, 'Safari/')
        ;

        // Validated bots (such as google and bing) use headless browsers.  This is OK.
        // Anyone else claiming to be a browser needs to prove it by setting a cookie.
        if (!$validated_bot && $claims_to_be_human && !$has_cookies) {
            $content =
                '<!DOCTYPE html>' .
                '<html lang="en">' .
                '<head>' .
                '<meta charset="utf-8">' .
                '<title>Cookie check</title>' .
                '<meta http-equiv="refresh" content="0">' .
                '</head>' .
                '<body>Cookie check</body>' .
                '</html>';

            return $this->response($content)
                ->withHeader('set-cookie', 'x=y; HttpOnly; SameSite=Strict');
        }

        // Bots get restricted access
        if ($validated_bot || $suspected_bot) {
            $request = $request->withAttribute(self::ROBOT_ATTRIBUTE_NAME, true);
        }

        // Scans for WordPress vulnerabilities?
        // Block these before wasting resources on DB connections, sessions, etc.
        $path = $request->getUri()->getPath();

        if (str_starts_with($path, '/xmlrpc.php') || str_starts_with($path, '/wp-')) {
            return $this->response('Not acceptable: not-wp');
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
            $mapper = static fn (string $range): RangeInterface|null => Factory::parseRangeString($range);
            $ranges = array_map($mapper, $ranges);

            return array_filter($ranges);
        }, random_int(self::WHOIS_TTL_MIN, self::WHOIS_TTL_MAX));
    }

    private function response(string $content): ResponseInterface
    {
        return response($content, StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
    }
}
