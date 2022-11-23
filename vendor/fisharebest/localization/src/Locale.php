<?php

namespace Fisharebest\Localization;

use DomainException;
use Fisharebest\Localization\Locale\LocaleInterface;

/**
 * Class Locale - Static functions to generate and compare locales.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class Locale
{
    /**
     * Some browsers let the user choose "Chinese, Traditional", but add headers for "zh-HK"...
     *
     * @var array<string,string>
     */
    private static $http_accept_chinese = array(
        'zh-cn' => 'zh-hans-cn',
        'zh-sg' => 'zh-hans-sg',
        'zh-hk' => 'zh-hant-hk',
        'zh-mo' => 'zh-hant-mo',
        'zh-tw' => 'zh-hant-tw',
    );

    /**
     * Callback for PHP sort functions - allows lists of locales to be sorted.
     * Diacritics are removed and text is capitalized to allow fast/simple sorting.
     *
     * @param LocaleInterface $x
     * @param LocaleInterface $y
     *
     * @return int
     */
    public static function compare(LocaleInterface $x, LocaleInterface $y)
    {
        return strcmp($x->endonymSortable(), $y->endonymSortable());
    }

    /**
     * Create a locale from a language tag (or locale code).
     *
     * @param string $code
     *
     * @return LocaleInterface
     * @throws DomainException
     */
    public static function create($code)
    {
        $class = __NAMESPACE__ . '\Locale\Locale' . implode(array_map(function ($x) {
            return ucfirst(strtolower($x));
        }, preg_split('/[^a-zA-Z0-9]+/', $code)));

        if (class_exists($class)) {
            $locale = new $class();

            if ($locale instanceof LocaleInterface) {
                return $locale;
            }
        }

        throw new DomainException($code);
    }

    /**
     * Create a locale from a language tag (or locale code).
     *
     * @param string[]          $server    The $_SERVER array
     * @param LocaleInterface[] $available All locales supported by the application
     * @param LocaleInterface   $default   Locale to show in no matching locales
     *
     * @return LocaleInterface
     */
    public static function httpAcceptLanguage(array $server, array $available, LocaleInterface $default)
    {
        if (!empty($server['HTTP_ACCEPT_LANGUAGE'])) {
            $http_accept_language = strtolower(str_replace(' ', '', $server['HTTP_ACCEPT_LANGUAGE']));
            preg_match_all('/(?:([a-z][a-z0-9_-]+)(?:;q=([0-9.]+))?)/', $http_accept_language, $match);
            $preferences = array_map(function ($x) {
                return $x === '' ? 1.0 : (float) $x;
            }, array_combine($match[1], $match[2]));

            // "Common sense" logic for badly configured clients.
            $preferences = self::httpAcceptChinese($preferences);
            $preferences = self::httpAcceptDowngrade($preferences);

            // Need a stable sort, as the original order is significant
            $preferences = array_map(function ($x) {
                static $n = 0;

                return array($x, --$n);
            }, $preferences);
            arsort($preferences);
            $preferences = array_map(function ($x) {
                return $x[0];
            }, $preferences);

            foreach (array_keys($preferences) as $code) {
                try {
                    $locale = self::create($code);
                    if (in_array($locale, $available, false)) {
                        return $locale;
                    }
                } catch (DomainException $ex) {
                    // An unknown locale?  Ignore it.
                }
            }
        }

        return $default;
    }

    /**
     * If a client requests "de-DE" (but not "de"), then add "de" as a lower-priority fallback.
     *
     * @param array<array-key,float> $preferences
     *
     * @return array<array-key,float>
     */
    private static function httpAcceptDowngrade($preferences)
    {
        foreach ($preferences as $code => $priority) {
            // Three parts: "zh-hans-cn" => "zh-hans" and "zh"
            if (preg_match('/^(([a-z]+)-[a-z]+)-[a-z]+$/', $code, $match)) {
                if (!array_key_exists($match[2], $preferences)) {
                    $preferences[$match[2]] = $priority * 0.95;
                }
                if (!array_key_exists($match[1], $preferences)) {
                    $preferences[$match[1]] = $priority * 0.95;
                }
            }
            // Two parts: "de-de" => "de"
            if (preg_match('/^([a-z]+)-[a-z]+$/', $code, $match) && !array_key_exists($match[1], $preferences)) {
                $preferences[$match[1]] = $priority * 0.95;
            }
        }

        return $preferences;
    }

    /**
     * Some browsers allow the user to select "Chinese (simplified)", but then use zh-CN instead of zh-Hans.
     * This goes against the advice of w3.org.
     *
     * @param array<array-key,float> $preferences
     *
     * @return array<array-key,float>
     */
    private static function httpAcceptChinese($preferences)
    {
        foreach (self::$http_accept_chinese as $old => $new) {
            if (array_key_exists($old, $preferences) && !array_key_exists($new, $preferences)) {
                $preferences[$new] = $preferences[$old] * 0.95;
            }
        }

        return $preferences;
    }
}
